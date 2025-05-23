<?php
require_once '../auth.php';

header('Content-Type: application/json; charset=utf-8');

function executeQuery($conn, $query, $types, $params) {
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        error_log("MySQL prepare failed: {$conn->error}");
        return ["status" => "error", "message" => "حدث خطأ في قاعدة البيانات."];
    }

    if ($stmt->bind_param($types, ...$params) === false) {
        error_log("MySQL bind_param failed: {$stmt->error}");
        $stmt->close();
        return ["status" => "error", "message" => "حدث خطأ في ربط المعاملات."];
    }

    if ($stmt->execute() === false) {
        error_log("MySQL execute failed: {$stmt->error}");
        $stmt->close();
        return ["status" => "error", "message" => "حدث خطأ أثناء تنفيذ الاستعلام."];
    }

    $result = $stmt->get_result();
    $stmt->close();
    return $result;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $assistantId = intval($_POST['assistantId']);

    // Check if user exists
    $result1 = executeQuery($conn, "SELECT * FROM users WHERE id = ?", "i", [$assistantId]);
    if (is_array($result1)) {
        echo json_encode($result1);
        exit();
    }

    if ($result1->num_rows > 0) {
        // Check if user is an assistant
        $result2 = executeQuery($conn, "SELECT title FROM teachers WHERE id = ?", "i", [$assistantId]);
        if (is_array($result2)) {
            echo json_encode($result2);
            exit();
        }

        $row = $result2->fetch_assoc();
        if ($row['title'] === "assistant") {
            // Check if assistant is already assigned
            $result3 = executeQuery($conn, "SELECT * FROM assigned_assistants WHERE teacher_id = ? AND assistant_id = ?", "ii", [$user_id, $assistantId]);
            if (is_array($result3)) {
                echo json_encode($result3);
                exit();
            }

            if ($result3->num_rows > 0) {
                echo json_encode(["status" => "warning", "message" => "هذا المعلم المساعد مضاف مسبقاً."]);
            } else {
                // Add assistant
                $result4 = executeQuery($conn, "INSERT INTO assigned_assistants (teacher_id, assistant_id) VALUES (?, ?)", "ii", [$user_id, $assistantId]);
                if (is_array($result4)) {
                    echo json_encode($result4);
                    exit();
                }

                echo json_encode(["status" => "success", "message" => "تم إضافة المعلم المساعد."]);
            }
        } else {
            echo json_encode(["status" => "warning", "message" => "هذا الشخص ليس معلم مساعد."]);
        }
    } else {
        echo json_encode(["status" => "warning", "message" => "هذا المستخدم ليس موجود."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "تعذر تنفيذ الطلب حالياً يرجى المحاولة مرة أخرى في وقت لاحق."]);
}