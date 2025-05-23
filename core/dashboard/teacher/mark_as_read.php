<?php
require '../auth.php'; // التأكد من هوية المستخدم

$sql = "UPDATE teacher_notifications SET read_status = 1 WHERE teacher_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => $stmt->error]);
}

$stmt->close();