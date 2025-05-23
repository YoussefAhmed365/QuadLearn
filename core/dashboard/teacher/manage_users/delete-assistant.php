<?php
require_once '../auth.php'; // يفترض أن هذا الملف يقوم بتهيئة $conn و $user_id

header('Content-Type: application/json; charset=utf-8');

// التأكد من أن الطلب هو POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // التحقق من وجود 'id' في الطلب وتحويله إلى عدد صحيح
    if (isset($_POST['id'])) {
        $assistantId = intval($_POST['id']);

        // التحقق من أن $user_id مهيأ (من auth.php)
        if (!isset($user_id)) {
            echo json_encode(['status' => 'error', 'message' => 'خطأ في تهيئة المستخدم.']);
            exit;
        }
        // التحقق من أن $conn مهيأ (من auth.php)
        if (!$conn) {
            echo json_encode(['status' => 'error', 'message' => 'خطأ في الاتصال بقاعدة البيانات.']);
            exit;
        }

        // استعلام SQL لحذف المساعد المعين لهذا المعلم فقط
        $sql = "DELETE FROM assigned_assistants WHERE assistant_id = ? AND teacher_id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ii", $assistantId, $user_id);
            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    echo json_encode(['status' => 'success', 'message' => 'تم حذف المساعد بنجاح.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'لم يتم العثور على المساعد أو لا تملك الإذن لحذفه.']);
                }
            } else {
                // خطأ في التنفيذ
                error_log("MySQL execute failed: " . $stmt->error); // تسجيل الخطأ
                echo json_encode(['status' => 'error', 'message' => 'حدث خطأ أثناء حذف المساعد.']);
            }
            $stmt->close();
        } else {
            // خطأ في إعداد الاستعلام
            error_log("MySQL prepare failed: " . $conn->error); // تسجيل الخطأ
            echo json_encode(['status' => 'error', 'message' => 'فشل إعداد الاستعلام لحذف المساعد.']);
        }
    } else {
        // 'id' غير موجود في الطلب
        echo json_encode(['status' => 'error', 'message' => 'معرف المساعد مطلوب.']);
    }
} else {
    // الطلب ليس POST
    echo json_encode(['status' => 'error', 'message' => 'طلب غير صالح. يجب أن يكون الطلب POST.']);
}
// لا يوجد قوس إغلاق زائد هنا