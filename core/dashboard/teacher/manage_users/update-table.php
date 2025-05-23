<?php
require_once '../auth.php';

// الحصول على مدخلات البحث
$searchInput = trim($_GET['searchInput'] ?? '');
$searchTerms = array_filter(explode(' ', $searchInput), 'strlen');

// إعداد الاستعلام الأساسي
$sql = "SELECT ast.student_id, u.first_name, u.last_name, u.phone_number, u.created_at, s.guardian_phone, u.gender, s.level
        FROM assigned_students ast
        JOIN users u ON ast.student_id = u.id
        JOIN students s ON ast.student_id = s.id
        WHERE ast.teacher_id = ?";

// إضافة شروط البحث إذا كانت المدخلات موجودة
if (!empty($searchTerms)) {
    $searchConditions = [];
    foreach ($searchTerms as $term) {
        $searchConditions[] = "(ast.student_id LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR u.phone_number LIKE ? OR s.guardian_phone LIKE ? OR u.gender LIKE ? OR s.level LIKE ?)";
    }
    $sql .= " AND (" . implode(' OR ', $searchConditions) . ")";
}

// تحضير الاستعلام
$stmt = $conn->prepare($sql);

// إعداد المتغيرات
$params = [$user_id];
$paramTypes = "i";

// إضافة متغيرات البحث
if (!empty($searchTerms)) {
    foreach ($searchTerms as $term) {
        $likeTerm = "%$term%";
        $params = array_merge($params, array_fill(0, 7, $likeTerm));
        $paramTypes .= str_repeat("s", 7);
    }
}

// ربط المعاملات
$stmt->bind_param($paramTypes, ...$params);

// تنفيذ الاستعلام
$stmt->execute();
$result = $stmt->get_result();

// عرض النتائج
if ($result->num_rows > 0) {
    echo '<div class="table-responsive">';
    echo '<table class="table table-striped align-middle">';
    echo '<thead>';
    echo '<tr>';
    echo '<td class="text-truncate">الرقم التعريفي</td>';
    echo '<td class="text-truncate">الاسم الأول</td>';
    echo '<td class="text-truncate">الاسم الأخير</td>';
    echo '<td class="text-truncate">رقم الهاتف</td>';
    echo '<td class="text-truncate">رقم ولي الأمر</td>';
    echo '<td class="text-truncate">الجنس</td>';
    echo '<td class="text-truncate">الصف الدراسي</td>';
    echo '<td class="text-truncate">تاريخ التسجيل</td>';
    echo '<td class="text-truncate">إدارة الطالب</td>';
    echo '<td class="text-truncate">حذف الطالب</td>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';

    while ($row = $result->fetch_assoc()) {
        $row["gender"] = match ($row["gender"]) {
            'male' => "ذكر",
            'female' => "أنثى",
            default => "توجد مشكلة بالنوع",
        };
        $row["level"] = match ($row["level"]) {
            'first' => "الأول",
            'second' => "الثاني",
            'third' => "الثالث",
            default => "توجد مشكلة بالصف الدراسي",
        };
        echo '<tr>';
        echo '<td>' . htmlspecialchars($row["student_id"]) . '</td>';
        echo '<td>' . htmlspecialchars($row["first_name"]) . '</td>';
        echo '<td>' . htmlspecialchars($row["last_name"]) . '</td>';
        echo '<td>' . htmlspecialchars($row["phone_number"]) . '</td>';
        echo '<td>' . htmlspecialchars($row["guardian_phone"]) . '</td>';
        echo '<td>' . htmlspecialchars($row["gender"]) . '</td>';
        echo '<td>' . htmlspecialchars($row["level"]) . '</td>';
        echo '<td>' . htmlspecialchars($row["created_at"]) . '</td>';
        echo '<td><a href="show-student-details.php?id=' . $row['student_id'] . '" class="btn btn-default w-100">إدارة</a></td>';
        echo '<td><a href="delete-student.php?id=' . $row["student_id"] . '" class="btn btn-danger w-100">حذف</a></td>';
        echo '</tr>';
    }

    echo '</tbody>';
    echo '</table>';
    echo '</div>';
} else {
    echo '<h5 class="text-danger text-center">لا توجد نتائج</h5>';
}

$stmt->close();