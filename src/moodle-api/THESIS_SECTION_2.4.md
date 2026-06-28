2.4. Xây dựng API cho hệ thống Moodle

2.4.1. Mục tiêu

Mục tiêu chính của phần này là xây dựng hệ thống API để truy xuất dữ liệu từ Moodle LMS một cách hiệu quả và có cấu trúc. Hệ thống API cần đáp ứng các yêu cầu sau:

Truy xuất dữ liệu Moodle: Xây dựng các endpoint RESTful để truy cập thông tin về courses (môn học), students (sinh viên), grades (điểm số), và competencies (CLO/PLO) từ hệ thống Moodle. Dữ liệu cần được tổ chức theo cấu trúc rõ ràng, dễ sử dụng.

Chuẩn hóa định dạng dữ liệu: Chuyển đổi dữ liệu từ Moodle Web Services sang định dạng JSON chuẩn, thống nhất cho tất cả các endpoints. Điều này giúp việc tích hợp và sử dụng API trở nên đơn giản hơn.

Tối ưu hiệu năng: Giảm thiểu số lượng requests cần thiết bằng cách tổng hợp dữ liệu từ nhiều nguồn trong Moodle thành một response duy nhất. Điều này giúp cải thiện thời gian phản hồi và giảm tải cho hệ thống.

Đảm bảo bảo mật: Bảo vệ dữ liệu và hệ thống thông qua các cơ chế xác thực, kiểm soát truy cập, và xử lý lỗi an toàn.

2.4.2. Danh sách API

Hệ thống cung cấp 8 RESTful endpoints chính với Base URL là /api/v1/moodle. Tất cả requests đều yêu cầu header X-API-Key để xác thực.

Bảng 2.1: Danh sách API endpoints

STT | Method | Endpoint | Mô tả
----|--------|----------|------
1 | GET | /courses | Lấy danh sách tất cả courses với category hierarchy
2 | GET | /courses/{courseId}/students | Lấy danh sách sinh viên trong course
3 | GET | /courses/{courseId}/grades | Lấy điểm của tất cả sinh viên trong course
4 | GET | /courses/{courseId}/competencies | Lấy danh sách CLO/PLO gắn vào course
5 | GET | /courses/{courseId}/competency-results | Lấy kết quả CLO của sinh viên
6 | GET | /courses/traceback | Truy xuất ngược từ mã môn học đến category hierarchy
7 | GET | /courses/{courseId}/full-results | Tổng hợp đầy đủ: course + students + grades + CLO
8 | GET | /students/{username}/results | Lấy kết quả học tập của một sinh viên cụ thể

2.4.3. Thiết kế và xử lý dữ liệu

a) Query nhiều bảng

Để cung cấp thông tin đầy đủ cho người dùng, hệ thống cần thực hiện truy vấn từ nhiều nguồn dữ liệu khác nhau trong Moodle và tổng hợp lại. Quá trình này được thực hiện thông qua việc gọi nhiều Moodle Web Services APIs.

Ví dụ với endpoint /students/{username}/results, hệ thống thực hiện chuỗi các truy vấn sau:

Bước 1: Truy vấn thông tin sinh viên bằng core_user_get_users_by_field với tham số là username hoặc idnumber.

Bước 2: Truy vấn danh sách tất cả courses trong hệ thống bằng core_course_get_courses.

Bước 3: Với mỗi course, truy vấn danh sách enrolled users bằng core_enrol_get_enrolled_users để xác định sinh viên có tham gia course đó không.

Bước 4: Với mỗi course mà sinh viên đã tham gia, truy vấn điểm số bằng gradereport_user_get_grade_items.

Bước 5: Truy vấn danh sách CLO/PLO của course bằng core_competency_list_course_competencies.

Bước 6: Với mỗi CLO/PLO, truy vấn kết quả đạt được của sinh viên bằng core_competency_get_user_competency_in_course.

Sau khi thu thập đủ dữ liệu từ các nguồn khác nhau, hệ thống thực hiện tổng hợp và trả về một JSON response hoàn chỉnh.

b) Mapping dữ liệu

Dữ liệu từ Moodle cần được chuyển đổi và cấu trúc lại để phù hợp với nhu cầu sử dụng. Hệ thống thực hiện các loại mapping sau:

Mapping Category Hierarchy: Moodle lưu trữ categories dạng phẳng với mỗi category có thuộc tính parent ID. Hệ thống xây dựng lại cấu trúc phân cấp bằng cách truy ngược từ category hiện tại lên các parent categories. Thuật toán bắt đầu từ category của course, lấy thông tin category cha, tiếp tục truy ngược cho đến khi đến category gốc. Kết quả là một mảng các categories theo thứ tự từ gốc đến lá, tạo thành đường dẫn phân cấp đầy đủ.

Mapping Student Role: Khi truy vấn enrolled users, Moodle trả về tất cả người dùng bao gồm sinh viên, giảng viên, và quản trị viên. Hệ thống thực hiện lọc để chỉ giữ lại những users có roleid bằng 5, tương ứng với vai trò sinh viên trong Moodle.

Mapping Grade Items: Dữ liệu điểm từ Moodle bao gồm cả tổng điểm course và điểm của từng hoạt động cụ thể. Hệ thống loại bỏ các grade items có itemtype là 'course' và chỉ giữ lại điểm của các activities như assignments, quizzes, forums. Mỗi grade item được chuyển đổi thành object chứa tên activity, loại module, điểm thô, điểm tối đa, và phần trăm đạt được.

Mapping Competency Status: Moodle lưu trữ mức độ đạt được của competency dưới dạng số (proficiency: 0 hoặc 1). Hệ thống chuyển đổi giá trị này sang text mô tả: giá trị 1 được chuyển thành "Đạt", giá trị 0 được chuyển thành "Chưa đạt".

2.4.4. Triển khai bằng Laravel

a) Route

Hệ thống sử dụng Laravel routing để định nghĩa các API endpoints trong file routes/api.php. Tất cả routes được nhóm dưới prefix 'v1/moodle' để quản lý phiên bản API.

Các routes được bảo vệ bởi một chuỗi middleware bao gồm:

Middleware log.request: Ghi log tất cả requests vào hệ thống, bao gồm thông tin về thời gian, IP address, endpoint được gọi, và kết quả trả về. Dữ liệu log này phục vụ cho việc audit và debug.

Middleware sanitize: Làm sạch dữ liệu đầu vào, loại bỏ các ký tự đặc biệt và HTML tags để ngăn chặn các tấn công XSS và SQL injection.

Middleware api.key: Xác thực API key từ header X-API-Key của request. Chỉ những requests có API key hợp lệ mới được phép truy cập vào hệ thống.

Middleware rate.limit: Giới hạn số lượng requests trong một khoảng thời gian (100 requests/phút) để bảo vệ hệ thống khỏi các tấn công DDoS.

Mỗi route được map tới một method tương ứng trong MoodleController. Ví dụ: route GET /courses được xử lý bởi method courses(), route GET /students/{username}/results được xử lý bởi method studentResults().

b) Controller

MoodleController được đặt trong namespace App\Http\Controllers\Api\V1 và kế thừa từ base Controller class. Controller sử dụng dependency injection để nhận MoodleClient service thông qua constructor.

Cấu trúc của Controller bao gồm một protected property $moodle kiểu MoodleClient, một constructor để inject dependency, 8 public methods tương ứng với 8 API endpoints, và một private method buildCategoryPath() để xây dựng category hierarchy.

Ví dụ về method studentResults(): Method này nhận parameter $username từ URL và trả về JsonResponse. Quá trình xử lý được chia thành các bước:

Đầu tiên, tìm kiếm thông tin sinh viên. Hệ thống gọi getUserByField() với field 'username'. Nếu không tìm thấy, thử lại với field 'idnumber'. Nếu vẫn không tìm thấy, trả về response 404 với thông báo lỗi.

Tiếp theo, lấy danh sách tất cả courses và categories từ Moodle. Categories được chuyển thành Collection và index theo id để tối ưu việc tra cứu.

Sau đó, xác định các courses mà sinh viên đã tham gia. Hệ thống duyệt qua từng course, gọi getEnrolledUsers() và kiểm tra xem student id có trong danh sách không.

Với mỗi course mà sinh viên tham gia, hệ thống thu thập thông tin chi tiết. Đầu tiên là xây dựng category_path bằng method buildCategoryPath(). Tiếp theo là lấy điểm số bằng getGrades() và lọc chỉ lấy điểm của các activities. Sau đó lấy danh sách competencies bằng getCourseCompetencies(). Với mỗi competency, gọi getUserCompetencyInCourse() để lấy kết quả đạt được.

Cuối cùng, tất cả thông tin được tổng hợp và trả về dưới dạng JSON response với cấu trúc: success flag, student info (id, username, fullname, email, idnumber), total_courses, và mảng courses chứa chi tiết từng course với grades và competencies.

Toàn bộ logic được đặt trong try-catch block để xử lý exceptions. Nếu có lỗi xảy ra, hệ thống trả về response 500 với error message.

Service Layer: Hệ thống sử dụng MoodleClient service class làm wrapper cho Moodle Web Services API. Class này có hai protected properties: $baseUrl chứa URL của Moodle site và $token chứa web service token. Constructor khởi tạo hai properties này từ config file.

Method call() là method chính để gọi bất kỳ Moodle Web Service function nào. Method này nhận tham số function name và parameters, construct URL endpoint, sử dụng Laravel HTTP client để gửi POST request với form data, parse JSON response, kiểm tra lỗi và throw Exception nếu có, cuối cùng trả về data nếu thành công.

Ngoài method call(), MoodleClient cung cấp các specific methods cho từng Moodle function: getCourses() để lấy tất cả courses, getEnrolledUsers() để lấy users trong course, getGrades() để lấy điểm số, getUserByField() để tìm user theo field, getCourseCompetencies() để lấy CLO/PLO, getUserCompetencyInCourse() để lấy kết quả CLO, và các methods khác như getCategories(), getCoursesByField(), getAssignments().

c) JSON response

Hệ thống sử dụng định dạng JSON chuẩn cho tất cả responses. Mỗi response đều có trường success để chỉ ra kết quả thành công hay thất bại.

Success Response có cấu trúc gồm success: true và data object chứa thông tin cụ thể. Ví dụ với endpoint /students/{username}/results, data object bao gồm: student object chứa id, username, fullname, email, idnumber; total_courses là số nguyên; courses array với mỗi phần tử chứa course_id, fullname, shortname, category_path, grades array, và competencies array.

Error Response có cấu trúc gồm success: false và message chứa mô tả lỗi. Hệ thống sử dụng HTTP status codes đúng chuẩn REST: 200 OK cho request thành công, 400 Bad Request khi thiếu parameters bắt buộc, 401 Unauthorized khi API key không hợp lệ, 404 Not Found khi resource không tồn tại, và 500 Internal Server Error khi có lỗi server hoặc lỗi từ Moodle API.

2.4.5. Kết quả

API hoạt động ổn định

Sau quá trình triển khai và kiểm thử, hệ thống API đã hoạt động ổn định với các chỉ số đạt yêu cầu.

Về hiệu năng: Response time trung bình cho các endpoint đơn giản dao động từ 200-500ms. Endpoint phức tạp /students/{username}/results có thời gian phản hồi 2-5 giây tùy số lượng courses. Throughput giới hạn 100 requests/phút. Tỷ lệ lỗi dưới 1%.

Về kiểm thử: Hệ thống đã được test với 11 test cases bao gồm các trường hợp thành công (200 OK), không tìm thấy dữ liệu (404), và thiếu xác thực (401). Tất cả test cases đều pass.

Về bảo mật: Hệ thống triển khai đầy đủ các cơ chế bảo vệ gồm Request Logging, Input Sanitization, API Key Authentication, Rate Limiting, và Error Handling.

Về tài liệu: Hệ thống có documentation đầy đủ bao gồm API Documentation, Postman Collection, Integration Guides, Setup Guides, và Architecture Diagrams.

Kết luận: Hệ thống API hoạt động ổn định, đáp ứng yêu cầu về chức năng, hiệu năng và bảo mật, sẵn sàng triển khai..
