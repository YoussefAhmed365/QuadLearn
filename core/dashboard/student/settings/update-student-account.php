<?php
require '../auth.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // التحقق من وجود user_id
    if (isset($user_id)) {
        // التحقق من وجود البيانات المطلوبة في POST
        $username = $_POST['new_username'];
        $password = !empty($_POST['new_password']) ? password_hash($_POST['new_password'], PASSWORD_DEFAULT) : null;
        $email = $_POST['new_email'];
        $phone_number = $_POST['new_phone_number'];
        $guardian_phone = $_POST['new_guardian_phone'];
        $level = $_POST['new_level'];
        $bio = $_POST['new_bio'];

        try {
            // بدء المعاملة
            $conn->begin_transaction();

            // تحديث جدول users
            $sql_users = "UPDATE users SET username = ?, email = ?, phone_number = ?, updated_at = NOW() WHERE id = ?";
            $stmt_users = $conn->prepare($sql_users);
            $stmt_users->bind_param("sssi", $username, $email, $phone_number, $user_id);
            $stmt_users->execute();

            // إذا كانت كلمة المرور غير فارغة، يتم تحديثها
            if ($password !== null) {
                $sql_password = "UPDATE users SET password = ? WHERE id = ?";
                $stmt_password = $conn->prepare($sql_password);
                $stmt_password->bind_param("si", $password, $user_id);
                $stmt_password->execute();
            }

            // تحديث جدول students
            $sql_students = "UPDATE students SET guardian_phone = ?, level = ?, bio = ? WHERE id = ?";
            $stmt_students = $conn->prepare($sql_students);
            $stmt_students->bind_param("sssi", $guardian_phone, $level, $bio, $user_id);
            $stmt_students->execute();

            // تأكيد المعاملة
            $conn->commit();

            // إرجاع المستخدم إلى صفحة إعدادات الحساب مع رسالة نجاح
            
            header("Location: settings.php?success=1");
            exit();

        } catch (Exception $e) {
            // في حال وجود خطأ، إلغاء المعاملة
            $conn->rollback();
            error_log("Error updating data for user $user_id: " . $e->getMessage());
            $errorMessage = "حدث خطأ ما أثناء تحديث البيانات. يرجى المحاولة لاحقاً!";
            header("Location: settings.php?error=" . urlencode($errorMessage));
            exit();
        }
    }
}