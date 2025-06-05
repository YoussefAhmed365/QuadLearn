<?php
require '../auth.php'; // Ensure authentication is included
header('Content-Type: application/json; charset=UTF-8');

// Function to send JSON response
function sendJsonResponse($status, $message, $data = [], $httpStatusCode = 200)
{
    http_response_code($httpStatusCode);
    echo json_encode(["status" => $status, "message" => $message, "data" => $data]);
    exit;
}

// Get data from the form
$postId = $_POST['post_id'] ?? null;
$title = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

// Badges are now expected as a JSON string [{name: "...", color: "..."}, ...]
$badges_json_input = $_POST['badges'] ?? '[]';
$decoded_badges = json_decode($badges_json_input, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    sendJsonResponse("error", "بيانات الشارات غير صالحة (JSON).", 400);
}


// Files to delete are expected as a JSON string of original file names ["file1.pdf", "image.jpg"]
$files_to_delete_json = $_POST['files_to_delete'] ?? '[]';
$files_to_delete_input = json_decode($files_to_delete_json, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    sendJsonResponse("error", "بيانات الملفات المراد حذفها غير صالحة (JSON).", 400);
}
if (!is_array($files_to_delete_input)) {
    $files_to_delete_input = []; // Fallback
}


if (empty($postId) || empty($content)) {
    sendJsonResponse("error", "البيانات غير مكتملة أو المحتوى فارغ.", 400);
}

// Fetch current post data to verify ownership and get current file list
$stmt_fetch = $conn->prepare("SELECT user_id, uploaded_files, original_file_names FROM community WHERE id = ?");
if (!$stmt_fetch) {
    sendJsonResponse("error", "خطأ في تجهيز الاستعلام لجلب المنشور: " . $conn->error, 500);
}
$stmt_fetch->bind_param("i", $postId);
$stmt_fetch->execute();
$result_fetch = $stmt_fetch->get_result();
$current_data = $result_fetch->fetch_assoc();
$stmt_fetch->close();

if (!$current_data) {
    sendJsonResponse("error", "المنشور غير موجود.", 404);
}

if ($current_data['user_id'] != $user_id) {
    sendJsonResponse("error", "غير مصرح لك بتعديل هذا المنشور.", 403);
}

$current_uploaded_files_json = $current_data['uploaded_files'];
$current_original_names_json = $current_data['original_file_names'];

// Process Badges
$badges_for_json_db = [];
if (is_array($decoded_badges)) {
    foreach ($decoded_badges as $badge_item) {
        if (!empty($badge_item['name'])) {
            $name = htmlspecialchars(trim($badge_item['name']));
            $color = isset($badge_item['color']) && is_string($badge_item['color']) ? htmlspecialchars(trim($badge_item['color'])) : 'green'; // Default to 'green'
            $badges_for_json_db[] = ['name' => $name, 'color' => $color];
        }
    }
}
$badge_json_to_save_db = !empty($badges_for_json_db) ? json_encode($badges_for_json_db) : NULL;


// File Handling
$upload_dir = '../../../../assets/files/'; // Make sure this path is correct from this script's location
$db_server_files = json_decode($current_uploaded_files_json, true) ?: [];
$db_original_names = json_decode($current_original_names_json, true) ?: [];

$final_server_files_to_save = [];
$final_original_names_to_save = [];
$upload_errors = [];

// 1. Handle deletions:
// Iterate through existing files and keep only those NOT in $files_to_delete_input
if (!empty($db_original_names)) {
    foreach ($db_original_names as $index => $original_name) {
        if (isset($db_server_files[$index])) { // Ensure server file exists for this original name
            $server_file_name = $db_server_files[$index];
            if (in_array($original_name, $files_to_delete_input)) {
                // This file is marked for deletion
                $file_to_delete_path = $upload_dir . $server_file_name;
                if (file_exists($file_to_delete_path)) {
                    if (!@unlink($file_to_delete_path)) {
                        $upload_errors[] = "فشل حذف الملف من الخادم: $original_name";
                    }
                }
            } else {
                // This file is to be kept
                $final_server_files_to_save[] = $server_file_name;
                $final_original_names_to_save[] = $original_name;
            }
        }
    }
}


// 2. Handle new uploads:
$allowed_extensions = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'docx', 'doc', 'txt', 'pptx', 'xlsx', 'csv', 'zip', 'rar', 'mp4', 'avi', 'mov', 'mkv', 'webm'];
$max_file_size = 5 * 1024 * 1024; // 5MB

if (isset($_FILES['new_uploaded_files']) && !empty($_FILES['new_uploaded_files']['name'][0])) {
    foreach ($_FILES['new_uploaded_files']['name'] as $key => $original_name_new) {
        if (empty(trim($original_name_new))) continue;

        $file_tmp = $_FILES['new_uploaded_files']['tmp_name'][$key];
        $file_size = $_FILES['new_uploaded_files']['size'][$key];
        $file_error = $_FILES['new_uploaded_files']['error'][$key];
        $file_ext_new = strtolower(pathinfo($original_name_new, PATHINFO_EXTENSION));
        // Sanitize original name for saving, but store the actual original name for display if needed
        $safe_original_name_new = htmlspecialchars(trim($original_name_new));


        if ($file_error !== UPLOAD_ERR_OK) {
            $upload_errors[] = "خطأ في رفع الملف '$safe_original_name_new': " . upload_error_message($file_error);
            continue;
        }
        if (!in_array($file_ext_new, $allowed_extensions)) {
            $upload_errors[] = "نوع الملف غير مدعوم: $safe_original_name_new";
            continue;
        }
        if ($file_size > $max_file_size) {
            $upload_errors[] = "حجم الملف كبير جداً: $safe_original_name_new (الحد الأقصى 5MB)";
            continue;
        }

        // Create a more unique random name
        $random_name_new = uniqid('file_', true) . '_' . bin2hex(random_bytes(4)) . '.' . $file_ext_new;
        $destination_new = $upload_dir . $random_name_new;

        if (move_uploaded_file($file_tmp, $destination_new)) {
            $final_server_files_to_save[] = $random_name_new;
            $final_original_names_to_save[] = $safe_original_name_new;
        } else {
            $upload_errors[] = "فشل نقل الملف المرفوع: $safe_original_name_new";
        }
    }
}

$final_uploaded_files_json_db = !empty($final_server_files_to_save) ? json_encode($final_server_files_to_save) : NULL;
$final_original_names_json_db = !empty($final_original_names_to_save) ? json_encode($final_original_names_to_save) : NULL;

// Update Database
$stmt_update = $conn->prepare("UPDATE community SET title = ?, content = ?, badges = ?, uploaded_files = ?, original_file_names = ?, updated_at = NOW() WHERE id = ? AND user_id = ?");
if (!$stmt_update) {
    sendJsonResponse("error", "خطأ في تجهيز استعلام التحديث: " . $conn->error, 500);
}
$stmt_update->bind_param("sssssii", $title, $content, $badge_json_to_save_db, $final_uploaded_files_json_db, $final_original_names_json_db, $postId, $user_id);

if ($stmt_update->execute()) {
    $message = "تم تحديث المنشور بنجاح.";
    if (!empty($upload_errors)) {
        $message .= " ملاحظات: " . implode("; ", $upload_errors);
        sendJsonResponse("warning", $message, 200);
    } else {
        sendJsonResponse("success", $message, 200);
    }
} else {
    sendJsonResponse("error", "حدث خطأ أثناء تحديث المنشور في قاعدة البيانات: " . $stmt_update->error, 500);
}
$stmt_update->close();
$conn->close();


// Helper function for upload errors
function upload_error_message($error_code)
{
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
            return 'الملف يتجاوز الحد المسموح به في php.ini.';
        case UPLOAD_ERR_FORM_SIZE:
            return 'الملف يتجاوز الحد المسموح به في النموذج.';
        case UPLOAD_ERR_PARTIAL:
            return 'تم رفع جزء من الملف فقط.';
        case UPLOAD_ERR_NO_FILE:
            return 'لم يتم رفع أي ملف.';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'مجلد الملفات المؤقت مفقود.';
        case UPLOAD_ERR_CANT_WRITE:
            return 'فشل الكتابة إلى القرص.';
        case UPLOAD_ERR_EXTENSION:
            return 'أحد الإضافات أوقف عملية الرفع.';
        default:
            return 'خطأ غير معروف في عملية الرفع.';
    }
}