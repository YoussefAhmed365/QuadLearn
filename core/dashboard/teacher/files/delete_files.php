<?php
require '../auth.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data['file_ids'])) {
    echo json_encode(["success" => false, "error" => "Invalid input"]);
    http_response_code(400);
    exit;
}

$fileIds = $data['file_ids']; // معرفات الملفات المراد حذفها

// تحويل المصفوفة إلى قائمة قابلة للحقن في SQL
$placeholders = implode(',', array_fill(0, count($fileIds), '?'));

// استعلام للحصول على أسماء الملفات
$query = "SELECT unique_file FROM subject_files WHERE id IN ($placeholders)";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => $conn->error]);
    exit;
}

// ربط القيم
$stmt->bind_param(str_repeat('i', count($fileIds)), ...$fileIds);
$stmt->execute();
$result = $stmt->get_result();

$filePaths = [];
while ($row = $result->fetch_assoc()) {
    $filePaths[] = "../../../../assets/subject_files/" . $row['unique_file'];
}

$stmt->close();

// حذف الملفات من النظام
foreach ($filePaths as $filePath) {
    if (is_file($filePath)) {
        unlink($filePath);
    }
}

// حذف السجلات من قاعدة البيانات
$query = "DELETE FROM subject_files WHERE id IN ($placeholders)";
$stmt = $conn->prepare($query);

if (!$stmt) {
    echo json_encode(["success" => false, "error" => $conn->error]);
    exit;
}

$stmt->bind_param(str_repeat('i', count($fileIds)), ...$fileIds);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmt->error]);
}

$stmt->close();

// require '../auth.php';

// $data = json_decode(file_get_contents("php://input"), true);
// if (!$data || !isset($data['file_ids'])) {
//     echo json_encode(["success" => false, "error" => "Invalid input"]);
//     http_response_code(400);
//     exit;
// }

// $fileIds = $data['file_ids']; // معرفات الملفات المراد حذفها

// // تحويل المصفوفة إلى قائمة قابلة للحقن في SQL
// $placeholders = implode(',', array_fill(0, count($fileIds), '?'));

// // إنشاء استعلام حذف
// $query = "DELETE FROM subject_files WHERE id IN ($placeholders)";
// $stmt = $conn->prepare($query);

// // التحقق من الاستعلام
// if (!$stmt) {
//     echo json_encode(["success" => false, "error" => $conn->error]);
//     exit;
// }

// // ربط القيم
// $stmt->bind_param(str_repeat('i', count($fileIds)), ...$fileIds);

// if ($stmt->execute()) {
//     echo json_encode(["success" => true]);
// } else {
//     echo json_encode(["success" => false, "error" => $stmt->error]);
// }

// $stmt->close();