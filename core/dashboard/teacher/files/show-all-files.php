<?php
require '../auth.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data['teacherClause']) || !isset($data['assistantClause']) || !isset($data['operands'])) {
    http_response_code(400);
    die("Invalid input");
}

$teacherClause = $data['teacherClause'];
$assistantClause = $data['assistantClause'];
$operands = $data['operands'];

$query = '';
if ($user['account_type'] === "teacher") {
    $query = $teacherClause;
} elseif ($user['account_type'] === "assistant") {
    $query = $assistantClause;
} else {
    echo "<script>alert('يجب تسجيل الدخول مجدداً');</script>";
    http_response_code(403);
    header("Location: logout.php");
}

if (!$query) {
    http_response_code(400);
    die("No query generated for the current user type.");
}

$stmt = $conn->prepare($query);
if (!$stmt) {
    die(json_encode(["status" => "error", "message" => "Failed to prepare query.", "error" => $conn->error]));
}

switch ($operands) {
    case 2:
        $stmt->bind_param("ii", $user_id, $user_id);
        break;
    case 1:
        $stmt->bind_param("i", $user_id);
        break;

    default:
        echo "توجد مشكلة حالياً في التصنيفات";
        http_response_code(400);
        die("Invalid operands value.");
}

if (!$stmt->execute()) {
    die(json_encode(["status" => "error", "message" => "Failed to execute query.", "error" => $stmt->error]));
}

$result = $stmt->get_result();
if ($result === false) {
    die(json_encode(["status" => "error", "message" => "Query execution failed.", "error" => $stmt->error]));
}


if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $file_url = "../../../../assets/subject_files/{$row['unique_file']}";
        $file_name = $row['file_name'];
        $created_at = $row['created_at'];
        $updated_at = $row['updated_at'];
        $teacher_name = $row['first_name'] . ' ' . $row['last_name'];
        $teacher_id = $row['teacher_id'];

        // تحديد مسار صورة الملف الشخصي
        $target_dir = "../../../../assets/images/profiles/";
        $profile_image = is_file("$target_dir$teacher_id.webp") ? "$target_dir$teacher_id.webp" : "{$target_dir}default.png";

        // تحويل التاريخ إلى صيغة عربية
        $months = [
            'January' => 'يناير',
            'February' => 'فبراير',
            'March' => 'مارس',
            'April' => 'أبريل',
            'May' => 'مايو',
            'June' => 'يونيو',
            'July' => 'يوليو',
            'August' => 'أغسطس',
            'September' => 'سبتمبر',
            'October' => 'أكتوبر',
            'November' => 'نوفمبر',
            'December' => 'ديسمبر'
        ];

        $englishCreated = date('F', strtotime($created_at));
        $arabicCreated = $months[$englishCreated];
        $createDate = $arabicCreated . date(' j, Y', strtotime($created_at));
        
        $englishUpdated = date('F', strtotime($updated_at));
        $arabicUpdated = $months[$englishUpdated];
        $updateDate = $arabicUpdated . date(' j, Y', strtotime($updated_at));

        echo "
        <tr>
            <td class='text-truncate'><input type='checkbox' class='form-check-input' id='fileCheckbox{$row['id']}'></td>
            <td class='text-truncate'>
                <div class='d-flex justify-content-start align-items-center gap-3'>
                    <div class='table-file bg-white rounded d-flex justify-content-center align-items-center'>
                        <i class='fa-regular fa-file'></i>
                    </div>
                    <a href='$file_url' class='text-decoration-none text-dark' download>$file_name</a>
                </div>
            </td>
            <td class='text-truncate'>$createDate</td>
            <td class='text-truncate'>$updateDate</td>
            <td class='text-truncate'>
                <div class='d-flex justify-content-start align-items-center gap-3'>
                    <div><img src='$profile_image' alt='Profile Photo' class='rounded-circle' width='50' height='50'></div>
                    <span>$teacher_name</span>
                </div>
            </td>
            <td class='text-truncate text-center'>
                <button class='btn btn-light' data-bs-toggle='modal' data-bs-target='#editModal' 
                    data-file-id='{$row['id']}' data-file-name='$file_name'>
                    تعديل
                </button>
                <button class='btn btn-light' data-bs-toggle='modal' data-bs-target='#deleteModal' 
                    data-file-id='{$row['id']}'>
                    حذف
                </button>
            </td>
        </tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center'>لا توجد ملفات لعرضها</td></tr>";
}

$stmt->close();