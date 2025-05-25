<?php
require '../auth.php';

$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data['limit'])) {
    http_response_code(400);
    die("Invalid input");
}

//Corrected Line: Added space before LIMIT clause
$limitClause = $data['limit'] ?? "";

$stmt = $conn->prepare("SELECT subject_files.*
                        FROM subject_files
                        LEFT JOIN assigned_teachers ON assigned_teachers.teacher_id = subject_files.teacher_id
                                                         AND assigned_teachers.student_id = ?
                        WHERE assigned_teachers.student_id IS NULL OR assigned_teachers.student_id = ?
                        ORDER BY subject_files.created_at DESC {$limitClause}");

if (!$stmt) {
    die(json_encode(["status" => "error", "message" => "Failed to prepare query.", "error" => $conn->error]));
}

$stmt->bind_param("ii", $user_id, $user_id);

if (!$stmt->execute()) {
    die(json_encode(["status" => "error", "message" => "Failed to execute query.", "error" => $stmt->error]));
}

$result = $stmt->get_result();
if ($result === false) {
    die(json_encode(["status" => "error", "message" => "Query execution failed.", "error" => $stmt->error]));
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $extension = pathinfo($row['file_name'], PATHINFO_EXTENSION);
        $fileIcon = match ($extension) {
            'pdf' => 'fa-regular fa-file-pdf',
            'jpg' => 'fa-regular fa-image',
            'jpeg' => 'fa-regular fa-image',
            'webp' => 'fa-regular fa-image',
            'png' => 'fa-regular fa-image',
            'doc' => 'fa-regular fa-file-word',
            'docx' => 'fa-regular fa-file-word',
            'csv' => 'fa-solid fa-file-csv',
            'xls' => 'fa-regular fa-file-excel',
            'xlsx' => 'fa-regular fa-file-excel',
            default => 'fa-regular fa-file'
        };
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

        $englishCreated = date('F', strtotime($row['created_at']));
        $arabicCreated = $months[$englishCreated];
        $createDate = $arabicCreated . date(' j, Y', strtotime($row['created_at']));
        echo '
            <div class="col-12 bg-white rounded-3 border border-secondary">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex justify-content-start align-items-center p-3 gap-3 text-truncate">
                        <i class="' . $fileIcon . ' fs-4" aria-hidden="true"></i>
                        <a href="#" class="text-default text-decoration-none" download>' . $row['file_name'] . '</a>
                    </div>
                    <h6 class="ms-3 mb-0 text-nowrap">' . $createDate . '</h6>
                </div>
            </div>
        ';
    }
    echo '<button class="show-all-btn btn btn-light border border-dark w-100 justify-content-center mt-3" id="showAllBtn">عرض الكل</button>';
} else {
    echo "<div class='col-12 text-center text-secondary fs-5'>لا توجد ملفات لعرضها</div>";
}

$stmt->close();