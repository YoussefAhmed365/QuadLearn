<?php
require '../auth.php';

header("Content-Type: application/json; charset=utf-8");

function sendJsonResponse($status, $message, $httpStatusCode) {
    http_response_code($httpStatusCode);
    echo json_encode(["status" => $status, "message" => $message]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject = $_POST['subject'] ?? NULL;
    $title = $_POST['title'] ?? NULL;
    $content = $_POST['content'] ?? NULL;
    $badge_names = $_POST['badges'] ?? NULL;
    
    if (empty($subject)) {
        sendJsonResponse("warning", "يجب اختيار مادة", 400);
    }

    if (empty($content)) {
        sendJsonResponse("warning", "يجب إدخال المحتوى", 400);
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

    // Process Uploaded Files
    $uploaded_files = [];
    $original_file_names = [];
    $upload_errors = [];

    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'docx', 'doc', 'txt', 'pptx', 'xlsx', 'csv', 'zip', 'rar', 'mp4', 'avi', 'mov', 'mkv', 'webm'];
    $max_file_size = 5 * 1024 * 1024; // 5MB
    $upload_dir = '../../../../assets/files/';

    // Ensure upload directory exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (!empty($_FILES['uploaded_files']['name'][0])) {
        foreach ($_FILES['uploaded_files']['name'] as $key => $original_name) {
            $file_tmp = $_FILES['uploaded_files']['tmp_name'][$key];
            $file_size = $_FILES['uploaded_files']['size'][$key];
            $file_ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

            if (!in_array($file_ext, $allowed_extensions)) {
                $upload_errors[] = "نوع الملف غير مدعوم: $original_name";
                continue;
            }

            if ($file_size > $max_file_size) {
                $upload_errors[] = "حجم الملف كبير جداً: $original_name";
                continue;
            }

            $random_name = uniqid('file_', true) . '.' . $file_ext;
            $destination = "$upload_dir$random_name";

            if (is_uploaded_file($file_tmp)) {
                if (move_uploaded_file($file_tmp, $destination)) {
                    $uploaded_files[] = $random_name;
                    $original_file_names[] = htmlspecialchars($original_name);
                } else {
                    $upload_errors[] = "فشل رفع الملف: $original_name";
                }
            } else {
                $upload_errors[] = "ملف غير صالح: $original_name";
            }
        }

        if (!empty($upload_errors)) {
            sendJsonResponse("warning", implode(" | ", $upload_errors), 400);
        }
    }

    $uploaded_files_json = json_encode($uploaded_files);
    $original_files_json = json_encode($original_file_names);

    // إدخال البيانات في قاعدة البيانات
    $stmt = $conn->prepare("INSERT INTO community (user_id, subject, title, content, badges, uploaded_files, original_file_names) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $user_id, $subject, $title, $content, $badge_json, $uploaded_files_json, $original_files_json);
    $stmt->execute();
    $stmt->close();

    sendJsonResponse("success", "تم إرسال المنشور", 200);
}