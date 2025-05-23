<?php
require '../auth.php'; // التحقق من المستخدم

$response = ['success' => false, 'message' => 'حدث خطأ غير متوقع'];

function handleException($e, &$response) {
    $response['message'] = 'خطأ أثناء تنفيذ العملية: ' . $e->getMessage();
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'add') {
            handleAddNotification($_POST);
        } elseif ($action === 'delete' && isset($_POST['id'])) {
            handleDeleteNotification(intval($_POST['id']));
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        loadNotifications();
    }
} catch (Exception $e) {
    handleException($e, $response);
}

echo json_encode($response);

// وظيفة لإضافة إشعار
function handleAddNotification($postData) {
    global $conn, $user_id, $response;

    $title = trim($postData['title'] ?? '');
    $content = trim($postData['content'] ?? '');

    if (!$title || !$content) {
        $response['message'] = 'يرجى ملء جميع الحقول.';
        return;
    }

    $conn->begin_transaction();

    try {
        // إدخال الإشعار الجديد
        $stmt = $conn->prepare("INSERT INTO notifications (teacher_id, title, content) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $user_id, $title, $content);
        $stmt->execute();
        $notificationId = $stmt->insert_id;
        $stmt->close();

        // إرسال الإشعار إلى المعلمين والطلاب
        notifyTeacherAndStudents($user_id, $notificationId, $_SESSION['title']);

        $conn->commit();

        // إرسال الإشعار عبر WebSocket
        $notificationData = [
            'title' => $title,
            'content' => $content,
            'teacher_id' => $user_id
        ];
        sendWebSocketNotification($notificationData);

        $response = ['success' => true, 'message' => 'تم إضافة الإشعار بنجاح!'];
    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        handleException($e, $response);
    }
}

// وظيفة لحذف إشعار
function handleDeleteNotification($notificationId) {
    global $conn, $response;

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("DELETE FROM notifications WHERE id = ?");
        $stmt->bind_param("i", $notificationId);
        $stmt->execute();
        $stmt->close();

        $conn->commit();
        $response = ['success' => true, 'message' => 'تم حذف الإشعار بنجاح!'];
    } catch (mysqli_sql_exception $e) {
        $conn->rollback();
        handleException($e, $response);
    }
}

// وظيفة لتحميل الإشعارات
function loadNotifications() {
    global $conn, $user_id, $response;

    try {
        $stmt = $conn->prepare("SELECT n.id, n.title, n.content, u.first_name, u.last_name, u.picture
            FROM notifications n
            JOIN users u ON n.teacher_id = u.id
            WHERE n.teacher_id = ? OR EXISTS (
                SELECT 1 FROM assigned_assistants aa WHERE aa.assistant_id = n.teacher_id AND aa.teacher_id = ?
            )
            ORDER BY n.created_at DESC
        ");
        $stmt->bind_param("ii", $user_id, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $notifications = [];

        while ($row = $result->fetch_assoc()) {
            $target_dir = "../../../../assets/images/profiles/";
            $row['dir'] = ($row['picture'] && file_exists("$target_dir{$row['picture']}")) ? "$target_dir{$row['picture']}" : "{$target_dir}default.png";
            $notifications[] = $row;
        }

        $stmt->close();

        $response = ['success' => true, 'notifications' => $notifications];
    } catch (mysqli_sql_exception $e) {
        handleException($e, $response);
    }
}

// وظيفة لإخطار المدرسين والطلاب
function notifyTeacherAndStudents($user_id, $notificationId, $userTitle) {
    global $conn;

    if ($userTitle === 'معلم') {
        // إشعارات المعلم الرئيسي
        $stmt2 = $conn->prepare("INSERT INTO teacher_notifications (teacher_id, notification_id) VALUES (?, ?)");
        $stmt2->bind_param("ii", $user_id, $notificationId);
        $stmt2->execute();
        $stmt2->close();

        // إشعارات الطلاب المرتبطين
        $stmt3 = $conn->prepare("SELECT student_id FROM assigned_students WHERE teacher_id = ?");
        $stmt3->bind_param("i", $user_id);
        $stmt3->execute();
        $stmt3->store_result();
        $stmt3->bind_result($student_id);

        $stmt4 = $conn->prepare("INSERT INTO student_notifications (student_id, notification_id) VALUES (?, ?)");
        while ($stmt3->fetch()) {
            $stmt4->bind_param("ii", $student_id, $notificationId);
            $stmt4->execute();
        }
        $stmt4->close();
        $stmt3->free_result();
        $stmt3->close();

        // إشعارات المعلمين المساعدين
        $stmt5 = $conn->prepare("SELECT assistant_id FROM assigned_assistants WHERE teacher_id = ?");
        $stmt5->bind_param("i", $user_id);
        $stmt5->execute();
        $stmt5->store_result();
        $stmt5->bind_result($assistant_id);

        $stmt6 = $conn->prepare("INSERT INTO teacher_notifications (teacher_id, notification_id) VALUES (?, ?)");
        while ($stmt5->fetch()) {
            $stmt6->bind_param("ii", $assistant_id, $notificationId);
            $stmt6->execute();
        }
        $stmt6->close();
        $stmt5->free_result();
        $stmt5->close();
    } elseif ($userTitle === 'معلم مساعد') {
        // إشعارات المعلم الرئيسي والطلاب
        $stmt7 = $conn->prepare("SELECT teacher_id FROM assigned_assistants WHERE assistant_id = ?");
        $stmt7->bind_param("i", $user_id);
        $stmt7->execute();
        $stmt7->store_result();
        $stmt7->bind_result($main_teacher_id);
        $stmt7->fetch();

        if ($main_teacher_id) {
            $stmt8 = $conn->prepare("INSERT INTO teacher_notifications (teacher_id, notification_id) VALUES (?, ?)");
            $stmt8->bind_param("ii", $main_teacher_id, $notificationId);
            $stmt8->execute();
            $stmt8->close();
        }
        $stmt7->free_result();
        $stmt7->close();

        $stmt9 = $conn->prepare("INSERT INTO teacher_notifications (teacher_id, notification_id) VALUES (?, ?)");
        $stmt9->bind_param("ii", $user_id, $notificationId);
        $stmt9->execute();
        $stmt9->close();

        // إشعارات الطلاب
        $stmt10 = $conn->prepare("SELECT student_id FROM assigned_students WHERE teacher_id = ?");
        $stmt10->bind_param("i", $main_teacher_id);
        $stmt10->execute();
        $stmt10->store_result();
        $stmt10->bind_result($student_id);

        $stmt11 = $conn->prepare("INSERT INTO student_notifications (student_id, notification_id) VALUES (?, ?)");
        while ($stmt10->fetch()) {
            $stmt11->bind_param("ii", $student_id, $notificationId);
            $stmt11->execute();
        }
        $stmt11->close();
        $stmt10->free_result();
        $stmt10->close();
    }
}

// وظيفة لإرسال إشعار عبر WebSocket
function sendWebSocketNotification($notificationData) {
    $socketServer = 'tcp://127.0.0.1:5500'; // عنوان خادم WebSocket
    $socket = stream_socket_client($socketServer, $errno, $errstr);

    if (!$socket) {
        error_log("Failed to connect to WebSocket server: $errstr ($errno)");
        return;
    }

    $message = json_encode($notificationData);
    fwrite($socket, $message);
    fclose($socket);
}