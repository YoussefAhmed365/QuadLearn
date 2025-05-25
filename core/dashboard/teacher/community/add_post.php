<?php
require '../auth.php';

function sendJsonResponse($status, $message, $httpStatusCode) {
    http_response_code($httpStatusCode);
    echo json_encode(["status" => $status, "message" => $message]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject = $_POST['subject'] ?? NULL;
    $title = $_POST['title'] ?? NULL;
    $content = $_POST['content'];
    $badge_names = $_POST['badges'] ?? NULL;

    if ($subject === NULL) {
        echo sendJsonResponse("error", "يجب اختيار مادة");
    }

    if (empty($content)) {
        echo sendJsonResponse("error", "يجب إدخال المحتوى");
    }

    if (!empty($badge_names)) {
        $badge_colors = $_POST['badge_colors'] ?? [];

        $badges = [];
        foreach ($badge_names as $index => $name) {
            if (!empty($name)) {
                $badges[] = ['name' => $name, 'color' => $badge_colors[$index] ?? 'green'];
            }
        }

        $badge_json = json_encode($badges);
    } else {
        $badge_json = NULL;
    }

    // معالجة الملفات المرفوعة
    $uploaded_files = [];
    $original_file_names = [];

    $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf', 'docx', 'doc', 'txt', 'pptx', 'xlsx', 'zip', 'rar'];
    $max_file_size = 5 * 1024 * 1024; // 5MB
    $upload_dir = '../../../../assets/files/';

    if (!empty($_FILES['uploaded_files']['name'][0])) {
        foreach ($_FILES['uploaded_files']['name'] as $key => $original_name) {
            $file_tmp = $_FILES['uploaded_files']['tmp_name'][$key];
            $file_size = $_FILES['uploaded_files']['size'][$key];
            $file_ext = pathinfo($original_name, PATHINFO_EXTENSION);

            if (!in_array(strtolower($file_ext), $allowed_extensions)) {
                sendJsonResponse("warning", "نوع الملف غير مدعوم: $original_name");
                continue;
            }

            if ($file_size > $max_file_size) {
                sendJsonResponse("warning", "حجم الملف كبير جداً: $original_name");
                continue;
            }

            $random_name = uniqid() . '.' . $file_ext;
            if (move_uploaded_file($file_tmp, "$upload_dir$random_name")) {
                $uploaded_files[] = $random_name;
                $original_file_names[] = htmlspecialchars($original_name);
            }
        }
    }

    $uploaded_files_json = json_encode($uploaded_files);
    $original_files_json = json_encode($original_file_names);

    // إدخال البيانات في قاعدة البيانات
    $stmt = $conn->prepare("INSERT INTO community (user_id, subject, title, content, badges, uploaded_files, original_file_names) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $user_id, $data['subject'], $title, $content, $badge_json, $uploaded_files_json, $original_files_json);
    $stmt->execute();
    $stmt->close();

    sendJsonResponse("success", "تم إرسال المنشور");
}