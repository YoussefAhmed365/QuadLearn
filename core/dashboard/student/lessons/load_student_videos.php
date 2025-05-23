<?php
require '../auth.php'; // التأكد من تسجيل الدخول
header('Content-Type: application/json');

// قراءة البيانات المرسلة من الجاڤا سكريبت
$filterData = json_decode(file_get_contents("php://input"), true);

// التحقق من وجود تصنيف
$filter = isset($filterData['filterType']) && !empty($filterData['filterType']) ? "AND teachers.subject = ?" : "";

// إنشاء الاستعلام
$query = "SELECT 
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
INNER JOIN teachers ON videos.teacher_id = teachers.id
LEFT JOIN assigned_assistants ON videos.teacher_id = assigned_assistants.teacher_id OR videos.teacher_id = assigned_assistants.assistant_id
INNER JOIN assigned_teachers ON assigned_teachers.teacher_id = videos.teacher_id
WHERE assigned_teachers.student_id = ? AND videos.level = ? $filter
ORDER BY videos.created_at DESC";

$stmt = $conn->prepare($query);

// ربط المعاملات
if (!empty($filter)) {
    $stmt->bind_param("iss", $user_id, $data['level'], $filterData['filterType']);
} else {
    $stmt->bind_param("is", $user_id, $data['level']);
}

$stmt->execute();
$result = $stmt->get_result();

// جمع النتائج
$videos = [];
while ($row = $result->fetch_assoc()) {
    $videos[] = $row;
}

// إرسال النتائج كـ JSON
echo json_encode($videos);