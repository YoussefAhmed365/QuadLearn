<?php
require '../auth.php'; // تأكد من أن التوثيق موجود
header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['post_id'];

    if (empty($postId)) {
        // إذا كانت البيانات غير مكتملة
        echo json_encode(['state' => "warning", 'message' => 'البيانات غير مكتملة']);
        exit;
    }

    // تحضير استعلام التحديث
    $query = "DELETE FROM community WHERE id = ?";
    $stmt = $conn->prepare($query);

    // ربط المعاملات
    $stmt->bind_param("i", $postId);

    // تنفيذ الاستعلام
    if ($stmt->execute()) {
        // إرسال استجابة JSON بنجاح
        echo json_encode(['state' => 'success', 'message' => 'تم حذف المنشور بنجاح']);
    } else {
        // إرسال استجابة JSON بفشل التحديث
        echo json_encode(['state' => "error", 'message' => 'حدث خطأ أثناء تحديث المنشور']);
    }

    $stmt->close();
} else {
    // إذا كانت البيانات غير صالحة
    echo json_encode(['state' => "warning", 'message' => 'البيانات غير صالحة']);
    exit;
}