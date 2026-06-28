# Kịch bản Thuyết trình + Demo — Phần Web Services API

> Đề tài: **Xây dựng hệ thống Chatbot thông minh hỗ trợ học tập dựa trên công nghệ RAG tích hợp Moodle qua Web Services API**
>
> Tài liệu này dùng để cầm theo khi bảo vệ: mỗi phần gồm **(A) lời nói lý thuyết** và **(B) thao tác demo** + **lời chốt**.

---

## 0. Chuẩn bị trước khi vào phòng (checklist)

- [ ] XAMPP: bật **MySQL** + **Apache**
- [ ] Laravel Gateway chạy: `cd D:\moodle-api` → `php artisan serve --port=8000`
- [ ] Python RAG backend chạy: `cd D:\moodle-rag-backend` → `.\restart.ps1`
- [ ] (nếu demo truy cập ngoài) `ngrok http 8000`
- [ ] Mở sẵn: **Postman**, **Moodle** (đã đăng nhập admin), file **`.env`** của `moodle-api` trong editor
- [ ] Đảm bảo `MOODLE_DATA_SOURCE=db` (trạng thái khởi đầu)
- [ ] Chuẩn bị sẵn 3 API key để copy nhanh:
  - student: `student-key-local-only-change-me`
  - teacher: `teacher-key-local-only-change-me`
  - admin: `admin-key-local-only-change-me`

⏱️ **Thời lượng đề xuất:** 6–8 phút cho cả phần API.

---

## 1. Mở đầu — định vị (30 giây)

**Lời nói:**
> "Phần tiếp theo em xin trình bày phần lõi của đề tài là **tích hợp Moodle qua Web Services API**. Hệ thống của em sử dụng Web Services API ở **ba mức**: thứ nhất là gọi vào các Web Services có sẵn của Moodle; thứ hai là em tự xây một tầng API Gateway trung gian có phân quyền; và thứ ba là em định nghĩa thêm một hàm Web Service mới ngay trong Moodle cho chatbot."

*(Chuyển slide sơ đồ kiến trúc nếu có)*

---

## 2. Lý thuyết nền (1 phút)

**Lời nói:**
> "Web Services API là cách để **hai phần mềm khác nhau giao tiếp qua HTTP** thay vì qua giao diện người dùng. Bên gọi (client) gửi một **request** đến một **endpoint**, bên cung cấp trả về dữ liệu dạng **JSON**, và có **token hoặc API key** để xác thực.
>
> Ưu điểm là **không phụ thuộc ngôn ngữ**: Moodle viết bằng PHP, chatbot của em viết bằng Python, nhưng vẫn nói chuyện được nhờ chuẩn chung HTTP + JSON. Đồng thời có **bảo mật và phân quyền** tập trung, thay vì để mỗi ứng dụng tự truy cập thẳng vào cơ sở dữ liệu."

**Bảng minh họa (có thể đưa lên slide):**

| Nhà hàng | Web Services API |
|---|---|
| Khách gọi món | Client gửi request `GET /courses` |
| Người phục vụ + thực đơn | API + danh sách endpoint |
| Món ăn bưng ra | Response JSON |
| Thẻ thành viên | API Key / token |

---

## 3. MÀN 1 — API Gateway tự xây + Phân quyền (RBAC) (1.5 phút)

### (A) Lý thuyết nói trước
> "Đây là tầng API trung gian em tự xây bằng Laravel. Nó cung cấp các endpoint REST chuẩn, có **xác thực bằng API Key** và **phân quyền theo vai trò**: sinh viên, giảng viên, admin. Quyền cao bao gồm quyền thấp."

### (B) Thao tác demo trong Postman
Cùng endpoint: `GET http://localhost:8000/api/v1/moodle/courses/3/grades` (xem điểm cả lớp), chỉ đổi header `X-API-Key`:

| Bước | X-API-Key | Kết quả mong đợi | Lời chốt |
|---|---|---|---|
| 1 | `student-key-local-only-change-me` | **403 Forbidden** | "Sinh viên KHÔNG được xem điểm cả lớp" |
| 2 | `teacher-key-local-only-change-me` | **200 OK** | "Giảng viên thì được phép" |
| 3 | `key-sai-bất-kỳ` | **401 Unauthorized** | "Sai key thì chặn ngay từ cổng" |

> **Chốt màn 1:** "Như vậy mọi truy cập đều đi qua một cổng API có kiểm soát và phân quyền."

---

## 4. MÀN 2 — Tích hợp Moodle qua Web Services API (màn QUAN TRỌNG NHẤT) (2 phút)

### (A) Lý thuyết nói trước
> "Moodle cung cấp sẵn các hàm Web Services như `core_course_get_courses`, `core_enrol_get_enrolled_users`, `gradereport_user_get_grade_items`. Hệ thống của em có thể lấy dữ liệu theo **hai cách** và chuyển đổi chỉ bằng một dòng cấu hình: gọi **Web Services API** của Moodle, hoặc truy vấn cơ sở dữ liệu trực tiếp để tối ưu tốc độ. Em xin demo cả hai."

### (B) Thao tác demo

> ⚠️ **Mọi request Postman PHẢI có header** `X-API-Key` (vd: `admin-key-local-only-change-me`).
> Nếu thiếu sẽ báo lỗi `"API Key required"`. Nên đặt header ở mức Collection để mọi request tự kèm.

**Bước 1 — Đang ở chế độ DB:** Postman gọi `GET http://localhost:8000/api/v1/moodle/courses`
(Headers: `X-API-Key: admin-key-local-only-change-me`)
→ Chỉ vào trường **`"source": "db"`** trong kết quả.
> "Hiện đang lấy từ cơ sở dữ liệu."

**Bước 2 — Chuyển sang Web Services:** mở file `.env`, sửa:
```env
MOODLE_DATA_SOURCE=ws
```

**Bước 3 — Áp dụng cấu hình:**
```powershell
cd D:\moodle-api
php artisan config:clear
```

**Bước 4 — Gọi lại đúng endpoint đó:**
→ Giờ trả về **`"source": "ws"`**, dữ liệu vẫn đúng.

> **Chốt màn 2 (câu quan trọng):**
> "Cùng một API, chỉ đổi cấu hình, hệ thống chuyển sang gọi **trực tiếp các hàm Web Services của Moodle**. Điều này chứng minh hệ thống làm chủ việc tích hợp qua Web Services API. Việc truy vấn cơ sở dữ liệu trực tiếp chỉ là **tối ưu hiệu năng** cho môi trường phát triển dùng server đơn luồng."

**Bước 5 (BẮT BUỘC) — trả lại trước khi demo chatbot:**
```env
MOODLE_DATA_SOURCE=db
```
```powershell
php artisan config:clear
```
> *(Lý do: tránh nghẽn luồng khi chatbot gọi lồng qua Moodle trên server dev.)*

---

## 5. MÀN 3 — Custom Web Service Function + Chatbot RAG (2 phút)

### (A) Lý thuyết nói trước
> "Ngoài việc dùng Web Services có sẵn, em còn **tự định nghĩa một hàm Web Service mới trong Moodle** tên là `local_chatbot_send_message`. Khung chat trong Moodle gọi hàm này để chuyển câu hỏi sang backend RAG xử lý."

*(Mở file `local/chatbot/db/services.php` cho hội đồng thấy định nghĩa hàm — nếu được hỏi.)*

### (B) Thao tác demo
**Bước 1:** Vào Moodle, mở khung chat (góc dưới), hỏi:
> *"Tôi có deadline nào sắp tới không?"*

**Bước 2:** Chatbot trả lời (lấy dữ liệu thật từ Moodle qua chuỗi tích hợp).

**Bước 3:** Giải thích luồng (slide vẽ sẵn):
```
[1] Moodle (hàm WS local_chatbot_send_message)
      ↓ HTTP
[2] Python RAG Backend (phân loại câu hỏi + RAG)
      ↓ HTTP (gọi API Gateway)
[3] Laravel Gateway API (/api/v1/moodle/...)
      ↓
[4] Moodle (Web Services API hoặc CSDL)
```

> **Chốt màn 3:** "Cả chuỗi này giao tiếp với nhau hoàn toàn qua Web Services API."

---

## 6. MÀN 4 (tùy chọn) — Truy cập từ Internet (30 giây)

**Thao tác:** trong Postman, đổi URL sang ngrok:
`GET https://<ten>.ngrok-free.dev/api/v1/moodle/courses` (kèm header API key)

> "API có thể cho hệ thống bên thứ ba truy cập an toàn từ Internet, không chỉ chạy nội bộ."

---

## 7. Câu tổng kết (15 giây)

> "Tóm lại, hệ thống của em đáp ứng đúng yêu cầu 'tích hợp Moodle qua Web Services API' ở ba lớp: **tiêu thụ** Web Services của Moodle, **tự xây** API Gateway có phân quyền, và **mở rộng** Moodle bằng một hàm Web Service mới."

---

## 8. Dự phòng câu hỏi hội đồng (Q&A)

**Hỏi: Web Service khác gì truy vấn database trực tiếp?**
> Web Service đi qua tầng trung gian chuẩn HTTP, có xác thực – phân quyền – kiểm soát, và không phụ thuộc cấu trúc bảng CSDL. Truy vấn DB thẳng nhanh hơn nhưng gắn chặt cấu trúc bảng, khó bảo trì và kém an toàn nếu mở ra ngoài.

**Hỏi: Sao có chỗ lại truy vấn DB trực tiếp, có đi ngược đề tài không?**
> Không. Kiến trúc được thiết kế quanh Web Services; DB trực tiếp chỉ là tối ưu hiệu năng cho server dev đơn luồng. Hệ thống có hai chế độ chuyển đổi bằng cấu hình và em vừa demo cả hai.

**Hỏi: Vì sao cần API trung gian mà không cho chatbot nối thẳng Moodle?**
> Để bảo mật và tái sử dụng: nhiều client (chatbot, app, bên thứ ba) dùng chung một API có phân quyền, thay vì mỗi client tự kết nối — vừa nguy hiểm vừa khó quản lý.

**Hỏi: Bảo mật API thế nào?**
> Có API Key, phân quyền theo vai trò (RBAC), giới hạn tần suất (rate limit), làm sạch dữ liệu đầu vào (chống injection) và ghi log request.

**Hỏi: RAG lấy dữ liệu khóa học bằng cách nào?**
> Qua Web Services `core_course_get_contents` và tải tài liệu qua `pluginfile` kèm token, sau đó nhúng vào vector store để truy hồi.

---

## 9. Lệnh nhanh hay dùng khi demo (copy sẵn)

```powershell
# Đổi nguồn dữ liệu rồi áp dụng
#   (sửa MOODLE_DATA_SOURCE trong D:\moodle-api\.env: db hoặc ws)
cd D:\moodle-api
php artisan config:clear

# Khởi động lại Python backend
cd D:\moodle-rag-backend
.\restart.ps1
```

> Mẹo: nếu lỡ để `ws` rồi chatbot bị treo, chỉ cần đổi lại `db` + `php artisan config:clear`.
