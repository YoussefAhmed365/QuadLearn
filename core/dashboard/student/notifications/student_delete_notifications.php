<?php
require '../auth.php';  // التأكد من أن المستخدم مسجل دخوله

// الحصول على البيانات المرسلة من الـ fetch
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['notificationIds']) && is_array($data['notificationIds'])) {
    $notificationIds = $data['notificationIds'];

    if (!empty($notificationIds)) {
        // تحويل المصفوفة إلى سلسلة مفصولة بفواصل (placeholders)
        $placeholders = implode(',', array_fill(0, count($notificationIds), '?'));

        // إعداد استعلام الحذف
        $query = "DELETE FROM student_notifications WHERE notification_id IN ($placeholders) AND student_id = ?";
        $stmt = $conn->prepare($query);

        // دمج معرفات الإشعارات مع معرّف الطالب في مصفوفة واحدة
        $types = str_repeat('i', count($notificationIds)) . 'i';  // 'i' للـ INTEGER
        $params = array_merge($notificationIds, [$user_id]);

        // استدعاء bind_param مع المعاملات الديناميكية
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'تم حذف الإشعارات بنجاح.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'فشل في تنفيذ عملية الحذف.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'لم يتم تمرير إشعارات صالحة.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'لم يتم تمرير الإشعارات المحددة.']);
}

$conn->close();