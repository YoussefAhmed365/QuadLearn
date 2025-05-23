<?php
if (isset($_GET['file']) && isset($_GET['original_name'])) {
    $file = $_GET['file']; // الاسم العشوائي للملف المخزن على الخادم
    $original_name = $_GET['original_name']; // الاسم الأصلي للملف
    
    // المسار إلى ملفات التحميل
    $file_path = "../../../../assets/files/$file";

    // تحقق مما إذا كان الملف موجودًا
    if (file_exists($file_path)) {
        // إعداد الرؤوس HTTP المناسبة لعملية التحميل
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($original_name) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        
        // قراءة الملف وإرساله للمتصفح
        readfile($file_path);
        exit;
    } else {
        echo "الملف غير موجود.";
    }
} else {
    echo "طلب غير صالح.";
}