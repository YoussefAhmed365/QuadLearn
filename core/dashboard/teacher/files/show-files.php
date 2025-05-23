<?php
require '../auth.php';

// إعداد الاستعلام بناءً على نوع المستخدم
if ($user['account_type'] === "teacher") {
    // في حالة كان المستخدم "معلم"
    $query = "
        (
            SELECT 
                subject_files.*, 
                users.first_name, 
                users.last_name, 
                users.id AS teacher_id
            FROM assigned_assistants
            JOIN subject_files ON subject_files.teacher_id = assigned_assistants.assistant_id
            JOIN users ON users.id = assigned_assistants.assistant_id
            WHERE assigned_assistants.teacher_id = ?
        )
        UNION
        (
            SELECT 
                subject_files.*, 
                users.first_name, 
                users.last_name, 
                users.id AS teacher_id
            FROM subject_files
            JOIN users ON subject_files.teacher_id = users.id
            WHERE users.id = ?
        )
        ORDER BY created_at DESC LIMIT 4;
    ";
} elseif ($user['account_type'] === "assistant") {
    // في حالة كان المستخدم "مساعد"
    $query = "
        SELECT 
            subject_files.id AS file_id,
            subject_files.unique_file AS unique_file,
            subject_files.file_name AS file_name,
            subject_files.created_at AS file_created_at,
            subject_files.updated_at AS file_updated_at,
            users.id AS teacher_id,
            users.first_name AS teacher_first_name,
            users.last_name AS teacher_last_name
        FROM subject_files
        JOIN users ON subject_files.teacher_id = users.id
        WHERE subject_files.teacher_id = ?
           OR subject_files.teacher_id IN (
               SELECT teacher_id 
               FROM assigned_assistants 
               WHERE assistant_id = ?
           )
        ORDER BY subject_files.created_at DESC LIMIT 4;
    ";
}

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

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