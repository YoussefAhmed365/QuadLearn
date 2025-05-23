<?php
require '../auth.php';
require_once __DIR__ . '/../../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

header('Content-Type: application/json; charset=utf-8');

function sendJsonResponse($status, $message, $httpStatusCode = 200)
{
    http_response_code($httpStatusCode);
    echo json_encode(["status" => $status, "message" => $message]);
    exit;
}

// --- Input Validation ---
if (!isset($_FILES['excelFile']) || $_FILES['excelFile']['error'] !== UPLOAD_ERR_OK) {
    $error_message = "حدث خطأ في رفع الملف أو لم يتم تحديد ملف صالح. كود الخطأ: " . ($_FILES['excelFile']['error'] ?? 'غير معروف');
    sendJsonResponse("error", $error_message, 400); // Bad Request
}

$tmpName = $_FILES['excelFile']['tmp_name'];
$fileName = $_FILES['excelFile']['name'];
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

// Validate file extension server-side
if ($fileExt !== 'xlsx') {
    sendJsonResponse("error", "نوع الملف غير مدعوم. يرجى رفع ملف .xlsx فقط.", 415); // Unsupported Media Type
}

// Get and validate exam title from POST data
// The JS should ensure 'examTitle' is populated correctly based on the checkbox
$exam_title = trim($_POST["examTitle"] ?? '');

if (empty($exam_title)) {
    sendJsonResponse("error", "عنوان الاختبار مطلوب.", 400); // Bad Request
}

// Assuming $user_id is set by auth.php and connection $conn is available and valid
if (!isset($user_id) || !$conn) {
    sendJsonResponse("error", "خطأ في تهيئة المستخدم أو قاعدة البيانات.", 500); // Internal Server Error
}

// --- Load Spreadsheet ---
try {
    // Use IOFactory to create the appropriate reader based on file extension
    $reader = IOFactory::createReaderForFile($tmpName);
    $spreadsheet = $reader->load($tmpName);
} catch (ReaderException $e) {
    sendJsonResponse("error", "حدث خطأ أثناء قراءة ملف Excel: " . $e->getMessage(), 500); // Internal Server Error
} catch (\Exception $e) {
    // Catch any other exceptions during loading
    sendJsonResponse("error", "حدث خطأ غير متوقع أثناء معالجة ملف Excel: " . $e->getMessage(), 500); // Internal Server Error
}

// Get the active worksheet
$worksheet = $spreadsheet->getActiveSheet();

// Use toArray with formatting and header row to get data easily (1-based indexing with headers)
$rows = $worksheet->toArray(null, true, true, true);

// Check if the sheet is empty or has only headers
if (count($rows) <= 1) {
    sendJsonResponse("error", "ملف Excel فارغ أو يحتوي على رؤوس الأعمدة فقط.", 400); // Bad Request
}

// --- Insert Exam Data ---
$stmt_exam = $conn->prepare("INSERT INTO exams (teacher_id, title) VALUES (?, ?)");

if (!$stmt_exam) {
    sendJsonResponse("error", "فشل تحضير استعلام إدخال الامتحان: {$conn->error}", 500); // Internal Server Error
}

$stmt_exam->bind_param("is", $user_id, $exam_title);

if (!$stmt_exam->execute()) {
    sendJsonResponse("error", "فشل إدخال بيانات الامتحان: {$stmt_exam->error}", 500); // Internal Server Error
}
$exam_id = $conn->insert_id;
$stmt_exam->close();


// --- Insert Student Scores ---
// Column mapping: A=Timestamp, B=Email, C=Score (adjust 'A', 'B', 'C' if columns are different)
$stmt_score = $conn->prepare("INSERT INTO student_score (teacher_id, exam_id, student_email, test_date, full_test_degree, score) VALUES (?, ?, ?, ?, ?, ?)");

if (!$stmt_score) {
    // Delete the exam record just inserted to avoid orphans if transaction isn't used
    $delete_exam_stmt = $conn->prepare("DELETE FROM exams WHERE id = ?");
    $delete_exam_stmt->bind_param("i", $exam_id);
    $delete_exam_stmt->execute();
    $delete_exam_stmt->close();

    sendJsonResponse("error", "فشل تحضير استعلام إدخال درجات الطلاب: {$conn->error}", 500); // Internal Server Error
}

$inserted_count = 0;
$error_rows = []; // Array to collect row numbers with errors

// Loop through rows, starting from the second row (index 2 assuming 1-based index from toArray)
for ($i = 2; $i <= count($rows); $i++) {
    $rowData = $rows[$i];

    // Extract data using column references (adjust 'A', 'B', 'C' based on file)
    $timestamp_raw = $rowData['A'] ?? null; // Assuming Timestamp is in column A
    $email = $rowData['B'] ?? null; // Assuming Email is in column B
    $score_raw = $rowData['C'] ?? null; // Assuming Score is in column C

    // --- Data Cleaning and Validation for each row ---

    // Trim whitespace from values
    $email = trim($email);
    $score_raw = trim($score_raw);

    // Basic row validation: Email and Score are required
    if (empty($email) || empty($score_raw)) {
        $error_rows[] = $i; // Add row number to error list
        // Log this error
        file_put_contents('upload_errors.log', "Skipping row $i: Missing email or score.\n", FILE_APPEND);
        continue; // Skip processing this row
    }

    // Process Timestamp
    $timestamp = null;
    if ($timestamp_raw !== null && $timestamp_raw !== '') {
        try {
            // Check if it's a numeric Excel date value
            if (is_numeric($timestamp_raw)) {
                // Convert Excel date to Unix timestamp, then format
                $unixTimestamp = Date::excelToTimestamp($timestamp_raw);
                // Check if conversion was successful (PhpSpreadsheet < 1.12 returns false on failure, >= 1.12 throws exception)
                $timestamp = ($unixTimestamp !== false) ? date('Y-m-d H:i:s', $unixTimestamp) : date('Y-m-d H:i:s');
            } else {
                // Try to parse as a string date/time (e.g., "m/d/Y H:i:s")
                $dt = DateTime::createFromFormat('m/d/Y H:i:s', $timestamp_raw); // Adjust format if needed
                $timestamp = $dt ? $dt->format('Y-m-d H:i:s') : date('Y-m-d H:i:s');
            }
        } catch (\Exception $e) {
            $timestamp = date('Y-m-d H:i:s');
            file_put_contents('upload_errors.log', "Row $i: Error processing timestamp '{$timestamp_raw}': {$e->getMessage()}. Using current time.\n", FILE_APPEND);
        }
    } else {
        $timestamp = date('Y-m-d H:i:s');
    }


    // Process Score "X / Y"
    $studentScore = 0.0;
    $examScore = 0.0;
    $parts = explode('/', $score_raw);

    if (count($parts) == 2) {
        $studentScore_str = trim($parts[0]);
        $examScore_str = trim($parts[1]);

        // Ensure parts are numeric before casting
        if (is_numeric($studentScore_str) && is_numeric($examScore_str)) {
            $studentScore = (double) $studentScore_str;
            $examScore = (double) $examScore_str;
            // Basic check: student score should not exceed exam score
            if ($studentScore > $examScore && $examScore > 0) {

                file_put_contents('upload_errors.log', "Row $i: Student score ({$studentScore}) exceeds exam score ({$examScore}).\n", FILE_APPEND);
                //skip row
                $error_rows[] = $i; // Add row number to error list
                continue; // Skip this row
            }
        } else {
            $error_rows[] = $i;
            file_put_contents('upload_errors.log', "Row $i: Invalid numeric format in score '{$score_raw}'. Skipping insert.\n", FILE_APPEND);
            continue; // Skip insertion for this row
        }
    } elseif (is_numeric($score_raw)) {
        // Handle cases where the score is just a single number
        $studentScore = (double) $score_raw;
        $examScore = 0.0;
        file_put_contents('upload_errors.log', "Row $i: Score is a single number '{$score_raw}'. Treating as student score only.\n", FILE_APPEND);
    } else {
        // Score is neither "X / Y" nor a single number
        $error_rows[] = $i;
        file_put_contents('upload_errors.log', "Row $i: Unparseable score value '{$score_raw}'. Skipping insert.\n", FILE_APPEND);
        continue; // Skip insertion for this row
    }


    // --- Execute Score Insertion ---
    if (!$stmt_score->bind_param("iissdd", $user_id, $exam_id, $email, $timestamp, $examScore, $studentScore)) {
        file_put_contents('upload_errors.log', "Row $i: Binding parameters failed: " . $stmt_score->error . "\n", FILE_APPEND);
        sendJsonResponse("error", "خطأ فني أثناء تجهيز بيانات الصف رقم {$i}.", 500); // Report a general error and stop
    }

    // Execute the prepared statement for the current row
    // Indicates a database error during execution for this row
    if (!$stmt_score->execute()) {
        $error_rows[] = $i; // Add row number to error list
        file_put_contents('upload_errors.log', "Row $i: Database execution failed: {$stmt_score->error}\n", FILE_APPEND);
        continue;
    }

    $inserted_count++; // Increment count only for successfully inserted rows
}

$stmt_score->close();
$conn->close();

if (empty($error_rows)) {
    sendJsonResponse("success", "تم استيراد البيانات بنجاح. تم إدخال {$inserted_count} سجل.");
} else {
    $total_rows_processed = count($rows) - 1; // Excluding header
    $skipped_rows_count = count($error_rows);
    $message = "تم استيراد بيانات {$inserted_count} سجل بنجاح من أصل {$total_rows_processed} سجل في الملف.";
    $message .= " تم تخطي أو حدثت أخطاء في {$skipped_rows_count} سجل (الصفوف: " . implode(', ', $error_rows) . "). قد تكون البيانات في هذه الصفوف ناقصة أو غير صالحة. حاول إصلاح هذه السجلات وإدخالها مرة أخرى يدوياً، وإذا لم تكن بها مشكلة يمكنك المحاولة لاحقاً أو التواصل مع فريق الدعم الفني.";
    // Warning can be assigned as Bad Request "400" instead of success "200"
    // But for user experience, we keep it as success "200" with warning message
    sendJsonResponse("warning", $message, 200);
}