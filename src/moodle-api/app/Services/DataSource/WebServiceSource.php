<?php

namespace App\Services\DataSource;

use App\Services\MoodleClient;

/**
 * Nguồn dữ liệu Moodle qua Web Services API (REST + wstoken).
 *
 * Đây là cách tích hợp "chuẩn" theo đề tài: gọi các external function của Moodle.
 * Phù hợp môi trường production (web server đa tiến trình: php-fpm/Apache).
 *
 * Lưu ý: trên dev server php -S (đơn luồng), nếu lời gọi WS lồng nhau xảy ra
 * trong cùng tiến trình đang phục vụ request -> có thể deadlock. Khi đó dùng
 * DatabaseSource (MOODLE_DATA_SOURCE=db).
 */
class WebServiceSource implements MoodleDataSourceInterface
{
    public function __construct(protected MoodleClient $client)
    {
    }

    public function name(): string
    {
        return 'ws';
    }

    public function getCourses(?string $search = null): array
    {
        $courses    = $this->client->getCourses();
        $categories = collect($this->client->getCategories())->keyBy('id');

        // Loai site course (id = 1).
        $courses = array_filter($courses, fn($c) => ($c['id'] ?? 0) > 1);

        if ($search) {
            $needle  = strtolower($search);
            $courses = array_filter($courses, function ($c) use ($needle) {
                return str_contains(strtolower($c['shortname'] ?? ''), $needle)
                    || str_contains(strtolower($c['fullname'] ?? ''), $needle)
                    || str_contains(strtolower($c['idnumber'] ?? ''), $needle);
            });
        }

        $result = array_map(function ($c) use ($categories) {
            $catId = $c['categoryid'] ?? $c['category'] ?? 0;
            $cat   = $categories[$catId] ?? null;

            return [
                'id'            => (int) $c['id'],
                'fullname'      => $c['fullname'] ?? '',
                'shortname'     => $c['shortname'] ?? '',
                'idnumber'      => $c['idnumber'] ?? '',
                'category'      => $cat ? ['id' => (int) $cat['id'], 'name' => $cat['name'] ?? ''] : null,
                'category_path' => [],
            ];
        }, $courses);

        return array_values($result);
    }

    public function getCourseStudents(int $courseId): array
    {
        $users = $this->client->getEnrolledUsers($courseId);

        // Chi giu role student (roleid = 5).
        $students = array_filter($users, function ($u) {
            foreach ($u['roles'] ?? [] as $role) {
                if (($role['roleid'] ?? 0) == 5) {
                    return true;
                }
            }
            return false;
        });

        $result = array_map(fn($u) => [
            'id'        => (int) $u['id'],
            'username'  => $u['username'] ?? '',
            'firstname' => $u['firstname'] ?? '',
            'lastname'  => $u['lastname'] ?? '',
            'fullname'  => $u['fullname'] ?? trim(($u['firstname'] ?? '') . ' ' . ($u['lastname'] ?? '')),
            'email'     => $u['email'] ?? '',
            'idnumber'  => $u['idnumber'] ?? '',
        ], $students);

        return array_values($result);
    }

    public function getCourseGrades(int $courseId): array
    {
        $gradeData  = $this->client->getGrades($courseId);
        $userGrades = $gradeData['usergrades'] ?? [];

        $result = array_map(function ($ug) {
            $activities = [];
            foreach ($ug['gradeitems'] ?? [] as $gi) {
                if (($gi['itemtype'] ?? '') === 'course') {
                    continue; // bo qua diem tong khoa
                }
                $activities[] = [
                    'name'       => $gi['itemname'] ?? '',
                    'module'     => $gi['itemmodule'] ?? '',
                    'grade'      => $gi['graderaw'] ?? null,
                    'max_grade'  => $gi['grademax'] ?? null,
                    'percentage' => $gi['percentageformatted'] ?? '',
                ];
            }

            return [
                'user_id'    => (int) $ug['userid'],
                'fullname'   => $ug['userfullname'] ?? '',
                'activities' => $activities,
            ];
        }, $userGrades);

        return array_values($result);
    }
}
