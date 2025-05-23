<?php
require '../auth.php'; // التأكد من تسجيل الدخول
header('Content-Type: application/json');

$stmt = $conn->prepare("SELECT 
    videos.id,
    videos.teacher_id,
    videos.name,
    videos.fileName,
    videos.thumbnail,
    videos.level,
    videos.created_at,
    users.first_name,
    users.last_name,
    users.picture,
    teachers.subject
FROM videos
INNER JOIN users ON videos.teacher_id = users.id
LEFT JOIN assigned_assistants ON videos.teacher_id = assigned_assistants.teacher_id OR videos.teacher_id = assigned_assistants.assistant_id
LEFT JOIN teachers ON videos.teacher_id = teachers.id
WHERE videos.teacher_id = ? OR assigned_assistants.teacher_id = ?
ORDER BY videos.created_at DESC");
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$videos = [];
while ($row = $result->fetch_assoc()) {
    $videos[] = $row;
}

echo json_encode($videos);