<?php
require 'db_connect.php';
header('Content-Type: application/json;charset=UTF-8');
function sendJsonMessage($status, $message, $httpCode)
{
    http_response_code($httpCode);
    echo json_encode(['status' => $status, 'message' => $message]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['confirmPassword'] ?? '';
    $user_id = $_POST['user_id'] ?? '';

    if (empty($password)) {
        sendJsonMessage('warning', 'يرجى كتابة كلمة مرور الحساب.', 400);
    } elseif (empty($user_id)) {
        sendJsonMessage('error', 'توجد مشكلة حالية، يرجى المحاولة لاحقاً.', 400);
    }

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($hashed_password);
    $stmt->fetch();
    $stmt->close();

    if (empty($hashed_password)) {
        sendJsonMessage('error', 'توجد مشكلة حالية، يرجى المحاولة لاحقاً.', 400);
    } elseif (!password_verify($password, $hashed_password)) {
        sendJsonMessage('warning', 'كلمة المرور غير صحيحة.', 400);
    } else {
        $stmt2 = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt2->bind_param("i", $user_id);
        if ($stmt2->execute() === false) {
            error_log("Error deleting user $user_id: " . $stmt2->error);
            $stmt2->close();
            sendJsonMessage('error', 'توجد مشكلة حالية، يرجى المحاولة لاحقاً.', 500);
        } else {
            $stmt2->close();
            sendJsonMessage('success', 'تم حذف الحساب بنجاح! سيتم تسجيل خروجك الآن', 200);
        }
    }
}