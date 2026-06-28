"""
Example: Tích hợp Laravel API Gateway với Chatbot RAG

Ví dụ này minh họa cách chatbot sử dụng API Gateway để lấy dữ liệu Moodle
mà không cần quản lý Moodle tokens của từng user.
"""

import requests
from typing import Dict, List, Optional

class MoodleAPIClient:
    """
    Client để gọi Laravel API Gateway
    Chatbot chỉ cần API key, không cần Moodle token
    """
    
    def __init__(self, base_url: str, api_key: str):
        """
        Args:
            base_url: URL của Laravel API Gateway (vd: http://localhost:8000/api/v1/moodle)
            api_key: API key được cấp (vd: chatbot_key_123)
        """
        self.base_url = base_url.rstrip('/')
        self.headers = {
            'X-API-Key': api_key,
            'Accept': 'application/json'
        }
    
    def _request(self, method: str, endpoint: str, params: Optional[Dict] = None) -> Dict:
        """Helper method để gọi API"""
        url = f"{self.base_url}{endpoint}"
        response = requests.request(method, url, headers=self.headers, params=params)
        response.raise_for_status()
        return response.json()
    
    def get_courses(self, search: Optional[str] = None) -> List[Dict]:
        """Lấy danh sách courses"""
        params = {'search': search} if search else None
        result = self._request('GET', '/courses', params)
        return result.get('data', [])
    
    def get_student_results(self, username: str) -> Dict:
        """Lấy kết quả học tập của sinh viên"""
        result = self._request('GET', f'/students/{username}/results')
        return result.get('data', {})
    
    def get_course_students(self, course_id: int) -> List[Dict]:
        """Lấy danh sách sinh viên trong course"""
        result = self._request('GET', f'/courses/{course_id}/students')
        return result.get('data', [])
    
    def get_course_grades(self, course_id: int) -> List[Dict]:
        """Lấy điểm của tất cả sinh viên trong course"""
        result = self._request('GET', f'/courses/{course_id}/grades')
        return result.get('data', [])
    
    def get_course_full_results(self, course_id: int) -> Dict:
        """Lấy tổng hợp đầy đủ dữ liệu course"""
        result = self._request('GET', f'/courses/{course_id}/full-results')
        return result.get('data', {})


class ChatbotRAG:
    """
    Ví dụ Chatbot sử dụng RAG với Moodle data
    """
    
    def __init__(self, api_client: MoodleAPIClient):
        self.api = api_client
    
    def handle_student_query(self, username: str, question: str) -> str:
        """
        Xử lý câu hỏi về sinh viên
        
        Example:
            question = "Điểm của tôi là gì?"
            username = "student01"
        """
        try:
            # 1. Lấy dữ liệu từ Moodle qua API Gateway
            student_data = self.api.get_student_results(username)
            
            # 2. Build context cho LLM
            context = self._build_student_context(student_data)
            
            # 3. Generate response (giả lập - thực tế sẽ gọi LLM)
            response = self._generate_response(context, question)
            
            return response
            
        except requests.exceptions.HTTPError as e:
            if e.response.status_code == 404:
                return f"Không tìm thấy sinh viên {username}"
            elif e.response.status_code == 401:
                return "Lỗi xác thực API. Vui lòng kiểm tra API key."
            else:
                return f"Lỗi: {str(e)}"
    
    def handle_course_query(self, course_name: str, question: str) -> str:
        """
        Xử lý câu hỏi về môn học
        
        Example:
            question = "Có bao nhiêu sinh viên trong môn Lập trình Web?"
            course_name = "IT4409"
        """
        try:
            # 1. Tìm course
            courses = self.api.get_courses(search=course_name)
            if not courses:
                return f"Không tìm thấy môn học {course_name}"
            
            course = courses[0]
            course_id = course['id']
            
            # 2. Lấy dữ liệu course
            students = self.api.get_course_students(course_id)
            
            # 3. Build context
            context = f"""
            Môn học: {course['fullname']} ({course['shortname']})
            Số sinh viên: {len(students)}
            Danh sách sinh viên: {', '.join([s['fullname'] for s in students[:5]])}
            {'...' if len(students) > 5 else ''}
            """
            
            # 4. Generate response
            response = self._generate_response(context, question)
            
            return response
            
        except Exception as e:
            return f"Lỗi: {str(e)}"
    
    def _build_student_context(self, student_data: Dict) -> str:
        """Build context string từ student data cho LLM"""
        student = student_data.get('student', {})
        courses = student_data.get('courses', [])
        
        context = f"""
        Thông tin sinh viên:
        - Họ tên: {student.get('fullname')}
        - MSSV: {student.get('idnumber')}
        - Email: {student.get('email')}
        - Tổng số môn học: {student_data.get('total_courses', 0)}
        
        Kết quả học tập:
        """
        
        for course in courses:
            context += f"\n\nMôn: {course['fullname']} ({course['shortname']})"
            
            # Grades
            if course.get('grades'):
                context += "\nĐiểm số:"
                for grade in course['grades']:
                    if grade.get('grade') is not None:
                        context += f"\n  - {grade['activity']}: {grade['grade']}/{grade['max_grade']}"
            
            # Competencies (CLO)
            if course.get('competencies'):
                context += "\nChuẩn đầu ra (CLO):"
                for comp in course['competencies']:
                    status = comp.get('status', 'Chưa đánh giá')
                    context += f"\n  - {comp['shortname']}: {status}"
        
        return context
    
    def _generate_response(self, context: str, question: str) -> str:
        """
        Generate response từ LLM
        (Đây là mock - thực tế sẽ gọi OpenAI/Claude/etc.)
        """
        # TODO: Integrate với LLM thực tế
        # prompt = f"Context:\n{context}\n\nQuestion: {question}\n\nAnswer:"
        # response = llm.generate(prompt)
        
        # Mock response
        return f"[Mock Response] Dựa trên context:\n{context[:200]}...\n\nTrả lời: {question}"


# ============================================================================
# USAGE EXAMPLES
# ============================================================================

def example_1_basic_usage():
    """Example 1: Sử dụng cơ bản"""
    print("=" * 60)
    print("Example 1: Basic Usage")
    print("=" * 60)
    
    # Initialize client
    api = MoodleAPIClient(
        base_url='http://localhost:8000/api/v1/moodle',
        api_key='chatbot_key_123'
    )
    
    # Lấy danh sách courses
    print("\n1. Lấy danh sách courses:")
    courses = api.get_courses()
    for course in courses[:3]:
        print(f"  - {course['shortname']}: {course['fullname']}")
    
    # Lấy thông tin sinh viên
    print("\n2. Lấy thông tin sinh viên 'student01':")
    student_data = api.get_student_results('student01')
    student = student_data.get('student', {})
    print(f"  - Họ tên: {student.get('fullname')}")
    print(f"  - Email: {student.get('email')}")
    print(f"  - Số môn học: {student_data.get('total_courses', 0)}")


def example_2_chatbot_integration():
    """Example 2: Tích hợp với Chatbot"""
    print("\n" + "=" * 60)
    print("Example 2: Chatbot Integration")
    print("=" * 60)
    
    # Initialize
    api = MoodleAPIClient(
        base_url='http://localhost:8000/api/v1/moodle',
        api_key='chatbot_key_123'
    )
    chatbot = ChatbotRAG(api)
    
    # Simulate user queries
    queries = [
        ("student01", "Điểm của tôi là gì?"),
        ("student01", "Tôi đã đạt những CLO nào?"),
        ("IT4409", "Có bao nhiêu sinh viên trong môn này?"),
    ]
    
    for username_or_course, question in queries:
        print(f"\nUser: {question}")
        if username_or_course.startswith('student'):
            response = chatbot.handle_student_query(username_or_course, question)
        else:
            response = chatbot.handle_course_query(username_or_course, question)
        print(f"Bot: {response[:200]}...")


def example_3_rag_pipeline():
    """Example 3: RAG Pipeline với vector database"""
    print("\n" + "=" * 60)
    print("Example 3: RAG Pipeline")
    print("=" * 60)
    
    api = MoodleAPIClient(
        base_url='http://localhost:8000/api/v1/moodle',
        api_key='chatbot_key_123'
    )
    
    # 1. Lấy dữ liệu từ Moodle
    print("\n1. Fetching data from Moodle...")
    courses = api.get_courses()
    print(f"   Found {len(courses)} courses")
    
    # 2. Transform data cho vector database
    print("\n2. Transforming data for vector DB...")
    documents = []
    for course in courses[:5]:  # Limit for demo
        doc = {
            'id': f"course_{course['id']}",
            'text': f"{course['fullname']} ({course['shortname']})",
            'metadata': {
                'type': 'course',
                'course_id': course['id'],
                'category': course.get('category', {}).get('name', '')
            }
        }
        documents.append(doc)
    print(f"   Created {len(documents)} documents")
    
    # 3. Index vào vector database (mock)
    print("\n3. Indexing to vector database...")
    # TODO: Integrate với Pinecone/Weaviate/ChromaDB
    # vector_db.index(documents)
    print("   [Mock] Indexed successfully")
    
    # 4. Query
    print("\n4. Querying...")
    query = "Môn học về lập trình"
    # TODO: Semantic search
    # results = vector_db.search(query, top_k=3)
    print(f"   Query: {query}")
    print("   [Mock] Found relevant courses")


def example_4_error_handling():
    """Example 4: Error handling"""
    print("\n" + "=" * 60)
    print("Example 4: Error Handling")
    print("=" * 60)
    
    api = MoodleAPIClient(
        base_url='http://localhost:8000/api/v1/moodle',
        api_key='invalid_key'  # Invalid key
    )
    
    try:
        print("\n1. Testing with invalid API key...")
        courses = api.get_courses()
    except requests.exceptions.HTTPError as e:
        if e.response.status_code == 401:
            print("   ✓ Caught 401 Unauthorized (expected)")
        else:
            print(f"   ✗ Unexpected error: {e}")
    
    # Test with valid key but invalid student
    api = MoodleAPIClient(
        base_url='http://localhost:8000/api/v1/moodle',
        api_key='chatbot_key_123'
    )
    
    try:
        print("\n2. Testing with non-existent student...")
        student_data = api.get_student_results('nonexistent_student')
    except requests.exceptions.HTTPError as e:
        if e.response.status_code == 404:
            print("   ✓ Caught 404 Not Found (expected)")
        else:
            print(f"   ✗ Unexpected error: {e}")


if __name__ == '__main__':
    print("""
    ╔══════════════════════════════════════════════════════════════╗
    ║  Moodle API Gateway - Chatbot Integration Examples          ║
    ║  Khóa luận: Chatbot RAG tích hợp Moodle                     ║
    ╚══════════════════════════════════════════════════════════════╝
    """)
    
    print("\nNOTE: Đảm bảo Laravel API Gateway đang chạy tại localhost:8000")
    print("      và đã cấu hình API key 'chatbot_key_123' trong .env\n")
    
    try:
        # Run examples
        example_1_basic_usage()
        example_2_chatbot_integration()
        example_3_rag_pipeline()
        example_4_error_handling()
        
        print("\n" + "=" * 60)
        print("All examples completed!")
        print("=" * 60)
        
    except requests.exceptions.ConnectionError:
        print("\n❌ ERROR: Cannot connect to API Gateway")
        print("   Please make sure Laravel server is running:")
        print("   $ php artisan serve")
    except Exception as e:
        print(f"\n❌ ERROR: {str(e)}")
