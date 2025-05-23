<?php
require '../auth.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$response = [];

$student_id = $data['student_id'];
$status = $data['status'];
$message = $data['message'] ?? null;

$conn->begin_transaction(); // بدء معاملة (transaction)

try {
    if ($status === 'Accepted') {
        // إضافة سجل جديد في assigned_teachers و assigned_students فقط إذا لم يكن موجودًا مسبقاً
        $sqlCheck = "SELECT COUNT(*) as count FROM assigned_teachers WHERE student_id = ? AND teacher_id = ?";
        $stmtCheck = $conn->prepare($sqlCheck);
        $stmtCheck->bind_param("ii", $student_id, $user_id);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] == 0) {
            // السجل غير موجود، لذا نضيفه
            $sql1 = "INSERT INTO assigned_teachers (student_id, teacher_id) VALUES (?, ?)";
            $sql2 = "INSERT INTO assigned_students (student_id, teacher_id) VALUES (?, ?)";

            $stmt1 = $conn->prepare($sql1);
            $stmt2 = $conn->prepare($sql2);

            $stmt1->bind_param("ii", $student_id, $user_id);
            $stmt2->bind_param("ii", $student_id, $user_id);

            $stmt1->execute();
            $stmt2->execute();
        }
    } else {
        // إذا كانت حالة الطلب ليست Accepted، نزيل السجل من الجداول المرتبطة
        $sqlDelete = "DELETE FROM assigned_teachers WHERE student_id = ? AND teacher_id = ?";
        $stmtDelete1 = $conn->prepare($sqlDelete);
        $stmtDelete1->bind_param("ii", $student_id, $user_id);
        $stmtDelete1->execute();

        $sqlDelete = "DELETE FROM assigned_students WHERE student_id = ? AND teacher_id = ?";
        $stmtDelete2 = $conn->prepare($sqlDelete);
        $stmtDelete2->bind_param("ii", $student_id, $user_id);
        $stmtDelete2->execute();
    }

    // تحديث حالة الطلب وعنوان الرسالة في جدول requests
    $sql = "UPDATE requests SET status = ?, message = ? WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $status, $message, $student_id);
    $stmt->execute();

    // التحقق من نجاح العملية
    if ($stmt->affected_rows > 0 || $stmt1->affected_rows > 0 || $stmt2->affected_rows > 0) {
        $conn->commit(); // تنفيذ المعاملة إذا كانت ناجحة
        $response = ['status' => 'success', 'response' => 'تم الحفظ بنجاح!'];
    } else {
        throw new Exception('No rows affected'); // رمي استثناء إذا لم تتأثر أي صفوف
    }

} catch (Exception $e) {
    // إذا حدث خطأ، نقوم بإرجاع العمليات
    $conn->rollback();
    $response = ['status' => 'error', 'response' => 'حدث خطأ أثناء الحفظ.'];
}

echo json_encode($response);

$conn->close();