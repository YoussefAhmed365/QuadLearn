<?php
require '../auth.php'; // التأكد من تسجيل الدخول
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $videoId = intval($_POST['id']);

    // التحقق من أن الفيديو مملوك من المدرس
    $sql = "SELECT fileName, level FROM videos WHERE id = ? AND teacher_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $videoId, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $video = $result->fetch_assoc();

    if ($video) {
        // تحديد مسار الفيديو
        $videoPath = "../../../../assets/videos/{$video['level']}/{$video['fileName']}";

        // محاولة حذف الفيديو من النظام إذا كان موجودًا
        if (file_exists($videoPath)) {
            if (!is_writable($videoPath) || !unlink($videoPath)) {
                // إذا فشل الحذف، سجل تحذيرًا ولكن استمر في حذف السجل من قاعدة البيانات
                error_log("Failed to delete video file: $videoPath");
            }
        }

        // حذف الفيديو من قاعدة البيانات
        $deleteSql = "DELETE FROM videos WHERE id = ?";
        $deleteStmt = $conn->prepare($deleteSql);
        $deleteStmt->bind_param("i", $videoId);

        if ($deleteStmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'تم حذف الفيديو بنجاح']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'فشل في حذف الفيديو من قاعدة البيانات']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'لا يمكنك حذف هذا الفيديو.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'طلب غير صالح.']);
}