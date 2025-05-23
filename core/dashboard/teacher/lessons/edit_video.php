<?php
require_once '../auth.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $videoId = filter_var($_POST['videoId'], FILTER_SANITIZE_NUMBER_INT);
    $videoName = filter_var($_POST['videoName'], FILTER_SANITIZE_STRING);
    $level = filter_var($_POST['level'], FILTER_SANITIZE_STRING);
    $thumbnail = $_FILES['thumbnail'];

    // التحقق من صحة البيانات
    if (empty($videoId) || empty($videoName) || empty($level)) {
        echo json_encode(['status' => 'error', 'message' => 'جميع الحقول مطلوبة.']);
        exit;
    }

    // Fetch the current thumbnail path from the database
    $sql = "SELECT thumbnail FROM videos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $videoId);
    $stmt->execute();
    $stmt->bind_result($currentThumbnailPath);
    $stmt->fetch();
    $stmt->close();

    $thumbnailPath = $currentThumbnailPath; // Default to the current thumbnail

    // Handle new thumbnail upload
    if ($thumbnail['error'] !== UPLOAD_ERR_NO_FILE) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
        if (!in_array($thumbnail['type'], $allowedTypes)) {
            echo json_encode(['status' => 'error', 'message' => 'نوع الملف غير مدعوم.']);
            exit;
        }

        if ($thumbnail['size'] > 5000000) { // 5MB limit
            echo json_encode(['status' => 'error', 'message' => 'حجم الملف كبير جداً.']);
            exit;
        }

        // Generate a unique file name for the new thumbnail
        $thumbnailExtension = pathinfo($thumbnail['name'], PATHINFO_EXTENSION);
        $thumbnailName = uniqid('thumb_', true) . '.' . $thumbnailExtension;
        $thumbnailPath = "../../../../assets/videos/$level/$thumbnailName";

        // Replace the old thumbnail with the new one
        if ($thumbnail['error'] === UPLOAD_ERR_OK) {
            if (!move_uploaded_file($thumbnail['tmp_name'], $thumbnailPath)) {
                echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء رفع الصورة المصغرة.']);
                exit;
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء رفع الصورة المصغرة.']);
            exit;
        }
    }

    // Update video data in the database
    $sql = "UPDATE videos SET name = ?, level = ?, thumbnail = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssi', $videoName, $level, $thumbnailName, $videoId);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'تم تعديل الفيديو.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء تعديل الفيديو.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'طلب غير صالح.']);
}