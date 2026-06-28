# Hướng dẫn Setup Moodle Web Services Token

## Tổng quan

Moodle Web Services **BẮT BUỘC** phải có token để truy cập API. Đây là hướng dẫn tạo token cho API Gateway.

## Kiến trúc Authentication

```
External System (Chatbot)
    ↓ [X-API-Key: simple_key]
Laravel API Gateway
    ↓ [wstoken: moodle_service_token]
Moodle Web Services
```

**Lợi ích:**
- Chatbot chỉ cần 1 API key đơn giản
- Laravel Gateway sử dụng 1 Moodle token duy nhất (service account)
- Không cần quản lý token của từng user

## Bước 1: Enable Web Services trong Moodle

### 1.1. Enable Web Services
1. Đăng nhập Moodle với quyền **Administrator**
2. Vào: **Site administration** → **Advanced features**
3. Tích chọn: **Enable web services**
4. Click **Save changes**

### 1.2. Enable REST Protocol
1. Vào: **Site administration** → **Server** → **Web services** → **Manage protocols**
2. Enable: **REST protocol**

## Bước 2: Tạo Service Account (Recommended)

**Tại sao cần service account?**
- Không nên dùng token của admin account thật
- Service account có quyền hạn cụ thể, dễ quản lý
- Dễ revoke khi cần

### 2.1. Tạo User mới
1. Vào: **Site administration** → **Users** → **Add a new user**
2. Điền thông tin:
   - Username: `moodle_api_service`
   - Password: (mật khẩu mạnh)
   - Email: `api-service@yourdomain.com`
   - First name: `API`
   - Last name: `Service Account`

### 2.2. Gán Role cho Service Account
1. Vào: **Site administration** → **Users** → **Permissions** → **Assign system roles**
2. Chọn role: **Manager** hoặc tạo custom role với các capabilities:
   - `webservice/rest:use`
   - `moodle/course:view`
   - `moodle/course:viewhiddencourses`
   - `moodle/user:viewdetails`
   - `moodle/grade:view`
   - `moodle/competency:coursecompetencyview`
   - `moodle/competency:usercompetencyview`

## Bước 3: Tạo Web Service

### 3.1. Tạo External Service
1. Vào: **Site administration** → **Server** → **Web services** → **External services**
2. Click **Add**
3. Điền thông tin:
   - Name: `Chatbot API Service`
   - Short name: `chatbot_api`
   - Enabled: ✅ Yes
   - Authorized users only: ✅ Yes (recommended)
   - Can download files: ✅ Yes (nếu cần)

### 3.2. Add Functions vào Service
Click **Add functions** và thêm các functions sau:

**Core Functions:**
```
core_webservice_get_site_info
core_course_get_courses
core_course_get_courses_by_field
core_course_get_categories
core_enrol_get_enrolled_users
core_user_get_users_by_field
```

**Grade Functions:**
```
gradereport_user_get_grade_items
```

**Assignment Functions:**
```
mod_assign_get_assignments
```

**Competency Functions (CLO/PLO):**
```
core_competency_list_course_competencies
core_competency_get_user_competency_in_course
```

**Tip:** Tìm nhanh bằng Ctrl+F trong danh sách functions

## Bước 4: Authorize User

1. Vào: **Site administration** → **Server** → **Web services** → **External services**
2. Click **Authorized users** ở service `Chatbot API Service`
3. Add user: `moodle_api_service`

## Bước 5: Tạo Token

### 5.1. Tạo Token cho Service Account
1. Vào: **Site administration** → **Server** → **Web services** → **Manage tokens**
2. Click **Add**
3. Chọn:
   - User: `moodle_api_service`
   - Service: `Chatbot API Service`
   - Valid until: (để trống = không hết hạn, hoặc chọn ngày)
4. Click **Save changes**
5. **Copy token** (dạng: `abc123def456...`)

### 5.2. Lưu Token vào Laravel .env
```env
MOODLE_URL=https://your-moodle-site.com
MOODLE_TOKEN=abc123def456ghi789jkl012mno345pqr678
```

## Bước 6: Test Connection

### 6.1. Test trực tiếp Moodle API
```bash
curl "https://your-moodle-site.com/webservice/rest/server.php?wstoken=YOUR_TOKEN&wsfunction=core_webservice_get_site_info&moodlewsrestformat=json"
```

Nếu thành công, bạn sẽ thấy thông tin site.

### 6.2. Test qua Laravel API Gateway
```bash
# Thêm API key vào .env
EXTERNAL_API_KEYS=test_key_123

# Test
curl -H "X-API-Key: test_key_123" \
  http://localhost:8000/api/v1/moodle/courses
```

## Security Best Practices

### ✅ Nên làm:
1. **Sử dụng service account riêng** - Không dùng admin account thật
2. **Giới hạn capabilities** - Chỉ cấp quyền cần thiết
3. **Enable "Authorized users only"** - Chỉ user được authorize mới dùng được
4. **Set token expiry** - Token tự động hết hạn sau thời gian
5. **HTTPS bắt buộc** - Không bao giờ dùng HTTP trong production
6. **IP Whitelist** - Giới hạn IP được phép gọi API (nếu có thể)
7. **Monitor logs** - Theo dõi Web service logs trong Moodle

### ❌ Không nên:
1. Commit token vào Git
2. Dùng token của admin account thật
3. Share token qua email/chat không mã hóa
4. Để token không hết hạn trong môi trường production
5. Cấp quá nhiều quyền cho service account

## Troubleshooting

### Lỗi: "Access control exception"
**Nguyên nhân:** User chưa được authorize hoặc thiếu capability
**Giải pháp:** 
- Check user đã được add vào "Authorized users"
- Check role có đủ capabilities

### Lỗi: "Invalid token"
**Nguyên nhân:** Token sai hoặc đã bị xóa
**Giải pháp:**
- Verify token trong Moodle → Manage tokens
- Tạo token mới nếu cần

### Lỗi: "Function not found"
**Nguyên nhân:** Function chưa được add vào service
**Giải pháp:**
- Vào External services → Add functions
- Add function bị thiếu

### Lỗi: "Web services are not enabled"
**Nguyên nhân:** Web services chưa được enable
**Giải pháp:**
- Site administration → Advanced features
- Enable web services

## Deploy lên Production

### Checklist:
- [ ] Moodle đã enable Web Services
- [ ] REST protocol đã enable
- [ ] Service account đã tạo với quyền phù hợp
- [ ] External service đã tạo với đầy đủ functions
- [ ] Token đã tạo và lưu vào .env
- [ ] HTTPS đã được cấu hình
- [ ] Laravel API Gateway đã deploy
- [ ] API Keys cho chatbot đã tạo
- [ ] Test tất cả endpoints

### Environment Variables (.env)
```env
# Moodle Configuration
MOODLE_URL=https://moodle.yourdomain.com
MOODLE_TOKEN=your_service_account_token_here

# API Keys for External Systems
EXTERNAL_API_KEYS=chatbot_prod_key_abc123,mobile_app_key_xyz789

# Laravel
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.yourdomain.com
```

## Monitoring

### Moodle Logs
Xem logs trong Moodle:
- **Site administration** → **Reports** → **Logs**
- Filter by: Web service

### Laravel Logs
```bash
tail -f storage/logs/laravel.log
```

## Tài liệu tham khảo

- [Moodle Web Services Documentation](https://docs.moodle.org/en/Web_services)
- [Moodle Web Services API](https://docs.moodle.org/dev/Web_services_API)
- [Creating a web service client](https://docs.moodle.org/en/Using_web_services)

## Kết luận

Với setup này:
- ✅ Moodle vẫn được bảo vệ bằng token (bắt buộc)
- ✅ Laravel API Gateway quản lý 1 token duy nhất
- ✅ Chatbot chỉ cần API key đơn giản
- ✅ Dễ dàng revoke/rotate tokens khi cần
- ✅ Phù hợp cho khóa luận và production
