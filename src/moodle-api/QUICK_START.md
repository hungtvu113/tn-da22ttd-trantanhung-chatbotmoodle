# Quick Start Guide - 5 phút setup

Hướng dẫn nhanh để chạy API Gateway trong 5 phút.

## Prerequisites

- PHP 8.2+
- Composer
- Moodle đã cài đặt (local hoặc remote)
- Quyền admin trong Moodle

## Bước 1: Clone và Install (1 phút)

```bash
# Clone project
git clone <your-repo-url>
cd <project-folder>

# Install dependencies
composer install
```

## Bước 2: Configure Environment (2 phút)

```bash
# Copy .env
cp .env.example .env

# Generate app key
php artisan key:generate
```

Mở `.env` và cập nhật:

```env
# Moodle Configuration
MOODLE_URL=http://localhost/moodle
MOODLE_TOKEN=your_moodle_token_here

# API Keys (tạo key đơn giản cho test)
EXTERNAL_API_KEYS=test_key_123
```

### Lấy Moodle Token nhanh:

1. Đăng nhập Moodle admin
2. Vào: **Site administration** → **Server** → **Web services** → **Manage tokens**
3. Click **Add**
4. Chọn user admin, để service = "All services"
5. Click **Save** và copy token

**Lưu ý:** Đây là cách nhanh cho test. Production nên tạo service account riêng (xem [MOODLE_SETUP_GUIDE.md](MOODLE_SETUP_GUIDE.md))

## Bước 3: Run Server (30 giây)

```bash
php artisan serve
```

Server chạy tại: `http://localhost:8000`

## Bước 4: Test API (1 phút)

### Option 1: Dùng curl

```bash
# Test lấy danh sách courses
curl -H "X-API-Key: test_key_123" \
  http://localhost:8000/api/v1/moodle/courses
```

### Option 2: Dùng Postman

1. Import file `postman_collection.json`
2. Cập nhật biến `api_key` = `test_key_123`
3. Click "Send" trên request "1. Get All Courses"

### Option 3: Dùng Python

```bash
cd examples
pip install requests
python chatbot_integration.py
```

## Kết quả mong đợi

Nếu thành công, bạn sẽ thấy response:

```json
{
  "success": true,
  "data": [
    {
      "id": 2,
      "fullname": "Lập trình Web",
      "shortname": "IT4409",
      "idnumber": "IT4409",
      "category": {
        "id": 5,
        "name": "Công nghệ thông tin"
      },
      "category_path": [...]
    }
  ]
}
```

## Troubleshooting

### Lỗi: "Unauthorized: Invalid API Key"
**Giải pháp:** Check `EXTERNAL_API_KEYS` trong `.env` và header `X-API-Key`

### Lỗi: "Moodle API: Invalid token"
**Giải pháp:** 
1. Check `MOODLE_TOKEN` trong `.env`
2. Verify token còn hợp lệ trong Moodle
3. Check Moodle Web Services đã enable

### Lỗi: "Connection refused"
**Giải pháp:**
1. Check `MOODLE_URL` đúng chưa
2. Check Moodle server đang chạy
3. Nếu Moodle local, dùng `http://localhost/moodle` không phải `http://127.0.0.1`

### Lỗi: "Function not found"
**Giải pháp:**
1. Vào Moodle: **Site administration** → **Advanced features**
2. Enable **Web services**
3. Vào **Server** → **Web services** → **Manage protocols**
4. Enable **REST protocol**

## Next Steps

✅ API đã chạy! Bây giờ bạn có thể:

1. **Xem tất cả endpoints:** [API_DOCUMENTATION.md](API_DOCUMENTATION.md)
2. **Hiểu kiến trúc:** [ARCHITECTURE.md](ARCHITECTURE.md)
3. **Setup production:** [MOODLE_SETUP_GUIDE.md](MOODLE_SETUP_GUIDE.md)
4. **Tích hợp chatbot:** [examples/README.md](examples/README.md)

## Test tất cả endpoints

```bash
# 1. Courses
curl -H "X-API-Key: test_key_123" \
  http://localhost:8000/api/v1/moodle/courses

# 2. Students in course
curl -H "X-API-Key: test_key_123" \
  http://localhost:8000/api/v1/moodle/courses/2/students

# 3. Grades
curl -H "X-API-Key: test_key_123" \
  http://localhost:8000/api/v1/moodle/courses/2/grades

# 4. Student results (thay student01 bằng username thật)
curl -H "X-API-Key: test_key_123" \
  http://localhost:8000/api/v1/moodle/students/student01/results
```

## Production Checklist

Trước khi deploy production:

- [ ] Tạo service account trong Moodle (không dùng admin)
- [ ] Tạo External Service với functions cụ thể
- [ ] Generate token cho service account
- [ ] Tạo API keys mạnh (không dùng `test_key_123`)
- [ ] Enable HTTPS
- [ ] Set `APP_DEBUG=false`
- [ ] Configure proper logging
- [ ] Add rate limiting (optional)
- [ ] Setup monitoring (optional)

Xem chi tiết: [MOODLE_SETUP_GUIDE.md](MOODLE_SETUP_GUIDE.md)

---

**Chúc mừng! 🎉 API Gateway của bạn đã sẵn sàng.**

Nếu gặp vấn đề, xem [Troubleshooting](#troubleshooting) hoặc check logs:
```bash
tail -f storage/logs/laravel.log
```
