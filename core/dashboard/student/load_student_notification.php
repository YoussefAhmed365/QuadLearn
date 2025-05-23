<?php
require 'auth.php'; // التأكد من هوية المستخدم

$sql = "SELECT notifications.*, 
       student_notifications.read_status, 
       users.first_name, 
       users.last_name, 
       users.picture
FROM notifications
JOIN (
    -- ربط الطالب بالمعلمين المساعدين والمعلمين الرئيسيين
    SELECT teacher_id, student_id
    FROM assigned_teachers
    WHERE student_id = ?
    UNION
    SELECT aa.assistant_id AS teacher_id, ast.student_id
    FROM assigned_assistants aa
    JOIN assigned_teachers ast ON aa.teacher_id = ast.teacher_id
    WHERE ast.student_id = ?
) AS teacher_students ON notifications.teacher_id = teacher_students.teacher_id
-- يجب أن يكون الإشعار موجودًا في جدول student_notifications ويكون غير مقروء (read_status = 0)
INNER JOIN student_notifications ON student_notifications.notification_id = notifications.id 
                                 AND student_notifications.student_id = teacher_students.student_id
                                 AND student_notifications.read_status = 0
JOIN users ON users.id = notifications.teacher_id
WHERE teacher_students.student_id = ?  
ORDER BY notifications.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$totalNotifications = $result->num_rows;
$notificationsHTML = '';

if ($totalNotifications > 0) {
    while ($row = $result->fetch_assoc()) {
        $Ttarget_dir = "../../../assets/images/profiles/";
        $dir = glob($Ttarget_dir . $row['teacher_id'] . ".*") ? basename(glob($Ttarget_dir . $row['teacher_id'] . ".*")[0]) : "default.png";
        $notificationsHTML .= '<div class="notification-box row mb-3 w-100">';
        $notificationsHTML .= '<div class="photo col-2 d-flex justify-content-center align-items-start">';
        $notificationsHTML .= "<img src=\"$Ttarget_dir$dir\" alt=\"Profile Photo\" class=\"rounded-circle\">";
        $notificationsHTML .= '</div>';
        $notificationsHTML .= '<div class="content col-10 d-flex justify-content-start align-items-center">';
        $notificationsHTML .= '<p><strong>' . htmlspecialchars($row["title"]) . '</strong>&nbsp;' . htmlspecialchars($row["content"]) . '</p>';
        $notificationsHTML .= '</div>';
        $notificationsHTML .= '</div>';
    }
} else {
    $notificationsHTML .= '<dotlottie-player src="https://lottie.host/3c885d46-f3aa-4044-abef-eba4a5eef344/Odb2njPPD3.json" background="transparent" speed="0.5" style="width: 250px; height: 250px;" loop autoplay></dotlottie-player>';
    $notificationsHTML .= '<h5 class="mt-3">ليس لديك أي إشعارات</h5>';
    $notificationsHTML .= '<p>سيتم إعلامك بمجرد وجود أي نشاط.</p>';
}

$stmt->close();

$response = [
    "total" => $totalNotifications,
    "html" => $notificationsHTML
];

echo json_encode($response);