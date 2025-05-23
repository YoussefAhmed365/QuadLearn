<?php
require '../auth.php';

if (!isset($_GET['test_id'])) {
    die('Test ID is missing');
}

$testId = intval($_GET['test_id']);

// استرجاع بيانات الاختبار
$sql = "SELECT * FROM tests WHERE test_id = $testId AND teacher_id = $user_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die('Test not found or you do not have permission to edit this test.');
}

$test = $result->fetch_assoc();

// استرجاع الأسئلة المرتبطة بالاختبار
$sql = "SELECT * FROM questions WHERE test_id = $testId";
$questionsResult = $conn->query($sql);

$questions = [];
while ($question = $questionsResult->fetch_assoc()) {
    $questionId = $question['question_id'];

    // استرجاع الخيارات إذا كان السؤال من نوع "اختيار من متعدد"
    if ($question['question_type'] == 'choice') {
        $sql = "SELECT * FROM question_choices WHERE question_id = $questionId";
        $choicesResult = $conn->query($sql);

        $choices = [];
        while ($choice = $choicesResult->fetch_assoc()) {
            $choices[] = $choice;
        }

        $question['choices'] = $choices;
    }

    $questions[] = $question;
}

// معالجة التحديث عند الإرسال
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $testTitle = $conn->real_escape_string($_POST['title']);
    $testDescription = $conn->real_escape_string($_POST['description']);
    $testLevel = $conn->real_escape_string($_POST['level']);

    // تحديث بيانات الاختبار
    $sql = "UPDATE tests SET title='$testTitle', description='$testDescription', level='$testLevel' WHERE test_id='$testId'";
    if ($conn->query($sql) !== TRUE) {
        die("Error updating test: " . $conn->error);
    }

    // تحديث الأسئلة
    foreach ($_POST['questionTitle'] as $index => $questionTitle) {
        $questionId = intval($_POST['questionId'][$index]);
        $questionTitle = $conn->real_escape_string($questionTitle);
        $questionType = $conn->real_escape_string($_POST['questionType'][$index]);
        $questionScore = intval($_POST['questionScore'][$index]);

        $correctAnswer = ($questionType == 'text') ? $conn->real_escape_string($_POST['correctTextAnswer'][$index]) : null;

        // تحديث السؤال
        $sql = "UPDATE questions SET question_title='$questionTitle', question_type='$questionType', correct_answer='$correctAnswer' WHERE question_id='$questionId'";
        if ($conn->query($sql) !== TRUE) {
            die("Error updating question: " . $conn->error);
        }

        // تحديث الخيارات إذا كان السؤال من نوع "اختيار من متعدد"
        if ($questionType == 'choice' && isset($_POST['options'][$index])) {
            // حذف الخيارات القديمة
            $sql = "DELETE FROM question_choices WHERE question_id='$questionId'";
            if ($conn->query($sql) !== TRUE) {
                die("Error deleting old choices: " . $conn->error);
            }

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
                    die("Error inserting new choice: " . $conn->error);
                }
            }

            // تحديث الإجابة الصحيحة للسؤال بعد إدخال الخيارات
            if ($correctChoiceId) {
                $sql = "UPDATE questions SET correct_answer = '$correctChoiceId' WHERE question_id = '$questionId'";
                if (!$conn->query($sql)) {
                    die("Error updating correct answer: " . $conn->error);
                }
            }
        }

        // تحديث درجة السؤال
        $sql = "UPDATE question_scores SET score='$questionScore' WHERE question_id='$questionId'";
        if ($conn->query($sql) !== TRUE) {
            die("Error updating question score: " . $conn->error);
        }
    }

    echo "Test updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../../../assets/images/favicon-16x16.ico" sizes="16x16" type="image/x-icon">
    <link rel="icon" href="../../../../assets/images/favicon-32x32.ico" sizes="32x32" type="image/x-icon">
    <link rel="icon" href="../../../../assets/images/favicon-48x48.ico" sizes="48x48" type="image/x-icon">
    <link rel="apple-touch-icon" href="../../../../assets/images/apple-touch-icon-180x180.ico" sizes="180x180">
    <title>تعديل الإختبار</title>
    <!-- إضافة أي مكتبات أو CSS إذا لزم -->
</head>
<body>
    <div id="loading" class="position-relative">
        <div class="cube position-absolute">
            <div class="sr">
                <div class="cube_item cube_x cube1"></div>
                <div class="cube_item cube_y cube3"></div>
                <div></div>
            </div>
            <div class="sl">
                <div></div>
                <div class="cube_item cube_y cube2"></div>
                <div class="cube_item cube_x cube4"></div>
            </div>
        </div>
    </div>
    <div id="content" style="display: none;">
        <aside class="d-none d-sm-block">
        <div class="logo text-center">
            <img src="../../../../assets/images/logo.png" alt="Logo">
        </div>
        <ul>
            <li>
                <a href="dashboard.php">
                    <div class="icon">
                        <i class="fa-solid fa-house"></i>
                    </div>
                    <span class="item">الرئيسية</span>
                </a>
            </li>
            <li class="activited">
                <a href="coming_tests.php" class="active">
                    <div class="icon">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <span class="item">الإختبارات</span>
                </a>
            </li>
            <li>
                <a href="notification.php">
                    <div class="icon">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <span class="item">الإشعارات</span>
                </a>
            </li>
            <li>
                <a href="uploadvideos.php">
                    <div class="icon">
                        <i class="fa-solid fa-circle-play"></i>
                    </div>
                    <span class="item">الدروس</span>
                </a>
            </li>
            <li>
                <a href="community.php">
                    <div class="icon">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <span class="item">المجتمع</span>
                </a>
            </li>
        </ul>
        <div class="separate"></div>
        <ul>
            <li>
                <a href="show-users.php">
                    <div class="icon">
                        <i class="fa-solid fa-user-group"></i>
                    </div>
                    <span class="item">إدارة الأعضاء</span>
                </a>
            </li>
            <li>
                <a href="requests.php">
                    <div class="icon">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <span class="item">الطلبات</span>
                </a>
            </li>
            <li>
                <a href="settings.php">
                    <div class="icon">
                        <i class="fa-solid fa-gear"></i>
                    </div>
                    <span class="item">الإعدادات</span>
                </a>
            </li>
        </ul>
        <br>
        <div style="width: -webkit-fill-available;display: flex;justify-content: center;">
                <form method="POST" action="../../../logout.php" style="position: absolute;bottom: 0;margin-bottom: 20px;">
                    <button type="submit" name="logout" style="display: flex;flex-direction: column;align-items: center;">
                        <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i>
                        <span>خروج</span>
                    </button>
                </form>
            </div>
    </aside>
    <main>
        <h1>تعديل الإختبار: <?php echo htmlspecialchars($test['title']); ?></h1>
        <form id="testForm" method="POST">
            <div>
                <label for="title">عنوان الإختبار:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($test['title']); ?>" required>
            </div>
            <div>
                <label for="description">وصف الإختبار:</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($test['description']); ?></textarea>
            </div>
            <div>
                <label for="level">مستوى الإختبار:</label>
                <input type="text" id="level" name="level" value="<?php echo htmlspecialchars($test['level']); ?>" required>
            </div>
    
            <div id="questionsContainer">
                <?php foreach ($questions as $index => $question): ?>
                <div class="question bg-white mb-3 p-4 rounded-2 shadow-sm" id="question<?php echo $index + 1; ?>">
                    <input type="hidden" name="questionId[]" value="<?php echo $question['question_id']; ?>">
                    <div class="questionContent">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="form-floating me-3 w-50">
                                    <input type="text" id="questionTitle<?php echo $index + 1; ?>" name="questionTitle[]" class="form-control" value="<?php echo htmlspecialchars($question['question_title']); ?>" required>
                                    <label for="questionTitle<?php echo $index + 1; ?>">عنوان السؤال</label>
                                </div>
                                <div class="form-floating me-3">
                                    <select class="questionType form-select" id="questionType<?php echo $index + 1; ?>" name="questionType[]" required>
                                        <option value="text" <?php echo $question['question_type'] == 'text' ? 'selected' : ''; ?>>نص</option>
                                        <option value="choice" <?php echo $question['question_type'] == 'choice' ? 'selected' : ''; ?>>إختيار من متعدد</option>
                                    </select>
                                    <label for="questionType<?php echo $index + 1; ?>">نوع السؤال</label>
                                </div>
                                <div class="form-floating w-25">
                                    <input type="number" id="questionScore<?php echo $index + 1; ?>" name="questionScore[]" class="form-control" value="<?php echo htmlspecialchars($question['score']); ?>" required>
                                    <label for="questionScore<?php echo $index + 1; ?>">الدرجة</label>
                                </div>
                            </div>
                            <div class="correctTextAnswerContainer form-floating mb-3" id="correctTextAnswerContainer<?php echo $index + 1; ?>" style="display: <?php echo $question['question_type'] == 'text' ? 'block' : 'none'; ?>;">
                                <input type="text" class="correctTextAnswer form-control" name="correctTextAnswer[]" value="<?php echo htmlspecialchars($question['correct_answer']); ?>">
                                <label>الإجابة الصحيحة</label>
                            </div>
                            <div class="optionsContainer" id="optionsContainer<?php echo $index + 1; ?>" style="display: <?php echo $question['question_type'] == 'choice' ? 'block' : 'none'; ?>;">
                                <button type="button" class="addOption" data-question="<?php echo $index + 1; ?>">إضافة خيار</button>
                                <div class="optionsList d-flex flex-column gap-2">
                                    <?php if ($question['question_type'] == 'choice' && isset($question['choices'])): ?>
                                        <?php foreach ($question['choices'] as $choiceIndex => $choice): ?>
                                        <div class="input-group mb-2">
                                            <div class="input-group-text">
                                                <input class="correctAnswer" name="correctAnswer[<?php echo $index; ?>][]" type="checkbox" value="<?php echo $choiceIndex + 1; ?>" <?php echo ($question['correct_answer'] == $choice['choice_id']) ? 'checked' : ''; ?>>
                                            </div>
                                            <input type="text" class="form-control" name="options[<?php echo $index; ?>][]" value="<?php echo htmlspecialchars($choice['choice']); ?>" required>
                                            <button type="button" class="btn btn-danger removeOption">X</button>
                                        </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
    
            <button type="submit">تحديث الإختبار</button>
        </form>
    </main>

    <script>
        // Add option dynamically
        document.querySelectorAll('.addOption').forEach(button => {
            button.addEventListener('click', function() {
                const questionIndex = this.dataset.question;
                const optionsContainer = document.querySelector(`#optionsContainer${questionIndex} .optionsList`);
                const optionCount = optionsContainer.children.length + 1;
                
                const newOption = document.createElement('div');
                newOption.classList.add('input-group', 'mb-2');
                newOption.innerHTML = `
                    <div class="input-group-text">
                        <input class="correctAnswer" name="correctAnswer[${questionIndex - 1}][]" type="checkbox" value="${optionCount}">
                    </div>
                    <input type="text" class="form-control" name="options[${questionIndex - 1}][]" required>
                    <button type="button" class="btn btn-danger removeOption">X</button>
                `;
                
                optionsContainer.appendChild(newOption);
                
                // Remove option dynamically
                newOption.querySelector('.removeOption').addEventListener('click', function() {
                    newOption.remove();
                });
            });
        });

        // Toggle correct answer input field based on question type
        document.querySelectorAll('.questionType').forEach(select => {
            select.addEventListener('change', function() {
                const questionIndex = this.id.replace('questionType', '');
                const correctTextAnswerContainer = document.querySelector(`#correctTextAnswerContainer${questionIndex}`);
                const optionsContainer = document.querySelector(`#optionsContainer${questionIndex}`);

                if (this.value === 'text') {
                    correctTextAnswerContainer.style.display = 'block';
                    optionsContainer.style.display = 'none';
                } else if (this.value === 'choice') {
                    correctTextAnswerContainer.style.display = 'none';
                    optionsContainer.style.display = 'block';
                }
            });
        });

        // Remove option dynamically
        document.querySelectorAll('.removeOption').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.input-group').remove();
            });
        });
    </script>
</body>
</html>