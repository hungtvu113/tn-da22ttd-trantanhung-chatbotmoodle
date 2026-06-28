# Moodle API Gateway - Hệ thống Chatbot RAG

API Gateway trung gian giữa Chatbot và Moodle LMS, cho phép truy cập dữ liệu Moodle mà không cần token của từng user.

## 🎯 Vấn đề giải quyết

**Vấn đề:** Moodle Web Services yêu cầu token cho mỗi user. Chatbot cần truy cập data của nhiều users → Phải quản lý hàng trăm tokens?

**Giải pháp:** API Gateway với Service Account Pattern - Chỉ cần 1 Moodle token duy nhất!

## Kiến trúc hệ thống

```
┌─────────────────────────────────────────────────────────┐
│  Chatbot / Mobile App / External Systems                │
│  Authentication: X-API-Key (simple)                     │
└────────────────┬────────────────────────────────────────┘
                 │
                 ↓
┌─────────────────────────────────────────────────────────┐
│  Laravel API Gateway (this project)                     │
│  - API Key Middleware                                   │
│  - 8 RESTful Endpoints                                  │
│  - Data Aggregation                                     │
│  Authentication: Service Account Token                  │
└────────────────┬────────────────────────────────────────┘
                 │
                 ↓
┌─────────────────────────────────────────────────────────┐
│  Moodle LMS                                             │
│  - Web Services (REST)                                  │
│  - Courses, Students, Grades, CLO/PLO                   │
└─────────────────────────────────────────────────────────┘
```

## 🚀 Quick Start

```bash
# 1. Install
composer install

# 2. Configure
cp .env.example .env
php artisan key:generate

# 3. Setup Moodle credentials in .env
MOODLE_URL=http://your-moodle-site.com
MOODLE_TOKEN=your_moodle_token
EXTERNAL_API_KEYS=chatbot_key_123

# 4. Run
php artisan serve

# 5. Test
curl -H "X-API-Key: chatbot_key_123" \
  http://localhost:8000/api/v1/moodle/courses
```

**Xem hướng dẫn chi tiết:** [QUICK_START.md](QUICK_START.md)

## Tính năng

- ✅ API Gateway pattern - một token Moodle phục vụ nhiều clients
- ✅ API Key authentication cho external systems
- ✅ 8 endpoints đầy đủ: courses, students, grades, CLO/PLO, etc.
- ✅ Error handling và logging
- ✅ Response format chuẩn JSON
- ✅ Category hierarchy và traceback

## Cài đặt

### 1. Clone và cài đặt dependencies
```bash
composer install
npm install
```

### 2. Cấu hình môi trường
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Cấu hình Moodle trong .env
```env
MOODLE_URL=http://your-moodle-site.com
MOODLE_TOKEN=your_moodle_webservice_token
```

**⚠️ QUAN TRỌNG: Về Moodle Token**

Moodle Web Services **BẮT BUỘC** phải có token (không thể bypass). Nhưng đây chính là lý do cần API Gateway này:

- **Moodle:** Yêu cầu token cho mọi request (bắt buộc)
- **Laravel API Gateway:** Sử dụng 1 token duy nhất (service account)
- **Chatbot/External:** Chỉ cần API key đơn giản

**Xem hướng dẫn chi tiết:** [MOODLE_SETUP_GUIDE.md](MOODLE_SETUP_GUIDE.md)

**Quick setup:**
1. Enable Web Services trong Moodle
2. Tạo service account (không dùng admin thật)
3. Tạo External Service với các functions cần thiết
4. Generate token cho service account
5. Copy token vào .env

### 4. Tạo API Keys cho external systems
```env
# Tạo API keys cho chatbot, mobile app, etc.
EXTERNAL_API_KEYS=chatbot_key_abc123,mobile_app_key_xyz789
```

### 5. Chạy server
```bash
php artisan serve
```

API sẽ chạy tại: `http://localhost:8000`

## Sử dụng API

Xem chi tiết trong [API_DOCUMENTATION.md](API_DOCUMENTATION.md)

### Quick Example
```bash
curl -H "X-API-Key: chatbot_key_abc123" \
  http://localhost:8000/api/v1/moodle/courses
```

## Tích hợp với Chatbot

### Python Example
```python
import requests

API_BASE = "http://localhost:8000/api/v1/moodle"
API_KEY = "chatbot_key_abc123"

def get_student_results(username):
    response = requests.get(
        f"{API_BASE}/students/{username}/results",
        headers={"X-API-Key": API_KEY}
    )
    return response.json()

# Sử dụng trong RAG pipeline
student_data = get_student_results("student01")
# Feed vào LLM context...
```

## API Endpoints

| Method | Endpoint | Mô tả |
|--------|----------|-------|
| GET | `/api/v1/moodle/courses` | Danh sách courses |
| GET | `/api/v1/moodle/courses/{id}/students` | Sinh viên trong course |
| GET | `/api/v1/moodle/courses/{id}/grades` | Điểm số |
| GET | `/api/v1/moodle/courses/{id}/competencies` | CLO/PLO |
| GET | `/api/v1/moodle/courses/{id}/competency-results` | Kết quả CLO |
| GET | `/api/v1/moodle/courses/traceback` | Truy xuất ngược |
| GET | `/api/v1/moodle/courses/{id}/full-results` | Tổng hợp đầy đủ |
| GET | `/api/v1/moodle/students/{username}/results` | Kết quả sinh viên |

## Security

- ✅ API Key authentication
- ✅ Middleware protection
- ✅ Error logging
- ⚠️ Nên dùng HTTPS trong production
- ⚠️ Có thể thêm rate limiting

## Testing

```bash
# Test API với curl
curl -H "X-API-Key: your_key" \
  http://localhost:8000/api/v1/moodle/courses

# Hoặc dùng Postman/Insomnia
```

## Khóa luận

**Đề tài:** Xây dựng hệ thống Chatbot thông minh hỗ trợ học tập dựa trên công nghệ RAG tích hợp hệ thống quản lý học tập Moodle qua Web Services API

**Vai trò của API Gateway này:**
- Cầu nối giữa Chatbot và Moodle
- Đơn giản hóa authentication (1 token thay vì nhiều)
- Chuẩn hóa data format cho RAG pipeline
- Tối ưu performance với caching (có thể thêm)

## License

MIT

---

## 🔒 Security

**Câu hỏi:** API Gateway có dễ bị tấn công không?
**Trả lời:** KHÔNG. API Gateway TĂNG bảo mật, không giảm!

### So sánh Bảo mật

**Không có Gateway (Kém bảo mật):**
- ❌ Moodle token exposed trong client code
- ❌ Mỗi client cần token riêng
- ❌ Khó kiểm soát access
- ❌ Không có centralized logging
- ❌ Không có rate limiting

**Có Gateway (Bảo mật tốt):**
- ✅ Moodle token ẨN trong server
- ✅ Chỉ 1 token duy nhất
- ✅ Centralized security control
- ✅ Comprehensive logging
- ✅ Rate limiting (100 req/min)
- ✅ Input sanitization
- ✅ Multiple protection layers

### Security Layers (Đã tích hợp)

1. **Request Logging** - Audit trail, detect attacks
2. **Input Sanitization** - Chống XSS, SQL Injection
3. **API Key Auth** - Xác thực clients
4. **Rate Limiting** - Chống DDoS
5. **IP Whitelist** - Optional, restrict by IP

**Xem chi tiết:** [SECURITY.md](SECURITY.md)

## 📚 Tài liệu đầy đủ

- **[QUICK_START.md](QUICK_START.md)** - Setup trong 5 phút
- **[HOW_IT_WORKS.md](HOW_IT_WORKS.md)** - Giải thích cách hoạt động
- **[SECURITY.md](SECURITY.md)** - ⭐ Bảo mật chi tiết (attack scenarios & defense)
- **[FAQ.md](FAQ.md)** - Câu hỏi thường gặp
- **[API_DOCUMENTATION.md](API_DOCUMENTATION.md)** - Chi tiết tất cả endpoints
- **[ARCHITECTURE.md](ARCHITECTURE.md)** - Kiến trúc hệ thống và luồng hoạt động
- **[MOODLE_SETUP_GUIDE.md](MOODLE_SETUP_GUIDE.md)** - Hướng dẫn setup Moodle Web Services
- **[THESIS_SUMMARY.md](THESIS_SUMMARY.md)** - Tóm tắt khóa luận
- **[THESIS_CHECKLIST.md](THESIS_CHECKLIST.md)** - Checklist hoàn thành khóa luận
- **[examples/](examples/)** - Code examples tích hợp với Chatbot
- **[postman_collection.json](postman_collection.json)** - Postman collection để test

## 🎓 Dành cho Khóa luận

Project này được thiết kế cho khóa luận với đầy đủ:
- ✅ Documentation chi tiết
- ✅ Architecture diagrams
- ✅ Code examples
- ✅ Postman collection
- ✅ Setup guides
- ✅ Best practices

**Xem [THESIS_SUMMARY.md](THESIS_SUMMARY.md) để hiểu rõ hơn về giải pháp và đóng góp của khóa luận.**
