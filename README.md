# Xây dựng hệ thống Chatbot thông minh hỗ trợ học tập dựa trên công nghệ RAG tích hợp Moodle qua Web Services API

**Sinh viên thực hiện:** Trần Tấn Hưng — MSSV: 110122081 — Lớp: DA22TTD  
**Giảng viên hướng dẫn:** ThS. Trịnh Quốc Việt  
**Trường:** Đại học Cần Thơ — Khoa Công nghệ thông tin & Truyền thông

---

## 1. Giới thiệu đồ án

Đồ án xây dựng hệ thống **chatbot hỗ trợ học tập** tích hợp trực tiếp trên nền tảng **Moodle**, cho phép sinh viên, giảng viên và quản trị viên đặt câu hỏi bằng ngôn ngữ tự nhiên và nhận phản hồi phù hợp với vai trò của mình.

Hệ thống kết hợp công nghệ **RAG (Retrieval-Augmented Generation)** với **mô hình ngôn ngữ lớn (LLM)** để trả lời các câu hỏi về nội dung tài liệu khóa học, đồng thời tra cứu chính xác dữ liệu học tập (điểm số, khóa học, deadline, danh sách lớp…) thông qua **API Gateway** kết nối **Moodle Web Services API**.

Ngoài chức năng chatbot, hệ thống còn có module **thông báo email chủ động** (hoạt động mới, nhắc deadline, nhắc nộp bài trễ hạn…) được hiện thực ngay trong plugin Moodle.

### Cấu trúc repository

```
tn-da22ttd-trantanhung-chatbotmoodle/
├── README.md                 # File này
├── docs/                     # Tài liệu khóa luận (đề cương, báo cáo)
└── src/
    ├── moodle/local/chatbot/ # Plugin Moodle (local_chatbot)
    ├── moodle-api/           # API Gateway (Laravel)
    ├── moodle-rag-backend/   # Backend RAG (FastAPI + ChromaDB)
    └── moodledata/           # Thư mục dữ liệu Moodle (tham khảo)
```

> **Lưu ý:** Repository chỉ chứa **plugin** `local_chatbot`, không bao gồm toàn bộ mã nguồn Moodle. Cần cài đặt Moodle riêng (XAMPP hoặc môi trường tương đương), sau đó copy thư mục `src/moodle/local/chatbot` vào `moodle/local/chatbot` trên máy cài Moodle.

---

## 2. Mục tiêu

- Xây dựng chatbot hỗ trợ học tập tích hợp Moodle, giao tiếp bằng ngôn ngữ tự nhiên.
- Ứng dụng **RAG** để trả lời câu hỏi nội dung khóa học bám sát tài liệu, kèm trích dẫn nguồn, hạn chế hiện tượng “ảo giác” của LLM.
- Tích hợp Moodle qua **Web Services API** với tầng **API Gateway** có xác thực, **phân quyền theo vai trò (RBAC)** và bảo mật đa lớp.
- Hỗ trợ tra cứu dữ liệu cá nhân (sinh viên) và dữ liệu lớp (giảng viên): điểm, khóa học, deadline, tiến độ, danh sách sinh viên, thống kê lớp…
- Triển khai hệ thống thông báo email tự động khi có sự kiện học tập quan trọng.
- Thiết kế kiến trúc **nhiều tầng**, tách biệt rõ ràng, dễ bảo trì và mở rộng.

---

## 3. Kiến trúc hệ thống

Hệ thống gồm **3 tầng chính**:

```
┌─────────────────────────────────────────────────────────────┐
│  Tầng 1: Moodle — Plugin local_chatbot                      │
│  • Giao diện chatbot (widget)                               │
│  • Gửi câu hỏi → Backend RAG                                │
│  • Thông báo email (observer, scheduled task, adhoc task)   │
└──────────────────────────┬──────────────────────────────────┘
                           │ POST /api/chat  (X-API-Key)
                           ▼
┌─────────────────────────────────────────────────────────────┐
│  Tầng 2: Backend RAG — FastAPI (moodle-rag-backend)         │
│  • Phân loại ý định câu hỏi (intent router)                 │
│  • Câu hỏi nội dung → RAG (ChromaDB + LLM/Embedding)        │
│  • Câu hỏi dữ liệu → gọi API Gateway, trả lời trực tiếp      │
└──────────────────────────┬──────────────────────────────────┘
                           │ GET /courses, /grades, …  (X-API-Key)
                           ▼
┌─────────────────────────────────────────────────────────────┐
│  Tầng 3: API Gateway — Laravel (moodle-api)                 │
│  • Middleware: log, sanitize, rate limit, RBAC              │
│  • Nguồn dữ liệu: CSDL trực tiếp (db) hoặc Web Services (ws)│
└──────────────────────────┬──────────────────────────────────┘
                           │ wstoken / SQL
                           ▼
┌─────────────────────────────────────────────────────────────┐
│  Moodle LMS + MySQL/MariaDB                                 │
└─────────────────────────────────────────────────────────────┘
```

### Luồng xử lý một câu hỏi

1. Người dùng nhập câu hỏi trên giao diện chatbot trong Moodle.
2. Plugin gửi câu hỏi (kèm thông tin người dùng và vai trò) tới `POST /api/chat` của backend RAG.
3. Backend phân loại ý định:
   - **Lời chào** → trả lời trực tiếp, không qua RAG.
   - **Nội dung tài liệu** → truy hồi trên ChromaDB, sinh câu trả lời bằng LLM kèm trích dẫn.
   - **Dữ liệu học tập** (điểm, lớp, deadline…) → gọi Laravel Gateway, trả lời chính xác từ số liệu Moodle.
4. Plugin hiển thị câu trả lời cho người dùng.

### Công nghệ sử dụng

| Thành phần | Công nghệ |
|---|---|
| LMS | Moodle (PHP), MySQL/MariaDB |
| Plugin | `local_chatbot` (PHP, JavaScript) |
| API Gateway | Laravel 11 (PHP 8.2+) |
| Backend RAG | FastAPI (Python 3.13) |
| Vector Database | ChromaDB |
| LLM / Embedding | Google Gemini hoặc NVIDIA NIM (OpenAI-compatible) |

Tài liệu chi tiết hơn: `src/moodle-api/ARCHITECTURE.md`, `src/moodle-rag-backend/README.md`.

---

## 4. Phần mềm cần thiết để triển khai

| Phần mềm | Phiên bản khuyến nghị | Mục đích |
|---|---|---|
| **XAMPP** (Apache + MariaDB/MySQL + PHP) | PHP 8.2+ | Chạy Moodle |
| **Moodle** | 4.x | Hệ thống LMS nguồn dữ liệu |
| **Composer** | 2.x | Cài dependency Laravel |
| **PHP** | ≥ 8.2 | API Gateway |
| **Python** | 3.13 | Backend RAG |
| **Git** | Mới nhất | Clone repository |
| **Google Gemini API key** | — | LLM + embedding ([aistudio.google.com/apikey](https://aistudio.google.com/apikey)) |
| **Postman** *(tuỳ chọn)* | — | Kiểm thử API Gateway |
| **Ngrok** *(tuỳ chọn)* | — | Expose API ra ngoài khi demo |

### Yêu cầu bổ sung

- Bật **Web Services** và **REST protocol** trong Moodle.
- Tạo **Moodle Web Services token** cho API Gateway (xem `src/moodle-api/MOODLE_SETUP_GUIDE.md`).
- Cấu hình **SMTP** trong Moodle nếu dùng thông báo email (ví dụ Gmail App Password).

---

## 5. Cách thức chạy chương trình

### Bước 1: Cài đặt Moodle và plugin

1. Cài **XAMPP**, khởi động **Apache** và **MySQL/MariaDB**.
2. Cài đặt **Moodle** vào thư mục web (ví dụ `C:\xampp\htdocs\moodle`).
3. Copy plugin vào Moodle:

```powershell
# Ví dụ: Moodle cài tại C:\xampp\htdocs\moodle
Copy-Item -Recurse "D:\tn-da22ttd-trantanhung-chatbotmoodle\src\moodle\local\chatbot" `
  "C:\xampp\htdocs\moodle\local\chatbot"
```

4. Đăng nhập Moodle với quyền admin → **Site administration → Notifications** để cài plugin.
5. Bật Web Services theo hướng dẫn trong `src/moodle-api/MOODLE_SETUP_GUIDE.md`.

### Bước 2: Chạy API Gateway (Laravel)

```powershell
cd D:\tn-da22ttd-trantanhung-chatbotmoodle\src\moodle-api

# Lần đầu: cài dependency
composer install

# Sao chép và chỉnh cấu hình
copy .env.example .env
php artisan key:generate
```

Mở file `.env`, cấu hình tối thiểu:

```env
MOODLE_URL=http://localhost/moodle
MOODLE_TOKEN=<token_webservice_moodle>
MOODLE_DATA_SOURCE=db          # hoặc ws

STUDENT_API_KEYS=<key_sinh_vien>
TEACHER_API_KEYS=<key_giang_vien>
ADMIN_API_KEYS=<key_admin>
```

Chạy server:

```powershell
php artisan serve
# Gateway chạy tại http://localhost:8000
```

Kiểm thử nhanh:

```powershell
curl -H "X-API-Key: <key_sinh_vien>" http://localhost:8000/api/v1/moodle/courses
```

### Bước 3: Chạy Backend RAG (FastAPI)

```powershell
cd D:\tn-da22ttd-trantanhung-chatbotmoodle\src\moodle-rag-backend

# Lần đầu: tạo môi trường ảo và cài package
py -3.13 -m venv .venv
.\.venv\Scripts\python.exe -m pip install -r requirements.txt

copy .env.example .env
# Mở .env: điền GEMINI_API_KEY, BACKEND_API_KEY, GATEWAY_API_KEY...
```

Nạp dữ liệu khóa học vào ChromaDB (chạy một lần hoặc khi có nội dung mới):

```powershell
.\.venv\Scripts\python.exe -m app.services.ingestion
# Hoặc chỉ 1 khóa học: --course 5
```

Chạy server:

```powershell
.\.venv\Scripts\python.exe -m app.main
# Backend chạy tại http://localhost:8001
```

Kiểm tra:

```powershell
curl http://localhost:8001/health
```

### Bước 4: Cấu hình plugin trong Moodle

Vào **Site administration → Plugins → Local plugins → RAG Chatbot** và thiết lập:

| Mục | Giá trị mẫu |
|---|---|
| Enabled | Có |
| API URL | `http://localhost:8001/api/chat` |
| API key | Khớp với `BACKEND_API_KEY` trong `.env` của backend RAG |
| Timeout | `90` (giây) |

### Bước 5: Sử dụng và kiểm thử

1. Đăng nhập Moodle bằng tài khoản sinh viên hoặc giảng viên.
2. Mở widget chatbot trên giao diện Moodle.
3. Thử các câu hỏi mẫu:
   - Nội dung: *"Khái niệm RAG là gì?"*
   - Cá nhân: *"Điểm của tôi thế nào?"*, *"Tôi sắp tới hạn bài nào?"*
   - Giảng viên: *"Danh sách sinh viên lớp này?"*, *"Ai chưa nộp bài?"*

### Bước 6 (tuỳ chọn): Cron email và Ngrok

**Cron Moodle** (cho thông báo email định kỳ):

- Cấu hình **Windows Task Scheduler** gọi `php C:\xampp\htdocs\moodle\admin\cli\cron.php` mỗi phút.

**Ngrok** (demo truy cập từ bên ngoài):

```powershell
ngrok http 8000
```

---

## Thứ tự khởi động dịch vụ

Khi chạy hệ thống, cần bật theo thứ tự:

1. **XAMPP** — Apache + MySQL (Moodle)
2. **Laravel Gateway** — cổng `8000`
3. **FastAPI RAG Backend** — cổng `8001`
4. Truy cập **Moodle** và sử dụng chatbot

---

## Tài liệu tham khảo trong repository

| File | Nội dung |
|---|---|
| `src/moodle-api/QUICK_START.md` | Hướng dẫn nhanh API Gateway |
| `src/moodle-api/MOODLE_SETUP_GUIDE.md` | Cấu hình Moodle Web Services |
| `src/moodle-api/API_DOCUMENTATION.md` | Danh mục endpoint API |
| `src/moodle-api/postman_collection.json` | Collection Postman kiểm thử |
| `src/moodle-rag-backend/README.md` | Hướng dẫn backend RAG |
| `docs/` | Đề cương và báo cáo khóa luận |

---

## Xử lý sự cố thường gặp

| Triệu chứng | Hướng xử lý |
|---|---|
| Chatbot không phản hồi | Kiểm tra backend RAG (`http://localhost:8001/health`) và URL/API key trong cấu hình plugin |
| Lỗi 401/403 khi gọi Gateway | Kiểm tra `X-API-Key` đúng vai trò (student/teacher/admin) |
| Câu hỏi nội dung không có câu trả lời | Chạy lại ingestion; kiểm tra `GEMINI_API_KEY` |
| Lỗi Moodle Web Services | Kiểm tra `MOODLE_TOKEN`, bật REST protocol, xem `MOODLE_SETUP_GUIDE.md` |

---

## Tác giả

**Trần Tấn Hưng** — MSSV: 110122081 — Lớp DA22TTD  
Khoa Công nghệ thông tin & Truyền thông — Đại học Cần Thơ  
Giảng viên hướng dẫn: **ThS. Trịnh Quốc Việt**
