<?php
require '../auth.php';

// Delete Accepted Requests Older Than 24 Hours
$sql = "DELETE FROM requests WHERE status = 'Accepted' AND updated_at < DATE_SUB(NOW(), INTERVAL 1 DAY)";
$stmt = $conn->prepare($sql);
$stmt->execute();
$stmt->close();
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
    <link rel="stylesheet" href="../../../../assets/css/requests.css">
    <link rel="stylesheet" href="../../../../assets/css/#default-styles.css">
    <title>إدارة طلبات الإلتحاق | QuadLearn</title>
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
                <a href="../files/subject_files.php">
                    <div class="icon">
                        <i class="fa-solid fa-book"></i>
                    </div>
                    <span class="item">المحتوى الدراسي</span>
                </a>
            </li>
            <li>
                <a href="../notifications/notification.php">
                    <div class="icon">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <span class="item">الإشعارات</span>
                </a>
            </li>
            <li>
                <a href="../lessons/uploadvideos.php">
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
                <a href="../manage_users/show-users.php">
                    <div class="icon">
                        <i class="fa-solid fa-user-group"></i>
                    </div>
                    <span class="item">إدارة الأعضاء</span>
                </a>
            </li>
            <li class="activated">
                <a href="requests.php" class="active">
                    <div class="icon">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <span class="item">الطلبات</span>
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
                        <h1 class="d-none d-sm-block">الطلبات</h1>
                        <nav class="navbar d-block d-sm-none">
                            <div class="container-fluid">
                                <h1>الطلبات</h1>
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
                                                <a class="nav-link" href="../files/subject_files.php">المحتوى الدراسي</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="../notifications/notification.php">الإشعارات</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="../lessons/uploadvideos.php">الدروس</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="../community/community.php">المجتمع</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="../manage_users/show-users.php">إدارة الأعضاء</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link active" href="requests.php">الطلبات</a>
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
                $user_id = $_SESSION['user_id']; // الحصول على معرّف المستخدم (المعلم)
                
                // الرسالة الافتراضية في حال عدم وجود طلبات
                $message = null;
                
                // استعلام SQL لجلب الطلبات المرتبطة بهذا المعلم
                $sql = "SELECT requests.student_id, users.first_name, users.last_name, users.phone_number, students.guardian_phone, students.level, requests.status
                        FROM requests
                        JOIN students ON requests.student_id = students.id
                        JOIN users ON requests.student_id = users.id
                        WHERE requests.teacher_id = ? ORDER BY requests.created_at DESC";
                
                // تحضير وتنفيذ الاستعلام
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();
            ?>
            <div class="container">
                <div class="head d-flex justify-content-between align-items-center">
                    <div class="input-group flex-nowrap w-50">
                        <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="search" id="searchInput" class="form-control" placeholder="بحث" aria-describedby="addon-wrapping" autofocus>
                    </div>
                </div>
                <hr>
                <div class="message"></div>
            
                <?php if ($result->num_rows > 0): ?>
                    <div class="table-container table-responsive" id="requestTable">
                        <table class="table table-hover mb-0 align-middle">
                            <thead>
                                <tr class="table-active">
                                    <td scope="col" class="text-truncate text-center text-secondary">#</td>
                                    <td scope="col" class="text-truncate text-center text-secondary">المعرف</td>
                                    <td scope="col" class="text-truncate text-secondary">الاسم</td>
                                    <td scope="col" class="text-truncate text-center text-secondary">المرحلة</td>
                                    <td scope="col" class="text-truncate text-center text-secondary">رقم الهاتف</td>
                                    <td scope="col" class="text-truncate text-center text-secondary">هاتف ولي الأمر</td>
                                    <td scope="col" class="text-truncate text-center text-secondary">حالة الطلب</td>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                <?php
                                $counter = 1;
                                while ($row = $result->fetch_assoc()):
                                    // تحويل المرحلة
                                    $row['level'] = match ($row['level']) {
                                        'first' => 'الأول الثانوي',
                                        'second' => 'الثاني الثانوي',
                                        'third' => 'الثالث الثانوي',
                                        default => 'غير محدد'
                                    };
                                
                                    // تحضير البيانات للعرض في الجدول
                                    $student_id = htmlspecialchars($row["student_id"]);
                                    $studentName = htmlspecialchars($row["first_name"] . ' ' . $row["last_name"]);
                                ?>
                                    <tr data-student-id="<?= $student_id ?>" onclick="openModal('<?= $student_id ?>', '<?= $studentName ?>')">
                                        <td class="text-truncate"><?= $counter ?></td>
                                        <td class="text-truncate"><?= $student_id ?></td>
                                        <td class="text-start text-truncate"><?= $studentName ?></td>
                                        <td class="text-truncate"><?= htmlspecialchars($row["level"]) ?></td>
                                        <td class="text-truncate"><?= htmlspecialchars($row["phone_number"]) ?></td>
                                        <td class="text-truncate"><?= htmlspecialchars($row["guardian_phone"] ?: "لا يوجد رقم هاتف") ?></td>
                                        <td class="text-truncate">
                                            <span class="badge text-bg-<?= htmlspecialchars($row['status']) ?>">
                                                <?= htmlspecialchars(match ($row['status']) {
                                                    'New' => 'جديد',
                                                    'Pending' => 'معلَّق',
                                                    'Accepted' => 'مقبول',
                                                    'Rejected' => 'مرفوض',
                                                    default => 'غير معروف'
                                                }) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php
                                    $counter++;
                                endwhile;
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <?php $message = "لا توجد طلبات حتى الآن"; ?>
                <?php endif; ?>
                
                <div class="modal fade" id="requestModal" tabindex="-1" aria-labelledby="requestModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="requestModalLabel">تفاصيل الطلب</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="requestState">
                                    <div class="row mb-2">
                                        <label for="modalStudentId" class="col-sm-2 col-form-label">معرِّف الطالب</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="modalStudentId" disabled>
                                        </div>
                                    </div>
                                    <div class="row mb-2">
                                        <label for="modalStudentName" class="col-sm-2 col-form-label">اســـــم الطالب</label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="modalStudentName" disabled>
                                        </div>
                                    </div>
                                    <fieldset class="row mb-2">
                                        <legend class="col-form-label col-sm-2 pt-0">حالة الطلب</legend>
                                        <div class="col-sm-10">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="requestStatus" value="Accepted" id="Accepted" checked>
                                                <label for="Accepted" class="form-check-label">مقبول</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="requestStatus" value="Pending" id="Pending">
                                                <label for="Pending" class="form-check-label">معلَّق</label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="requestStatus" value="Rejected" id="Rejected">
                                                <label for="Rejected" class="form-check-label">مرفوض</label>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <div class="row mb-2">
                                        <div class="col-sm-10">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="messageCheck">
                                                <label for="messageCheck" class="form-check-label">تضمين رسالة</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3" id="messageArea" style="display: none;">
                                        <label for="Textarea1" class="form-label">موضوع الرسالة</label>
                                        <textarea class="form-control" id="Textarea1" rows="3"></textarea>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" id="saveChanges" class="btn btn-default">حفظ التغييرات</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if ($message): ?>
                    <div class="h-100 d-flex flex-column justify-content-center align-items-center">
                        <dotlottie-player src="https://lottie.host/6acf3b9a-3c86-4c3b-abb5-11a02a1db2f4/79fjM8x5uj.json" background="transparent" speed="1" style="width: 300px; height: 250px;" loop autoplay></dotlottie-player>
                        <h4 class="text-secondary"><?= $message; ?></h4>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <script src="https://kit.fontawesome.com/35b8a1f8f5.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="../../../../assets/js/requests.js"></script>
    <script src="../../../../assets/js/handle_teacher_notification_panel.js"></script>
</body>
</html>