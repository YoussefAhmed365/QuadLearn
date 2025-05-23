<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'auth.php'; // التأكد من هوية المستخدم

$sql = "SELECT notifications.*, 
               teacher_notifications.read_status, 
               users.picture
        FROM notifications
        INNER JOIN teacher_notifications ON notifications.id = teacher_notifications.notification_id 
                                        AND teacher_notifications.teacher_id = ?
        LEFT JOIN users ON notifications.teacher_id = users.id
        WHERE teacher_notifications.teacher_id = ?  
              AND (teacher_notifications.read_status = 0 OR teacher_notifications.read_status IS NULL)
        ORDER BY notifications.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
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
        $notificationsHTML .= '<img src="' . $Ttarget_dir . $dir . '" alt="Profile Photo" class="rounded-circle">';
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

header('Content-Type: application/json');
echo json_encode($response);