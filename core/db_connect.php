<?php
// db_connect.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "datainfo";

// إنشاء الاتصال بقاعدة البيانات
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    error_log("Connection failed: {$conn->connect_error}", 0);
    exit("Connection to the database failed.");
}

// تعيين الترميز إلى UTF-8 لضمان التعامل بشكل صحيح مع النصوص
$conn->set_charset("utf8mb4");