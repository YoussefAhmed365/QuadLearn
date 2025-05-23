<?php
// logout.php

// بدء الجلسة
session_start();

// حذف جميع بيانات الجلسة
$_SESSION = [];

// حذف كوكي الجلسة عن طريق تحديد تاريخ انتهاء قديم
if (isset($_COOKIE['PHPSESSID'])) {
    setcookie('PHPSESSID', '', time() - 3600, '/'); // مسح الكوكي
}

// تدمير الجلسة الحالية
session_destroy();

// إعادة التوجيه إلى صفحة تسجيل الدخول أو الصفحة الرئيسية
header('Location: ../index.php');
exit();


// require_once 'session_handler.php'; 

// session_start();

// // الحصول على معرف الجلسة الحالي
// $sessionId = session_id();

// // تدمير الجلسة في قاعدة البيانات (إذا كنت تستخدم معالج جلسات مخصصًا)
// destroySession($sessionId);

// // إلغاء جميع المتغيرات الخاصة بالجلسة
// session_unset();

// // تدمير الجلسة
// session_destroy();

// // حذف ملفات تعريف الارتباط "تذكرني"
// setcookie('remember_me_selector', '', time() - 3600, '/', '', true, true);
// setcookie('remember_me_authenticator', '', time() - 3600, '/', '', true, true);

// // إعادة التوجيه إلى الصفحة الرئيسية
// header("Location: ../index.php");
// exit();