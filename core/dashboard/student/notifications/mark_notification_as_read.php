<?php
require '../auth.php'; // التأكد من أن الطالب مسجل دخوله

// الحصول على محتويات جسم الطلب (البيانات)
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['notificationId'])) {
    $notificationId = intval($data['notificationId']);
    
    // تحديث حالة الإشعار إلى "مقروء" في قاعدة البيانات
    $query = "UPDATE student_notifications SET read_status = 1 WHERE notification_id = ? AND student_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ii', $notificationId, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'فشل في تحديث الإشعار.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'لم يتم تمرير معرف الإشعار.']);
}

$conn->close();