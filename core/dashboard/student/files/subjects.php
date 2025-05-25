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
    <link rel="stylesheet" href="../../../../assets/css/subjects.css">
    <link rel="stylesheet" href="../../../../assets/css/#default-styles.css">
    <title>المحتوى الدراسي | QuadLearn</title>
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
                <li>
                    <a href="../notifications/notifications.php">
                        <div class="icon">
                            <i class="fa-solid fa-bell"></i>
                        </div>
                        <span class="item">الإشعارات</span>
                    </a>
                </li>
                <li class="activated">
                    <a href="subjects.php" class="active">
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
                        <h1 class="d-none d-sm-block">المحتوى الدراسي</h1>
                        <nav class="navbar d-block d-sm-none">
                            <div class="container-fluid">
                                <h1>المحتوى الدراسي</h1>
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
                                                <a class="nav-link" href="../notifications/notifications.php">الإشعارات</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link active" href="subjects.php">المحتوى الدراسي</a>
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
                <?php
                $stmt_check = $conn->prepare("SELECT * FROM assigned_teachers WHERE student_id = ? LIMIT 1");
                $stmt_check->bind_param("i", $user_id);
                $stmt_check->execute();
                $result_check = $stmt_check->get_result();

                if ($result_check->num_rows > 0) {
                    ?>
                    <div class="previous row gy-3">
                        <div class="col-12">
                            <h3>مضافة مؤخراً</h3>
                        </div>
                        <div class="cards col-12 d-flex justify-content-start align-items-center gap-5 pb-2 overflow-x-scroll">
                            <?php
                            $stmt = $conn->prepare("SELECT sf.*, u.first_name, t.subject
                                                    FROM subject_files sf
                                                    INNER JOIN teachers t ON sf.teacher_id = t.id
                                                    INNER JOIN users u ON t.id = u.id
                                                    INNER JOIN assigned_teachers AS at ON t.id = at.teacher_id
                                                    WHERE at.student_id = ?
                                                    ORDER BY sf.created_at DESC LIMIT 8");
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();

                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
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

                                    $extension = pathinfo($row['file_name'], PATHINFO_EXTENSION);
                                    $fileIcon = match ($extension) {
                                        'pdf' => 'fa-regular fa-file-pdf',
                                        'jpg' => 'fa-regular fa-image',
                                        'jpeg' => 'fa-regular fa-image',
                                        'webp' => 'fa-regular fa-image',
                                        'png' => 'fa-regular fa-image',
                                        'doc' => 'fa-regular fa-file-word',
                                        'docx' => 'fa-regular fa-file-word',
                                        'csv' => 'fa-solid fa-file-csv',
                                        'xls' => 'fa-regular fa-file-excel',
                                        'xlsx' => 'fa-regular fa-file-excel',
                                        default => 'fa-regular fa-file'
                                    };
                                    echo '
                                    <a href="' . $row['unique_file'] . '" class="text-decoration-none text-normal" download>
                                        <div class="card flex-row row rounded-3" id="card" role="button" aria-disabled="true" style="min-width: 17rem;max-width: 17rem;height: 6rem;">
                                            <div class="card-icon col-2 d-flex justify-content-center align-items-center">
                                                <i class="' . $fileIcon . ' fs-4" aria-hidden="true"></i>
                                            </div>
                                            <div class="card-body col-10">
                                                <div class="row">
                                                    <h6 class="card-title col-12 text-truncate">' . $row["file_name"] . '</h6>
                                                    <span class="col-5 text-secondary">' . $row['first_name'] . '</span>
                                                    <span class="col-7 text-end text-secondary">' . $row['subject'] . '</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                    ';
                                }
                            } else {
                                echo "<p class='ms-4'>لم يقم أي معلم بإضافة ملفات بعد.</p>";
                            }
                            ?>
                        </div>
                    </div>
                    <hr class="my-5">
                    <div class="row gy-3">
                        <h3 class="col-12">المواد الدراسية</h3>
                        <div class="col-12">
                            <div class="subjects row row-cols-1 row-cols-lg-5 g-3 gap-3">
                                <?php
                                $stmt = $conn->prepare("SELECT COUNT(sf.id) AS files, u.first_name, t.subject
                                                        FROM subject_files sf
                                                        INNER JOIN teachers t ON sf.teacher_id = t.id
                                                        INNER JOIN users u ON t.id = u.id
                                                        INNER JOIN assigned_teachers AS at ON t.id = at.teacher_id
                                                        WHERE at.student_id = ?
                                                        GROUP BY u.id, u.first_name, t.subject
                                                        ORDER BY MAX(at.assign_date) DESC");
                                $stmt->bind_param("i", $user_id);
                                $stmt->execute();
                                $result = $stmt->get_result();

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $subject = match ($row['subject']) {
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
                                            'Geography' => 'الجغرافيا',
                                            default => 'غير محدد'
                                        };
                                        $icon = match ($row['subject']) {
                                            'Arabic' => 'fa-solid fa-book-open-reader',
                                            'English' => 'fa-solid fa-book-open-reader',
                                            'Spanish' => 'fa-solid fa-book-open-reader',
                                            'French' => 'fa-solid fa-book-open-reader',
                                            'German' => 'fa-solid fa-book-open-reader',
                                            'Italian' => 'fa-solid fa-book-open-reader',
                                            'Physics' => 'fa-solid fa-atom',
                                            'Chemistry' => 'fa-solid fa-flask-vial',
                                            'Biology' => 'fa-solid fa-dna',
                                            'Geology' => 'fa-solid fa-earth-americas',
                                            'Mathematics' => 'fa-solid fa-square-root-variable',
                                            'Philosophy' => 'fa-solid fa-landmark',
                                            'History' => 'fa-solid fa-scroll',
                                            'Geography' => 'fa-solid fa-cloud-sun',
                                            default => 'fa-solid fa-question'
                                        };
                                        echo '
                                        <button class="btn btn-default col overflow-hidden shadow-sm rounded-3 px-4 pt-3 pb-0" style="height: 7rem;">
                                            <div class="row">
                                                <h4 class="text-start col-8">' . $subject . '</h4>
                                                <h4 class="col-4">' . $row['files'] . '</h4>
                                                <div class="col-6 position-relative">
                                                    <i class="' . $icon . '" aria-hidden="true"></i>
                                                </div>
                                                <h6 class="col-6 text-light text-end pe-4 fw-medium">' . $row['first_name'] . '</h6>
                                            </div>
                                        </button>
                                    ';
                                    }
                                } else {
                                    echo "<p class='ms-4'>لم يقم أي معلم بإضافة ملفات بعد.</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <hr class="my-5">
                    <div class="new-files row row-cols-5 g-3">
                        <div class="col-12">
                            <h3>الموارد</h3>
                        </div>
                        <div class="col-12">
                            <div class="row gy-3" id="filesContainer">
                            </div>
                        </div>
                    </div>
                    <?php
                } else {
                    echo "<p>لا يوجد معلمين حتى الآن. يجب الإلتحاق بمواد دراسية أولاً لرؤية المحتوى.</p>";
                }
                ?>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <script src="https://kit.fontawesome.com/35b8a1f8f5.js" crossorigin="anonymous"></script>
    <script src="../../../../assets/js/subjects.js"></script>
    <script src="../../../../assets/js/handle_student_notification_panel.js"></script>
</body>
</html>