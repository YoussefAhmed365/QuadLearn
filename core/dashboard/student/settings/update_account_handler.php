<?php
require '../auth.php';
header('Content-Type: application/json;charset=UTF-8');
function sendJsonMessage($status, $message, $httpCode) {
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
    $guardian_phone = !empty($_POST['guardian_phone']) ? $_POST['guardian_phone'] : null;
    $level = $_POST['level'];
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
                $sql_password = "UPDATE users SET password = ? WHERE id = ?";
                $stmt_password = $conn->prepare($sql_password);
                $stmt_password->bind_param("si", $new_password, $user_id);
                $stmt_password->execute();
                if ($stmt_password->execute() === false) {
                    throw new Exception("Error updating password: {$stmt_password->error}");
                }
                $stmt_password->close();
            }

            // تحديث جدول students
            $sql_students = "UPDATE students SET guardian_phone = ?, level = ? WHERE id = ?";
            $stmt_students = $conn->prepare($sql_students);
            $stmt_students->bind_param("ssi", $guardian_phone, $level, $user_id);
            $stmt_students->execute();
            if ($stmt_students->execute() === false) {
                throw new Exception("Error updating students: {$stmt_students->error}");
            }
            $stmt_students->close();

            // تأكيد المعاملة
            $conn->commit();

            sendJsonMessage('success', 'تم تحديث البيانات بنجاح!', 200);
        } catch (Exception $e) {
            $conn->rollback();
            sendJsonMessage('error', 'حدث خطأ أثناء تحديث البيانات. يرجى المحاولة لاحقاً!', 500);
        }
    } else {
        sendJsonMessage('warning', 'يرجى إدخال كلمة المرور الحالية.', 400);
    }
}