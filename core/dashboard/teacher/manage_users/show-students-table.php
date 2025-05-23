<?php
require_once '../auth.php';

$searchInput = trim($_GET['searchInput'] ?? '');

$searchTerms = array_filter(explode(' ', $searchInput), 'strlen');

$genderMap = [
    'male' => 'ذكر',
    'female' => 'أنثى'
];
$levelMap = [
    'first' => 'الأولى',
    'second' => 'الثانية',
    'third' => 'الثالثة'
];

$sql = "SELECT
            ast.student_id,
            u.first_name,
            u.last_name,
            u.phone_number,
            u.created_at,
            s.guardian_phone,
            u.gender,
            s.level
        FROM
            assigned_students ast
        JOIN
            users u ON ast.student_id = u.id
        JOIN
            students s ON ast.student_id = s.id
        WHERE
            ast.teacher_id = ?";

$params = [$user_id];
$paramTypes = "i";

if (!empty($searchTerms)) {
    $sql .= " AND (";
    $termConditions = [];

    foreach ($searchTerms as $term) {
        $likeTerm = "%" . $conn->real_escape_string($term) . "%";
        $currentTermConditions = [
            "ast.student_id LIKE ?",
            "u.first_name LIKE ?",
            "u.last_name LIKE ?",
            "u.phone_number LIKE ?",
            "s.guardian_phone LIKE ?",
        ];

        $currentTermParams = [$likeTerm, $likeTerm, $likeTerm, $likeTerm, $likeTerm];
        $currentTermTypes = str_repeat("s", 5);

        // --- Handle Gender Search (checking original term and mapped English) ---
        $currentTermConditions[] = "u.gender LIKE ?";
        $currentTermParams[] = $likeTerm;
        $currentTermTypes .= "s";
        // Check if the term matches an Arabic gender keyword
        if (isset($genderMap[$term])) {
            $currentTermConditions[] = "u.gender LIKE ?";
            $currentTermParams[] = "%" . $genderMap[$term] . "%";
            $currentTermTypes .= "s";
        }

        // --- Handle Level Search (checking original term and mapped English) ---
        $currentTermConditions[] = "s.level LIKE ?";
        $currentTermParams[] = $likeTerm;
        $currentTermTypes .= "s";
        // Check if the term matches an Arabic level keyword
        if (isset($levelMap[$term])) {
            $currentTermConditions[] = "s.level LIKE ?";
            $currentTermParams[] = "%" . $levelMap[$term] . "%";
            $currentTermTypes .= "s";
        }

        // Combine all conditions for the current term using OR
        $termConditions[] = "(" . implode(' OR ', $currentTermConditions) . ")";

        // Merge the parameters and types for the current term into the main arrays
        $params = array_merge($params, $currentTermParams);
        $paramTypes .= $currentTermTypes;
    }

    // Combine the conditions for all search terms using AND
    $sql .= implode(' AND ', $termConditions);
    $sql .= ")";
}

$sql .= " ORDER BY u.first_name, u.last_name";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    error_log("MySQL prepare failed: {$conn->error}");
    echo '<h5 class="text-danger text-center">حدث خطأ في قاعدة البيانات أثناء إعداد الاستعلام.</h5>';
    exit();
}

if ($stmt->bind_param($paramTypes, ...$params) === false) {
    error_log("MySQL bind_param failed: {$stmt->error}");
    echo '<h5 class="text-danger text-center">حدث خطأ في ربط المعاملات للاستعلام.</h5>';
    $stmt->close();
    exit();
}

if ($stmt->execute() === false) {
    error_log("MySQL execute failed: {$stmt->error}");
    echo '<h5 class="text-danger text-center">حدث خطأ أثناء جلب بيانات الطلاب.</h5>';
    $stmt->close();
    exit();
}

$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-striped align-middle" id="studentsTable">';
    echo '<thead>';
    echo '<tr>';
    echo '<td class="text-truncate">الرقم التعريفي</td>';
    echo '<td class="text-truncate">الاسم الأول</td>';
    echo '<td class="text-truncate">الاسم الأخير</td>';
    echo '<td class="text-truncate">رقم الهاتف</td>';
    echo '<td class="text-truncate">رقم ولي الأمر</td>';
    echo '<td class="text-truncate">الجنس</td>';
    echo '<td class="text-truncate">المرحلة الدراسية</td>';
    echo '<td class="text-truncate">تاريخ الإضافة</td>';
    echo '<td class="text-truncate">حذف الطالب</td>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    while ($row = $result->fetch_assoc()) {
        $gender = $genderMap[$row["gender"]] ?? "غير محدد";
        $level = $levelMap[$row["level"]] ?? "غير محدد";

        echo '<tr data-id="' . htmlspecialchars($row["student_id"]) . '" data-name="' . htmlspecialchars($row["first_name"] . ' ' . $row["last_name"]) . '">';
        echo '<td>' . htmlspecialchars($row["student_id"]) . '</td>';
        echo '<td>' . htmlspecialchars($row["first_name"]) . '</td>';
        echo '<td>' . htmlspecialchars($row["last_name"]) . '</td>';
        echo '<td>' . htmlspecialchars($row["phone_number"]) . '</td>';
        echo '<td>' . htmlspecialchars($row["guardian_phone"]) . '</td>';
        echo '<td>' . htmlspecialchars($gender) . '</td>';
        echo '<td>' . htmlspecialchars($level) . '</td>';
        echo '<td>' . htmlspecialchars($row["created_at"]) . '</td>';
        echo '<td><button class="btn btn-danger w-100 delete-btn" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" data-id="' . htmlspecialchars($row["student_id"]) . '" data-name="' . htmlspecialchars($row["first_name"] . ' ' . $row["last_name"]) . '" data-type="الطالب">حذف</button></td>';
        echo '</tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';
} else {
    if (empty($searchInput)) {
        echo '<h5 class="text-danger text-center">لا يوجد طلاب مسجلين.</h5>'; // Message for no assigned students
    } else {
        echo '<h5 class="text-danger text-center">لا توجد نتائج مطابقة لمدخلات البحث.</h5>'; // Message for no search results
    }
}

$stmt->close();