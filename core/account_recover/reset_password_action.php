<?php
include 'db_connect.php'; // الاتصال بقاعدة البيانات

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = mysqli_real_escape_string($conn, $_POST['user_id']);
    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT); // تشفير كلمة المرور
    
    // تحديث كلمة المرور
    mysqli_query($conn, "UPDATE users SET password='$new_password' WHERE id='$user_id'");
    
    // حذف الرمز المستخدم
    mysqli_query($conn, "DELETE FROM password_resets WHERE user_id='$user_id'");
    
    echo "تم إعادة تعيين كلمة المرور بنجاح.";
}