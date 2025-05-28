<?php
// تضمين ملفات الاتصال وإعدادات PHPMailer
require 'db_connect.php';
require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // التحقق من صحة البريد الإلكتروني المُدخل
    if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $email = $_POST['email'];

        // التحقق من وجود البريد الإلكتروني في قاعدة البيانات
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // إذا كان البريد الإلكتروني موجودًا
            $user = $result->fetch_assoc();
            $user_id = $user['id'];

            // إنشاء رمز إعادة تعيين عشوائي
            $token = bin2hex(random_bytes(50));

            // حذف أي رموز قديمة لنفس المستخدم
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            // إدخال الرمز الجديد في قاعدة البيانات
            $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, created_at) VALUES (?, ?, NOW())");
            $stmt->bind_param("is", $user_id, $token);
            $stmt->execute();

            // إعداد البريد الإلكتروني باستخدام PHPMailer
            $mail = new PHPMailer(true);

            try {
                // إعدادات SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = getenv('youssefahmed3655@gmail.com'); // من متغيرات البيئة
                $mail->Password = getenv('Iqp3!Mi2d1cFGpr'); // من متغيرات البيئة
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // إعداد البريد
                $mail->setFrom('no-reply@yourdomain.com', 'QuadLearn');
                $mail->addAddress($email);

                // إعداد المحتوى
                $mail->isHTML(true);
                $mail->Subject = 'إعادة تعيين كلمة المرور';
                $reset_link = "http://localhost/quadlearn/QuadLearn/core/account_recover/reset_password.php?token=$token";
                $mail->Body = "
                    <p>مرحبًا،</p>
                    <p>لقد طلبت إعادة تعيين كلمة المرور الخاصة بك. اضغط على الرابط أدناه لإتمام العملية:</p>
                    <p><a href='$reset_link'>$reset_link</a></p>
                    <p>إذا لم تطلب ذلك، يرجى تجاهل هذا البريد.</p>
                ";
                $mail->AltBody = "لإعادة تعيين كلمة المرور، يرجى زيارة الرابط التالي: $reset_link";

                // إرسال البريد
                $mail->send();
                echo json_encode(['status' => 'success', 'message' => 'تم إرسال رابط إعادة تعيين كلمة المرور إلى بريدك الإلكتروني.']);
            } catch (Exception $e) {
                error_log("Mail Error: {$mail->ErrorInfo}");
                echo json_encode(['status' => 'error', 'message' => 'لم يتم إرسال البريد الإلكتروني. الرجاء المحاولة مرة أخرى لاحقًا.']);
            }
        } else {
            // البريد الإلكتروني غير موجود
            echo json_encode(['status' => 'error', 'message' => 'البريد الإلكتروني غير موجود.']);
        }
    } else {
        // البريد الإلكتروني غير صالح
        echo json_encode(['status' => 'error', 'message' => 'يرجى إدخال بريد إلكتروني صالح.']);
    }
}