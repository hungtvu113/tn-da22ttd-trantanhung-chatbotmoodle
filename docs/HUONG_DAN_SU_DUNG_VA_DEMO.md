# Hướng dẫn sử dụng chương trình và chạy Demo

> **Đề tài:** Xây dựng hệ thống Chatbot thông minh hỗ trợ học tập dựa trên công nghệ RAG tích hợp Moodle qua Web Services API  
> **Tác giả:** Trần Tấn Hưng — MSSV: 110122081 — Lớp DA22TTD

Tài liệu này hướng dẫn **cách khởi động hệ thống**, **sử dụng chatbot theo từng vai trò** và **kịch bản demo** khi bảo vệ / trình bày đồ án.

---

## Mục lục

1. [Tổng quan hệ thống](#1-tổng-quan-hệ-thống)
2. [Checklist chuẩn bị trước khi dùng](#2-checklist-chuẩn-bị-trước-khi-dùng)
3. [Khởi động hệ thống (từng bước)](#3-khởi-động-hệ-thống-từng-bước)
4. [Hướng dẫn sử dụng chatbot](#4-hướng-dẫn-sử-dụng-chatbot)
5. [Hướng dẫn theo vai trò người dùng](#5-hướng-dẫn-theo-vai-trò-người-dùng)
6. [Kịch bản Demo bảo vệ (6–10 phút)](#6-kịch-bản-demo-bảo-vệ-610-phút)
7. [Demo API Gateway bằng Postman](#7-demo-api-gateway-bằng-postman)
8. [Demo thông báo email](#8-demo-thông-báo-email)
9. [Xử lý sự cố thường gặp](#9-xử-lý-sự-cố-thường-gặp)

---

## 1. Tổng quan hệ thống

Hệ thống gồm **3 dịch vụ** cần chạy đồng thời:

| Dịch vụ | Công nghệ | Cổng mặc định | Vai trò |
|---|---|---|---|
| Moodle + plugin | PHP (XAMPP) | `80` (Apache) | Giao diện người dùng, chatbot widget |
| API Gateway | Laravel | `8000` | Truy xuất dữ liệu Moodle, phân quyền RBAC |
| Backend RAG | FastAPI | `8001` | Phân loại câu hỏi, RAG, sinh câu trả lời |

**Luồng sử dụng cơ bản:**

```
Người dùng gõ câu hỏi trên Moodle
    → Plugin local_chatbot gửi tới FastAPI (:8001)
        → Câu hỏi nội dung: RAG (ChromaDB + LLM)
        → Câu hỏi dữ liệu: gọi Laravel Gateway (:8000) → Moodle
    → Hiển thị câu trả lời trên khung chat
```

---

## 2. Checklist chuẩn bị trước khi dùng

### Phần mềm

- [ ] XAMPP (Apache + MySQL/MariaDB) đã cài và Moodle hoạt động
- [ ] Plugin `local_chatbot` đã copy vào `moodle/local/chatbot` và cài qua **Notifications**
- [ ] PHP ≥ 8.2, Composer
- [ ] Python 3.13, virtualenv
- [ ] API key Google Gemini (lấy tại [aistudio.google.com/apikey](https://aistudio.google.com/apikey))
- [ ] (Tuỳ chọn) Postman, Ngrok

### Cấu hình một lần

- [ ] Moodle: bật **Web Services** + **REST protocol**
- [ ] Moodle: tạo token Web Services (xem `src/moodle-api/MOODLE_SETUP_GUIDE.md`)
- [ ] `src/moodle-api/.env`: cấu hình `MOODLE_URL`, `MOODLE_TOKEN`, API keys theo vai trò
- [ ] `src/moodle-rag-backend/.env`: cấu hình `GEMINI_API_KEY`, `BACKEND_API_KEY`, `GATEWAY_API_KEY`
- [ ] Moodle admin: **Plugins → Local plugins → RAG Chatbot** — URL `http://localhost:8001/api/chat`, API key khớp `BACKEND_API_KEY`
- [ ] Đã chạy **ingestion** ít nhất một lần để nạp nội dung khóa học vào ChromaDB

### Trước mỗi lần demo

- [ ] XAMPP: bật **Apache** + **MySQL**
- [ ] Laravel Gateway đang chạy cổng `8000`
- [ ] FastAPI backend đang chạy cổng `8001`
- [ ] `MOODLE_DATA_SOURCE=db` trong `.env` của Gateway *(khuyến nghị khi demo chatbot)*
- [ ] Mở sẵn trình duyệt Moodle (đã đăng nhập), Postman (nếu demo API)

---

## 3. Khởi động hệ thống (từng bước)

> Thay đường dẫn Moodle nếu máy bạn cài khác vị trí mặc định.

### Bước 1 — Bật Moodle (XAMPP)

1. Mở **XAMPP Control Panel**
2. Start **Apache** và **MySQL**
3. Truy cập Moodle: `http://localhost/moodle` *(hoặc URL bạn đã cấu hình)*

### Bước 2 — Chạy API Gateway (Laravel)

Mở **Terminal / PowerShell**:

```powershell
cd D:\tn-da22ttd-trantanhung-chatbotmoodle\src\moodle-api
php artisan serve --port=8000
```

Kiểm tra:

```powershell
curl -H "X-API-Key: student-key-local-only-change-me" http://localhost:8000/api/v1/moodle/courses
```

Kết quả mong đợi: JSON có `"success": true` và danh sách khóa học.

### Bước 3 — Chạy Backend RAG (FastAPI)

Mở **Terminal / PowerShell thứ hai**:

```powershell
cd D:\tn-da22ttd-trantanhung-chatbotmoodle\src\moodle-rag-backend
.\restart.ps1
```

Hoặc chạy thủ công:

```powershell
.\.venv\Scripts\python.exe -m app.main
```

Kiểm tra:

```powershell
curl http://localhost:8001/health
```

Kết quả mong đợi: `{"status":"ok"}`

### Bước 4 — Nạp dữ liệu RAG (nếu chưa có hoặc khóa học mới)

Chỉ cần chạy khi lần đầu hoặc sau khi thêm/sửa nội dung khóa học:

```powershell
cd D:\tn-da22ttd-trantanhung-chatbotmoodle\src\moodle-rag-backend
.\.venv\Scripts\python.exe -m app.services.ingestion

# Chỉ 1 khóa học (thay 3 bằng ID thật):
.\.venv\Scripts\python.exe -m app.services.ingestion --course 3

# Xem thống kê chunk đã nạp:
.\.venv\Scripts\python.exe -m app.services.ingestion --stats
```

### Bước 5 — Đăng nhập Moodle và mở chatbot

1. Đăng nhập bằng tài khoản cần demo (sinh viên / giảng viên / admin)
2. Widget chatbot hiển thị ở góc màn hình *(floating button)*
3. Nhấn biểu tượng chat → nhập câu hỏi → Enter hoặc nút Gửi

---

## 4. Hướng dẫn sử dụng chatbot

### 4.1. Giao diện

- **Khung chat:** hiển thị lịch sử hội thoại (câu hỏi của bạn + câu trả lời bot)
- **Ô nhập liệu:** gõ câu hỏi bằng tiếng Việt tự nhiên
- **Nguồn trích dẫn:** với câu hỏi nội dung, bot có thể kèm link/tên tài liệu tham chiếu

### 4.2. Các loại câu hỏi hệ thống hiểu

| Loại | Ví dụ | Cách xử lý |
|---|---|---|
| Lời chào | "Xin chào", "Bạn là ai?" | Trả lời trực tiếp, không qua RAG |
| Nội dung khóa học | "Bài 1 nói về gì?", "Khái niệm RAG là gì?" | RAG — truy hồi tài liệu + LLM |
| Dữ liệu cá nhân | "Điểm của tôi?", "Tôi sắp tới hạn bài nào?" | Lấy số liệu Moodle qua Gateway |
| Dữ liệu lớp *(giảng viên)* | "Danh sách sinh viên?", "Ai chưa nộp bài?" | Lấy số liệu lớp qua Gateway |

### 4.3. Mẹo sử dụng hiệu quả

- Hỏi **rõ ràng, đủ ngữ cảnh**: *"Trong khóa Lập trình Web, bài tập tuần 2 hạn nộp khi nào?"*
- Với câu hỏi điểm/deadline, dùng từ **"của tôi"**, **"tôi"** để bot nhận diện đúng ý định cá nhân
- Giảng viên hỏi về lớp: dùng *"lớp"*, *"sinh viên"*, *"cả lớp"*, *"ai chưa nộp"*
- Nếu bot báo không có thông tin: kiểm tra đã **ingest** nội dung khóa học chưa, hoặc câu hỏi nằm ngoài tài liệu đã nạp

---

## 5. Hướng dẫn theo vai trò người dùng

### 5.1. Sinh viên

**Đăng nhập:** tài khoản có vai trò *Student* (ví dụ `sinhvien1`)

**Có thể hỏi:**

| Chức năng | Câu hỏi mẫu |
|---|---|
| Khóa học đang học | "Tôi đang học những khóa nào?" |
| Điểm cá nhân | "Điểm của tôi thế nào?" / "Kết quả học tập của tôi?" |
| Deadline | "Tôi sắp tới hạn bài nào?" / "Deadline nào sắp đến?" |
| Hoạt động khóa học | "Khóa học có những hoạt động gì?" |
| Tiến độ | "Tôi hoàn thành bao nhiêu %?" / "Tiến độ học của tôi?" |
| Nội dung bài học | "Giải thích cho tôi về [chủ đề trong tài liệu]" |

**Không thể:** xem điểm cả lớp, danh sách sinh viên, thống kê lớp.

---

### 5.2. Giảng viên

**Đăng nhập:** tài khoản *Teacher* hoặc *Editing teacher*

**Có thể hỏi:** tất cả chức năng sinh viên **+** các câu hỏi về lớp:

| Chức năng | Câu hỏi mẫu |
|---|---|
| Danh sách lớp | "Lớp này có những sinh viên nào?" |
| Điểm cả lớp | "Điểm của cả lớp thế nào?" |
| Thống kê | "Điểm trung bình lớp là bao nhiêu?" |
| Chưa nộp bài | "Ai chưa nộp bài?" / "Danh sách chưa nộp bài" |
| Tiến độ lớp | "Tiến độ học của lớp thế nào?" |
| Khóa đang dạy | "Tôi đang dạy khóa nào, bao nhiêu sinh viên?" |

---

### 5.3. Quản trị viên

**Đăng nhập:** tài khoản *Administrator*

**Sử dụng chatbot:** tương tự giảng viên (tra cứu nội dung + dữ liệu).

**Vận hành hệ thống (ngoài chatbot):**

| Tác vụ | Cách thực hiện |
|---|---|
| Cấu hình chatbot | **Site admin → Plugins → Local plugins → RAG Chatbot** |
| Nạp dữ liệu RAG | Chạy lệnh `ingestion` (mục 3, Bước 4) |
| Quản lý API keys | Sửa `STUDENT/TEACHER/ADMIN_API_KEYS` trong `moodle-api/.env` |
| Chuyển nguồn dữ liệu | `MOODLE_DATA_SOURCE=db` hoặc `ws` trong `moodle-api/.env` |
| Cron email | Task Scheduler gọi `php admin/cli/cron.php` mỗi phút |

---

## 6. Kịch bản Demo bảo vệ (6–10 phút)

### 6.1. Chuẩn bị phòng demo

Mở sẵn các tab/cửa sổ:

1. Moodle — đăng nhập **sinh viên**
2. Moodle — tab ẩn đăng nhập **giảng viên** *(hoặc đổi tài khoản nhanh)*
3. Postman — import `src/moodle-api/postman_collection.json`
4. Editor — file `src/moodle-api/.env`
5. Terminal — 2 cửa sổ (Laravel + FastAPI đang chạy)

**API keys demo (local):**

| Vai trò | `X-API-Key` |
|---|---|
| Sinh viên | `student-key-local-only-change-me` |
| Giảng viên | `teacher-key-local-only-change-me` |
| Admin | `admin-key-local-only-change-me` |

---

### 6.2. Màn 1 — Chatbot cho sinh viên (2 phút)

**Lời dẫn:**
> "Em xin demo chức năng chatbot trên Moodle. Sinh viên có thể hỏi bằng ngôn ngữ tự nhiên thay vì điều hướng nhiều trang."

**Thao tác:**

1. Hỏi nội dung (RAG):  
   *"Giải thích cho tôi [một khái niệm có trong tài liệu khóa học]"*  
   → Chỉ vào **câu trả lời + phần trích dẫn nguồn**

2. Hỏi dữ liệu cá nhân:  
   *"Tôi sắp tới hạn bài nào?"*  
   → Chỉ ra câu trả lời lấy **số liệu thật** từ Moodle

3. Hỏi điểm:  
   *"Điểm của tôi thế nào?"*

**Chốt:** Bot phân biệt câu hỏi nội dung (RAG) và câu hỏi dữ liệu (tra cứu chính xác).

---

### 6.3. Màn 2 — Chatbot cho giảng viên (1.5 phút)

**Đổi sang tài khoản giảng viên**, hỏi:

1. *"Lớp này có những sinh viên nào?"*
2. *"Ai chưa nộp bài?"*
3. *"Điểm trung bình lớp là bao nhiêu?"*

**Chốt:** Cùng một giao diện chatbot nhưng phản hồi khác nhau theo **vai trò người dùng**.

---

### 6.4. Màn 3 — Phân quyền API (RBAC) trên Postman (2 phút)

**Lời dẫn:**
> "Mọi truy cập dữ liệu đều đi qua API Gateway có phân quyền theo vai trò."

**Endpoint demo:**  
`GET http://localhost:8000/api/v1/moodle/courses/{id}/grades`  
*(thay `{id}` bằng ID khóa học thật, ví dụ `3`)*

| Bước | Header `X-API-Key` | Kết quả |
|---|---|---|
| 1 | Key **sinh viên** | `403 Forbidden` |
| 2 | Key **giảng viên** | `200 OK` + dữ liệu điểm lớp |
| 3 | Key **sai** | `401 Unauthorized` |

**Chốt:** RBAC hoạt động đúng — sinh viên không xem được điểm cả lớp.

---

### 6.5. Màn 4 — Tích hợp Web Services API (2 phút)

**Lời dẫn:**
> "Hệ thống hỗ trợ hai nguồn dữ liệu: truy vấn CSDL trực tiếp hoặc gọi Moodle Web Services API, chuyển đổi chỉ bằng cấu hình."

**Thao tác:**

1. Postman gọi `GET http://localhost:8000/api/v1/moodle/courses` (key admin)  
   → Chỉ `"source": "db"`

2. Sửa `.env`: `MOODLE_DATA_SOURCE=ws`

3. Chạy:
   ```powershell
   cd D:\tn-da22ttd-trantanhung-chatbotmoodle\src\moodle-api
   php artisan config:clear
   ```

4. Gọi lại endpoint → `"source": "ws"`, dữ liệu vẫn đúng

5. **Trước khi demo chatbot tiếp**, đổi lại:
   ```env
   MOODLE_DATA_SOURCE=db
   ```
   ```powershell
   php artisan config:clear
   ```

**Chốt:** Chứng minh tích hợp Moodle qua Web Services API; chế độ `db` chỉ để tối ưu hiệu năng môi trường dev.

---

### 6.6. Màn 5 (tuỳ chọn) — Ngrok / Email

**Ngrok:** `ngrok http 8000` → gọi API từ URL public trên Postman.

**Email:** tạo hoạt động mới trong khóa học → sinh viên nhận email thông báo *(cần SMTP đã cấu hình trong Moodle)*.

---

### 6.7. Câu tổng kết demo

> "Hệ thống đáp ứng yêu cầu đề tài: chatbot RAG hỗ trợ học tập trên Moodle, tích hợp qua Web Services API với API Gateway phân quyền RBAC, phân tách rõ câu hỏi nội dung và câu hỏi dữ liệu để đảm bảo độ chính xác."

---

## 7. Demo API Gateway bằng Postman

### 7.1. Import collection

1. Mở Postman → **Import**
2. Chọn file `src/moodle-api/postman_collection.json`
3. Tạo biến môi trường:
   - `base_url` = `http://localhost:8000/api/v1/moodle`
   - `api_key` = key theo vai trò cần test

### 7.2. Headers bắt buộc

```
X-API-Key: <api_key>
Accept: application/json
```

### 7.3. Một số request thường dùng

```http
# Danh sách khóa học
GET {{base_url}}/courses

# Điểm sinh viên (thay username)
GET {{base_url}}/students/sinhvien1/results

# Deadline sinh viên
GET {{base_url}}/students/sinhvien1/deadlines

# Danh sách SV trong lớp (teacher key)
GET {{base_url}}/courses/3/students

# SV chưa nộp bài (teacher key)
GET {{base_url}}/courses/3/unsubmitted
```

Chi tiết đầy đủ: `src/moodle-api/API_TESTING_BY_ROLE.md`

---

## 8. Demo thông báo email

Hệ thống gửi email tự động qua Moodle SMTP. Cần cấu hình **Outgoing mail configuration** trong Moodle trước.

| Sự kiện | Ai nhận | Cách kích hoạt demo |
|---|---|---|
| Hoạt động mới trong khóa | Sinh viên | Giảng viên thêm bài tập/bài kiểm tra mới |
| Phân công giảng dạy | Giảng viên | Admin gán role teacher cho user |
| Nhắc deadline (24h) | Sinh viên | Scheduled task (cron chạy hằng ngày) |
| Quá hạn chưa nộp | Sinh viên + GV | Scheduled task (mỗi 5 phút) |

**Chạy cron thủ công (test nhanh):**

```powershell
cd C:\xampp\htdocs\moodle
php admin\cli\cron.php
```

**Cron tự động (Windows):** Task Scheduler chạy lệnh trên **mỗi phút**.

---

## 9. Xử lý sự cố thường gặp

| Triệu chứng | Nguyên nhân có thể | Cách xử lý |
|---|---|---|
| Chatbot không hiện | Plugin chưa bật | Admin → RAG Chatbot → Enabled = Có |
| "Không kết nối được backend" | FastAPI chưa chạy | Kiểm tra `http://localhost:8001/health` |
| Chatbot trả lời chậm/treo | Gateway ở chế độ `ws` trên server dev | Đổi `MOODLE_DATA_SOURCE=db` + `php artisan config:clear` |
| Câu hỏi nội dung không trả lời | Chưa ingest / hết quota Gemini | Chạy ingestion; kiểm tra `GEMINI_API_KEY` |
| 401 khi gọi API | Sai hoặc thiếu API key | Kiểm tra header `X-API-Key` và `.env` |
| 403 khi gọi API | Key không đủ quyền | Dùng key đúng vai trò (student/teacher/admin) |
| Email không gửi | SMTP chưa cấu hình / cron chưa chạy | Cấu hình SMTP Moodle; chạy `cron.php` |
| Lỗi Web Services | Token hết hạn / REST chưa bật | Xem `src/moodle-api/MOODLE_SETUP_GUIDE.md` |

### Lệnh khởi động lại nhanh

```powershell
# Gateway — áp dụng lại .env
cd D:\tn-da22ttd-trantanhung-chatbotmoodle\src\moodle-api
php artisan config:clear

# Backend RAG — restart
cd D:\tn-da22ttd-trantanhung-chatbotmoodle\src\moodle-rag-backend
.\restart.ps1
```

---

## Tài liệu liên quan

| File | Mô tả |
|---|---|
| [README.md](README.md) | Giới thiệu, kiến trúc, cài đặt tổng quan |
| [src/moodle-api/KICH_BAN_DEMO_BAO_VE.md](src/moodle-api/KICH_BAN_DEMO_BAO_VE.md) | Kịch bản demo chi tiết phần Web Services API |
| [src/moodle-api/API_TESTING_BY_ROLE.md](src/moodle-api/API_TESTING_BY_ROLE.md) | Test API đầy đủ theo vai trò |
| [src/moodle-api/MOODLE_SETUP_GUIDE.md](src/moodle-api/MOODLE_SETUP_GUIDE.md) | Cấu hình Moodle Web Services |
| [src/moodle-rag-backend/README.md](src/moodle-rag-backend/README.md) | Hướng dẫn backend RAG |

---

**Trần Tấn Hưng** — MSSV: 110122081 — Lớp DA22TTD  
Giảng viên hướng dẫn: **ThS. Trịnh Quốc Việt**
