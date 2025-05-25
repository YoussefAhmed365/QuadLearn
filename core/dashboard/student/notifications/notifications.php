<?php
require '../auth.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../../../../assets/images/favicon-16x16.ico" sizes="16x16" type="image/x-icon">
    <link rel="icon" href="../../../../assets/images/favicon-32x32.ico" sizes="32x32" type="image/x-icon">
    <link rel="icon" href="../../../../assets/images/favicon-48x48.ico" sizes="48x48" type="image/x-icon">
    <link rel="apple-touch-icon" href="../../../../assets/images/apple-touch-icon-180x180.ico" sizes="180x180">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" integrity="sha384-nU14brUcp6StFntEOOEBvcJm4huWjB0OcIeQ3fltAfSmuZFrkAif0T+UtNGlKKQv" crossorigin="anonymous">
    <link rel="stylesheet" href="../../../../assets/css/student_notifications.css">
    <link rel="stylesheet" href="../../../../assets/css/#default-styles.css">
    <title>صندوق الإشعارات | QuadLearn</title>
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
                <a href="../main/dashboard.php">
                    <div class="icon">
                        <i class="fa-solid fa-house"></i>
                    </div>
                    <span class="item">الرئيسية</span>
                </a>
            </li>
            <li class="activated">
                <a href="notifications.php" class="active">
                    <div class="icon">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <span class="item">الإشعارات</span>
                </a>
            </li>
            <li>
                <a href="../files/subjects.php">
                    <div class="icon">
                        <i class="fa-solid fa-book"></i>
                    </div>
                    <span class="item">المحتوى الدراسي</span>
                </a>
            </li>
            <li>
                <a href="../lessons/videos.php">
                    <div class="icon">
                        <i class="fa-solid fa-circle-play"></i>
                    </div>
                    <span class="item">الدروس</span>
                </a>
            </li>
            <li>
                <a href="../community/community.php">
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
                <a href="../instructors/show-teachers.php">
                    <div class="icon">
                        <i class="fa-solid fa-chalkboard-user"></i>
                    </div>
                    <span class="item">المعلمين</span>
                </a>
            </li>
            <li>
                <a href="../settings/settings.php">
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
        <div class="container">
            <header class="manage mb-4">
                <nav class="row">
                    <div class="col-sm-6 text">
                        <h1 class="d-none d-sm-block">الإشعارات</h1>
                        <nav class="navbar d-block d-sm-none">
                            <div class="container-fluid">
                                <h1>الإشعارات</h1>
                                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas"data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar"aria-label="Toggle navigation">
                                    <span class="navbar-toggler-icon"></span>
                                </button>
                                <div class="offcanvas offcanvas-end w-75 fs-5" tabindex="-1" id="offcanvasNavbar"aria-labelledby="offcanvasNavbarLabel">
                                    <div class="offcanvas-header">
                                        <h5 class="offcanvas-title" id="offcanvasNavbarLabel"><img src="../../../../assets/images/logo.svg" alt="QuadLearn" class="me-3" style="width: 40px;">QuadLearn</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"aria-label="Close"></button>
                                    </div>
                                    <div class="offcanvas-body">
                                        <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                                            <li class="nav-item">
                                                <a class="nav-link" aria-current="page" href="../main/dashboard.php">الرئيسية</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link active" href="notifications.php">الإشعارات</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="../files/subjects.php">المحتوى الدراسي</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="../lessons/videos.php">الدروس</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="../community/community.php">المجتمع</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="../instructors/show-teachers.php">المعلمين</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="../settings/settings.php">الإعدادات</a>
                                            </li>
                                        </ul>
                                        <br>
                                        <form method="POST" action="../../../logout.php" style="position: absolute;bottom: 0;margin-bottom: 20px;">
                                            <button type="submit" name="logout" style="display: flex;flex-direction: column;align-items: center;">
                                                <i class="fa-solid fa-arrow-right-from-bracket fs-1" aria-hidden="true"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </nav>
                    </div>
                    <div class="col-sm-6 justify-content-end profile">
                        <div class="notification d-flex flex-column justify-content-center align-items-end">
                            <div class="icon position-relative">
                                <button class="btn btn-default shadow-sm position-relative" id="readState">
                                    <i id="bell" class="fa-solid fa-bell"></i>
                                    <span id="unreadCount" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        <span class="visually-hidden">الإشعارات غير المقروؤة</span>
                                    </span>
                                </button>
                                <div class="panel shadow-sm rounded-top-4 position-absolute z-2">
                                    <div class="header p-3 shadow-sm bg-white">
                                        <h5>الإشعارات</h5>
                                    </div>
                                    <hr>
                                    <div class="body overflow-y-scroll bg-white d-flex flex-column justify-content-start align-items-center p-3">
                                        <!-- ظهور المحتوى هنا -->
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="../settings/settings.php" class="profile-badge d-flex bg-white rounded-4 shadow-sm p-2 text-decoration-none align-items-center gap-3 position-relative">
                            <span class="position-absolute top-0 start-100 translate-middle p-2 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">التنبيهات الجديدة</span>
                            </span>
                            <div class="photo">
                                <?php
                                    $target_dir = "../../../../assets/images/profiles/";
                                    $dir = (glob("$target_dir$user_id.*")) ? "$target_dir$user_id.webp" : "{$target_dir}default.png";
                                ?>
                                <img src="<?php echo $dir; ?>" alt="Profile Photo">
                            </div>
                            <div class="name">
                                <h5>مرحبا، <?php echo $_SESSION['first_name']; ?></h5>
                            </div>
                        </a>
                    </div>
                </nav>
            </header>
            <div class="row rounded-4 bg-white p-4">
                <div class="col-12 d-flex justify-content-start align-items-center p-1">
                    <button type="button" class="btn btn-transparent rounded-circle">
                        <input type="checkbox" name="check_all" id="checkAll">
                    </button>
                    <button type="button" class="btn btn-transparent rounded-circle" id="delete">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                    <button type="button" class="btn btn-transparent rounded-circle" id="update">
                        <i class="fa-solid fa-arrow-rotate-right"></i>
                    </button>
                </div>
                <div class="table-responsive col-12 d-flex justify-content-start align-items-center ps-1">
                    <div class="subjects d-flex justify-content-start align-items-center">
                        <li class="subjectItem active">
                            <button type="button" class="subject btn btn-lg btn-transparent fs-6 text-start p-3">عرض الكل</button>
                        </li>
                        <?php
                            $stmt = $conn->prepare("SELECT users.id, teachers.subject
                                                        FROM assigned_teachers 
                                                        INNER JOIN teachers ON assigned_teachers.teacher_id = teachers.id 
                                                        INNER JOIN users ON teachers.id = users.id
                                                        WHERE assigned_teachers.student_id = ?");
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $teacher_id = $row['id'];
                                    $row['subject'] = match ($row['subject']) {
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
                                    };
                                    ?>
                                        <li class="subjectItem">
                                            <button type="button" class="subject btn btn-lg btn-transparent fs-6 text-start p-3"><?php echo htmlspecialchars($row['subject']); ?></button>
                                        </li>
                                    <?php
                                }
                            }
                        ?>
                    </div>
                </div>
                <div class="table-responsive col-12" id="notificationContainer">
                </div>
                <div id="notificationDetails" class="d-none">
                    <h5 id="notificationTitle"></h5>
                    <p id="notificationContent"></p>
                    <p id="notificationTime"></p>
                    <p id="notificationPublisher"></p>
                    <button id="backToTable" class="btn btn-primary">العودة إلى جميع الإشعارات</button>
                </div>
            </div>
        </div>
    </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <script src="https://kit.fontawesome.com/35b8a1f8f5.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="../../../../assets/js/student_notifications.js"></script>
    <script src="../../../../assets/js/handle_student_notification_panel.js"></script>
</body>
</html>