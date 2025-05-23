<?php
require '../auth.php';

// استلام البيانات من النموذج
$testTitle = $conn->real_escape_string($_POST['title']);
$testDescription = $conn->real_escape_string($_POST['description']);
$testLevel = $conn->real_escape_string($_POST['level']);
$teacherId = $user_id; // استخدم معرف المعلم من نظام تسجيل الدخول

// إدخال بيانات الاختبار في جدول tests
$sql = "INSERT INTO tests (teacher_id, title, description, level) VALUES ('$teacherId', '$testTitle', '$testDescription', '$testLevel')";
if ($conn->query($sql) === TRUE) {
    $testId = $conn->insert_id; // الحصول على معرف الاختبار الذي تم إدخاله
} else {
    die("Error: $sql<br>{$conn->error}");
}

// إدخال الأسئلة
foreach ($_POST['questionTitle'] as $index => $questionTitle) {
    $questionTitle = $conn->real_escape_string($questionTitle);
    $questionType = $conn->real_escape_string($_POST['questionType'][$index]);
    $questionScore = intval($_POST['questionScore'][$index]);

    // تعيين الإجابة الصحيحة إذا كان السؤال من نوع "نص"
    $correctAnswer = ($questionType == 'text') ? $conn->real_escape_string($_POST['correctTextAnswer'][$index]) : null;

    // إدخال السؤال في جدول questions
    $sql = "INSERT INTO questions (test_id, question_title, question_type, correct_answer) VALUES ('$testId', '$questionTitle', '$questionType', '$correctAnswer')";
    if ($conn->query($sql) === TRUE) {
        $questionId = $conn->insert_id;

        // إدخال الخيارات إذا كان السؤال من نوع "اختيار من متعدد"
        if ($questionType == 'choice' && isset($_POST['options'][$index])) {
            $correctChoiceId = null;
            foreach ($_POST['options'][$index] as $optionIndex => $option) {
                $option = $conn->real_escape_string($option);
                $sql = "INSERT INTO question_choices (question_id, choice) VALUES ('$questionId', '$option')";

                if ($conn->query($sql) === TRUE) {
                    $choiceId = $conn->insert_id;

                    // تعيين معرف الخيار كإجابة صحيحة إذا كان checkbox محددًا
                    if (isset($_POST['correctAnswer'][$index]) && in_array($optionIndex + 1, $_POST['correctAnswer'][$index])) {
                        $correctChoiceId = $choiceId;
                    }
                } else {
                    die("Error: $sql<br>{$conn->error}");
                }
            }

            // تحديث الإجابة الصحيحة للسؤال بعد إدخال الخيارات
            if ($correctChoiceId) {
                $sql = "UPDATE questions SET correct_answer = '$correctChoiceId' WHERE question_id = '$questionId'";
                if (!$conn->query($sql)) {
                    die("Error: $sql<br>{$conn->error}");
                }
            }
        }

        // إدخال درجة السؤال في جدول question_scores
        $sql = "INSERT INTO question_scores (question_id, score) VALUES ('$questionId', '$questionScore')";
        if (!$conn->query($sql)) {
            die("Error: $sql<br>{$conn->error}");
        }
    } else {
        die("Error: $sql<br>{$conn->error}");
    }
}

echo "تم إدخال الاختبار بنجاح";