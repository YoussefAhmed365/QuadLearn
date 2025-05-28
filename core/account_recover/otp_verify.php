<?php
session_start();
$message = '';
if (!isset($_SESSION['recovery_email'])) {
    header("Location: account_recovery.php");
    exit;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['otp'])) {
    $input_otp = trim($_POST['otp']);
    if (
        isset($_SESSION['recovery_otp']) &&
        $input_otp == $_SESSION['recovery_otp'] &&
        time() - $_SESSION['recovery_otp_time'] < 600 // 10 min expiry
    ) {
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.php");
        exit;
    } else {
        $message = "رمز التحقق غير صحيح أو منتهي الصلاحية.";
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>التحقق من الرمز | QuadLearn</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <form method="POST" class="bg-white rounded-4 px-4 pt-2 pb-5 shadow row gy-4 w-50">
            <div class="col-12">
                <h5>أدخل رمز التحقق</h5>
                <h6 class="text-secondary">تم إرسال رمز التحقق إلى بريدك الإلكتروني.</h6>
            </div>
            <div class="col-12">
                <?php if ($message): ?>
                    <div class="alert alert-danger"><?= $message ?></div>
                <?php endif; ?>
                <div class="form-floating mb-2 mt-3">
                    <input type="text" name="otp" class="form-control" id="otpInput" placeholder="OTP" required>
                    <label for="otpInput">رمز التحقق</label>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-default btn-lg fs-6 w-100">تأكيد</button>
            </div>
        </form>
    </div>
</body>
</html>
