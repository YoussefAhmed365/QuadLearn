<?php
require '../auth.php'; // التأكد من تسجيل الدخول
header('Content-Type: application/json');

try {
    // جلب المواد الدراسية المميزة للطالب المرتبط بالمعلمين
    $stmt = $conn->prepare("SELECT DISTINCT teachers.subject
                            FROM teachers
                            INNER JOIN assigned_teachers 
                            ON teachers.id = assigned_teachers.teacher_id
                            WHERE assigned_teachers.student_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // تحويل النتائج إلى مصفوفة
    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row['subject'];
    }

    // إرسال النتيجة كـ JSON
    echo json_encode($subjects);
} catch (Exception $e) {
    // معالجة الأخطاء
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}