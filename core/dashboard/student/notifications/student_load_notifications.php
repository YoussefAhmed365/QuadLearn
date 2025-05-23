<?php
require '../auth.php';
require '../../../helper_functions.php';

$filterType = $_POST['filterType'] ?? 'عرض الكل';
$filterType = match ($filterType) {
    'عرض الكل' => 'All',
    'اللغة العربية' => 'Arabic',
    'اللغة الإنجليزية' => 'English',
    'اللغة الإسبانية' => 'Spanish',
    'اللغة الفرنسية' => 'French',
    'اللغة الألمانية' => 'German',
    'اللغة الإيطالية' => 'Italian',
    'الفيزياء' => 'Physics',
    'الكيمياء' => 'Chemistry',
    'الأحياء' => 'Biology',
    'الجيولوجيا' => 'Geology',
    'الرياضيات' => 'Mathematics',
    'الفلسفة وعلم النفس' => 'Philosophy',
    'التاريخ' => 'History',
    'الجغرافيا' => 'Geography'
};
$query = "";
$stmt = NULL;

switch ($filterType) {
    case 'All':
        $query = "SELECT n.id, n.title, n.content, n.created_at, sn.read_status, u.first_name, u.last_name, t.subject, u.id AS publisher_id
                FROM notifications n
                JOIN users u ON n.teacher_id = u.id
                LEFT JOIN teachers t ON u.id = t.id
                INNER JOIN student_notifications sn ON n.id = sn.notification_id AND sn.student_id = ?
                ORDER BY n.created_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        break;

    case $filterType !== 'All':
        $query = "SELECT n.id, n.title, n.content, n.created_at, sn.read_status, u.first_name, u.last_name, t.subject, u.id AS publisher_id
                FROM notifications n
                JOIN users u ON n.teacher_id = u.id
                LEFT JOIN teachers t ON u.id = t.id
                INNER JOIN student_notifications sn ON n.id = sn.notification_id AND sn.student_id = ? AND t.subject = ?
                ORDER BY n.created_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("is", $user_id, $filterType);
        break;
    
    default:
        echo "<p>نوع الفلتر غير صالح.</p>";
        break;
}

$stmt->execute();
$result = $stmt->get_result();



if ($result->num_rows > 0) {
    ?>
    <table class="table table-hover align-middle table-responsive">
        <tbody>
    <?php
    while ($row = $result->fetch_assoc()) {
        $notificationId = htmlspecialchars($row['id']);
        $title = isset($row['title']) ? htmlspecialchars($row['title']) : '';
        $content = isset($row['content']) ? htmlspecialchars($row['content']) : '';
        $created_at = (!empty($row['created_at'])) ? formatCreatedAt($row['created_at']) : "تاريخ غير متوفر";
        $readStatus = $row['read_status'] ?? 0;
        $firstName = isset($row['first_name']) ? htmlspecialchars($row['first_name']) : '';
        $lastName = isset($row['last_name']) ? htmlspecialchars($row['last_name']) : '';
        $subject = isset($row['subject']) ? htmlspecialchars($row['subject']) : '';
        $publisher_id = $row['publisher_id'];
        ?>
            <tr class="notification <?php echo ($readStatus == 0) ? 'unread' : ''; ?>" id="notification" data-id="<?php echo $notificationId; ?>" data-title="<?php echo $title; ?>" data-content="<?php echo $content; ?>" data-time="<?php echo $created_at; ?>" data-publisherId="<?php echo $publisher_id ?>" data-firstName="<?php echo $firstName; ?>" data-lastName="<?php echo $lastName; ?>" data-subject="<?php echo $subject; ?>">
                <td class="check">
                    <input type="checkbox" id="check-<?php echo $notificationId; ?>" class="btn btn-transparent">
                </td>
                <td class="title">
                    <p class="mb-0 text-truncate"><?php echo $title; ?></p>
                </td>
                <td class="content ps-4">
                    <?php
                    $subject = match ($subject) {
                        'Arabic' => 'اللغة العربية',
                        'English' => 'اللغة الإنجليزية',
                        'Spanish' => 'اللغة الإسبانية',
                        'French' => 'اللغة الفرنسية',
                        'German' => 'اللغة الألمانية',
                        'Italian' => 'اللغة الإيطالية',
                        'Physics' => 'الفيزياء',
                        'Chemistry' => 'الكيمياء',
                        'Biology' => 'الأحياء',
                        'Geology' => 'الجيولوجيا',
                        'Mathematics' => 'الرياضيات',
                        'Philosophy' => 'الفلسفة وعلم النفس',
                        'History' => 'التاريخ',
                        'Geography' => 'الجغرافيا'
                    }
                    ?>
                    <p class="mb-0 text-truncate"><small class="text-secondary"><?php echo $subject; ?> .</small> <?php echo $content; ?></p>
                </td>
                <td class="time text-end">
                    <h6><?php echo $created_at; ?></h6>
                </td>
            </tr>
        <?php
    }
    ?>
        </tbody>
    </table>
    <?php
} else {
    ?>
    <div class="d-flex justify-content-center align-items-center">
        <h6>لا توجد إشعارات حتى الآن.</h6>
    </div>
    <?php
}