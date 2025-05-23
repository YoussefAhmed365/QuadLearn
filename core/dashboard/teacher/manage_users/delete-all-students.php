<?php
require_once '../auth.php';

header('Content-Type: application/json; charset=utf-8');
function sendJsonResponse($status, $message, $httpStatusCode) {
    http_response_code($httpStatusCode);
    echo json_encode(["status" => $status, "message" => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("DELETE FROM assigned_students WHERE teacher_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    if ($stmt->affected_rows > 0) {
        sendJsonResponse("success", "تم حذف جميع الطلاب بنجاح.", 200);
    } elseif ($stmt->affected_rows === 0) {
        sendJsonResponse("warning", "لا يوجد طلاب لحذفهم.", 404);
    } else {
        sendJsonResponse("error", "حدث خطأ غير متوقع أثناء الحذف.", 500);
    }
    $stmt->close();
} else {
    sendJsonResponse("error", "طلب غير صالح.", 400);
}