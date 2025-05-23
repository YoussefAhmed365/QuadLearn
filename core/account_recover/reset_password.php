<?php
require 'db_connect.php'; // الاتصال بقاعدة البيانات

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // تحقق من صحة الرمز
    $result = mysqli_query($conn, "SELECT user_id FROM password_resets WHERE token='$token' AND created_at > NOW() - INTERVAL 1 HOUR");
    
    if (mysqli_num_rows($result) > 0) {
        $user_id = mysqli_fetch_assoc($result)['user_id'];
    } else {
        die("الرابط غير صالح أو انتهت صلاحيته.");
    }
}
?>
<form method="POST" action="reset_password_action.php">
    <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
    <label for="password">أدخل كلمة مرور جديدة:</label>
    <input type="password" id="password" name="password" required>
    <button type="submit">إعادة تعيين كلمة المرور</button>
</form>