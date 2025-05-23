<?php
require '../db_connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// توليد رمز CSRF إذا لم يكن موجودًا بالفعل
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    header('Content-Type: application/json');
    
    // تحقق من CSRF Token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['type' => 'danger', 'message' => 'Invalid CSRF token'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // استلام المدخلات وتطهيرها
    $username = htmlspecialchars(trim($_POST['username'] ?? ''));
    $email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL) ? trim($_POST['email']) : null;
    $password = htmlspecialchars(trim($_POST['password'] ?? ''));
    $first_name = htmlspecialchars(trim($_POST['first_name'] ?? ''));
    $last_name = htmlspecialchars(trim($_POST['last_name'] ?? ''));
    $phone_number = htmlspecialchars(trim($_POST['phone_number'] ?? $_POST['sphone_number'] ?? ''));
    $account_type = htmlspecialchars(trim($_POST['account_type'] ?? ''));
    $gender = htmlspecialchars(trim($_POST['gender'] ?? ''));

    // التحقق من المدخلات
    if (empty($username) || empty($email) || empty($password) || empty($first_name) || empty($last_name)) {
        echo json_encode(['type' => 'danger', 'message' => 'الرجاء ملء جميع الحقول المطلوبة'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // التحقق من وجود اسم المستخدم أو البريد الإلكتروني مسبقًا
    if (isUsernameExists($conn, $username)) {
        echo json_encode(['type' => 'warning', 'message' => 'اسم المستخدم موجود يرجى اختيار اسم آخر'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    if (isEmailExists($conn, $email)) {
        echo json_encode(['type' => 'warning', 'message' => 'البريد الإلكتروني موجود يرجى اختيار عنوان بريد آخر'], JSON_UNESCAPED_UNICODE);
        exit();
    }

    // توليد معرف فريد
    do {
        $id = random_int(10000000, 99999999);
    } while (isIdExists($conn, $id));

    // تشفير كلمة المرور
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // إدخال المستخدم في جدول "users"
    $stmt = $conn->prepare("INSERT INTO users (id, username, password, email, first_name, last_name, phone_number, account_type, gender) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssss", $id, $username, $hashed_password, $email, $first_name, $last_name, $phone_number, $account_type, $gender);

    if ($stmt->execute()) {
        // التعامل مع التسجيل حسب نوع الحساب
        if ($account_type === 'teacher') {
            $title = htmlspecialchars(trim($_POST['title'] ?? ''));
            $subject = htmlspecialchars(trim($_POST['subject'] ?? ''));

            $stmt = $conn->prepare("INSERT INTO teachers (id, title, subject) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $id, $title, $subject);
        } elseif ($account_type === 'student') {
            $level = htmlspecialchars(trim($_POST['level'] ?? ''));
            $guardian_phone = htmlspecialchars(trim($_POST['guardian_phone'] ?? null));

            $stmt = $conn->prepare("INSERT INTO students (id, guardian_phone, level) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $id, $guardian_phone, $level);
        }

        if ($stmt->execute()) {
            // هنا يتم تسجيل الدخول التلقائي للمستخدم بعد التسجيل
            session_regenerate_id(true);
            $_SESSION['user_id'] = $id;
            $_SESSION['account_type'] = $account_type;

            // التوجيه بناءً على نوع الحساب
            $redirect_page = ($account_type === 'teacher') ? '../dashboard/teacher/main/dashboard.php' : '../dashboard/student/main/dashboard.php';
            echo json_encode(['type' => 'success', 'message' => 'تم التسجيل بنجاح', 'redirect' => $redirect_page], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['type' => 'error', 'message' => 'حدث خطأ أثناء تسجيل حسابك'], JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode(['type' => 'error', 'message' => 'حدث خطأ أثناء تسجيل الحساب'], JSON_UNESCAPED_UNICODE);
    }

    $stmt->close();
    $conn->close();
    exit();
}

// دوال التحقق من البيانات في قاعدة البيانات
function isUsernameExists($conn, $username) {
    $query = "SELECT COUNT(*) as count FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['count'] > 0;
}

function isEmailExists($conn, $email) {
    $query = "SELECT COUNT(*) as count FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['count'] > 0;
}

function isIdExists($conn, $id) {
    $query = "SELECT COUNT(*) as count FROM users WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['count'] > 0;
}