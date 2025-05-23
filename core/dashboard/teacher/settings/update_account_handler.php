<?php
require '../auth.php';
header('Content-Type: application/json;charset=UTF-8');
function sendJsonMessage($status, $message, $httpCode)
{
    http_response_code($httpCode);
    echo json_encode(['status' => $status, 'message' => $message]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = !empty($_POST['password']) ? $_POST['password'] : null;
    $new_password = !empty($_POST['new_password']) ? password_hash($_POST['new_password'], PASSWORD_DEFAULT) : null;
    $email = $_POST['email'];
    $phone_number = $_POST['phone'];
    $bio = $_POST['bio'];

    if ($password !== null) {
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();

        if (!password_verify($password, $hashed_password)) {
            sendJsonMessage('warning', 'كلمة المرور الحالية غير صحيحة.', 400);
        }

        try {
            // بدء المعاملة
            $conn->begin_transaction();

            // تحديث جدول users
            $sql2 = "UPDATE users SET username = ?, email = ?, phone_number = ?, bio = ?, updated_at = NOW() WHERE id = ?";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("ssssi", $username, $email, $phone_number, $bio, $user_id);
            if ($stmt2->execute() === false) {
                throw new Exception("Error executing query: {$stmt2->error}");
            }

            // إذا كانت كلمة المرور غير فارغة، يتم تحديثها
            if ($new_password !== null) {
                $stmt_password->bind_param("si", $new_password, $user_id);
                $stmt_password = $conn->prepare($sql_password);
                $stmt_password->bind_param("si", $password, $user_id);
                $stmt_password->execute();
            }


            // تأكيد المعاملة
            $conn->commit();

            sendJsonMessage('success', 'تم تحديث البيانات بنجاح!', 200);
        } catch (Exception $e) {
            // في حال وجود خطأ، إلغاء المعاملة
            $conn->rollback();
            error_log("Error updating data for user $user_id: " . $e->getMessage());
            sendJsonMessage('error', 'حدث خطأ أثناء تحديث البيانات. يرجى المحاولة لاحقاً!', 500);
        }
    } else {
        sendJsonMessage('warning', 'يرجى إدخال كلمة المرور الحالية.', 400);
    }
}