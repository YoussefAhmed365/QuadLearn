<?php
require '../auth.php';

header('Content-Type: application/json; charset=utf-8');

function sendJsonResponse($status, $message, $httpStatusCode = 200)
{
    http_response_code($httpStatusCode);
    echo json_encode(["status" => $status, "message" => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    sendJsonResponse("error", "طلب غير صالح.", 400);
}

$student_id = $_POST['id'];

if (!is_numeric($student_id)) {
    sendJsonResponse("error", "معرف الطالب غير صالح.", 400);
}

if (!isset($user_id) || !$conn) {
    sendJsonResponse("error", "خطأ في تهيئة المستخدم أو قاعدة البيانات.", 500);
}

$stmt = $conn->prepare("DELETE FROM assigned_students WHERE student_id = ? AND teacher_id = ?");

if (!$stmt) {
    sendJsonResponse("error", "فشل تحضير استعلام الحذف: {$conn->error}", 500);
}

$stmt->bind_param("ii", $student_id, $user_id);

if (!$stmt->execute()) {
    sendJsonResponse("error", "خطأ أثناء تنفيذ عملية الحذف: {$stmt->error}", 500);
}

if ($stmt->affected_rows > 0) {
    sendJsonResponse("success", "تم حذف الطالب بنجاح.");
} elseif ($stmt->affected_rows === 0) {
    sendJsonResponse("error", "لم يتم العثور على الطالب المحدد أو لا تملك الإذن لحذفه.", 404);
} else {
    sendJsonResponse("error", "حدث خطأ غير متوقع بعد التنفيذ.", 500);
}

$stmt->close();