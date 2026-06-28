<?php

namespace App\Services\DataSource;

/**
 * Giao diện nguồn dữ liệu Moodle.
 *
 * Cho phép chuyển đổi giữa 2 cách tích hợp Moodle (cấu hình qua MOODLE_DATA_SOURCE):
 *   - 'ws' : gọi Moodle Web Services API (REST + token)  -> WebServiceSource
 *   - 'db' : truy vấn trực tiếp CSDL Moodle               -> DatabaseSource
 *
 * Mỗi phương thức trả về MẢNG đã chuẩn hoá (cùng cấu trúc cho cả 2 nguồn),
 * giúp Controller không phụ thuộc vào cách lấy dữ liệu.
 */
interface MoodleDataSourceInterface
{
    /**
     * Danh sách khóa học (đã loại site course id=1).
     *
     * @return array<int, array{id:int, fullname:string, shortname:string, idnumber:string, category:?array, category_path:array}>
     */
    public function getCourses(?string $search = null): array;

    /**
     * Danh sách sinh viên (role student) trong 1 khóa học.
     *
     * @return array<int, array{id:int, username:string, firstname:string, lastname:string, fullname:string, email:string, idnumber:string}>
     */
    public function getCourseStudents(int $courseId): array;

    /**
     * Điểm của tất cả sinh viên trong 1 khóa học.
     *
     * @return array<int, array{user_id:int, fullname:string, activities:array}>
     */
    public function getCourseGrades(int $courseId): array;

    /**
     * Tên nguồn dữ liệu hiện tại ('ws' | 'db') — dùng cho debug/metadata.
     */
    public function name(): string;
}
