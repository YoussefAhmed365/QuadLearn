<?php
require '../auth.php'; // التأكد من تسجيل الدخول للمدرس
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $videoName = mysqli_real_escape_string($conn, $_POST['videoName']);
    $stage = mysqli_real_escape_string($conn, $_POST['stage']);

    // التحقق من وجود ملف تم رفعه
    if (empty($_FILES['videoFile']['name'])) {
        echo json_encode(["status" => "error", "message" => "لم يتم اختيار ملف فيديو"]);
        exit;
    }

    // تعيين المتغيرات المطلوبة
    $fileTmp = $_FILES['videoFile']['tmp_name'];
    $extension = strtolower(pathinfo($_FILES['videoFile']['name'], PATHINFO_EXTENSION));

    // السماح فقط بامتدادات الفيديو المحددة
    $allowedExtensions = ['mp4', 'webm', 'wav'];
    if (!in_array($extension, $allowedExtensions)) {
        echo json_encode(["status" => "error", "message" => "امتداد الفيديو غير مدعوم"]);
        exit;
    }

    // تعيين اسم جديد للفيديو والتحقق من وجود المجلد وإنشائه إذا لزم الأمر
    $newFileName = uniqid();
    $newVideoName = "$newFileName.$extension"; // اسم الفيديو مع الامتداد
    $uploadDir = "../../../../assets/videos/$stage/";
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
        echo json_encode(["status" => "error", "message" => "خطأ في إنشاء المجلد"]);
        exit;
    }

    // استخدام متغير subject من ملف auth.php
    $subject = $data['subject'];
    $newThumbnailName = "$subject.webp"; // يمكن تعديله بناءً على متغير subject

    // التحقق من وجود الصورة المصغرة ورفعها
    if (!empty($_FILES['thumbnail']['name'])) {
        $thumbnail = $_FILES['thumbnail']['tmp_name'];
        $thumbnailExtension = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));

        // السماح فقط بامتدادات الصور المصغرة المحددة
        $allowedThumbnailExtensions = ['jpeg', 'jpg', 'webp', 'png'];
        if (!in_array($thumbnailExtension, $allowedThumbnailExtensions)) {
            echo json_encode(["status" => "error", "message" => "امتداد الصورة غير مدعوم"]);
            exit;
        }

        $newThumbnailName = "$newFileName.$thumbnailExtension";
        move_uploaded_file($thumbnail, "$uploadDir$newThumbnailName");
    }

    // رفع الفيديو وتخزين البيانات في قاعدة البيانات
    $uploadFile = "$uploadDir$newVideoName"; // هنا تأكد من استخدام الامتداد عند تحديد مسار الملف
    if (move_uploaded_file($fileTmp, $uploadFile)) {
        $sql = "INSERT INTO videos (teacher_id, name, fileName, thumbnail, level) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issss", $user_id, $videoName, $newVideoName, $newThumbnailName, $stage);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "تم رفع الفيديو بنجاح"]);
        } else {
            echo json_encode(["status" => "error", "message" => "خطأ في تخزين البيانات"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "فشل في رفع الفيديو"]);
    }    
} else {
    echo json_encode(["status" => "error", "message" => "طلب غير صالح"]);
}