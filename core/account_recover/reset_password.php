<?php
session_start();
require 'db_connect.php'; // الاتصال بقاعدة البيانات

$message = '';
if (!isset($_SESSION['otp_verified']) || !$_SESSION['otp_verified']) {
    header("Location: account_recovery.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $new_password = password_hash($_POST['password'], PASSWORD_BCRYPT); // تشفير كلمة المرور
    $email = $_SESSION['recovery_email'];
        
    // تحديث كلمة المرور
    mysqli_query($conn, "UPDATE users SET password='$new_password' WHERE email='$email'");
    
    // حذف الرمز المستخدم
    mysqli_query($conn, "DELETE FROM password_resets WHERE email='$email'");
    
    // Auto-login (set session, etc.)
    $_SESSION['user_email'] = $email;
    
    // Clean up recovery session vars
    unset($_SESSION['recovery_email'], $_SESSION['recovery_otp'], $_SESSION['recovery_otp_time'], $_SESSION['otp_verified']);
    header("Location: ../login/login.php"); // Redirect to user dashboard
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعيين كلمة مرور جديدة | QuadLearn</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <form action="reset_password.php" method="POST" class="bg-white rounded-4 px-4 pt-2 pb-5 shadow row gy-4 w-50">
            <div class="col-12">
                <h5>تعيين كلمة مرور جديدة</h5>
            </div>
            <div class="col-12">
                <?php if ($message): ?>
                    <div class="alert alert-danger"><?= $message ?></div>
                <?php endif; ?>
                <div class="form-floating mb-2 mt-3">
                    <input type="password" name="password" class="form-control" id="passwordInput" placeholder="كلمة المرور الجديدة" required>
                    <label for="passwordInput">كلمة المرور الجديدة</label>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-default btn-lg fs-6 w-100">تعيين وتسجيل الدخول</button>
            </div>
        </form>
    </div>
</body>
</html>