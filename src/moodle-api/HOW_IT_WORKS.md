# Cách hoạt động - API Gateway Pattern

Giải thích đơn giản về cách API Gateway giải quyết vấn đề token management.

## 🤔 Vấn đề

### Scenario: Chatbot cần lấy điểm của 100 sinh viên

**Cách truyền thống (KHÔNG khả thi):**
```
Chatbot cần:
- Token của sinh viên 1
- Token của sinh viên 2
- Token của sinh viên 3
- ...
- Token của sinh viên 100

❌ Vấn đề:
- Phải lưu 100 tokens
- Tokens có thể expire
- Bảo mật kém
- Không scale
```

## ✅ Giải pháp: API Gateway

### Cách hoạt động

```
┌─────────────────────────────────────────────────────────┐
│ CHATBOT                                                 │
│ Chỉ cần biết 1 thứ: API Key = "chatbot_key_123"       │
└────────────────┬────────────────────────────────────────┘
                 │
                 │ Request: GET /students/student01/results
                 │ Header: X-API-Key: chatbot_key_123
                 │
                 ↓
┌─────────────────────────────────────────────────────────┐
│ LARAVEL API GATEWAY                                     │
│                                                          │
│ Step 1: Check API Key                                  │
│   ✓ "chatbot_key_123" hợp lệ? → Yes, continue         │
│                                                          │
│ Step 2: Lấy Moodle Token từ .env                       │
│   MOODLE_TOKEN = "service_account_token_xyz"           │
│                                                          │
│ Step 3: Gọi Moodle APIs (với service token)            │
│   - getUserByField("student01")                        │
│   - getCourses()                                        │
│   - getGrades(courseId, userId)                        │
│   - getCompetencies(courseId)                          │
│                                                          │
│ Step 4: Aggregate data và return JSON                  │
└────────────────┬────────────────────────────────────────┘
                 │
                 │ Multiple requests với CÙNG 1 token
                 │
                 ↓
┌─────────────────────────────────────────────────────────┐
│ MOODLE                                                  │
│                                                          │
│ Nhận requests với token: "service_account_token_xyz"   │
│                                                          │
│ Service account có quyền:                               │
│ ✓ Đọc tất cả courses                                   │
│ ✓ Đọc tất cả students                                  │
│ ✓ Đọc tất cả grades                                    │
│ ✓ Đọc tất cả competencies                              │
│                                                          │
│ Return data                                             │
└─────────────────────────────────────────────────────────┘
```

## 🔑 Key Concepts

### 1. Service Account
**Là gì?** Một user đặc biệt trong Moodle với quyền đọc data của tất cả users

**Tại sao?** Thay vì dùng token của 100 users, chỉ cần 1 token của service account

**Ví dụ:**
```
User thường:     student01 → chỉ xem được data của mình
Service account: api_service → xem được data của TẤT CẢ
```

### 2. API Key vs Moodle Token

**API Key (của bạn tạo):**
- Mục đích: Bảo vệ Laravel API Gateway
- Format: Bất kỳ (vd: "chatbot_key_123")
- Quản lý: Trong .env file
- Dùng bởi: Chatbot, Mobile App, etc.

**Moodle Token (Moodle tạo):**
- Mục đích: Xác thực với Moodle Web Services
- Format: 32+ chars random string
- Quản lý: Trong Moodle admin panel
- Dùng bởi: Laravel API Gateway

### 3. Two-Layer Authentication

```
Layer 1: API Key
├─ Bảo vệ: Laravel API Gateway
├─ Check: ApiKeyAuth Middleware
└─ Fail: Return 401 Unauthorized

Layer 2: Moodle Token
├─ Bảo vệ: Moodle Web Services
├─ Check: Moodle validates token
└─ Fail: Return "Invalid token" error
```

## 📊 So sánh

### Trước (Không có API Gateway)

```
Chatbot → Moodle (token của user1)
Chatbot → Moodle (token của user2)
Chatbot → Moodle (token của user3)
...

Quản lý: N tokens (N = số users)
Bảo mật: ❌ Tokens exposed
Scale: ❌ Không scale
```

### Sau (Có API Gateway)

```
Chatbot → Laravel (API key)
           ↓
        Moodle (1 service token)

Quản lý: 1 Moodle token + M API keys (M = số clients)
Bảo mật: ✅ Token hidden trong server
Scale: ✅ Dễ dàng scale
```

## 🎬 Example Flow

### User hỏi: "Điểm của tôi là gì?"

**Step 1: Chatbot nhận câu hỏi**
```python
user_question = "Điểm của tôi là gì?"
username = "student01"  # From user session
```

**Step 2: Chatbot gọi API Gateway**
```python
response = requests.get(
    "http://api.domain.com/api/v1/moodle/students/student01/results",
    headers={"X-API-Key": "chatbot_key_123"}
)
```

**Step 3: Laravel check API key**
```php
// ApiKeyAuth Middleware
if ($apiKey !== 'chatbot_key_123') {
    return response()->json(['error' => 'Unauthorized'], 401);
}
// ✓ Valid, continue
```

**Step 4: Laravel gọi Moodle**
```php
// MoodleClient
$student = $this->call('core_user_get_users_by_field', [
    'wstoken' => env('MOODLE_TOKEN'),  // service_account_token
    'field' => 'username',
    'value' => 'student01'
]);

$courses = $this->call('core_course_get_courses', [
    'wstoken' => env('MOODLE_TOKEN')
]);

// ... more calls
```

**Step 5: Moodle trả về data**
```json
{
  "student": {...},
  "courses": [
    {
      "course_id": 2,
      "fullname": "Lập trình Web",
      "grades": [
        {"activity": "Bài tập 1", "grade": 8.5}
      ]
    }
  ]
}
```

**Step 6: Chatbot generate response**
```python
# Parse JSON
data = response.json()

# Feed to LLM
prompt = f"Context: {data}\nQuestion: {user_question}"
answer = llm.generate(prompt)

# Return to user
print("Bạn có điểm 8.5/10 trong bài tập 1 môn Lập trình Web")
```

## 🔒 Security

### Moodle Token được bảo vệ như thế nào?

**1. Stored in .env (server-side)**
```env
MOODLE_TOKEN=abc123...  # Never exposed to clients
```

**2. Never sent to clients**
```
Chatbot → Laravel: X-API-Key (simple key)
Laravel → Moodle: wstoken (Moodle token)

Chatbot KHÔNG BAO GIỜ thấy Moodle token!
```

**3. Service account có quyền hạn cụ thể**
```
Service account CAN:
✓ Read courses
✓ Read students
✓ Read grades
✓ Read competencies

Service account CANNOT:
✗ Delete courses
✗ Modify grades
✗ Delete users
```

## 🚀 Benefits

### 1. Simplicity
- Chatbot chỉ cần 1 API key
- Không cần hiểu Moodle API
- Không cần quản lý tokens

### 2. Security
- Moodle token hidden
- API keys dễ rotate
- Service account có quyền hạn cụ thể

### 3. Scalability
- Thêm client = thêm API key
- Không cần thêm Moodle tokens
- Có thể load balance

### 4. Maintainability
- Thay đổi Moodle API chỉ sửa 1 chỗ
- Centralized logging
- Dễ debug

## 🎓 Kết luận

**Câu hỏi:** Moodle có yêu cầu token không?
**Trả lời:** CÓ, bắt buộc!

**Câu hỏi:** Vậy làm sao chatbot không cần token?
**Trả lời:** Chatbot KHÔNG gọi trực tiếp Moodle. Chatbot gọi Laravel API Gateway, và Laravel dùng service account token để gọi Moodle.

**Câu hỏi:** Vậy có bypass được Moodle token không?
**Trả lời:** KHÔNG. Moodle token vẫn cần thiết. Nhưng thay vì quản lý N tokens, chỉ cần quản lý 1 token.

---

**TL;DR:** API Gateway = Cầu nối giữa Chatbot và Moodle, giúp quản lý 1 token thay vì nhiều tokens.
