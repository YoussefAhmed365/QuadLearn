<?php
require '../auth.php';
header("Content-Type: application/json; charset=utf-8;");

$upload_dir = '../../../../assets/subject_files/';
$response = ['success' => false];

if (!isset($user_id)) {
    $response['message'] = "يرجى تسجيل الدخول قبل رفع الملفات.";
    echo json_encode($response);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_FILES['uploaded_files']['name'][0])) {
    $max_file_size = 20 * 1024 * 1024;
    $response['files'] = [];

    foreach ($_FILES['uploaded_files']['name'] as $key => $original_name) {
        $file_tmp = $_FILES['uploaded_files']['tmp_name'][$key];
        $file_size = $_FILES['uploaded_files']['size'][$key];
        $file_ext = pathinfo($original_name, PATHINFO_EXTENSION);

        if ($file_size > $max_file_size) {
            $response['files'][] = ['file' => $original_name, 'success' => false, 'message' => "حجم الملف كبير جدًا"];
            continue;
        }

        // استخدام time() و random_bytes() لتوليد اسم فريد
        do {
            $unique_name = bin2hex(random_bytes(16)) . '.' . $file_ext;
            $stmt_check = $conn->prepare("SELECT COUNT(*) FROM subject_files WHERE unique_file = ?");
            $stmt_check->bind_param("s", $unique_name);
            $stmt_check->execute();
            $stmt_check->bind_result($count);
            $stmt_check->fetch();
            $stmt_check->close();
        } while ($count > 0);

        if (move_uploaded_file($file_tmp, "$upload_dir$unique_name")) {
            $stmt = $conn->prepare("INSERT INTO subject_files (teacher_id, unique_file, file_name) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user_id, $unique_name, $original_name);

            if ($stmt->execute()) {
                $response['files'][] = ['file' => $original_name, 'success' => true];
            } else {
                $error_message =  $stmt->error; // Get the specific MySQL error
                $response['files'][] = ['file' => $original_name, 'success' => false, 'message' => "فشل إدخال الملف في قاعدة البيانات: " . $error_message];
            }

            $stmt->close();

        } else {
            $response['files'][] = ['file' => $original_name, 'success' => false, 'message' => "فشل نقل الملف"];
        }
    }

     $response['success'] = !empty(array_filter($response['files'], fn($file) => $file['success'])); //Check for any successful uploads
} else {
    $response['message'] = "طلب غير صالح.";
}

echo json_encode($response);