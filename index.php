<?php
// MUST be the very first thing before ANY output (even whitespace)
session_start();

require 'core/db_connect.php'; // Ensure this path is correct and $conn is established

$user_id = null;
$accountType = null; // Initialize account type

// --- 1. Check for User ID ---

// Check session first
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // Optimization: Check if account type is already in session to potentially avoid DB query
    if (isset($_SESSION['account_type'])) {
        $accountType = $_SESSION['account_type'];
    }
}
// If not in session, check remember me cookie
elseif (isset($_COOKIE['remember_me'])) {
    $remember_token = $_COOKIE['remember_me'];
    $stmt = null; // Initialize statement variable

    try {
        // Prepare and execute query to find user by remember token
        $sql = "SELECT id, account_type FROM users WHERE remember_token = ? LIMIT 1"; // Fetch account_type too
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Prepare failed: {$conn->error}");
        }
        $stmt->bind_param("s", $remember_token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            // Set session variables upon successful cookie validation
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['account_type'] = $row['account_type']; // Store account type in session
            $user_id = $row['id'];
            $accountType = $row['account_type'];
        } else {
            // Invalid token, clear the cookie
            setcookie("remember_me", "", time() - 3600, "/");
        }
    } catch (Exception $e) {
        error_log("Error retrieving user from cookie: " . $e->getMessage());
        // Optionally clear cookie on error too
        setcookie("remember_me", "", time() - 3600, "/");
    } finally {
        // Close statement if it was prepared
        if ($stmt) {
            $stmt->close();
        }
    }
}

// --- 2. Redirect if Logged In ---

// Check if we successfully identified a user and their account type
if (isset($user_id) && isset($accountType)) {

    // If account type wasn't found earlier (e.g., only user_id was in session), fetch it now
    if ($accountType === null) {
        $stmt = null; // Initialize statement variable
        try {
            $sql = "SELECT account_type FROM users WHERE id = ? LIMIT 1";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Prepare failed: {$conn->error}");
            }
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                $accountType = $row['account_type'];
                $_SESSION['account_type'] = $accountType; // Store in session for next time
            } else {
                // User ID exists but couldn't find account type - data inconsistency? Log out.
                error_log("Inconsistent state: User ID {$user_id} found but no account type.");
                $user_id = null; // Invalidate user
                $accountType = null;
            }
        } catch (Exception $e) {
            error_log("Error retrieving account type for user ID {$user_id}: " . $e->getMessage());
            $user_id = null; // Invalidate user on error
            $accountType = null;
        } finally {
            if ($stmt) {
                $stmt->close();
            }
        }
    }
}

// استعلامات SQL معدلة لحساب عدد الطلاب والمعلمين في استعلام واحد
$sqlCounts = "
SELECT 
    COUNT(CASE WHEN account_type = 'student' THEN 1 END) AS studentCount,
    COUNT(CASE WHEN account_type = 'teacher' THEN 1 END) AS teacherCount
FROM users";
    $resultCounts = $conn->query($sqlCounts);
    $rowCounts = $resultCounts->fetch_assoc();
    $studentCount = htmlspecialchars($rowCounts['studentCount'], ENT_QUOTES, 'UTF-8');
    $teacherCount = htmlspecialchars($rowCounts['teacherCount'], ENT_QUOTES, 'UTF-8');

    $sqlLessons = "SELECT COUNT(*) AS lessonCount FROM videos";
    $resultLessons = $conn->query($sqlLessons);
    $rowLessons = $resultLessons->fetch_assoc();
    $lessonCount = $rowLessons['lessonCount'];

// --- 3. Perform Redirect or Cleanup ---

// Now, make the final decision based on whether we have valid user data
if (isset($user_id) && isset($accountType)) {
    // Determine the correct dashboard directory
    $directory = ($accountType === "teacher") ? "core/dashboard/teacher/main/dashboard.php" : "core/dashboard/student/main/dashboard.php";

    // Close the database connection *before* redirecting
    $conn->close();

    // Perform server-side redirect
    header("Location: {$directory}");
    exit(); // IMPORTANT: Stop script execution immediately after redirect header

} else {
    // User is not logged in or data was invalid/inconsistent
    // Clear potentially corrupted session/cookie data
    session_unset();
    session_destroy();
    // Ensure cookie is cleared even if checks failed earlier
    if (isset($_COOKIE['remember_me'])) {
        setcookie("remember_me", "", time() - 3600, "/");
    }

    // Close the connection if it's still open
    if (isset($conn) && $conn->ping()) { // Check if connection is still alive before closing
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/images/favicon-16x16.ico" sizes="16x16" type="image/x-icon">
    <link rel="icon" href="assets/images/favicon-32x32.ico" sizes="32x32" type="image/x-icon">
    <link rel="icon" href="assets/images/favicon-48x48.ico" sizes="48x48" type="image/x-icon">
    <link rel="apple-touch-icon" href="assets/images/apple-touch-icon-180x180.ico" sizes="180x180">
    <meta name="description" content="#">
    <meta name="keywords" content="#">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" integrity="sha384-nU14brUcp6StFntEOOEBvcJm4huWjB0OcIeQ3fltAfSmuZFrkAif0T+UtNGlKKQv" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>QuadLearn | طريقك للتعلم الصحيح</title>
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
    <div id="pageContent">
        <div class="position-absolute" id="cookie">
            <div class="card position-fixed z-2">
                <svg xml:space="preserve" viewBox="0 0 122.88 122.25" y="0px" x="0px" id="cookieSvg" version="1.1"><g><path d="M101.77,49.38c2.09,3.1,4.37,5.11,6.86,5.78c2.45,0.66,5.32,0.06,8.7-2.01c1.36-0.84,3.14-0.41,3.97,0.95 c0.28,0.46,0.42,0.96,0.43,1.47c0.13,1.4,0.21,2.82,0.24,4.26c0.03,1.46,0.02,2.91-0.05,4.35h0v0c0,0.13-0.01,0.26-0.03,0.38 c-0.91,16.72-8.47,31.51-20,41.93c-11.55,10.44-27.06,16.49-43.82,15.69v0.01h0c-0.13,0-0.26-0.01-0.38-0.03 c-16.72-0.91-31.51-8.47-41.93-20C5.31,90.61-0.73,75.1,0.07,58.34H0.07v0c0-0.13,0.01-0.26,0.03-0.38 C1,41.22,8.81,26.35,20.57,15.87C32.34,5.37,48.09-0.73,64.85,0.07V0.07h0c1.6,0,2.89,1.29,2.89,2.89c0,0.4-0.08,0.78-0.23,1.12 c-1.17,3.81-1.25,7.34-0.27,10.14c0.89,2.54,2.7,4.51,5.41,5.52c1.44,0.54,2.2,2.1,1.74,3.55l0.01,0 c-1.83,5.89-1.87,11.08-0.52,15.26c0.82,2.53,2.14,4.69,3.88,6.4c1.74,1.72,3.9,3,6.39,3.78c4.04,1.26,8.94,1.18,14.31-0.55 C99.73,47.78,101.08,48.3,101.77,49.38L101.77,49.38z M59.28,57.86c2.77,0,5.01,2.24,5.01,5.01c0,2.77-2.24,5.01-5.01,5.01 c-2.77,0-5.01-2.24-5.01-5.01C54.27,60.1,56.52,57.86,59.28,57.86L59.28,57.86z M37.56,78.49c3.37,0,6.11,2.73,6.11,6.11 s-2.73,6.11-6.11,6.11s-6.11-2.73-6.11-6.11S34.18,78.49,37.56,78.49L37.56,78.49z M50.72,31.75c2.65,0,4.79,2.14,4.79,4.79 c0,2.65-2.14,4.79-4.79,4.79c-2.65,0-4.79-2.14-4.79-4.79C45.93,33.89,48.08,31.75,50.72,31.75L50.72,31.75z M119.3,32.4 c1.98,0,3.58,1.6,3.58,3.58c0,1.98-1.6,3.58-3.58,3.58s-3.58-1.6-3.58-3.58C115.71,34.01,117.32,32.4,119.3,32.4L119.3,32.4z M93.62,22.91c2.98,0,5.39,2.41,5.39,5.39c0,2.98-2.41,5.39-5.39,5.39c-2.98,0-5.39-2.41-5.39-5.39 C88.23,25.33,90.64,22.91,93.62,22.91L93.62,22.91z M97.79,0.59c3.19,0,5.78,2.59,5.78,5.78c0,3.19-2.59,5.78-5.78,5.78 c-3.19,0-5.78-2.59-5.78-5.78C92.02,3.17,94.6,0.59,97.79,0.59L97.79,0.59z M76.73,80.63c4.43,0,8.03,3.59,8.03,8.03 c0,4.43-3.59,8.03-8.03,8.03s-8.03-3.59-8.03-8.03C68.7,84.22,72.29,80.63,76.73,80.63L76.73,80.63z M31.91,46.78 c4.8,0,8.69,3.89,8.69,8.69c0,4.8-3.89,8.69-8.69,8.69s-8.69-3.89-8.69-8.69C23.22,50.68,27.11,46.78,31.91,46.78L31.91,46.78z M107.13,60.74c-3.39-0.91-6.35-3.14-8.95-6.48c-5.78,1.52-11.16,1.41-15.76-0.02c-3.37-1.05-6.32-2.81-8.71-5.18 c-2.39-2.37-4.21-5.32-5.32-8.75c-1.51-4.66-1.69-10.2-0.18-16.32c-3.1-1.8-5.25-4.53-6.42-7.88c-1.06-3.05-1.28-6.59-0.61-10.35 C47.27,5.95,34.3,11.36,24.41,20.18C13.74,29.69,6.66,43.15,5.84,58.29l0,0.05v0h0l-0.01,0.13v0C5.07,73.72,10.55,87.82,20.02,98.3 c9.44,10.44,22.84,17.29,38,18.1l0.05,0h0v0l0.13,0.01h0c15.24,0.77,29.35-4.71,39.83-14.19c10.44-9.44,17.29-22.84,18.1-38l0-0.05 v0h0l0.01-0.13v0c0.07-1.34,0.09-2.64,0.06-3.91C112.98,61.34,109.96,61.51,107.13,60.74L107.13,60.74z M116.15,64.04L116.15,64.04 L116.15,64.04L116.15,64.04z M58.21,116.42L58.21,116.42L58.21,116.42L58.21,116.42z"></path></g></svg>
                <p class="cookieHeading">نحن نستخدم الكوكيز</p>
                <p class="cookieDescription">نحن نستخدم الكوكيز للتأكد من أنك تحصل على أفضل تجربة على منصتنا. <br><a href="#">راجع سياسات الكوكيز</a>.</p>
                <div class="buttonContainer">
                    <button class="acceptButton" id="acceptCookies">موافق</button>
                    <button class="declineButton" id="rejectCookies">رفض</button>
                </div>
            </div>
        </div>
        <nav class="navbar navbar-expand-sm" id="navbar" aria-label="Third navbar example">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
    
                <a class="navbar-brand col-lg-3 me-5" href="#">
                    <img src="assets/images/logo.svg" alt="Logo" height="57.26px">
                </a>
                
                <div class="collapse navbar-collapse d-lg-flex justify-content-between" id="mainNavbar">
                    
                    <ul class="navbar-nav col-lg-6 justify-content-lg-center">
                        <li class="nav-item">
                            <a class="nav-link text-truncate active" aria-current="page" href="index.php">الرئيسية</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-truncate" href="#">نبذة عنا</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-truncate" href="#">عن المحتوى</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-truncate" href="#">المهارات</a>
                        </li>
                    </ul>
    
                    <div class="d-lg-flex-col-lg-3 justify-content-lg-end">
                        <a href="core/signup/signup.php" class="d-flex justify-content-center align-items-center text-decoration-none">
                            <button class="signup rounded">إبدأ الآن <small>— إنضم إلينا</small></button>
                        </a>
                    </div>
                </div>
            </div>
        </nav>
        <header id="Home" class="overflow-hidden">
            <div class="header row ps-5 pe-5 h-100">
                <section class="col d-flex flex-column justify-content-center ps-5">
                    <h1 class="mb-4 text-default">انضم إلينا اليوم وابدأ رحلتك التعليمية</h1>
                    <h5 class="text-secondary mb-4">منصة تعليمية متكاملة مصممة خصيصًا لتوفر لك بيئة تعليمية تفاعلية، وطرق رائعة في التعلم مع QuadLeran بفضل واجهة المستخدم البسيطة، وسهولة الوصول للمحتوى في أي وقت.</h5>
                    <div class="btns d-flex justify-content-start align-items-center gap-4 mt-3">
                        <a href="core/signup/signup.php" class="text-decoration-none">
                            <button class="start rounded d-flex justify-content-center align-items-center">إبدأ التعلم</button>
                        </a>
                        <a href="#about" class="paly text-decoration-none d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="#2c6975" class="bi bi-play-circle-fill" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0M6.79 5.093A.5.5 0 0 0 6 5.5v5a.5.5 0 0 0 .79.407l3.5-2.5a.5.5 0 0 0 0-.814l-3.5-2.5"></path>
                            </svg>
                            <span class="text-truncate ms-2">شاهد كيف يعمل</span>
                        </a>
                    </div>
                </section>
                <section class="col d-flex justify-content-center align-items-center">
                    <div class="left">
                        <div class="sr">
                            <div class="shape1"></div>
                            <div class="shape2"></div>
                            <div></div>
                        </div>
                        <div class="sl">
                            <div></div>
                            <div class="shape3"></div>
                            <div class="shape4"></div>
                        </div>
                    </div>
                </section>
            </div>
        </header>
        <section class="bar" id="About">
            <div class="box1">
                <h3>+<?php echo $lessonCount; ?></h3>
                <h4>درس تعليمي</h4>
            </div>
            <div class="line"></div>
            <div class="box2">
                <h3>+<?php echo $studentCount; ?></h3>
                <h4>طالب مسجل</h4>
            </div>
            <div class="line"></div>
            <div class="box3">
                <h3>+<?php echo $teacherCount; ?></h3>
                <h4>معلم</h4>
            </div>
        </section>
        <main>
            <div class="about" id="about">
                <section class="row d-flex justify-content-center align-items-center gap-5">
                    <div class="col d-flex justify-content-center">
                        <div class="video">
                            <video id="video" controls>
                                <source src="assets/videos/preview.mp4" type="video/mp4">
                            </video>
                        </div>
                    </div>
                    <div class="col d-flex flex-column justify-content-center">
                        <h6 class="text-title">الـتـعـلـم نـحـو الأفـضـل</h6>
                        <h1 class="text-default mb-3">التفاعل مع مجتمع تعليمي نابض بالحياة</h1>
                        <p class="fs-5 mb-4">تواصل مع معلميك وزملائك، وشارك في نقاشات مثمرة، وتبادل الأفكار والمعلومات، واحصل على الدعم والتشجيع الذي تحتاجه للنجاح.</p>
                        <a href="core/signup/signup.php" class="d-flex justify-content-start align-items-center text-decoration-none">
                            <button class="signup rounded">إبدأ الآن <small>— إنضم إلينا</small></button>
                        </a>
                    </div>
                </section>
                <section class="row d-flex justify-content-center align-items-center gap-5" id="Content">
                    <div class="col">
                        <h1 class="text-default mb-3">انضم إلى QuadLearn اليوم وابدأ رحلتك نحو مستقبل تعليمي مشرق!</h1>
                        <ul class="fs-6 d-flex flex-column gap-2 text-decoration-underline mb-5">
                            <li><h6 class="text-secondary">دروس مسجلة بتقنية عالية، مع إمكانية التفاعل وطرح الأسئلة على المعلم.</h6></li>
                            <li><h6 class="text-secondary">اختبارات مصممة بعناية لقياس فهمك للمواد وتتبع تقدمك.</h6></li>
                            <li><h6 class="text-secondary">تواصل مع معلميك وزملائك وتبادل الأفكار والمعلومات.</h6></li>
                            <li><h6 class="text-secondary">واجهة مستخدم بسيطة وتطبيق للهواتف الذكية للتعلم أثناء التنقل.</h6></li>
                            <li><h6 class="text-secondary">ملفات PDF، وملاحظات، وأمثلة محلولة لمساعدتك على فهم المواد بشكل أعمق.</h6></li>
                        </ul>
                        <a href="core/signup/signup.php" class="d-flex justify-content-start align-items-center text-decoration-none">
                            <button class="signup rounded">إبدأ الآن <small>— إنضم إلينا</small></button>
                        </a>
                    </div>
                    <div class="col d-flex justify-content-center align-items-center">
                        <img src="assets/images/Learning-bro.svg" alt="learning-bro">
                    </div>
                </section>
            </div>
            <div class="icon-describe row gap-5">
                <div class="col d-flex justify-content-center align-items-center">
                    <div class="icon-box">
                        <div class="icon-container">
                            <svg xmlns="http://www.w3.org/2000/svg" height="34" width="34" fill="white" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2023 Fonticons, Inc.--><path d="M96 0C43 0 0 43 0 96V416c0 53 43 96 96 96H384h32c17.7 0 32-14.3 32-32s-14.3-32-32-32V384c17.7 0 32-14.3 32-32V32c0-17.7-14.3-32-32-32H384 96zm0 384H352v64H96c-17.7 0-32-14.3-32-32s14.3-32 32-32zm32-240c0-8.8 7.2-16 16-16H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16zm16 48H336c8.8 0 16 7.2 16 16s-7.2 16-16 16H144c-8.8 0-16-7.2-16-16s7.2-16 16-16z"></path></svg>
                        </div>
                        <h5>دروس مشروحة بطريقة مبسطة</h5>
                    </div>
                </div>
                <div class="col d-flex justify-content-center align-items-center">
                    <div class="icon-box">
                        <div class="icon-container">
                            <svg xmlns="http://www.w3.org/2000/svg" height="34" width="34" fill="white" viewBox="0 0 640 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2023 Fonticons, Inc.--><path d="M160 64c0-35.3 28.7-64 64-64H576c35.3 0 64 28.7 64 64V352c0 35.3-28.7 64-64 64H336.8c-11.8-25.5-29.9-47.5-52.4-64H384V320c0-17.7 14.3-32 32-32h64c17.7 0 32 14.3 32 32v32h64V64L224 64v49.1C205.2 102.2 183.3 96 160 96V64zm0 64a96 96 0 1 1 0 192 96 96 0 1 1 0-192zM133.3 352h53.3C260.3 352 320 411.7 320 485.3c0 14.7-11.9 26.7-26.7 26.7H26.7C11.9 512 0 500.1 0 485.3C0 411.7 59.7 352 133.3 352z"></path></svg>
                        </div>
                        <h5>متابعة مستمرة من المعلمين</h5>
                    </div>
                </div>
                <div class="col d-flex justify-content-center align-items-center">
                    <div class="icon-box">
                        <div class="icon-container">
                            <svg xmlns="http://www.w3.org/2000/svg" height="34" width="34" fill="white" viewBox="0 0 384 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2023 Fonticons, Inc.--><path d="M64 464c-8.8 0-16-7.2-16-16V64c0-8.8 7.2-16 16-16H224v80c0 17.7 14.3 32 32 32h80V448c0 8.8-7.2 16-16 16H64zM64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V154.5c0-17-6.7-33.3-18.7-45.3L274.7 18.7C262.7 6.7 246.5 0 229.5 0H64zm56 256c-13.3 0-24 10.7-24 24s10.7 24 24 24H264c13.3 0 24-10.7 24-24s-10.7-24-24-24H120zm0 96c-13.3 0-24 10.7-24 24s10.7 24 24 24H264c13.3 0 24-10.7 24-24s-10.7-24-24-24H120z"></path></svg>
                        </div>
                        <h5>إختبارات دورية لتحسين مستوى الطالب</h5>
                    </div>
                </div>
            </div>
            <section class="FAQ p-5 rounded-4">
                <div class="container-sm d-flex flex-column justify-content-center align-items-center">
                    <div class="text-center mb-5">
                        <h2 class="mb-2">الأسئلة الشائعة</h2>
                        <h5 class="text-secondary">سعداء بالإجابة عن أسئلتك، فلا تتردد في سؤالنا</h5>
                    </div>
                    <div class="accordion w-100 px-5" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                هل تغطي المنصة جميع المناهج الدراسية للمرحلة الثانوية؟
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    نعم، تغطي المنصة كافة المناهج الدراسية للمرحلة الثانوية بمصر كما تشمل شروحات وفيديوهات يوفرها المعلمين لطلابهم الملتحقين بهم ومراجعة مستمرة من المعلمين في كافة المواد الدراسية لجميع المراحل الثانوية
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                كيف يمكنني التواصل مع المعلمين في حال واجهت أي صعوبة؟
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                يمكنك التواصل مع معلميك مباشرة من خلال المنصة. هناك عدة طرق للتواصل مع المعلمين والمعلمين المساعدين إما عن طريق المجتمع حيث يمكنك إرسال منشورات عامة للمادة الدراسية ويجيب عنها المعلمين والطلاب أو إما عن طريق التحدث إلى المعلم عبر الواتس اب من خلال صفحة إدارة المعلمين والمواد
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                هل المنصة آمنة لحماية بيانات الطلاب؟
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                نعم، نولي اهتمامًا كبيرًا لأمن وخصوصية بيانات المستخدمين، ونقوم بتطبيق أحدث تقنيات الأمان لحماية بياناتك الشخصية. جميع البيانات المخزنة على المنصة مشفرة، ولا يمكن الوصول إليها إلا من قبل المستخدمين المصرح لهم.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                ما هي تكلفة الاشتراك في المنصة؟ هل هناك خيارات اشتراك مختلفة؟
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                الإجابة: تختلف أسعار الاشتراك في المنصة حسب المدة وعدد الميزات الإضافية التي تختارها. نقدم خيارات اشتراك متنوعة لتناسب جميع الميزانيات، بما في ذلك اشتراكات شهرية وسنوية. يمكنك الاطلاع على تفاصيل الخطط المتاحة والأسعار من خلال صفحة الأسعار في المنصة.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                هل يمكنني تحميل ملفاتي الشخصية (ملاحظات، مذكرات، إلخ) على المنصة؟
                                </button>
                            </h2>
                            <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    يمكنك رفع ملفاتك الشخصية بشكل آمن كالملاحظات والمذكرات  وملفاتك الدراسية الخاصة بك كما تشاء ما يصل إلى 1GB ولا يمكن لأحد الوصول إليها 
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <footer class="mt-5">
            <div class="hero_area">
                <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
                    <defs>
                        <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
                    </defs>
                    <g class="parallax">
                        <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(44, 150, 117, 0.7)" />
                        <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(44, 105, 117, 0.5)" />
                        <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(41, 92, 82, 0.3)" />
                        <use xlink:href="#gentle-wave" x="48" y="7" fill="#001418" />
                    </g>
                </svg>
            </div>
            <div class="container mt-4">
                <div class="logo text-center">
                    <img src="assets/images/logo.svg" alt="Logo">
                </div>
                <div class="links">
                    <ul>
                        <li>
                            <a href="#">سياسة الخصوصية</a>
                        </li>
                        <li>
                            <a href="#">إتفاقية الإستخدام</a>
                        </li>
                        <li>
                            <a href="#">تواصل معنا</a>
                        </li>
                    </ul>
                </div>
                <div class="icons">
                    <ul>
                        <li class="icon-li">
                            <a href="#" class="icon-a">
                                <svg xmlns="http://www.w3.org/2000/svg" height="35" width="35" fill="white" viewBox="0 0 512 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2023 Fonticons, Inc.--><path d="M512 256C512 114.6 397.4 0 256 0S0 114.6 0 256C0 376 82.7 476.8 194.2 504.5V334.2H141.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H287V510.1C413.8 494.8 512 386.9 512 256h0z"/></svg>
                            </a>
                        </li>
                        <li class="icon-li">
                            <a href="#" class="icon-a">
                                <svg xmlns="http://www.w3.org/2000/svg" height="35" width="33" fill="white" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2023 Fonticons, Inc.--><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"/></svg>
                            </a>
                        </li>
                        <li class="icon-li">
                            <a href="#" class="icon-a">
                                <svg xmlns="http://www.w3.org/2000/svg" height="35" width="37" fill="white" viewBox="0 0 576 512"><!--!Font Awesome Free 6.5.1 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2023 Fonticons, Inc.--><path d="M549.7 124.1c-6.3-23.7-24.8-42.3-48.3-48.6C458.8 64 288 64 288 64S117.2 64 74.6 75.5c-23.5 6.3-42 24.9-48.3 48.6-11.4 42.9-11.4 132.3-11.4 132.3s0 89.4 11.4 132.3c6.3 23.7 24.8 41.5 48.3 47.8C117.2 448 288 448 288 448s170.8 0 213.4-11.5c23.5-6.3 42-24.2 48.3-47.8 11.4-42.9 11.4-132.3 11.4-132.3s0-89.4-11.4-132.3zm-317.5 213.5V175.2l142.7 81.2-142.7 81.2z"/></svg>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="rights">
                    <h6>QuadLearn 2025 © All Rights Reserved</h6>
                </div>
            </div>
        </footer>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/35b8a1f8f5.js" crossorigin="anonymous"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>