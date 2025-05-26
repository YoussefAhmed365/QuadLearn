<?php
require $_SERVER['DOCUMENT_ROOT'] . '/quadlearn/QuadLearn/core/db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    // إعداد الجلسة مع خيارات الأمان
    session_set_cookie_params([
        'lifetime' => 30 * 24 * 60 * 60, // 30 يوم
        'path' => '/',
        'domain' => '', 
        'secure' => isset($_SERVER['HTTPS']),  // التأكد من أن secure يستخدم فقط في HTTPS
        'httponly' => true,
        'samesite' => 'Strict'  // منع CSRF
    ]);
    
    session_start();
}

// تحقق من وجود المستخدم في الجلسة
if (!isset($_SESSION['user_id'])) {
    // التحقق من وجود جلسة فعلية، إذا لم تكن هناك جلسة إعادة التوجيه إلى صفحة تسجيل الدخول
    if (!isset($_COOKIE['PHPSESSID'])) {
        header('Location:../../ login.php');
        exit();
    }
}

// إنشاء CSRF token إذا لم يكن موجوداً
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    $user_id = $_SESSION['user_id'] ?? null;

    // التأكد من أن user_id موجود في الجلسة
    if (!$user_id) {
        header("Location:../../ login.php");
        exit();
    }

    // جلب بيانات المستخدم من قاعدة البيانات
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // التحقق من أن المستخدم موجود وأن الحساب نشط
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // تأمين البيانات قبل تخزينها في الجلسة
        $_SESSION['account_type'] = htmlspecialchars($user['account_type'], ENT_QUOTES, 'UTF-8');
        
        // التحقق من أن المستخدم هو "معلم" وليس نوع حساب آخر
        if ($_SESSION['account_type'] !== 'student') {
            header("Location:../../ login.php");
            exit();
        }

        // تخزين باقي البيانات في الجلسة بشكل آمن
        $_SESSION['first_name'] = htmlspecialchars($user['first_name'], ENT_QUOTES, 'UTF-8');
        $_SESSION['last_name'] = htmlspecialchars($user['last_name'], ENT_QUOTES, 'UTF-8');
        $_SESSION['username'] = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
        $_SESSION['email'] = htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
        $_SESSION['phone_number'] = htmlspecialchars($user['phone_number'], ENT_QUOTES, 'UTF-8');
        $_SESSION['bio'] = htmlspecialchars($user['bio'], ENT_QUOTES, 'UTF-8');
        
        // ترجمة الجنس
        $roles = [
            'male' => 'ذكر',
            'female' => 'أنثى'
        ];
        $_SESSION['gender'] = $roles[$user['gender']] ?? 'غير معروف';

        // جلب بيانات الطالب من جدول الطلاب
        $sql2 = "SELECT * FROM students WHERE id = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $user_id);
        $stmt2->execute();
        $student_data = $stmt2->get_result();

        if ($student_data->num_rows === 1) {
            $data = $student_data->fetch_assoc();

            // تخزين بيانات الطالب في الجلسة بشكل آمن
            $_SESSION['guardian_phone'] = htmlspecialchars($data['guardian_phone'], ENT_QUOTES, 'UTF-8');
            
            $student_level = [
                'first' => 'الأول',
                'second' => 'الثاني',
                'third' => 'الثالث'
            ];
            $_SESSION['level'] = $levels[$data['level']] ?? 'غير معروف';
        }

    } else {
        // إذا لم يتم العثور على المستخدم، يتم توجيهه إلى صفحة الخروج
        header("Location: ../../logout.php");
        exit();
    }

    // تجديد معرّف الجلسة لضمان الحماية من اختطاف الجلسات
    session_regenerate_id(true);

} catch (Exception $e) {
    // في حالة حدوث خطأ، يتم تسجيله في سجل الأخطاء مع إعادة التوجيه لصفحة خطأ
    error_log("Error fetching student data: {$e->getMessage()}");
    header("Location: ../../error/error_page.php?error=" . urlencode("حدث خطأ أثناء تسجيل الدخول"));
    exit();
}
