<?php
require '../auth.php';

// التحقق من استعلام البحث
$query = isset($_POST['query']) ? trim($_POST['query']) : '';

$searchTerms = [];
$whereClause = [];
$params = [];

if (!empty($query)) {
    // تقسيم استعلام البحث إلى كلمات
    $searchTerms = array_filter(explode(' ', $query), fn($term) => !empty($term));
    
    // بناء جزء WHERE ديناميكي لكل كلمة
    foreach ($searchTerms as $term) {
        $likeTerm = "%$term%";
        $whereClause[] = "(users.first_name LIKE ? OR users.last_name LIKE ? OR requests.status LIKE ? OR users.phone_number LIKE ? OR students.guardian_phone LIKE ? OR students.id LIKE ?)";
        array_push($params, $likeTerm, $likeTerm, $likeTerm, $likeTerm, $likeTerm, $likeTerm);
    }
    $whereSql = " AND (" . implode(' AND ', $whereClause) . ")";
} else {
    $whereSql = ''; // إذا كان شريط البحث فارغًا، عرض جميع الطلبات
}

// إعداد استعلام SQL
$sql = "SELECT requests.student_id, users.first_name, users.last_name, users.phone_number, students.guardian_phone, students.level, requests.status
        FROM requests
        JOIN students ON requests.student_id = students.id
        JOIN users ON students.id = users.id
        WHERE requests.teacher_id = ? $whereSql";

$stmt = $conn->prepare($sql);

// إعداد المعاملات
if (!empty($query)) {
    $paramTypes = str_repeat('s', count($params));
    $stmt->bind_param("i$paramTypes", $user_id, ...$params);
} else {
    $stmt->bind_param('i', $user_id);
}

// تنفيذ الاستعلام
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo '<table class="table table-hover mb-0">';
    echo '<thead class="text-center">';
    echo '<tr class="table-active">';
    echo '<th scope="col" class="text-secondary">#</th>';
    echo '<th scope="col" class="text-secondary">المعرف</th>';
    echo '<th scope="col" class="text-start text-secondary">الاسم</th>';
    echo '<th scope="col" class="text-secondary">المرحلة</th>';
    echo '<th scope="col" class="text-secondary">رقم الهاتف</th>';
    echo '<th scope="col" class="text-secondary">هاتف ولي الأمر</th>';
    echo '<th scope="col" class="text-secondary">حالة الطلب</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody class="text-center">';

    $counter = 1;
    while ($row = $result->fetch_assoc()) {
        $levelTranslation = match ($row['level']) {
            'first' => 'الأول الثانوي',
            'second' => 'الثاني الثانوي',
            'third' => 'الثالث الثانوي',
            default => 'غير معروف',
        };

        $student_id = htmlspecialchars($row['student_id']);
        $studentName = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
        echo "<tr data-student-id=\"$student_id\" onclick=\"openModal('$student_id', '$studentName')\">";
        echo "<td>$counter</td>";
        echo "<td>$student_id</td>";
        echo "<td class='text-start'>$studentName</td>";
        echo "<td>" . htmlspecialchars($levelTranslation) . "</td>";
        echo "<td>" . htmlspecialchars($row['phone_number']) . "</td>";
        echo "<td>" . htmlspecialchars($row['guardian_phone']) . "</td>";
        echo "<td><span class='badge text-bg-" . htmlspecialchars($row['status']) . "'>" . translateStatus($row['status']) . "</span></td>";
        echo "</tr>";
        $counter++;
    }

    echo '</tbody>';
    echo '</table>';
} else {
    echo "<p class='text-center text-secondary m-2'>لا توجد نتائج مطابقة.</p>";
}

$stmt->close();
$conn->close();

// دالة لترجمة حالة الطلب
function translateStatus($status) {
    return match ($status) {
        'New' => 'جديد',
        'Pending' => 'معلَّق',
        'Accepted' => 'مقبول',
        'Rejected' => 'مرفوض',
        default => 'غير معروف',
    };
}