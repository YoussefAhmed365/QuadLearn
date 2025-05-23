<?php
require '../auth.php'; // تأكد من أن التوثيق موجود

// جلب البيانات من النموذج
$postId = $_POST['post_id'] ?? null;
$title = $_POST['title'] ?? '';
$content = $_POST['content'] ?? '';

if ($postId && $title && $content) {
    // تحضير استعلام التحديث
    $query = "UPDATE community SET title = ?, content = ?, updated_at = NOW() WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);

    // ربط المعاملات
    $stmt->bind_param("ssii", $title, $content, $postId, $user_id);

    // تنفيذ الاستعلام
    if ($stmt->execute()) {
        // إرسال استجابة JSON بنجاح
        echo json_encode(['success' => true]);
    } else {
        // إرسال استجابة JSON بفشل التحديث
        echo json_encode(['success' => false, 'message' => 'حدث خطأ أثناء تحديث المنشور']);
    }

    $stmt->close();
} else {
    // إذا كانت البيانات غير صالحة
    echo json_encode(['success' => false, 'message' => 'البيانات غير مكتملة']);
}