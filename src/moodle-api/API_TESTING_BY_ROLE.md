# Hướng dẫn Test API theo Vai trò (Role-Based)

Tài liệu này mô tả toàn bộ endpoint của Moodle API Gateway, phân theo **quyền truy cập của từng vai trò** (sinh viên / giáo viên / admin), kèm ví dụ test bằng Postman / cURL.

---

## 1. Tổng quan

- **Base URL (local):** `http://localhost:8000/api/v1/moodle`
- **Base URL (ngrok):** `https://<ten-ngrok>.ngrok-free.dev/api/v1/moodle`
- **Xác thực:** mọi request phải gửi header `X-API-Key`.
- **Phân quyền:** theo cấp bậc `admin > teacher > student`. Key cấp cao dùng được tất cả endpoint của cấp thấp hơn.

### API Keys (môi trường local)

| Vai trò | Header `X-API-Key` |
|---|---|
| Sinh viên | `student-key-local-only-change-me` |
| Giáo viên | `teacher-key-local-only-change-me` |
| Admin | `admin-key-local-only-change-me` |
| Chatbot (legacy) | `dev-chatbot-key-local-only-change-me` *(tương đương admin)* |

> ⚠️ Đây là key demo. Khi deploy production, đổi trong `.env` (`STUDENT_API_KEYS`, `TEACHER_API_KEYS`, `ADMIN_API_KEYS`) thành chuỗi ngẫu nhiên mạnh.

### Headers chung

```
X-API-Key: <key theo vai trò>
Accept: application/json
ngrok-skip-browser-warning: true   # chỉ cần khi gọi qua ngrok
```

### Mã trạng thái

| Code | Ý nghĩa |
|---|---|
| `200` | Thành công |
| `400` | Thiếu tham số bắt buộc |
| `401` | Thiếu hoặc sai API Key |
| `403` | Key không đủ quyền cho endpoint này |
| `404` | Không tìm thấy dữ liệu (user/course) |
| `429` | Vượt giới hạn rate limit |
| `500` | Lỗi máy chủ |

---

## 2. Ma trận quyền (Endpoint × Vai trò)

| Endpoint | Student | Teacher | Admin |
|---|:---:|:---:|:---:|
| `GET /courses` | ✅ | ✅ | ✅ |
| `GET /courses/{id}/competencies` | ✅ | ✅ | ✅ |
| `GET /courses/{id}/contents` | ✅ | ✅ | ✅ |
| `GET /students/{username}/results` | ✅ | ✅ | ✅ |
| `GET /students/{username}/courses` | ✅ | ✅ | ✅ |
| `GET /students/{username}/deadlines` | ✅ | ✅ | ✅ |
| `GET /students/{username}/activities` | ✅ | ✅ | ✅ |
| `GET /students/{username}/progress` | ✅ | ✅ | ✅ |
| `GET /courses/{id}/students` | ⛔ | ✅ | ✅ |
| `GET /courses/{id}/grades` | ⛔ | ✅ | ✅ |
| `GET /courses/{id}/grade-stats` | ⛔ | ✅ | ✅ |
| `GET /courses/{id}/unsubmitted` | ⛔ | ✅ | ✅ |
| `GET /courses/{id}/progress` | ⛔ | ✅ | ✅ |
| `GET /courses/{id}/competency-results` | ⛔ | ✅ | ✅ |
| `GET /courses/{id}/full-results` | ⛔ | ✅ | ✅ |
| `GET /courses/traceback` | ⛔ | ⛔ | ✅ |
| `GET /courses/{id}/text-content` | ⛔ | ⛔ | ✅ |
| `GET /files/download` | ⛔ | ⛔ | ✅ |

✅ = truy cập được · ⛔ = trả về `403 Forbidden`

---

## 3. Nhóm SINH VIÊN (student trở lên)

> Dữ liệu cá nhân của người học và nội dung khóa học.

### 3.1. Danh sách khóa học
```
GET /courses
GET /courses?search=lap trinh      # lọc theo tên/mã (tùy chọn)
```

### 3.2. CLO/PLO của khóa học
```
GET /courses/{courseId}/competencies
```

### 3.3. Cấu trúc nội dung khóa học (sections + modules + files)
```
GET /courses/{courseId}/contents
```

### 3.4. Kết quả học tập của 1 sinh viên
```
GET /students/{username}/results
```

### 3.5. Khóa học đã ghi danh của 1 sinh viên
```
GET /students/{username}/courses
```

### 3.6. Deadline sắp tới (assignment + quiz)
```
GET /students/{username}/deadlines
GET /students/{username}/deadlines?includepast=1   # gồm cả deadline đã qua
```

### 3.7. Hoạt động trong các khóa đang học
```
GET /students/{username}/activities
```

### 3.8. Tiến độ hoàn thành khóa học
```
GET /students/{username}/progress
```

---

## 4. Nhóm GIÁO VIÊN (teacher trở lên)

> Quản lý lớp, điểm, tiến độ của sinh viên.

### 4.1. Danh sách sinh viên trong khóa
```
GET /courses/{courseId}/students
```

### 4.2. Điểm tất cả sinh viên trong khóa
```
GET /courses/{courseId}/grades
```

### 4.3. Thống kê điểm khóa học (TB, cao nhất, thấp nhất)
```
GET /courses/{courseId}/grade-stats
```

### 4.4. Sinh viên chưa nộp bài
```
GET /courses/{courseId}/unsubmitted
GET /courses/{courseId}/unsubmitted?assign=do an   # lọc theo tên bài tập
```

### 4.5. Tiến độ hoàn thành của từng sinh viên
```
GET /courses/{courseId}/progress
```

### 4.6. Kết quả CLO của sinh viên trong khóa
```
GET /courses/{courseId}/competency-results
```

### 4.7. Tổng hợp toàn bộ dữ liệu khóa (SV + điểm + CLO + bài tập)
```
GET /courses/{courseId}/full-results
```

---

## 5. Nhóm ADMIN (chỉ admin)

> Vận hành hệ thống, phục vụ RAG ingest và tải file.

### 5.1. Truy xuất ngược: Mã môn → Khóa học → Ngành
```
GET /courses/traceback?search=220268      # tham số search BẮT BUỘC
```

### 5.2. Nội dung text phẳng của khóa (tối ưu cho RAG indexing)
```
GET /courses/{courseId}/text-content
```

### 5.3. Proxy tải file Moodle (PDF/DOCX) kèm token
```
GET /files/download?fileurl=<url-file-moodle>   # tham số fileurl BẮT BUỘC
```

---

## 6. Dữ liệu mẫu để test (local)

| Loại | Giá trị |
|---|---|
| Sinh viên | `sinhvien1` |
| Giáo viên | `giangvien1` |
| Khóa học 1 | id `4` — Kỹ thuật lập trình (KTLT) |
| Khóa học 2 | id `3` — Phát triển ứng dụng hướng dịch vụ (220268) |

---

## 7. Ví dụ test bằng cURL

### Sinh viên xem khóa học của mình (200 OK)
```bash
curl -H "X-API-Key: student-key-local-only-change-me" \
  "http://localhost:8000/api/v1/moodle/students/sinhvien1/courses"
```

### Sinh viên thử xem điểm cả lớp (403 Forbidden)
```bash
curl -H "X-API-Key: student-key-local-only-change-me" \
  "http://localhost:8000/api/v1/moodle/courses/3/grades"
```

### Giáo viên xem điểm cả lớp (200 OK)
```bash
curl -H "X-API-Key: teacher-key-local-only-change-me" \
  "http://localhost:8000/api/v1/moodle/courses/3/grades"
```

### Admin truy xuất ngược mã môn (200 OK)
```bash
curl -H "X-API-Key: admin-key-local-only-change-me" \
  "http://localhost:8000/api/v1/moodle/courses/traceback?search=220268"
```

### Key sai (401 Unauthorized)
```bash
curl -H "X-API-Key: sai-key" \
  "http://localhost:8000/api/v1/moodle/courses"
```

---

## 8. Cách test nhanh phân quyền trong Postman

1. Tạo **Environment** với 3 biến: `student_key`, `teacher_key`, `admin_key`.
2. Trong request, đặt header `X-API-Key: {{student_key}}` (hoặc teacher/admin).
3. Đổi biến để kiểm tra cùng 1 endpoint với các vai trò khác nhau:
   - Endpoint nhóm student → cả 3 key đều `200`.
   - Endpoint nhóm teacher → student `403`, teacher/admin `200`.
   - Endpoint nhóm admin → student/teacher `403`, admin `200`.

---

## 9. Định dạng response chung

Thành công:
```json
{
  "success": true,
  "data": { ... }
}
```

Lỗi:
```json
{
  "success": false,
  "message": "Mô tả lỗi"
}
```

---

## 10. Khởi chạy hệ thống (nhắc nhanh)

```powershell
# 1. Bật MySQL trong XAMPP

# 2. Chạy Laravel Gateway (port 8000)
cd D:\moodle-api
php artisan serve --port=8000

# 3. (Tùy chọn) Expose ra ngoài qua ngrok
ngrok http 8000
```

> Sau khi sửa `.env` hoặc key, chạy `php artisan config:clear` để áp dụng.
