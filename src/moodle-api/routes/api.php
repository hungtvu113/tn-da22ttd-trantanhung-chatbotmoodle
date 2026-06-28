<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\MoodleController;

// API Gateway cho external systems (Chatbot, Mobile App, etc.)
// Security layers:
// 1. log.request - Log tất cả requests
// 2. sanitize    - Sanitize input chống XSS/SQL injection
// 3. api.key:ROLE - API Key authentication + phân quyền theo vai trò
// 4. rate.limit  - Rate limiting chống DDoS
// 5. ip.whitelist - IP whitelist (optional, uncomment nếu cần)
//
// Phân cấp quyền: admin > teacher > student
//   - api.key:student  -> student, teacher, admin đều truy cập được
//   - api.key:teacher  -> teacher, admin
//   - api.key:admin    -> chỉ admin
Route::prefix('v1/moodle')
    ->middleware(['log.request', 'sanitize', 'rate.limit'/*, 'ip.whitelist'*/])
    ->group(function () {

        // =====================================================================
        // NHÓM STUDENT — dữ liệu cá nhân + nội dung khóa học (student trở lên)
        // =====================================================================
        Route::middleware('api.key:student')->group(function () {
            // Danh sach courses
            Route::get('/courses', [MoodleController::class, 'courses']);

            // CLO/PLO gan vao course
            Route::get('/courses/{courseId}/competencies', [MoodleController::class, 'courseCompetencies']);

            // Cau truc course: sections + modules + danh sach files
            Route::get('/courses/{courseId}/contents', [MoodleController::class, 'courseContents']);

            // Ket qua hoc tap cua 1 SV
            Route::get('/students/{username}/results', [MoodleController::class, 'studentResults']);

            // Danh sach khoa hoc da ghi danh cua 1 SV
            Route::get('/students/{username}/courses', [MoodleController::class, 'studentCourses']);

            // Deadline (assignment + quiz) sap toi cua 1 SV
            Route::get('/students/{username}/deadlines', [MoodleController::class, 'studentDeadlines']);

            // Cac hoat dong (module) trong cac khoa hoc SV dang ghi danh
            Route::get('/students/{username}/activities', [MoodleController::class, 'studentActivities']);

            // Tien do hoan thanh khoa hoc cua 1 SV
            Route::get('/students/{username}/progress', [MoodleController::class, 'studentProgress']);
        });

        // =====================================================================
        // NHÓM TEACHER — quản lý lớp, điểm, tiến độ (teacher trở lên)
        // =====================================================================
        Route::middleware('api.key:teacher')->group(function () {
            // SV trong course
            Route::get('/courses/{courseId}/students', [MoodleController::class, 'courseStudents']);

            // Diem tat ca SV trong course
            Route::get('/courses/{courseId}/grades', [MoodleController::class, 'courseGrades']);

            // Thong ke diem 1 khoa hoc (TB, max, min)
            Route::get('/courses/{courseId}/grade-stats', [MoodleController::class, 'courseGradeStats']);

            // Sinh vien chua nop bai (co the loc theo ten bai: ?assign=...)
            Route::get('/courses/{courseId}/unsubmitted', [MoodleController::class, 'courseUnsubmitted']);

            // Tien do hoan thanh cua tung SV trong 1 khoa hoc
            Route::get('/courses/{courseId}/progress', [MoodleController::class, 'courseProgress']);

            // Ket qua CLO cua SV trong course
            Route::get('/courses/{courseId}/competency-results', [MoodleController::class, 'courseCompetencyResults']);

            // Tong hop tat ca du lieu course
            Route::get('/courses/{courseId}/full-results', [MoodleController::class, 'courseFullResults']);
        });

        // =====================================================================
        // NHÓM ADMIN — vận hành hệ thống, RAG ingest, tải file (chỉ admin)
        // =====================================================================
        Route::middleware('api.key:admin')->group(function () {
            // Truy xuat nguoc: Ma mon -> Khoa hoc -> Nganh
            Route::get('/courses/traceback', [MoodleController::class, 'courseTraceback']);

            // Flat text content cua course (toi uu cho RAG indexing)
            Route::get('/courses/{courseId}/text-content', [MoodleController::class, 'courseTextContent']);

            // Proxy download file Moodle (PDF/DOCX) co kem wstoken
            Route::get('/files/download', [MoodleController::class, 'downloadFile']);
        });
    });
