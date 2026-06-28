# FAQ - Câu hỏi thường gặp

## Về Moodle Token

### Q: Moodle có bắt buộc phải có token không?
**A:** CÓ, bắt buộc 100%. Moodle Web Services không thể hoạt động mà không có token. Đây là cơ chế bảo mật của Moodle.

### Q: Vậy làm sao chatbot không cần token của từng user?
**A:** Chatbot KHÔNG gọi trực tiếp Moodle. Chatbot gọi Laravel API Gateway, và Laravel sử dụng 1 service account token duy nhất để gọi Moodle thay mặt chatbot.

### Q: Service account là gì?
**A:** Là một user đặc biệt trong Moodle có quyền đọc data của tất cả users. Thay vì dùng token của 100 users, chỉ cần 1 token của service account.

### Q: Token có hết hạn không?
**A:** Có thể. Khi tạo token trong Moodle, bạn có thể set expiry date. Nếu để trống thì token không hết hạn. Trong production nên set expiry và rotate định kỳ.

### Q: Làm sao lấy Moodle token?
**A:** Xem chi tiết trong [MOODLE_SETUP_GUIDE.md](MOODLE_SETUP_GUIDE.md). Tóm tắt:
1. Enable Web Services trong Moodle
2. Tạo service account
3. Tạo External Service
4. Generate token
5. Copy vào .env

## Về API Gateway

### Q: Tại sao cần API Gateway? Gọi trực tiếp Moodle không được sao?
**A:** Được, nhưng:
- Phải quản lý token của TỪNG user
- Chatbot phải hiểu Moodle API
- Khó bảo mật (tokens exposed)
- Không scale

API Gateway giải quyết tất cả vấn đề này.

### Q: API Gateway có làm chậm hệ thống không?
**A:** Có thêm latency nhưng không đáng kể (thường <100ms). Có thể optimize bằng caching. Trade-off này đáng giá so với lợi ích về bảo mật và quản lý.

### Q: Có thể cache data không?
**A:** Có. Bạn có thể thêm Redis caching cho các endpoints ít thay đổi như courses, categories. Xem hướng dẫn thêm cache trong Laravel docs.

### Q: API Gateway có thể handle bao nhiêu requests?
**A:** Phụ thuộc vào server. Với server trung bình, có thể handle 100-500 req/s. Có thể scale bằng load balancing nhiều Laravel instances.

## Về Authentication

### Q: API Key và Moodle Token khác nhau như thế nào?
**A:**
- **API Key:** Bảo vệ Laravel API Gateway, do bạn tạo, format tùy ý
- **Moodle Token:** Xác thực với Moodle, do Moodle tạo, format cố định

### Q: Có thể dùng JWT thay vì API Key không?
**A:** Có. Bạn có thể thay ApiKeyAuth middleware bằng JWT authentication. Nhưng API Key đơn giản hơn cho use case này.

### Q: Làm sao tạo API Key mạnh?
**A:** Dùng random string generator:
```bash
# Linux/Mac
openssl rand -hex 32

# PHP
php -r "echo bin2hex(random_bytes(32));"
```

### Q: Có thể có nhiều API Keys không?
**A:** Có. Thêm vào .env cách nhau bằng dấu phẩy:
```env
EXTERNAL_API_KEYS=chatbot_key_abc,mobile_key_xyz,web_key_123
```

## Về Security

### Q: Moodle token có bị lộ không?
**A:** KHÔNG. Token được lưu trong .env (server-side) và không bao giờ được gửi đến clients. Chỉ Laravel API Gateway biết token.

### Q: Có cần HTTPS không?
**A:** Trong development (localhost) thì không bắt buộc. Trong production thì BẮT BUỘC phải dùng HTTPS để bảo vệ API keys.

### Q: Làm sao ngăn abuse API?
**A:** Có thể thêm:
- Rate limiting (Laravel throttle middleware)
- IP whitelist
- Request logging và monitoring
- API key expiry

### Q: Service account có quá nhiều quyền không?
**A:** Không nếu setup đúng. Service account chỉ nên có quyền READ (view courses, view grades, etc.), không có quyền WRITE (delete, modify).

## Về Chatbot Integration

### Q: Chatbot cần biết gì về Moodle?
**A:** KHÔNG CẦN. Chatbot chỉ cần:
- Base URL của API Gateway
- API Key
- Biết gọi REST API

### Q: Có thể dùng với bất kỳ chatbot framework nào không?
**A:** Có. Miễn là chatbot có thể gọi HTTP REST API. Ví dụ:
- Python: requests, httpx
- Node.js: axios, fetch
- Java: HttpClient
- C#: HttpClient

### Q: Có example code không?
**A:** Có. Xem thư mục [examples/](examples/) với Python integration example.

### Q: Làm sao tích hợp với RAG?
**A:** 
1. Fetch data từ API Gateway
2. Transform thành documents
3. Index vào vector database (Pinecone, ChromaDB)
4. Semantic search khi user query
5. Feed context vào LLM

Xem example trong [examples/chatbot_integration.py](examples/chatbot_integration.py)

## Về Deployment

### Q: Có thể deploy lên shared hosting không?
**A:** Khó. Laravel cần:
- PHP 8.2+
- Composer
- Command line access
- Có thể config web server

Nên dùng VPS (DigitalOcean, AWS, etc.)

### Q: Có thể deploy lên Heroku không?
**A:** Có. Heroku support PHP và Laravel. Cần thêm Procfile và config buildpack.

### Q: Có thể dùng Docker không?
**A:** Có. Tạo Dockerfile:
```dockerfile
FROM php:8.2-fpm
# Install dependencies
# Copy code
# Run Laravel
```

### Q: Database có cần thiết không?
**A:** Không bắt buộc cho API Gateway này vì chỉ proxy data từ Moodle. Nhưng nếu muốn thêm caching, logging, hoặc user management thì cần database.

## Về Performance

### Q: Response time bao lâu?
**A:** Phụ thuộc vào:
- Moodle server response time (thường 200-500ms)
- Laravel processing (thường 50-100ms)
- Network latency

Tổng: 300-700ms cho mỗi request

### Q: Làm sao tăng tốc?
**A:**
1. **Caching:** Cache courses, categories (ít thay đổi)
2. **Batch requests:** Gọi nhiều Moodle APIs parallel
3. **Database:** Cache vào database thay vì gọi Moodle mỗi lần
4. **CDN:** Nếu có static assets

### Q: Có thể handle concurrent requests không?
**A:** Có. Laravel handle concurrent requests tốt. Nếu cần scale hơn, dùng:
- Load balancer
- Multiple Laravel instances
- Queue system cho heavy tasks

## Về Testing

### Q: Làm sao test API?
**A:** Có 3 cách:
1. **Postman:** Import [postman_collection.json](postman_collection.json)
2. **curl:** Xem examples trong [API_DOCUMENTATION.md](API_DOCUMENTATION.md)
3. **Python:** Run [examples/chatbot_integration.py](examples/chatbot_integration.py)

### Q: Có unit tests không?
**A:** Chưa có trong version này. Bạn có thể thêm PHPUnit tests nếu cần.

### Q: Làm sao test với Moodle local?
**A:**
1. Install Moodle local (XAMPP/MAMP)
2. Enable Web Services
3. Tạo sample data
4. Point MOODLE_URL đến localhost

## Về Khóa luận

### Q: Đủ cho khóa luận không?
**A:** Có. Project này có:
- ✅ Vấn đề rõ ràng
- ✅ Giải pháp cụ thể
- ✅ Implementation hoàn chỉnh
- ✅ Documentation đầy đủ
- ✅ Demo được

### Q: Cần thêm gì nữa không?
**A:** Tùy yêu cầu khóa luận. Có thể thêm:
- Unit tests
- Performance benchmarks
- More advanced chatbot features
- Admin dashboard
- Analytics

### Q: Làm sao demo?
**A:** Xem [THESIS_CHECKLIST.md](THESIS_CHECKLIST.md) phần "Demo Day Checklist"

### Q: Có thể dùng cho production không?
**A:** Có, nhưng cần:
- Setup production Moodle
- HTTPS
- Proper server (VPS)
- Monitoring
- Backup

## Troubleshooting

### Q: Lỗi "Unauthorized: Invalid API Key"
**A:** Check:
1. API key trong .env đúng chưa
2. Header `X-API-Key` có đúng không
3. Restart Laravel server sau khi sửa .env

### Q: Lỗi "Moodle API: Invalid token"
**A:** Check:
1. MOODLE_TOKEN trong .env đúng chưa
2. Token còn hợp lệ trong Moodle không
3. Web Services đã enable chưa

### Q: Lỗi "Connection refused"
**A:** Check:
1. MOODLE_URL đúng chưa
2. Moodle server có đang chạy không
3. Firewall có block không

### Q: Lỗi "Function not found"
**A:** Function chưa được add vào External Service. Vào Moodle admin và add function.

### Q: Response rỗng
**A:** Check:
1. Moodle có data không (courses, students)
2. Service account có quyền đọc không
3. Check Laravel logs: `tail -f storage/logs/laravel.log`

## Liên hệ

### Q: Gặp vấn đề không giải quyết được?
**A:** 
1. Check documentation files
2. Check Laravel logs
3. Check Moodle logs
4. Google error message
5. Ask on Stack Overflow với tags: laravel, moodle, web-services

### Q: Muốn contribute?
**A:** Welcome! Fork repo và tạo pull request.

---

**Không tìm thấy câu trả lời?** Tạo issue trên GitHub hoặc check [MOODLE_SETUP_GUIDE.md](MOODLE_SETUP_GUIDE.md) và [ARCHITECTURE.md](ARCHITECTURE.md) để hiểu rõ hơn.
