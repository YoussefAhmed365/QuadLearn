<?php
require '../auth.php'; // Ensure this path is correct and $user_id, $conn, and potentially $data['subject'] are available.

header("Content-Type: application/json; charset=utf-8");

// Function to send JSON response
function sendJsonResponse($status, $message, $httpStatusCode = 200) {
    http_response_code($httpStatusCode);
    echo json_encode(["status" => $status, "message" => $message]);
    exit;
}

// Helper function for more descriptive upload errors
function upload_error_message($error_code) {
    switch ($error_code) {
        case UPLOAD_ERR_OK:
            return ''; // No error
        case UPLOAD_ERR_INI_SIZE:
            return 'الملف يتجاوز الحد المسموح به في php.ini.';
        case UPLOAD_ERR_FORM_SIZE:
            return 'الملف يتجاوز الحد المسموح به في النموذج.';
        case UPLOAD_ERR_PARTIAL:
            return 'تم رفع جزء من الملف فقط.';
        case UPLOAD_ERR_NO_FILE:
            return 'لم يتم رفع أي ملف.'; // This might be a valid case if files are optional
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'مجلد الملفات المؤقت مفقود على الخادم.';
        case UPLOAD_ERR_CANT_WRITE:
            return 'فشل الكتابة إلى القرص على الخادم.';
        case UPLOAD_ERR_EXTENSION:
            return 'أحد الإضافات في الخادم أوقف عملية الرفع.';
        default:
            return 'خطأ غير معروف في عملية الرفع.';
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = isset($_POST['title']) ? trim($_POST['title']) : NULL;
    $content = isset($_POST['content']) ? trim($_POST['content']) : NULL;
    
    // Badges are now expected as a JSON string: [{"name":"...","color":"..."}, ...]
    $badges_json_input = $_POST['badges'] ?? '[]'; // Default to an empty JSON array string
    $decoded_badges = json_decode($badges_json_input, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        sendJsonResponse("error", "بيانات الشارات غير صالحة (JSON format error).", 400);
    }
    
    if (empty($content)) { // Changed from $content == NULL to also catch empty strings after trim
        sendJsonResponse("error", "يجب إدخال محتوى المنشور.", 400);
    }

    $badges_to_save_db = [];
    if (is_array($decoded_badges)) {
        foreach ($decoded_badges as $badge_item) {
            if (!empty($badge_item['name'])) {
                $name = htmlspecialchars(trim($badge_item['name']));
                $color = isset($badge_item['color']) && is_string($badge_item['color']) 
                         ? htmlspecialchars(trim($badge_item['color'])) 
                         : 'green'; // Default color
                $badges_to_save_db[] = ['name' => $name, 'color' => $color];
            }
        }
    }
    $badge_json_for_db = !empty($badges_to_save_db) ? json_encode($badges_to_save_db) : NULL;

    // Process Uploaded Files
    $uploaded_server_files = [];
    $original_file_names_for_db = [];
    $upload_errors = [];

    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'docx', 'doc', 'txt', 'pptx', 'xlsx', 'csv', 'zip', 'rar', 'mp4', 'avi', 'mov', 'mkv', 'webm'];
    $max_file_size = 5 * 1024 * 1024; // 5MB
    $upload_dir = '../../../../assets/files/'; // Ensure this path is correct

    // Ensure upload directory exists and is writable
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0775, true)) { // Changed permissions to be more standard
            sendJsonResponse("error", "فشل في إنشاء مجلد الرفع على الخادم.", 500);
        }
    }
    if (!is_writable($upload_dir)){
        sendJsonResponse("error", "مجلد الرفع على الخادم غير قابل للكتابة.", 500);
    }


    if (isset($_FILES['uploaded_files']) && !empty($_FILES['uploaded_files']['name'][0])) {
        foreach ($_FILES['uploaded_files']['name'] as $key => $original_name) {
            if (empty(trim($original_name))) continue; // Skip empty file inputs

            $file_tmp = $_FILES['uploaded_files']['tmp_name'][$key];
            $file_size = $_FILES['uploaded_files']['size'][$key];
            $file_error = $_FILES['uploaded_files']['error'][$key];
            
            $error_message = upload_error_message($file_error);
            if ($error_message && $file_error !== UPLOAD_ERR_NO_FILE) { // UPLOAD_ERR_NO_FILE is ok if multiple files are optional
                $upload_errors[] = htmlspecialchars($original_name) . ": " . $error_message;
                continue;
            }
            if($file_error === UPLOAD_ERR_NO_FILE) continue; // Skip if no file was actually uploaded for this input slot


            $file_ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
            $safe_original_name = htmlspecialchars(trim($original_name)); // Sanitize original name for DB

            if (!in_array($file_ext, $allowed_extensions)) {
                $upload_errors[] = "نوع الملف غير مدعوم: " . $safe_original_name;
                continue;
            }

            if ($file_size > $max_file_size) {
                $upload_errors[] = "حجم الملف كبير جداً (" . round($file_size / 1024 / 1024, 2) . "MB): " . $safe_original_name . " (الحد الأقصى 5MB)";
                continue;
            }
            
            // Generate a more unique random name
            $random_server_name = uniqid('file_', true) . '_' . bin2hex(random_bytes(4)) . '.' . $file_ext;
            $destination = $upload_dir . $random_server_name;

            if (move_uploaded_file($file_tmp, $destination)) {
                $uploaded_server_files[] = $random_server_name;
                $original_file_names_for_db[] = $safe_original_name;
            } else {
                $upload_errors[] = "فشل في نقل الملف المرفوع: " . $safe_original_name;
            }
        }
    }

    // Check if there were critical upload errors that should prevent DB insertion
    // For now, we send a warning if some files failed but still proceed.
    // If no files are a hard requirement, this is fine.
    // If files are required, you might want to sendJsonResponse("error", ...) here.

    $uploaded_files_json_for_db = !empty($uploaded_server_files) ? json_encode($uploaded_server_files) : NULL;
    $original_files_json_for_db = !empty($original_file_names_for_db) ? json_encode($original_file_names_for_db) : NULL;

    // --- Database Insertion ---
    // IMPORTANT: Ensure $data['subject'] is available from your auth.php or other included files.
    // If 'subject' is not a field in your 'community' table or is handled differently, adjust the SQL and bind_param.
    $subject_for_db = isset($data['subject']) ? $data['subject'] : NULL; // Example: Get subject from $data array (e.g. from session)

    $stmt = $conn->prepare("INSERT INTO community (user_id, subject, title, content, badges, uploaded_files, original_file_names, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
    if (!$stmt) {
        // Log error: $conn->error
        sendJsonResponse("error", "خطأ في تجهيز استعلام قاعدة البيانات.", 500);
    }
    
    // The type for subject 's' might need to change if it's an ID (integer 'i')
    $stmt->bind_param("issssss", $user_id, $subject_for_db, $title, $content, $badge_json_for_db, $uploaded_files_json_for_db, $original_files_json_for_db);
    
    if ($stmt->execute()) {
        $final_message = "تم نشر المنشور بنجاح.";
        if (!empty($upload_errors)) {
            $final_message .= " بعض الملفات لم يتم رفعها: " . implode("; ", $upload_errors);
            sendJsonResponse("warning", $final_message, 200); // HTTP 200 for success with warnings
        } else {
            sendJsonResponse("success", $final_message, 201); // HTTP 201 for resource created
        }
    } else {
        // Log error: $stmt->error
        sendJsonResponse("error", "حدث خطأ أثناء حفظ المنشور في قاعدة البيانات.", 500);
    }
    $stmt->close();
    $conn->close();

} else {
    sendJsonResponse("error", "طلب غير صالح.", 405); // Method Not Allowed
}