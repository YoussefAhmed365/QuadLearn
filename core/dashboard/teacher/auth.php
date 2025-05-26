<?php
require $_SERVER['DOCUMENT_ROOT'] . '/quadlearn/QuadLearn/core/db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
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

if (!isset($_SESSION['user_id'])) {
    if (!isset($_COOKIE['PHPSESSID'])) {
        header('Location: ../../login/login.php');
        exit();
    }
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

try {
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        header("Location: ../../login/login.php");
        exit();
    }

    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $_SESSION['account_type'] = htmlspecialchars($user['account_type'], ENT_QUOTES, 'UTF-8');
        
        if ($_SESSION['account_type'] !== 'teacher') {
            header("Location: ../../login/login.php");
            exit();
        }

        $_SESSION['first_name'] = htmlspecialchars($user['first_name'], ENT_QUOTES, 'UTF-8');
        $_SESSION['last_name'] = htmlspecialchars($user['last_name'], ENT_QUOTES, 'UTF-8');
        $_SESSION['username'] = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
        $_SESSION['email'] = htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');
        $_SESSION['phone_number'] = htmlspecialchars($user['phone_number'], ENT_QUOTES, 'UTF-8');
        $_SESSION['bio'] = htmlspecialchars($user['bio'] ?? '', ENT_QUOTES, 'UTF-8');
        
        $roles = [
            'male' => 'ذكر',
            'female' => 'أنثى'
        ];
        $_SESSION['gender'] = $roles[$user['gender']] ?? 'غير معروف';

        $sql2 = "SELECT * FROM teachers WHERE id = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $user_id);
        $stmt2->execute();
        $teacher_data = $stmt2->get_result();

        if ($teacher_data->num_rows === 1) {
            $data = $teacher_data->fetch_assoc();
            $_SESSION['subject'] = htmlspecialchars($data['subject'], ENT_QUOTES, 'UTF-8');

            $teacher_roles = [
                'teacher' => 'معلم',
                'assistant' => 'معلم مساعد'
            ];
            $_SESSION['title'] = $teacher_roles[$data['title']] ?? 'غير معروف';
            $title = $_SESSION['title'];
        }

    } else {
        header("Location: ../../logout.php");
        exit();
    }
    session_regenerate_id(true);
} catch (Exception $e) {
    error_log("Error fetching teacher data: {$e->getMessage()}");
    header("Location: ../../error/error_page.php?error=" . urlencode("حدث خطأ أثناء تسجيل الدخول"));
    exit();
}