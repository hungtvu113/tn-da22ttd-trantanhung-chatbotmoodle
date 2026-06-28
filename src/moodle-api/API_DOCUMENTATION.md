# Moodle API Gateway Documentation

## Tổng quan
API Gateway này cung cấp interface để các hệ thống bên ngoài (Chatbot, Mobile App, etc.) truy cập dữ liệu Moodle mà không cần token của từng user.

## Authentication
Tất cả requests phải có header:
```
X-API-Key: your_api_key_here
```

## Base URL
```
http://your-domain.com/api/v1/moodle
```

## Endpoints

### 1. Lấy danh sách courses
```http
GET /courses?search={keyword}
```

**Query Parameters:**
- `search` (optional): Tìm kiếm theo shortname, fullname, hoặc idnumber

**Response:**
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
      "category_path": [
        {"id": 1, "name": "Khoa CNTT"},
        {"id": 5, "name": "Công nghệ thông tin"}
      ]
    }
  ]
}
```

### 2. Lấy danh sách sinh viên trong course
```http
GET /courses/{courseId}/students
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 123,
      "username": "student01",
      "firstname": "Nguyen",
      "lastname": "Van A",
      "fullname": "Nguyen Van A",
      "email": "student01@example.com",
      "idnumber": "20210001"
    }
  ]
}
```

### 3. Lấy điểm của sinh viên trong course
```http
GET /courses/{courseId}/grades
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "user_id": 123,
      "fullname": "Nguyen Van A",
      "activities": [
        {
          "name": "Bài tập 1",
          "module": "assign",
          "grade": 8.5,
          "max_grade": 10,
          "percentage": "85.00 %"
        }
      ]
    }
  ]
}
```

### 4. Lấy CLO/PLO của course
```http
GET /courses/{courseId}/competencies
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "shortname": "CLO1",
      "description": "Hiểu được các khái niệm cơ bản",
      "idnumber": "CLO1"
    }
  ]
}
```

### 5. Lấy kết quả CLO của sinh viên
```http
GET /courses/{courseId}/competency-results
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "user_id": 123,
      "fullname": "Nguyen Van A",
      "competencies": [
        {
          "competency_id": 1,
          "grade": 3,
          "proficiency": 1,
          "status": "Dat"
        }
      ]
    }
  ]
}
```

### 6. Truy xuất ngược từ mã môn học
```http
GET /courses/traceback?search={keyword}
```

**Query Parameters:**
- `search` (required): Mã môn học hoặc tên môn học

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "course": {
        "id": 2,
        "fullname": "Lập trình Web",
        "shortname": "IT4409",
        "idnumber": "IT4409"
      },
      "category_hierarchy": [
        {"id": 1, "name": "Khoa CNTT"},
        {"id": 5, "name": "Công nghệ thông tin"}
      ],
      "breadcrumb": "Khoa CNTT > Công nghệ thông tin > IT4409"
    }
  ]
}
```

### 7. Lấy tổng hợp đầy đủ dữ liệu course
```http
GET /courses/{courseId}/full-results
```

**Response:**
```json
{
  "success": true,
  "data": {
    "course": {
      "id": 2,
      "fullname": "Lập trình Web",
      "shortname": "IT4409"
    },
    "competencies_clo": [...],
    "assignments": [...],
    "students": [
      {
        "id": 123,
        "fullname": "Nguyen Van A",
        "email": "student01@example.com",
        "grades": [...]
      }
    ]
  }
}
```

### 8. Lấy kết quả học tập của một sinh viên
```http
GET /students/{username}/results
```

**Path Parameters:**
- `username`: Username hoặc idnumber của sinh viên

**Response:**
```json
{
  "success": true,
  "data": {
    "student": {
      "id": 123,
      "username": "student01",
      "fullname": "Nguyen Van A",
      "email": "student01@example.com",
      "idnumber": "20210001"
    },
    "total_courses": 5,
    "courses": [
      {
        "course_id": 2,
        "fullname": "Lập trình Web",
        "shortname": "IT4409",
        "category_path": [...],
        "grades": [...],
        "competencies": [...]
      }
    ]
  }
}
```

## Error Responses

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Unauthorized: Invalid API Key"
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Khong tim thay SV: student01"
}
```

### 500 Internal Server Error
```json
{
  "success": false,
  "message": "Moodle API: Error message here"
}
```

## Sử dụng với Chatbot

### Example: Python/FastAPI
```python
import requests

API_BASE = "http://your-domain.com/api/v1/moodle"
API_KEY = "your_api_key_here"

headers = {
    "X-API-Key": API_KEY
}

# Lấy thông tin sinh viên
response = requests.get(
    f"{API_BASE}/students/student01/results",
    headers=headers
)
data = response.json()
```

### Example: JavaScript/Node.js
```javascript
const axios = require('axios');

const API_BASE = 'http://your-domain.com/api/v1/moodle';
const API_KEY = 'your_api_key_here';

const headers = {
  'X-API-Key': API_KEY
};

// Lấy danh sách courses
axios.get(`${API_BASE}/courses`, { headers })
  .then(response => {
    console.log(response.data);
  });
```

## Setup

1. Copy `.env.example` thành `.env`
2. Cấu hình Moodle:
   ```
   MOODLE_URL=http://your-moodle-site.com
   MOODLE_TOKEN=your_moodle_webservice_token
   ```
3. Tạo API key cho chatbot:
   ```
   EXTERNAL_API_KEYS=chatbot_key_abc123,mobile_app_key_xyz789
   ```
4. Chatbot sử dụng API key này trong header `X-API-Key`

## Security Notes

- API key phải được bảo mật, không commit vào git
- Nên sử dụng HTTPS trong production
- Có thể thêm rate limiting để tránh abuse
- Log tất cả requests để audit
