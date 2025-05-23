<?php
header('Content-Type: application/json');
require '../auth.php'; // التحقق من جلسة الطالب

$response = [];

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $teacherId = trim($_POST["teacherId"] ?? '');

        // تحقق من أن معرف المعلم هو رقم صحيح مكون من 8 أرقام
        if (ctype_digit($teacherId) && strlen($teacherId) == 8) {
            // التحقق إذا كان هناك طلب موجود مسبقًا
            $queryCheck = "SELECT 1 FROM requests WHERE student_id = ? AND teacher_id = ?";
            $stmtCheck = $conn->prepare($queryCheck);
            $stmtCheck->bind_param("ii", $user_id, $teacherId);
            $stmtCheck->execute();
            $stmtCheck->store_result(); // لتجنب الخطأ عند محاولة التحقق من النتيجة

            if ($stmtCheck->num_rows > 0) {
                $response = ['status' => 'error', 'message' => 'لقد أرسلت طلبًا بالفعل لهذا المعلم.'];
            } else {
                // إضافة الطلب الجديد
                $query = "INSERT INTO requests (student_id, teacher_id) VALUES (?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("ii", $user_id, $teacherId);

                if ($stmt->execute()) {
                    $response = ['status' => 'success', 'message' => 'تم تقديم طلب الانضمام بنجاح.'];
                } else {
                    $response = ['status' => 'error', 'message' => 'فشل في تقديم طلب الانضمام.'];
                }
            }
        } else {
            $response = ['status' => 'error', 'message' => 'يرجى التأكد من صحة معرف المعلم (يجب أن يكون 8 أرقام).'];
        }
    } else {
        $response = ['status' => 'error', 'message' => 'طلب غير صالح.'];
    }
} catch (Exception $e) {
    // سجل الخطأ في ملف سجلات (logging)
    error_log("Error in add-teachers.php: " . $e->getMessage());

    $response = ['status' => 'error', 'message' => 'حدث خطأ غير متوقع: ' . $e->getMessage()];
}

echo json_encode($response);