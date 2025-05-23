<?php
require '../auth.php';

// التحقق من أن معرف الملف واسم الملف تم تمريرهما وأن المستخدم مسجل دخول
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $file_id = isset($_POST['file_id']) ? intval($_POST['file_id']) : null;
    $fileNameInput = isset($_POST['fileNameInput']) ? trim($_POST['fileNameInput']) : null;

    if ($file_id === null || $fileNameInput === null) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request. No file ID or new file name provided.']);
        exit;
    }

    // تحديث اسم الملف
    $stmt = $conn->prepare("UPDATE subject_files SET file_name = ? WHERE id = ?");
    $stmt->bind_param("si", $fileNameInput, $file_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update file name.']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request. Only POST method is allowed.']);
}