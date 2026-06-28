# Kiến trúc Hệ thống - API Gateway Pattern

## Vấn đề cần giải quyết

### ❌ Không có API Gateway:
```
Chatbot
  ↓ Cần token của user1
Moodle (user1's token)

Chatbot  
  ↓ Cần token của user2
Moodle (user2's token)

Chatbot
  ↓ Cần token của user3
Moodle (user3's token)
```

**Vấn đề:**
- Phải quản lý token của TỪNG user
- Chatbot phải lưu trữ nhiều tokens
- Khó bảo mật
- Token user có thể hết hạn bất kỳ lúc nào
- Không scale được

### ✅ Có API Gateway (Giải pháp của bạn):
```
Chatbot (chỉ cần 1 API key)
  ↓ X-API-Key: chatbot_key_123
Laravel API Gateway (1 service token)
  ↓ wstoken: service_account_token
Moodle (service account có quyền đọc tất cả)
```

**Lợi ích:**
- ✅ Chatbot chỉ cần 1 API key đơn giản
- ✅ Laravel quản lý 1 Moodle token duy nhất
- ✅ Service account có quyền đọc data của tất cả users
- ✅ Dễ bảo mật và quản lý
- ✅ Có thể thêm caching, rate limiting
- ✅ Scale tốt

## Luồng hoạt động chi tiết

### Scenario: Chatbot hỏi "Điểm của sinh viên A là gì?"

```
┌─────────────────────────────────────────────────────────────┐
│ 1. USER                                                     │
│    "Cho tôi biết điểm của sinh viên student01"             │
└────────────────┬────────────────────────────────────────────┘
                 │
                 ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. CHATBOT (Python/Node.js)                                │
│    - Parse câu hỏi                                          │
│    - Xác định cần gọi API: /students/student01/results     │
│    - Gửi request với API key                                │
│                                                              │
│    GET /api/v1/moodle/students/student01/results           │
│    Header: X-API-Key: chatbot_key_123                       │
└────────────────┬────────────────────────────────────────────┘
                 │
                 ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. LARAVEL API GATEWAY                                      │
│    ┌──────────────────────────────────────────────┐        │
│    │ 3.1. ApiKeyAuth Middleware                   │        │
│    │      - Check X-API-Key header                │        │
│    │      - Validate: chatbot_key_123 có hợp lệ?  │        │
│    │      - ✅ Valid → Continue                    │        │
│    │      - ❌ Invalid → Return 401                │        │
│    └──────────────────────────────────────────────┘        │
│                 ↓                                            │
│    ┌──────────────────────────────────────────────┐        │
│    │ 3.2. MoodleController                        │        │
│    │      - Nhận request: username = "student01"  │        │
│    │      - Gọi MoodleClient service              │        │
│    └──────────────────────────────────────────────┘        │
│                 ↓                                            │
│    ┌──────────────────────────────────────────────┐        │
│    │ 3.3. MoodleClient Service                    │        │
│    │      - Lấy MOODLE_TOKEN từ config            │        │
│    │      - Gọi nhiều Moodle APIs:                │        │
│    │        • getUserByField('username', ...)     │        │
│    │        • getCourses()                        │        │
│    │        • getEnrolledUsers(courseId)          │        │
│    │        • getGrades(courseId, userId)         │        │
│    │        • getCourseCompetencies(courseId)     │        │
│    └──────────────────────────────────────────────┘        │
└────────────────┬────────────────────────────────────────────┘
                 │ Multiple requests với cùng 1 token
                 ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. MOODLE WEB SERVICES                                      │
│    ┌──────────────────────────────────────────────┐        │
│    │ Request 1:                                   │        │
│    │ POST /webservice/rest/server.php             │        │
│    │ wstoken=service_token                        │        │
│    │ wsfunction=core_user_get_users_by_field      │        │
│    │ → Return: user info                          │        │
│    └──────────────────────────────────────────────┘        │
│    ┌──────────────────────────────────────────────┐        │
│    │ Request 2:                                   │        │
│    │ wsfunction=core_course_get_courses           │        │
│    │ → Return: all courses                        │        │
│    └──────────────────────────────────────────────┘        │
│    ┌──────────────────────────────────────────────┐        │
│    │ Request 3-N:                                 │        │
│    │ wsfunction=gradereport_user_get_grade_items  │        │
│    │ wsfunction=core_competency_...               │        │
│    │ → Return: grades, competencies, etc.         │        │
│    └──────────────────────────────────────────────┘        │
└────────────────┬────────────────────────────────────────────┘
                 │ Return data
                 ↓
┌─────────────────────────────────────────────────────────────┐
│ 5. LARAVEL API GATEWAY                                      │
│    - Aggregate data từ nhiều Moodle calls                   │
│    - Format response:                                       │
│      {                                                       │
│        "success": true,                                     │
│        "data": {                                            │
│          "student": {...},                                  │
│          "courses": [                                       │
│            {                                                │
│              "course_id": 2,                                │
│              "grades": [...],                               │
│              "competencies": [...]                          │
│            }                                                │
│          ]                                                  │
│        }                                                    │
│      }                                                      │
└────────────────┬────────────────────────────────────────────┘
                 │ JSON response
                 ↓
┌─────────────────────────────────────────────────────────────┐
│ 6. CHATBOT                                                  │
│    - Parse JSON response                                    │
│    - Extract thông tin cần thiết                            │
│    - Feed vào RAG pipeline / LLM context                    │
│    - Generate câu trả lời tự nhiên                          │
└────────────────┬────────────────────────────────────────────┘
                 │
                 ↓
┌─────────────────────────────────────────────────────────────┐
│ 7. USER                                                     │
│    "Sinh viên student01 có điểm:                           │
│     - Lập trình Web: 8.5/10                                │
│     - Cơ sở dữ liệu: 9.0/10                                │
│     Đã đạt 5/6 CLO..."                                     │
└─────────────────────────────────────────────────────────────┘
```

## So sánh Authentication

### Moodle Token (Bắt buộc)
```
Type: Moodle Web Service Token
Format: abc123def456ghi789... (32+ chars)
Scope: Moodle Web Services API
Created: Trong Moodle admin panel
Used by: Laravel API Gateway
Permissions: Service account role (Manager/Custom)
```

### API Key (Của bạn tạo)
```
Type: Custom API Key
Format: Bất kỳ (vd: chatbot_key_123)
Scope: Laravel API Gateway
Created: Trong .env file
Used by: Chatbot, Mobile App, External Systems
Permissions: Tất cả endpoints trong API Gateway
```

## Tại sao cần cả 2 layers authentication?

### Layer 1: API Key (Laravel)
**Mục đích:** Bảo vệ Laravel API Gateway của bạn
- Ngăn người lạ gọi API của bạn
- Dễ quản lý: thêm/xóa keys trong .env
- Có thể tạo nhiều keys cho nhiều clients
- Không phụ thuộc vào Moodle

### Layer 2: Moodle Token
**Mục đích:** Xác thực với Moodle (bắt buộc)
- Moodle yêu cầu token cho mọi Web Service call
- Service account token có quyền đọc data của tất cả users
- Được quản lý trong Moodle admin panel
- Có thể set expiry, revoke khi cần

## Ưu điểm của kiến trúc này

### 1. Separation of Concerns
- Chatbot không cần biết về Moodle
- Moodle không cần biết về Chatbot
- Laravel làm cầu nối và translator

### 2. Security
- API keys dễ rotate
- Moodle token được giấu trong server
- Service account có quyền hạn cụ thể
- Có thể thêm rate limiting, IP whitelist

### 3. Scalability
- Thêm nhiều clients chỉ cần tạo API key mới
- Không cần tạo thêm Moodle tokens
- Có thể thêm caching layer
- Có thể load balance Laravel instances

### 4. Maintainability
- Thay đổi Moodle API chỉ sửa MoodleClient
- Thêm endpoints mới không ảnh hưởng Moodle
- Dễ debug và monitor
- Centralized logging

### 5. Flexibility
- Có thể aggregate data từ nhiều sources
- Transform data format cho phù hợp chatbot
- Thêm business logic nếu cần
- Cache expensive queries

## Deployment Architecture

### Development
```
Laptop/PC
├── Moodle (localhost:80)
├── Laravel API Gateway (localhost:8000)
└── Chatbot (localhost:5000)
```

### Production
```
Internet
    ↓
Load Balancer (HTTPS)
    ↓
┌─────────────────────────────────────┐
│ Laravel API Gateway Cluster         │
│ ├── Instance 1 (api.domain.com)    │
│ ├── Instance 2 (api.domain.com)    │
│ └── Instance 3 (api.domain.com)    │
└─────────────────┬───────────────────┘
                  ↓
┌─────────────────────────────────────┐
│ Moodle Server (moodle.domain.com)  │
│ ├── Web Services Enabled            │
│ └── Service Account Token           │
└─────────────────────────────────────┘

External Clients:
├── Chatbot Server (chatbot.domain.com)
├── Mobile App
└── Other Services
```

## Kết luận

Kiến trúc này:
- ✅ Giải quyết vấn đề token management
- ✅ Bảo mật tốt với 2 layers authentication
- ✅ Scale tốt cho nhiều clients
- ✅ Phù hợp cho khóa luận và production
- ✅ Dễ maintain và extend

**Moodle token là BẮT BUỘC**, nhưng với API Gateway, bạn chỉ cần quản lý 1 token duy nhất thay vì hàng trăm tokens của users!
