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
        <link rel="stylesheet" href="../../../../assets/css/scommunity.css">
        <link rel="stylesheet" href="../../../../assets/css/#default-styles.css">
        <title>المجتمع | QuadLearn</title>
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
        <!-- Modal تعديل المنشور -->
        <div class="modal fade" id="editPostModal" tabindex="-1" aria-labelledby="editPostModalLabel" role="dialog">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editPostModalLabel">تعديل المنشور</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="edit_post.php" method="POST">
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label for="editPostTitle" class="form-label">العنوان</label>
                                    <input type="text" class="form-control" id="editPostTitle" name="title">
                                </div>
                                <div class="mb-3">
                                    <label for="editPostContent" class="form-label">المحتوى</label>
                                    <textarea class="form-control" id="editPostContent" name="content" rows="4"></textarea>
                                </div>
                                <input type="hidden" id="editPostId" name="post_id">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-primary">تحديث</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
                    <li>
                        <a href="../files/subjects.php">
                            <div class="icon">
                                <i class="fa-solid fa-book"></i>
                            </div>
                            <span class="item">المحتوى الدراسي</span>
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
                    <li class="activated">
                        <a href="community.php" class="active">
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
                                <h1 class="d-none d-sm-block">المجتمع</h1>
                                <nav class="navbar d-block d-sm-none">
                                    <div class="container-fluid">
                                        <h1>المجتمع</h1>
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
                                                        <a class="nav-link" href="../files/subjects.php">المحتوى الدراسي</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link" href="../lessons/videos.php">الدروس</a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a class="nav-link active" href="community.php">المجتمع</a>
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
                    <div class="row flex-column-reverse flex-sm-row g-3">
                        <div class="col-md-9">
                            <div class="add-post rounded-3 bg-white shadow-sm p-3 mb-4 d-flex justify-content-between align-items-center">
                                <h5 class="text-secondary m-0">إضافة منشور جديد</h5>
                                <button type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-target="#addPost"><i class="fa-solid fa-plus"></i></button>
                            </div>
                            <div class="modal fade" id="addPost" tabindex="-1" aria-labelledby="addPost" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header border-0">
                                            <h1 class="modal-title fs-5" id="addPostLabel">ما الجديد اليوم؟</h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body px-5 pt-2">
                                            <form id="addPostForm" action="add_post.php" method="POST" enctype="multipart/form-data">
                                                <div class="row g-3 pb-5">
                                                    <div class="col-12">
                                                        <label for="subject" class="form-label">المادة الدراسية</label>
                                                        <select name="subject" id="subject" class="form-select" required>
                                                            <option selected disabled>--</option>
                                                            <?php
                                                            $stmt = $conn->prepare("SELECT DISTINCT subject
                                                                                    FROM teachers
                                                                                    JOIN assigned_teachers ON assigned_teachers.teacher_id = teachers.id
                                                                                    WHERE assigned_teachers.student_id = ?
                                                                                    ORDER BY subject DESC");
                                                            $stmt->bind_param("i", $user_id);
                                                            $stmt->execute();
                                                            $result = $stmt->get_result();
                                                            if ($result->num_rows > 0) {
                                                                while ($row = $result->fetch_assoc()) {
                                                                    $subject = $row['subject'];
                                                                    $translated_subject = match ($subject) {
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
                                                                    <option value="<?php echo $subject; ?>"><?php echo htmlspecialchars($translated_subject); ?></option>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-12">
                                                        <label for="title" class="form-label">العنوان</label>
                                                        <input type="text" id="title" class="form-control" name="title">
                                                    </div>
                                                    <div class="col-12">
                                                        <label for="postContent" class="form-label">المحتوى<small class="text-danger">*</small></label>
                                                        <textarea id="postContent" class="form-control" name="content" rows="4" required></textarea>
                                                    </div>
                                                    <div class="col-5">
                                                        <p>الشارات</p>
                                                        <div id="badges-container"></div>
                                                        <button type="button" class="btn btn-light w-100" onclick="addBadge()">إضافة شارة</button>
                                                    </div>
                                                    <div class="col-7">
                                                        <label for="uploaded_files" class="form-label">رفع الملفات</label>
                                                        <input type="file" id="uploaded_files" class="form-control" name="uploaded_files[]" multiple>
                                                        <small class="text-muted">الملفات المسموح بها: JPEG, PNG, PDF. الحد الأقصى للحجم: 5 ميغابايت</small>
                                                    </div>
                                                    <div class="col-12">
                                                        <button type="submit" class="btn btn-default w-100">نشر</button>
                                                        <div id="loadingSpinner" class="spinner-border text-primary d-none" role="status">
                                                            <span class="visually-hidden">جاري التحميل...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group flex-nowrap mb-4 shadow-sm">
                                <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></span>
                                <input type="search" id="searchInput" class="form-control" placeholder="بحث" aria-describedby="addon-wrapping">
                            </div>
                            <div id="liveAlertPlaceholder"></div>
                            <div class="post-container" id="postContainer"></div>
                        </div>
                        <div class="col-md-3 shadow-sm rounded-3 bg-white h-100 pb-3">
                            <ul class="tabs pt-5 ps-4 d-flex flex-column align-items-start gap-2">
                                <li class="listItem active" id="listItem">
                                    <button class="btn border-0 d-flex align-items-center gap-4">
                                        <i class="fa-solid fa-house color-secondary rounded"></i>
                                        <span>الرئيسية</span>
                                    </button>
                                </li>
                                <li class="listItem" id="listItem">
                                    <button class="btn border-0 d-flex align-items-center gap-4">
                                        <i class="fa-solid fa-comment color-secondary rounded"></i>
                                        <span>منشوراتك</span>
                                    </button>
                                </li>
                                <li class="listItem" id="listItem">
                                    <button class="btn border-0 d-flex align-items-center gap-4">
                                        <i class="fa-solid fa-bookmark color-secondary rounded"></i>
                                        <span>محفوظاتك</span>
                                    </button>
                                </li>
                            </ul>
                            <div class="accordion" id="accordionExample">
                                <div class="accordion-item border-0">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button bg-white text-black shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            المواد الدراسية
                                        </button>
                                    </h2>
                                    <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#accordionExample">
                                        <div class="accordion-body rounded-4 bg-light">
                                            <div class="subjects d-flex flex-column justify-content-center align-items-start px-2 gap-3">
                                                <?php
                                                $stmt->prepare("SELECT users.id, users.first_name, users.last_name, teachers.subject
                                                                FROM assigned_teachers
                                                                JOIN users ON assigned_teachers.teacher_id = users.id
                                                                LEFT JOIN teachers ON teachers.id = assigned_teachers.teacher_id
                                                                WHERE assigned_teachers.student_id = ?");
                                                $stmt->bind_param("i", $user_id);
                                                $stmt->execute();
                                                $result = $stmt->get_result();

                                                if ($result->num_rows > 0) {
                                                    $teachers = []; // مصفوفة لتخزين المعلمين
                                                
                                                    while ($row = $result->fetch_assoc()) {
                                                        $teacher_id = $row['id'];
                                                    
                                                        // تحقق مما إذا كان المعلم موجودًا بالفعل في المصفوفة
                                                        if (!isset($teachers[$teacher_id])) {
                                                            $teachers[$teacher_id] = [
                                                                'first_name' => $row['first_name'],
                                                                'last_name' => $row['last_name'],
                                                                'subject' => $row['subject']
                                                            ];
                                                        }
                                                    }
                                                
                                                    // عرض المعلمين
                                                    foreach ($teachers as $teacher_id => $teacher) {
                                                        $first_name = $teacher['first_name'];
                                                        $last_name = $teacher['last_name'];
                                                        $subject = $teacher['subject'];
                                                        ?>
                                                        <button class="row btn btn-light d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#subjectModal" data-subject="<?php echo htmlspecialchars($subject); ?>" data-teacher-id="<?php echo $teacher_id; ?>">
                                                            <div class="col-4 overflow-hidden rounded d-flex justify-content-center align-items-center">
                                                                <img src="../../../../assets/images/<?php echo htmlspecialchars($subject); ?>.webp" alt="subject">
                                                            </div>
                                                            <div class="col-8 text-start">
                                                                <?php
                                                                $translated_subject = match ($subject) {
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
                                                                    'Geography' => 'جغرافيا',
                                                                    default => $subject
                                                                };
                                                                ?>
                                                                <h6><?php echo htmlspecialchars($translated_subject); ?></h6>
                                                                <h6 class="text-secondary"><?php echo htmlspecialchars("$first_name $last_name"); ?></h6>
                                                            </div>
                                                        </button>
                                                        <?php
                                                    }
                                                } else {
                                                    echo 'لا توجد مواد دراسية';
                                                }
                                                $stmt->close();
                                                ?>

                                                <!-- Subject Posts -->
                                                <div class="modal fade" id="subjectModal" tabindex="-1" aria-labelledby="subjectModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-xl modal-fullscreen-md-down modal-dialog-centered modal-dialog-scrollable">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h1 class="modal-title fs-5" id="subjectModalLabel">منشورات المادة</h1>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body" id="modal-content"></div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Add Teacher -->
                                                <a href="../instructors/show-teachers.php" class="add-teacher row d-flex align-items-center btn btn-default rounded-4 shadow mt-4">
                                                    <div class="col-3 rounded-4 bg-light d-flex justify-content-center align-items-center h-75">
                                                        <i class="fa-solid fa-user-plus text-secondary fs-5" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="col-9">
                                                        <h6 class="text-light fw-medium">الإنضمام إلى معلم</h6>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item border-0">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button bg-white text-black shadow-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                            الملفات
                                        </button>
                                    </h2>
                                    <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
                                        <div class="accordion-body rounded-4 bg-light">
                                            <?php
                                            $images = ["jpg", "jpeg", "png", "webp"];
                                            $word = ["doc", "docx"];
                                            $excel = ["csv", "xlsx"];
                                            $video = ["mp4", "avi", "mov", "mkv", "webm"];
                                            $stmt = $conn->prepare("SELECT DISTINCT c.uploaded_files, c.original_file_names, u.first_name, u.last_name, u.account_type
                                                                    FROM community c
                                                                    JOIN users u ON c.user_id = u.id
                                                                    WHERE c.user_id = ?
                                                                        OR c.user_id IN (
                                                                            SELECT teacher_id
                                                                            FROM assigned_teachers
                                                                            WHERE student_id = ?
                                                                        )
                                                                        OR c.user_id IN (
                                                                            SELECT aa.assistant_id
                                                                            FROM assigned_assistants aa
                                                                            INNER JOIN assigned_teachers at ON aa.teacher_id = at.teacher_id
                                                                            WHERE at.student_id = ?
                                                                        )
                                                                        OR (
                                                                            c.user_id IN (
                                                                                SELECT ast.student_id
                                                                                FROM assigned_students ast
                                                                                INNER JOIN assigned_teachers at ON ast.teacher_id = at.teacher_id
                                                                                WHERE at.student_id = ?
                                                                            )
                                                                            AND c.user_id != ?
                                                                        )
                                                                    ORDER BY c.updated_at DESC");
                                            $stmt->bind_param("iiiii", $user_id, $user_id, $user_id, $user_id, $user_id);
                                            $stmt->execute();
                                            $result = $stmt->get_result();
                                            if ($result->num_rows > 0) {
                                                while ($row = $result->fetch_assoc()) {
                                                    $first_name = $row['first_name'];
                                                    $last_name = $row['last_name'];
                                                    $account_type = $row['account_type'];
                                                    $uploadedFilesJson = $row['uploaded_files'];
                                                    $originalFileNamesJson = $row['original_file_names'];
                                                    // Decode the JSON strings into PHP arrays
                                                    // Setting the second parameter to true decodes into associative arrays
                                                    $uploadedFiles = json_decode($uploadedFilesJson, true);
                                                    $originalFileNames = json_decode($originalFileNamesJson, true);

                                                    if (!empty($uploadedFiles) && is_array($uploadedFiles)) {
                                                        // Loop through the uploaded files array
                                                        foreach ($uploadedFiles as $index => $fileName) {
                                                            // Access the corresponding original file name using the same index
                                                            $originalName = $originalFileNames[$index] ?? 'N/A'; // Use null coalescing for safety
                                                            $fileURL = "../../../../assets/files/$fileName";
                                                            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                                            if (in_array($fileExtension, $video)) {
                                                                $icon = "fa-file-video";
                                                            } elseif (in_array($fileExtension, $word)) {
                                                                $icon = "fa-file-word";
                                                            } elseif (in_array($fileExtension, $excel)) {
                                                                $icon = "fa-file-excel";
                                                            } elseif (in_array($fileExtension, $images)) {
                                                                $icon = "fa-image";
                                                            } elseif ($fileExtension == "pdf") {
                                                                $icon = "fa-file-pdf";
                                                            } else {
                                                                $icon = "fa-file";
                                                            }
                                                            ?>
                                                            <a class="file-box row bg-transparent rounded-3 text-decoration-none mb-2" href='<?php echo $fileURL; ?>' download>
                                                                <div class="col-2 d-flex justify-content-center align-items-center">
                                                                    <i class="fa-solid <?php echo $icon; ?> me-1 fs-3"></i>
                                                                </div>
                                                                <div class="col">
                                                                    <h6 class="mb-0 text-black"><?php echo htmlspecialchars($originalName); ?></h6>
                                                                    <p class="mb-0 text-secondary"><?php echo htmlspecialchars("$first_name $last_name"); ?></p>
                                                                </div>
                                                            </a>
                                                            <?php
                                                        }
                                                    }
                                                }
                                            } else {
                                                echo '<p class="text-secondary">لم يتم نشر ملفات بعد.</p>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Common Modal -->
                <div class="modal fade" id="commonModal" tabindex="-1" aria-labelledby="commonModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="d-flex flex-column justify-content-center align-items-center" id="messageDiv"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
        <script>
            const userRole = 'student';
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
        <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
        <script src="https://kit.fontawesome.com/35b8a1f8f5.js" crossorigin="anonymous"></script>
        <script src="../../../../assets/js/handle_student_notification_panel.js"></script>
        <script src="../../../../assets/js/community-common.js"></script>
    </body>
</html>