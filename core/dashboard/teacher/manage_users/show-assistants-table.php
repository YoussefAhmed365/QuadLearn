<?php
require_once '../auth.php'; // يفترض أن هذا الملف يقوم بتهيئة $conn و $user_id

// التحقق من أن $user_id مهيأ
if (!isset($user_id)) {
    echo '<h5 class="text-danger text-center">خطأ: لم يتم تهيئة المستخدم بشكل صحيح.</h5>';
    exit();
}
// التحقق من أن $conn مهيأ
if (!$conn) {
    echo '<h5 class="text-danger text-center">خطأ: لا يمكن الاتصال بقاعدة البيانات.</h5>';
    exit();
}

$sql = "SELECT aas.assistant_id, u.first_name, u.last_name
        FROM assigned_assistants aas
        JOIN users u ON aas.assistant_id = u.id
        WHERE aas.teacher_id = ?
        ORDER BY u.first_name, u.last_name";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    error_log("MySQL prepare failed: {$conn->error}");
    echo '<h5 class="text-danger text-center">حدث خطأ في قاعدة البيانات أثناء إعداد الاستعلام للمساعدين.</h5>';
    exit();
}

if ($stmt->bind_param("i", $user_id) === false) { // تم تعديل "i" لتطابق عدد المتغيرات
    error_log("MySQL bind_param failed: {$stmt->error}");
    echo '<h5 class="text-danger text-center">حدث خطأ في ربط المعاملات للاستعلام الخاص بالمساعدين.</h5>';
    $stmt->close();
    exit();
}

if ($stmt->execute() === false) {
    error_log("MySQL execute failed: {$stmt->error}");
    echo '<h5 class="text-danger text-center">حدث خطأ أثناء جلب بيانات المساعدين.</h5>';
    $stmt->close();
    exit();
}

$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo '<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">'; // بداية الصفوف
    while ($row = $result->fetch_assoc()) {
        $assistantId = htmlspecialchars($row['assistant_id']); // تأمين المخرجات
        $firstName = htmlspecialchars($row['first_name']);
        $lastName = htmlspecialchars($row['last_name']);
        $name = "{$firstName} {$lastName}";
        
        // تأمين مسار الصورة والتحقق من وجودها
        $profile_image_path = "../../../../assets/images/profiles/{$assistantId}.webp";
        $profile_image_default = "../../../../assets/images/profiles/default.png";
        $profile_image = file_exists($profile_image_path) ? $profile_image_path : $profile_image_default;

        // تمت إضافة data-id هنا إلى العنصر .col
        echo "<div class=\"col\" data-id=\"{$assistantId}\">";
        echo '<div class="card shadow-sm">'; // إضافة ظل خفيف للبطاقة
        echo '<div class="card-body">';
        echo '<div class="d-flex justify-content-between align-items-center mb-3">';
        echo '<div class="d-flex align-items-center gap-3">';
        echo "<img src=\"" . htmlspecialchars($profile_image) . "\" alt=\"Profile picture of {$name}\" class=\"rounded-circle\" width=\"50\" height=\"50\" style=\"object-fit: cover;\">";
        echo "<h5 class=\"card-title mb-0\">{$name}</h5>";
        echo '</div>';
        echo "<h6 class=\"text-secondary\">المعرف<br>{$assistantId}</h6>";
        echo '</div>';
        // استخدام data-bs-toggle و data-bs-target لفتح النافذة المنبثقة
        echo "<button class=\"btn btn-sm btn-danger delete-btn w-100\" data-bs-toggle=\"modal\" data-bs-target=\"#deleteConfirmationModal\" data-id=\"{$assistantId}\" data-name=\"{$name}\" data-type=\"المساعدين\">حذف</button>";
        echo '</div>'; // card-body
        echo '</div>'; // card
        echo '</div>'; // col
    }
    echo '</div>'; // نهاية الصفوف
} else {
    echo '<div class="text-center py-5">';
    echo '<i class="fas fa-user-tie fa-3x text-muted mb-3"></i>'; // أيقونة للمساعدين
    echo '<h5 class="text-secondary">لا يوجد مدرسون مساعدون مضافون بعد.</h5>';
    echo '</div>';
}

$stmt->close();