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
    <link rel="stylesheet" href="../../../../assets/css/show-teachers.css">
    <link rel="stylesheet" href="../../../../assets/css/#default-styles.css">
    <title>إدراة المعلمين | QuadLearn</title>
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
                <a href="../notifications/student_notifications.php">
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
                <a href="../lessons/student_videos.php">
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
            <li class="activated">
                <a href="show-teachers.php" class="active">
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
                        <h1 class="d-none d-sm-block">المعلمين</h1>
                        <nav class="navbar d-block d-sm-none">
                            <div class="container-fluid">
                                <h1>المعلمين</h1>
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
                                                <a class="nav-link" href="../notifications/student_notifications.php">الإشعارات</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="../files/subjects.php">المحتوى الدراسي</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="../lessons/student_videos.php">الدروس</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="../community/community.php">المجتمع</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link active" href="show-teachers.php">المعلمين</a>
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
                                <span class="visually-hidden">تنبيهات جديدة</span>
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
            <div class="head-container p-xxl-5 shadow my-5 rounded-4">
                <div class="container row g-5 align-items-center p-4">
                    <div class="col-sm-8 text-start">
                        <h2>قم بالإلتحاق الى صفوف المعلمين المختلفة وقم برفع أدائك الدراسي</h2>
                    </div>
                    <div class="col-sm-4 text-center">
                        <button type="button" class="btn btn-light w-50 p-2" data-bs-toggle="modal" data-bs-target="#addTeacher">
                            إضافة معلم
                        </button>
                        <div class="modal fade" id="addTeacher" tabindex="-1" aria-labelledby="addTeacherLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header border-0">
                                        <h1 class="modal-title fs-5" id="addTeacherLabel">ما عليك سوى إتمام الخطوات التالية</h1>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form id="searchForm">
                                        <div class="modal-body text-start">
                                            <div>
                                                <label for="search" class="form-label">المعرِّف</label>
                                                <input type="search" id="search" name="identitySearch" class="form-control" aria-describedby="identityHelpBlock">
                                                <div id="identityHelpBlock" class="form-text">
                                                    ليس لديك المعرِّف الخاص بالمعلم! أطلب منه المعرف الخاص به المكون من 8 أرقام.
                                                </div>
                                            </div>
                                            <div id="teacherInfo" class="w-100 mt-3"></div>
                                        </div>
                                        <div class="modal-footer border-0 d-flex justify-content-center">
                                            <button type="submit" class="btn btn-default w-75">بحث</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-container">
                <div id="deleteTeacher"></div>
                <div class="row justify-content-center g-4">
                <?php
                $sql = 'SELECT users.id, users.first_name, users.last_name, users.phone_number, users.picture, teachers.title, teachers.subject, users.bio
                        FROM assigned_teachers 
                        INNER JOIN teachers ON assigned_teachers.teacher_id = teachers.id 
                        INNER JOIN users ON teachers.id = users.id
                        WHERE assigned_teachers.student_id = ?';
                $stmt = $conn->prepare($sql);
                $stmt->bind_param('i', $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $teacher_id = $row['id'];
                        ?>
                        <div class="col-sm-4" style="width: max-content;">
                            <div class="card h-auto overflow-hidden">
                                <img src="../../../../assets/images/<?php echo htmlspecialchars($row['subject']); ?>.webp" class="card-img-top" alt="..." style="height: 250px;">
                                <div class="card-body position-relative teacher-card">
                                    <div class="image-box">
                                        <?php
                                            $Ttarget_dir = "../../../../assets/images/profiles/";
                                            $dir = (glob((string) $Ttarget_dir . $row['id'] . ".*")) ? "$Ttarget_dir{$row['id']}.webp" : "{$Ttarget_dir}default.png";
                                        ?>
                                        <img src="<?php echo $dir; ?>" alt="Profile Photo" class="rounded-circle">
                                    </div>
                                    <figure>
                                        <blockquote class="blockquote">
                                            <h5>
                                                <?php
                                                $row['subject'] = match ($row['subject']) {
                                                    'Arabic' => 'لغة عربية',
                                                    'English' => 'لغة إنجليزية',
                                                    'Spanish' => 'لغة إسبانية',
                                                    'French' => 'لغة فرنسية',
                                                    'German' => 'لغة ألمانية',
                                                    'Italian' => 'لغة إيطالية',
                                                    'Physics' => 'فيزياء',
                                                    'Chemistry' => 'كيمياء',
                                                    'Biology' => 'أحياء',
                                                    'Geology' => 'جيولوجيا',
                                                    'Mathematics' => 'رياضيات',
                                                    'Philosophy' => 'فلسفة وعلم نفس',
                                                    'History' => 'تاريخ',
                                                    'Geography' => 'جغرافيا'
                                                };
                                                echo htmlspecialchars($row['subject']);
                                                ?>
                                            </h5>
                                        </blockquote>
                                        <figcaption class="blockquote-footer card-text">
                                            <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?> 
                                            <cite title="Source Title">
                                                <?php
                                                $row['title'] = match ($row['title']) {
                                                    'teacher' => 'معلم',
                                                    default => 'معلم مساعد',
                                                };
                                                echo htmlspecialchars($row['title']);
                                                ?>
                                            </cite>
                                        </figcaption>
                                        <!-- زر فتح المودال -->
                                        <button type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-target="#teacherModal<?php echo $teacher_id; ?>">
                                            عرض التفاصيل
                                        </button>
                                    </figure>
                                </div>
                            </div>
                        </div>
                                            
                        <!-- مودال عرض التفاصيل -->
                        <div class="modal fade" id="teacherModal<?php echo $teacher_id; ?>" tabindex="-1" aria-labelledby="teacherModalLabel<?php echo $teacher_id; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="teacherModalLabel<?php echo $teacher_id; ?>">تفاصيل المعلم</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body modal-teacher-content" id="modalBody" data-teacher-id="<?php echo $teacher_id; ?>">
                                        <div class="modal-page page-1" id="page1-<?php echo $teacher_id; ?>" style="transform: translateX(0%);">
                                            <!-- محتوى الصفحة 1 هنا -->
                                            <div class="row">
                                                <h5 class="col-12">أ/ <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></h5>
                                                <h6 class="col-md-6"><?php echo htmlspecialchars($row['title'] . ' ' . $row['subject']); ?></h6>
                                                <h6 class="col-md-6">رقم الهاتف: <?php echo htmlspecialchars($row['phone_number']); ?></h6>
                                                <h6 class="col-md-5">المعرف: <?php echo htmlspecialchars($row['id']); ?></h6>
                                                <a href="https://wa.me/<?php echo $row['phone_number']; ?>" class="btn btn-default rounded-pill col-md-6">محادثة واتساب <i class="fa-brands fa-whatsapp text-white"></i></a>
                                                <div class="col-12">
                                                    <fieldset class="border rounded-3 ps-3 pe-3 pt-0 pb-0 mb-2">
                                                        <legend class="float-none w-auto px-3 fs-5">الوصف</legend>
                                                        <?php
                                                        if (!empty($row['bio'])) {
                                                            echo "<p>" . htmlspecialchars($row['bio']) . "</p>";
                                                        } else {
                                                            echo '<div class="text-secondary">لا يوجد وصف</div>';
                                                        }
                                                        ?>
                                                    </fieldset>
                                                </div>
                                                <div class="col-12">
                                                    <h6>المعلمون المساعدون</h6>
                                                    <ul>
                                                        <?php
                                                        $assistants_sql = 'SELECT users.first_name, users.last_name, users.phone_number, users.bio 
                                                                           FROM assigned_assistants 
                                                                           INNER JOIN teachers ON assigned_assistants.assistant_id = teachers.id
                                                                           INNER JOIN users ON teachers.id = users.id
                                                                           WHERE assigned_assistants.teacher_id = ?';
                                                        $assistants_stmt = $conn->prepare($assistants_sql);
                                                        $assistants_stmt->bind_param('i', $teacher_id);
                                                        $assistants_stmt->execute();
                                                        $assistants_result = $assistants_stmt->get_result();
                                                        
                                                        if ($assistants_result->num_rows > 0) {
                                                            while ($assistant_row = $assistants_result->fetch_assoc()) {
                                                                echo '<li class="row">';
                                                                echo '<p class="col-md-6">' . htmlspecialchars($assistant_row['first_name'] . ' ' . $assistant_row['last_name']) . '</p>';
                                                                echo '<p class="col-md-6">رقم الهاتف: ' . htmlspecialchars($assistant_row['phone_number']) . '</p>';
                                                                echo '<a href="https://wa.me/' . $assistant_row["phone_number"] . '" class="btn btn-default rounded-pill col-12">محادثة واتساب <i class="fa-brands fa-whatsapp text-white"></i></a>';
                                                                echo '</li>';
                                                                if (!empty($assistant_row['bio'])) {
                                                                    echo '<fieldset class="border rounded-3 ps-3 pe-3 pt-0 pb-0 mb-2">';
                                                                    echo '<legend class="float-none w-auto px-3 fs-5">الوصف</legend>';
                                                                    echo "<p>" . htmlspecialchars($assistant_row['bio']) . "</p>";
                                                                    echo '</fieldset>';
                                                                }
                                                            }
                                                        } else {
                                                            echo '<li>لا يوجد معلمون مساعدون.</li>';
                                                        }
                                                        ?>
                                                    </ul>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-danger deletePage" data-teacher-id="<?php echo $teacher_id; ?>">حذف</button>
                                        </div>
                                        <div class="modal-page page-2" id="page2-<?php echo $teacher_id; ?>" style="transform: translateX(-120%);">
                                            <h1 class="modal-title fs-5">هل أنت متأكد من الحذف!</h1>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <div class="col-12 d-flex">
                                                        <p>أنت على وشك حذف هذا المعلم:&nbsp;</p>
                                                        <h6><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></h6>&nbsp;
                                                    </div>
                                                    <div class="col-12 d-flex justify-content-start align-items-end gap-3">
                                                        <button type="button" class="btn btn-secondary homePage" data-teacher-id="<?php echo $teacher_id; ?>">العودة</button>
                                                        <button type="button" class="btn btn-danger delete-teacher-btn" data-teacher-id="<?php echo $teacher_id; ?>" data-bs-dismiss="modal">حذف</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                    }
                } else {
                    echo '<div class="h-100 w-100 d-flex flex-column justify-content-center align-items-center">
                            <dotlottie-player src="https://lottie.host/6acf3b9a-3c86-4c3b-abb5-11a02a1db2f4/79fjM8x5uj.json" background="transparent" speed="1" style="width: 300px; height: 250px;" loop autoplay></dotlottie-player>
                            <h4 class="text-secondary">لا يوجد معلمين حتى الآن</h4>
                        </div>';
                }
                ?>
                </div>
            </div>
        </div>
    </div>
    </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <script src="https://kit.fontawesome.com/35b8a1f8f5.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="../../../../assets/js/show-teachers.js"></script>
    <script src="../../../../assets/js/handle_student_notification_panel.js"></script>
</body>
</html>