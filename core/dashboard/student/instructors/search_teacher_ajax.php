<?php
header('Content-Type: application/json');
require '../auth.php';

$response = [];

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $code = trim($_POST["identitySearch"] ?? '');

        if (strlen($code) === 8 && ctype_digit($code)) {
            // التحقق من وجود المعلم أو المساعد المعين مسبقاً
            $queries = [
                "SELECT teacher_id, student_id FROM assigned_teachers WHERE student_id = ? AND teacher_id = ?",
                "SELECT status, message FROM requests WHERE student_id = ? AND teacher_id = ?",
                "SELECT t.title, u.first_name, u.last_name FROM teachers t JOIN users u ON t.id = u.id WHERE u.id = ?",
                "SELECT u.id, u.first_name, u.last_name, t.title, t.subject FROM users u JOIN teachers t ON u.id = t.id WHERE u.id = ?"
            ];

            // التحقق إذا كان المعلم معين مسبقاً
            $stmt = $conn->prepare($queries[0]);
            $stmt->bind_param("ii", $user_id, $code);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                $response = ['status' => 'assigned', 'requestMessage' => 'المعلم مضاف بالفعل.'];
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                exit();
            }

            // التحقق من وجود طلب سابق
            $stmt = $conn->prepare($queries[1]);
            $stmt->bind_param("ii", $user_id, $code);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                $request = $result->fetch_assoc();
                $response['status'] = 'exist';
                $response['requestStatus'] = [
                    'New' => 'جديد',
                    'Accepted' => 'مقبول',
                    'Pending' => 'قيد الانتظار',
                    'Rejected' => 'مرفوض'
                ][$request['status']] ?? $request['status'];
                $response['requestMessage'] = $request['message'];

                // استعلام لجلب بيانات المعلم
                $stmt = $conn->prepare($queries[3]);
                $stmt->bind_param("i", $code);
                $stmt->execute();
                $teacherResult = $stmt->get_result();
                if ($teacherResult->num_rows === 1) {
                    $teacher = $teacherResult->fetch_assoc();
                    $response['firstName'] = $teacher['first_name'];
                    $response['lastName'] = $teacher['last_name'];
                    $response['subject'] = $teacher['subject'];
                }
                echo json_encode($response, JSON_UNESCAPED_UNICODE);
                exit();
            }

            // استعلام للتحقق إذا كان معلم مساعد
            $stmt = $conn->prepare($queries[2]);
            $stmt->bind_param("i", $code);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                $teacherData = $result->fetch_assoc();
                if ($teacherData['title'] === 'assistant') {
                    $response["status"] = "assistant";

                    // جلب تفاصيل المعلم المرتبط بالمساعد
                    $query = "SELECT s.teacher_id, u.first_name, u.last_name FROM assigned_assistants s JOIN users u ON s.teacher_id = u.id WHERE s.assistant_id = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("i", $code);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if ($result->num_rows === 1) {
                        $assignedTeacher = $result->fetch_assoc();
                        $response['requestMessage'] = 'هذا المساعد مرتبط بالمعلم "' . $assignedTeacher['first_name'] . ' ' . $assignedTeacher['last_name'] . '" وله المعرف: ' . $assignedTeacher['teacher_id'];
                    }
                    echo json_encode($response, JSON_UNESCAPED_UNICODE);
                    exit();
                }
            }

            // إذا لم يكن مساعداً، جلب تفاصيل المعلم
            $stmt = $conn->prepare($queries[3]);
            $stmt->bind_param("i", $code);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows === 1) {
                $teacher = $result->fetch_assoc();

                $subjects = [
                    'Arabic' => 'لغة عربية',
                    'English' => 'لغة إنجليزية',
                    'Spanish' => 'اللغة الإسبانية',
                    'French' => 'لغة فرنسية',
                    'German' => 'لغة ألمانية',
                    'Italian' => 'لغة إيطالية',
                    'Physics' => 'فيزياء',
                    'Chemistry' => 'كيمياء',
                    'Biology' => 'أحياء',
                    'Geology' => 'جيولوجيا',
                    'Mathematics' => 'رياضيات',
                    'Philosophy' => 'فلسفة وعلم نفس',
                    'History' => 'تاريخ',
                    'Geography' => 'جغرافيا'
                ];

                $teacher['subject'] = $subjects[$teacher['subject']] ?? $teacher['subject'];

                $response = [
                    'status' => 'success',
                    'teacher_id' => $teacher['id'],
                    'firstName' => $teacher['first_name'],
                    'lastName' => $teacher['last_name'],
                    'title' => $teacher['title'],
                    'subject' => $teacher['subject']
                ];
            } else {
                $response = ['status' => 'error', 'message' => 'لم يتم العثور على معلم بالمعرف المدخل.'];
            }
        } else {
            $response = ['status' => 'invalidIdLength', 'requestMessage' => 'يجب أن يكون المعرف من 8 أرقام'];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'طلب غير صالح.'];
    }
} catch (Exception $e) {
    $response = ['status' => 'error', 'message' => 'حدث خطأ غير متوقع: ' . $e->getMessage()];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
