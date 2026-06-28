# Moodle RAG Backend

Backend Python (FastAPI) cho khoá luận **"Xây dựng chatbot hỗ trợ học tập trên Moodle sử dụng RAG"**.

Đây là mắt xích còn thiếu trong kiến trúc:

```
Moodle Plugin (local_chatbot)
        │  POST /api/chat  (X-API-Key)
        ▼
Python FastAPI Backend  ◀── đây
        │  ├─ ChromaDB (vector store)
        │  └─ Gemini  (embedding + sinh câu trả lời)
        │
        │  GET /courses, /text-content, /files/download  (X-API-Key)
        ▼
Laravel Gateway API  →  Moodle Web Services
```

Backend đảm nhận 2 vai trò:
1. **Chat (online):** nhận câu hỏi từ plugin Moodle, truy xuất ngữ cảnh từ ChromaDB, gọi Gemini sinh câu trả lời kèm trích dẫn nguồn.
2. **Ingestion (offline):** kéo nội dung khoá học qua Laravel Gateway → cắt chunk → tạo embedding → lưu ChromaDB.

## 1. Yêu cầu

- Python 3.13 (đã test). Tránh 3.14 vì một số wheel chưa sẵn sàng.
- Laravel Gateway (`moodle-api`) đang chạy và cấu hình `EXTERNAL_API_KEYS`.
- API key Google Gemini: https://aistudio.google.com/apikey

## 2. Cài đặt

```powershell
cd D:\moodle-rag-backend
py -3.13 -m venv .venv
.\.venv\Scripts\python.exe -m pip install -r requirements.txt
copy .env.example .env   # rồi mở .env điền GEMINI_API_KEY, GATEWAY_API_KEY...
```

## 3. Cấu hình (`.env`)

| Biến | Ý nghĩa |
|------|---------|
| `BACKEND_API_KEY` | Key plugin Moodle gửi qua `X-API-Key`. Phải khớp cấu hình plugin. |
| `GATEWAY_BASE_URL` | URL nhóm route `/api/v1/moodle` của Laravel. |
| `GATEWAY_API_KEY` | Key gọi Laravel (khớp `EXTERNAL_API_KEYS`). |
| `GEMINI_API_KEY` | API key Google Gemini. |
| `MOODLE_PUBLIC_URL` | (tuỳ chọn) để tạo link trích dẫn tới bài học. |

## 4. Ingest dữ liệu Moodle → ChromaDB

```powershell
# Ingest tất cả course
.\.venv\Scripts\python.exe -m app.services.ingestion

# Chỉ ingest 1 course
.\.venv\Scripts\python.exe -m app.services.ingestion --course 5

# Xem số chunk đang có
.\.venv\Scripts\python.exe -m app.services.ingestion --stats
```

## 5. Chạy server

```powershell
.\.venv\Scripts\python.exe -m app.main
# hoặc:
.\.venv\Scripts\uvicorn.exe app.main:app --host 0.0.0.0 --port 8001
```

Kiểm tra:

```powershell
curl http://localhost:8001/health
```

## 6. API

### `GET /health`
Không cần key. Trả `{ "status": "ok" }`.

### `POST /api/chat`
Header: `X-API-Key: <BACKEND_API_KEY>`

Request:
```json
{ "user_id": 123, "message": "Bài giảng tuần 3 nói về gì?", "conversation_id": "c-abc" }
```

Response (đúng hợp đồng plugin `local_chatbot` mong đợi):
```json
{
  "answer": "...",
  "sources": [ { "title": "Môn X › Bài 3", "url": "http://.../mod/page/view.php?id=42" } ],
  "conversation_id": "c-abc"
}
```

## 7. Kết nối với plugin Moodle

Trong Moodle: **Site administration → Plugins → Local plugins → RAG Chatbot**
- **API URL:** `http://localhost:8001/api/chat`
- **API key:** đúng bằng `BACKEND_API_KEY` trong `.env`.

## 8. Test

```powershell
$env:PYTHONPATH="D:\moodle-rag-backend"
.\.venv\Scripts\python.exe tests\test_chunking.py
.\.venv\Scripts\python.exe tests\test_api.py
# hoặc: pip install pytest && pytest
```

## 9. Cấu trúc mã nguồn

```
app/
├── main.py                  # FastAPI app, /api/chat, /health
├── config.py                # Cấu hình (.env)
├── schemas.py               # Model request/response
├── security.py              # Xác thực X-API-Key
├── clients/
│   └── gateway_client.py    # Gọi Laravel Gateway
├── rag/
│   ├── chunking.py          # Cắt văn bản
│   ├── file_extract.py      # Trích text PDF/DOCX
│   ├── embeddings.py        # Gemini embeddings
│   ├── vector_store.py      # ChromaDB
│   ├── retriever.py         # Truy xuất ngữ cảnh
│   └── generator.py         # Gemini sinh câu trả lời
└── services/
    ├── ingestion.py         # Pipeline ingest (CLI)
    ├── conversation.py      # Lịch sử hội thoại in-memory
    └── rag_service.py       # Ghép retriever + generator
```
