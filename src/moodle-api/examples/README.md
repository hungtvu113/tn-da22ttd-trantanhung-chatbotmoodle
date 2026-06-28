# Examples - Tích hợp Chatbot với Moodle API Gateway

Thư mục này chứa các ví dụ minh họa cách tích hợp Chatbot với Laravel API Gateway.

## Setup

### 1. Cài đặt dependencies
```bash
pip install -r requirements.txt
```

### 2. Đảm bảo Laravel API Gateway đang chạy
```bash
# Trong thư mục root của project
php artisan serve
```

### 3. Cấu hình API key trong .env
```env
EXTERNAL_API_KEYS=chatbot_key_123
```

## Chạy Examples

### Example 1: Basic Usage
```bash
python chatbot_integration.py
```

Ví dụ này minh họa:
- Kết nối với API Gateway
- Lấy danh sách courses
- Lấy thông tin sinh viên
- Error handling

## Tích hợp với Chatbot thực tế

### Với OpenAI
```python
import openai
from chatbot_integration import MoodleAPIClient

api = MoodleAPIClient(
    base_url='http://localhost:8000/api/v1/moodle',
    api_key='chatbot_key_123'
)

# Lấy context từ Moodle
student_data = api.get_student_results('student01')

# Build prompt
prompt = f"""
Context: {student_data}

User question: Điểm của tôi là gì?

Answer:
"""

# Generate response
response = openai.ChatCompletion.create(
    model="gpt-4",
    messages=[{"role": "user", "content": prompt}]
)
```

### Với LangChain
```python
from langchain.llms import OpenAI
from langchain.chains import RetrievalQA
from langchain.vectorstores import Chroma
from chatbot_integration import MoodleAPIClient

# Initialize
api = MoodleAPIClient(...)
llm = OpenAI()

# Fetch data từ Moodle
courses = api.get_courses()

# Index vào vector store
# ... (xem example_3_rag_pipeline)

# Create QA chain
qa = RetrievalQA.from_chain_type(
    llm=llm,
    chain_type="stuff",
    retriever=vectorstore.as_retriever()
)

# Query
answer = qa.run("Có những môn học nào về lập trình?")
```

## Architecture

```
User Question
    ↓
Chatbot (Python)
    ↓ [X-API-Key]
Laravel API Gateway
    ↓ [Moodle Token]
Moodle
    ↓
Data
    ↓
LLM (OpenAI/Claude)
    ↓
Natural Language Answer
```

## Lưu ý

- API key phải được cấu hình trong `.env` của Laravel
- Moodle token phải hợp lệ và có đủ permissions
- Trong production nên dùng HTTPS
- Có thể thêm caching để giảm số lượng requests

## Tài liệu tham khảo

- [API Documentation](../API_DOCUMENTATION.md)
- [Architecture](../ARCHITECTURE.md)
- [Moodle Setup Guide](../MOODLE_SETUP_GUIDE.md)
