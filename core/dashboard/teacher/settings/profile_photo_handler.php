<?php
require '../auth.php';

// تفعيل عرض الأخطاء للتصحيح أثناء التطوير
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// تحديد نوع المحتوى ليكون JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // تحقق من الملف المرفوع
    $allowed_types = ['jpg', 'jpeg', 'png'];
    $file = $_FILES['fileToUpload'];
    $file_name = $file['name'];
    $file_size = $file['size'];
    $file_tmp = $file['tmp_name'];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($file_ext, $allowed_types)) {
        echo json_encode(['status' => 'error', 'message' => 'نوع الملف غير مدعوم. الملفات المدعومة هي: jpg, jpeg, png.']);
        exit;
    }

    if ($file_size > 5 * 1024 * 1024) {
        echo json_encode(['status' => 'error', 'message' => 'حجم الملف يجب أن لا يتجاوز 5 ميجابايت.']);
        exit;
    }

    // تعيين مسار الملف المرفوع
    $target_dir = "../../../../assets/images/profiles/"; 
    $newFileName = "$user_id.webp"; // حفظ الملف النهائي كـ webp
    $target_file = "$target_dir$newFileName"; 

    // البحث عن الصورة القديمة باستخدام اسم المستخدم بغض النظر عن الامتداد
    $existingFiles = glob($target_dir . $user_id . ".*"); 

    // إذا كانت الصورة موجودة، احذفها
    foreach ($existingFiles as $oldFile) {
        if (is_file($oldFile)) {
            unlink($oldFile); 
        }
    }

    // تحميل الصورة بناءً على النوع
    $image = null;
    if ($file_ext === 'jpg' || $file_ext === 'jpeg') {
        $image = imagecreatefromjpeg($file_tmp);
    } elseif ($file_ext === 'png') {
        $image = imagecreatefrompng($file_tmp);
    }

    if ($image === null) {
        echo json_encode(['status' => 'error', 'message' => 'تعذر قراءة الصورة المرفوعة.']);
        exit;
    }

    // تحويل الصورة إلى webp وحفظها
    if (!imagewebp($image, $target_file)) {
        echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء تحويل الصورة إلى webp.']);
        exit;
    }

    // تحرير الذاكرة المستخدمة
    imagedestroy($image);

    // تحديث اسم الصورة في قاعدة البيانات
    $stmt = $conn->prepare("UPDATE users SET picture = ? WHERE id = ?");
    if (!$stmt) {
        echo json_encode(['status' => 'error', 'message' => 'فشل إعداد الاستعلام']);
        exit;
    }

    $stmt->bind_param("si", $newFileName, $user_id); // تخزين الاسم الجديد في قاعدة البيانات
    $stmt->execute();
    $stmt->close();

    echo json_encode(['status' => 'success', 'message' => 'تم رفع الصورة بنجاح وتحويلها إلى webp']);
}