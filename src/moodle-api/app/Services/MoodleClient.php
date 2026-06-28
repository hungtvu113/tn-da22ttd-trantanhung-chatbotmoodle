<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MoodleClient
{
    protected string $baseUrl;
    protected string $token;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.moodle.url'), '/');
        $this->token = config('services.moodle.token');
    }

    /**
     * Goi Moodle Web Service API
     */
    public function call(string $function, array $params = []): mixed
    {
        $url = $this->baseUrl . '/webservice/rest/server.php';

        $response = Http::asForm()->post($url, array_merge([
            'wstoken' => $this->token,
            'wsfunction' => $function,
            'moodlewsrestformat' => 'json',
        ], $params));

        $data = $response->json();

        if (isset($data['exception'])) {
            Log::error('Moodle API Error', [
                'function' => $function,
                'error' => $data['message'] ?? 'Unknown error',
            ]);
            throw new \Exception('Moodle API: ' . ($data['message'] ?? 'Unknown error'));
        }

        return $data;
    }

    // ===== COURSES =====

    public function getCourses(): array
    {
        return $this->call('core_course_get_courses') ?? [];
    }

    public function getCoursesByField(string $field, string $value): array
    {
        $result = $this->call('core_course_get_courses_by_field', [
            'field' => $field,
            'value' => $value,
        ]);
        return $result['courses'] ?? [];
    }

    public function getCategories(): array
    {
        return $this->call('core_course_get_categories') ?? [];
    }

    // ===== USERS / STUDENTS =====

    public function getEnrolledUsers(int $courseId): array
    {
        return $this->call('core_enrol_get_enrolled_users', [
            'courseid' => $courseId,
        ]);
    }

    public function getUserByField(string $field, string $value): array
    {
        $result = $this->call('core_user_get_users_by_field', [
            'field' => $field,
            'values[0]' => $value,
        ]);
        return $result[0] ?? [];
    }

    // ===== GRADES =====

    public function getGrades(int $courseId, int $userId = 0): array
    {
        $params = ['courseid' => $courseId];
        if ($userId > 0) {
            $params['userid'] = $userId;
        }
        return $this->call('gradereport_user_get_grade_items', $params) ?? [];
    }

    // ===== ASSIGNMENTS =====

    public function getAssignments(array $courseIds): array
    {
        $params = [];
        foreach ($courseIds as $i => $id) {
            $params["courseids[$i]"] = $id;
        }
        $result = $this->call('mod_assign_get_assignments', $params);
        return $result['courses'] ?? [];
    }

    // ===== COMPETENCIES (CLO/PLO) =====

    public function getCourseCompetencies(int $courseId): array
    {
        return $this->call('core_competency_list_course_competencies', [
            'id' => $courseId,
        ]);
    }

    public function getUserCompetencyInCourse(int $courseId, int $userId, int $competencyId): array
    {
        return $this->call('core_competency_get_user_competency_in_course', [
            'courseid' => $courseId,
            'userid' => $userId,
            'competencyid' => $competencyId,
        ]);
    }

    // ===== SITE INFO =====

    public function getSiteInfo(): array
    {
        return $this->call('core_webservice_get_site_info');
    }

    // ===== COURSE CONTENT (cho RAG ingestion) =====

    /**
     * Lay toan bo sections + modules + files cua 1 course
     */
    public function getCourseContents(int $courseId): array
    {
        return $this->call('core_course_get_contents', [
            'courseid' => $courseId,
        ]) ?? [];
    }

    /**
     * Lay noi dung HTML cua tat ca module Page trong cac course
     */
    public function getPagesByCourses(array $courseIds): array
    {
        $params = [];
        foreach ($courseIds as $i => $id) {
            $params["courseids[$i]"] = $id;
        }
        $result = $this->call('mod_page_get_pages_by_courses', $params);
        return $result['pages'] ?? [];
    }

    /**
     * Lay metadata Book + intro cua cac module Book trong courses
     */
    public function getBooksByCourses(array $courseIds): array
    {
        $params = [];
        foreach ($courseIds as $i => $id) {
            $params["courseids[$i]"] = $id;
        }
        $result = $this->call('mod_book_get_books_by_courses', $params);
        return $result['books'] ?? [];
    }

    /**
     * Lay text Label trong cac course
     */
    public function getLabelsByCourses(array $courseIds): array
    {
        $params = [];
        foreach ($courseIds as $i => $id) {
            $params["courseids[$i]"] = $id;
        }
        $result = $this->call('mod_label_get_labels_by_courses', $params);
        return $result['labels'] ?? [];
    }

    /**
     * Download file tu Moodle (pluginfile.php) co kem wstoken
     * Tra ve [content, mime, filename]
     */
    public function downloadFile(string $fileurl): array
    {
        // Moodle tra ve fileurl dang: https://moodle/.../pluginfile.php/...
        // Can them ?token=xxx de download voi quyen service account
        $separator = str_contains($fileurl, '?') ? '&' : '?';
        $url = $fileurl . $separator . 'token=' . $this->token;

        $response = Http::timeout(60)->get($url);

        if (!$response->successful()) {
            throw new \Exception('File download failed: HTTP ' . $response->status());
        }

        $mime = $response->header('Content-Type') ?: 'application/octet-stream';
        $filename = basename(parse_url($fileurl, PHP_URL_PATH));

        return [$response->body(), $mime, $filename];
    }
}
