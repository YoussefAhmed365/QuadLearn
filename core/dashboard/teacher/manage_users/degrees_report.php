<?php
require '../auth.php';

function sendJsonResponse($status, $message, $pageContent = '', $httpCode = 200)
{
    header('Content-Type: application/json');
    http_response_code($httpCode);
    echo json_encode(['status' => $status, 'message' => $message, 'pageContent' => $pageContent]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    // Fetch exams
    $exams = [];
    $stmt = $conn->prepare("SELECT id, title, created_at FROM exams WHERE teacher_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc())
        $exams[] = $row;
    $stmt->close();

    // Determine selected exam id
    $selected_id = 0;
    if (isset($_POST['test_id']) && $_POST['test_id']) {
        $selected_id = (int) $_POST['test_id'];
    }

    $students_degrees = [];
    $current_title = '';

    if ($selected_id > 0) {
        foreach ($exams as $exam) {
            if ($exam['id'] == $selected_id) {
                $current_title = htmlspecialchars($exam['title']);
                break;
            }
        }
        $stmt = $conn->prepare("SELECT u.id AS student_id, u.first_name, u.last_name, u.gender, s.level, e.title AS exam_title, ss.full_test_degree, ss.score, ss.test_date
                        FROM student_score ss
                        JOIN users u ON ss.student_email = u.email
                        JOIN students s ON u.id = s.id
                        JOIN exams e ON ss.exam_id = e.id
                        WHERE e.id = ?
                        ORDER BY u.last_name, u.first_name");
        $stmt->bind_param("i", $selected_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc())
            $students_degrees[] = $row;
        $stmt->close();
    }

    // ... (after fetching $students_degrees)
    $total_score = 0;
    $scores = [];
    if ($students_degrees) {
        foreach ($students_degrees as $student) {
            $total_score += (float)$student['score'];
            $scores[] = (float)$student['score'];
        }
        $average_score = count($scores) > 0 ? $total_score / count($scores) : 0;
        $highest_score = count($scores) > 0 ? max($scores) : 0;
        $lowest_score = count($scores) > 0 ? min($scores) : 0;
    }

    ob_start();
    ?>
    <div class="container-fluid p-3">
        <h3 class="mb-3">
            تقرير الدرجات
            <?php if ($selected_id > 0): ?>
                <span class="text-primary"> (<?php echo $current_title ?: ("#$selected_id"); ?>)</span>
            <?php endif; ?>
        </h3>
        <form class="mb-3">
            <div class="row g-2 align-items-center">
                <div class="col-auto">
                    <label for="degreeReport_test_id_selector" class="col-form-label fw-bold">اختر اختبار:</label>
                </div>
                <div class="col-md-6 col-lg-4">
                    <select name="test_id" id="degreeReport_test_id_selector" class="form-select form-select-sm"
                        style="min-width: 250px;">
                        <option value="">-- اختر اختبار --</option>
                        <?php
                        foreach ($exams as $exam) :
                        ?>
                            <option value="<?php echo $exam['id']; ?>" <?php if ($selected_id == $exam['id']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($exam['title']); ?>
                                (<?php echo date("d M Y", strtotime($exam['created_at'])); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </form>
        <?php if ($selected_id > 0): ?>
            <?php if ($students_degrees): ?>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>متوسط الدرجات:</strong> <?php echo round($average_score, 2); ?></div>
                    <div class="col-md-3"><strong>أعلى درجة:</strong> <?php echo $highest_score; ?></div>
                    <div class="col-md-3"><strong>أقل درجة:</strong> <?php echo $lowest_score; ?></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered caption-top">
                        <caption class="text-muted small">عدد الطلاب: <?php echo count($students_degrees); ?></caption>
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>كود الطالب</th>
                                <th>الاسم</th>
                                <th>الصف</th>
                                <th>النوع</th>
                                <th>الدرجة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 1;
                            foreach ($students_degrees as $student): ?>
                                <?php
                                // Translate gender
                                $translated_gender = match (strtolower($student['gender'] ?? '')) {
                                    'male' => 'ذكر',
                                    'female' => 'أنثى',
                                    default => $student['gender'] ?? 'غير محدد', // Fallback to original or 'غير محدد'
                                };

                                // Translate level
                                $translated_level = match (strtolower($student['level'] ?? '')) {
                                    'first' => 'الأولى',
                                    'second' => 'الثانية',
                                    'third' => 'الثالثة',
                                    default => $student['level'] ?? 'غير محدد', // Fallback to original or 'غير محدد'
                                };
                                ?>
                                <tr>
                                    <td><?php echo $i++; ?></td>
                                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($translated_level); ?></td>
                                    <td><?php echo htmlspecialchars($translated_gender); ?></td>
                                    <td>
                                        <?php
                                        echo htmlspecialchars($student['score']) . ' / ' . htmlspecialchars($student['full_test_degree']);
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center mt-3">لا توجد درجات لهذا الاختبار.</div>
            <?php endif; ?>
        <?php else: ?>
            <div class="alert alert-secondary text-center mt-3">يرجى اختيار اختبار لعرض الدرجات.</div>
        <?php endif; ?>
    </div>
    <?php
    $html = ob_get_clean();
    sendJsonResponse('success', 'تم تحميل تقرير الدرجات.', $html);
}
?>