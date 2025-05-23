<?php
header('Content-Type: application/json');
require '../auth.php';

$responseDelete = [];

try {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $teacher_id = $_POST['teacher_id'] ?? null;

        // تحقق من أن معرف المعلم صالح
        if (!$teacher_id || !ctype_digit($teacher_id)) {
            $responseDelete = ['status' => 'warning', 'message' => 'معرّف المعلم غير صالح'];
        } else {
            // التحقق إذا كان المعلم معينًا بالفعل لهذا الطالب
            $queryCheck = "SELECT 1 FROM assigned_teachers WHERE teacher_id = ? AND student_id = ?";
            $stmtCheck = $conn->prepare($queryCheck);
            $stmtCheck->bind_param("ii", $teacher_id, $user_id);
            $stmtCheck->execute();
            $stmtCheck->store_result();

            if ($stmtCheck->num_rows === 0) {
                $responseDelete = ['status' => 'error', 'message' => 'المعلم غير موجود ضمن المعلمين المعينين'];
            } else {
                // بدأ المعاملة لحذف البيانات
                $conn->begin_transaction();

                try {
                    // حذف المعلم من الجدول assigned_teachers
                    $stmtDeleteAssigned = $conn->prepare("DELETE FROM assigned_teachers WHERE teacher_id = ? AND student_id = ?");
                    $stmtDeleteAssigned->bind_param("ii", $teacher_id, $user_id);
                    $stmtDeleteAssigned->execute();
                    
                    // حذف الطلبات المتعلقة بالمعلم من جدول requests
                    $stmtDeleteRequests = $conn->prepare("DELETE FROM requests WHERE teacher_id = ? AND student_id = ?");
                    $stmtDeleteRequests->bind_param("ii", $teacher_id, $user_id);
                    $stmtDeleteRequests->execute();
                    
                    // تأكيد العملية
                    $conn->commit();

                    $responseDelete = ['status' => 'success', 'message' => 'تم حذف المعلم بنجاح'];
                } catch (mysqli_sql_exception $e) {
                    // التراجع عن المعاملة في حالة حدوث خطأ
                    $conn->rollback();
                    $responseDelete = ['status' => 'error', 'message' => 'حدث خطأ أثناء حذف المعلم: ' . $e->getMessage()];
                } finally {
                    // إغلاق الاستعلامات
                    $stmtDeleteAssigned->close();
                    $stmtDeleteRequests->close();
                }
            }

            // إغلاق التحقق
            $stmtCheck->close();
        }
    } else {
        $responseDelete = ['status' => 'error', 'message' => 'طلب غير صالح'];
    }
} catch (Exception $e) {
    // سجل الخطأ في ملف السجلات
    error_log("Error in delete-assigned-teacher.php: " . $e->getMessage());

    $responseDelete = ['status' => 'error', 'message' => 'حدث خطأ غير متوقع: ' . $e->getMessage()];
}

echo json_encode($responseDelete);