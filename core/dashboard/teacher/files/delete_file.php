<?php
require '../auth.php';

// التحقق من أن معرف الملف تم تمريره وأن المستخدم مسجل دخول
if (isset($_GET['file_id'])) {
    $file_id = intval($_GET['file_id']);
    
    // استعلام للحصول على اسم الملف
    $stmt = $conn->prepare("SELECT unique_file FROM subject_files WHERE id = ?");
    $stmt->bind_param("i", $file_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $filePath = "../../../../assets/subject_files/" . $row['unique_file'];

        // حذف الملف من النظام إذا كان موجودًا
        if (is_file($filePath)) {
            unlink($filePath);
        }

        // استعلام الحذف من قاعدة البيانات
        $stmt = $conn->prepare("DELETE FROM subject_files WHERE id = ?");
        $stmt->bind_param("i", $file_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to delete file record from database.']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'File not found in database.']);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
}