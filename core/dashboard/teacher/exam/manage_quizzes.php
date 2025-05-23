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
    <title>إدارة الإختبارات وإدارتها | QuadLearn</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" integrity="sha384-nU14brUcp6StFntEOOEBvcJm4huWjB0OcIeQ3fltAfSmuZFrkAif0T+UtNGlKKQv" crossorigin="anonymous">
    <link rel="stylesheet" href="../../../../assets/css/manage_quizzes.css">
    <link rel="stylesheet" href="../../../../assets/css/#default-styles.css">
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
            <li class="activated">
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
        <div class="container">
            <header class="manage mb-4">
                <nav class="row">
                    <div class="col-sm-6 text">
                        <h1 class="d-none d-sm-block">الإختبارات</h1>
                        <nav class="navbar d-block d-sm-none">
                            <div class="container-fluid">
                                <h1>الإختبارات</h1>
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
                                                <a class="nav-link active" aria-current="page" href="dashboard.php">الرئيسية</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="subject_files.php">المحتوى الدراسي</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="notification.php">الإشعارات</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="uploadvideos.php">الدروس</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="community.php">المجتمع</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="show-users.php">إدارة الأعضاء</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="requests.php">الطلبات</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="settings.php">الإعدادات</a>
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
                        <a href="settings.php" class="profile-badge d-flex bg-white rounded-4 shadow-sm p-2 text-decoration-none align-items-center gap-3 position-relative">
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
            <div class="container">
                <div class="head d-flex justify-content-between align-items-center">
                    <div class="input-group flex-nowrap w-50">
                        <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" id="searchInput" class="form-control focus-ring focus-ring-dark py-1 px-2 text-decoration-none border" placeholder="بحث" aria-describedby="addon-wrapping" autofocus>
                    </div>
                    <div class="w-25">
                        <a href="create_test.php" class="d-flex flex-row-reverse text-decoration-none">
                            <button class="btn btn-default w-50">إنشاء</button>
                        </a>
                    </div>
                </div>
                <hr>
                <?php
                    $message = null;
                    // استرجاع الاختبارات من الأحدث إلى الأقدم
                    $sql = "SELECT t.test_id, t.title, t.description, t.created_at, t.status, 
                                   COUNT(DISTINCT q.question_id) as num_questions, 
                                   SUM(COALESCE(qs.score, 0)) as total_score
                            FROM tests t
                            LEFT JOIN questions q ON t.test_id = q.test_id
                            LEFT JOIN question_scores qs ON q.question_id = qs.question_id
                            GROUP BY t.test_id
                            ORDER BY t.created_at DESC";
                    $result = $conn->query($sql);
                    
                    if ($result->num_rows > 0) {
                        echo '<div class="table-container">';
                        echo '<table class="table table-hover mb-0">';
                        echo '<thead class="text-center">';
                        echo '<tr class="table-active">';
                        echo '<th scope="col" class="text-secondary">#</th>';
                        echo '<th scope="col" class="text-start text-secondary">عنوان الإختبار</th>';
                        echo '<th scope="col" class="text-start text-secondary">الوصف</th>';
                        echo '<th scope="col" class="text-secondary">عدد الأسئلة</th>';
                        echo '<th scope="col" class="text-secondary">مجموع الدرجات</th>';
                        echo '<th scope="col" class="text-secondary">الردود</th>';
                        echo '<th scope="col" class="text-secondary">تاريخ الإنشاء</th>';
                        echo '<th scope="col" class="text-secondary">الحالة</th>';
                        echo '</tr>';
                        echo '</thead>';
                        echo '<tbody class="text-center">';
                        
                        $counter = 1;
                        while($row = $result->fetch_assoc()) {
                            // تعيين صنف الحالة بناءً على قيم معينة
                            $status_class = [
                                'published' => 'success',
                                'suspended' => 'warning',
                                'expired' => 'danger'
                            ];
                            $badge_class = $status_class[$row['status']] ?? 'secondary';
                        
                            echo "<tr onclick=\"window.location.href='edit_test.php?test_id=" . $row['test_id'] . "'\">";
                            echo "<th>$counter</th>";
                            echo "<td class='text-start'>" . htmlspecialchars($row['title']) . "</td>";
                            echo "<td class='text-start'>" . htmlspecialchars($row['description']) . "</td>";
                            echo "<td>" . $row['num_questions'] . "</td>";
                            echo "<td>" . $row['total_score'] . "</td>";
                            echo "<td> الردود </td>"; // يمكنك تعديل هذا الحقل بناءً على عدد الردود الفعلية
                            echo "<td>" . $row['created_at'] . "</td>";
                            echo "<td><span class='w-75 badge text-bg-" . $badge_class . "'>" . htmlspecialchars(match ($row['status']) {
                            'published' => 'تم النشر',
                            'suspended' => 'معلَّق',
                            'expired' => 'إنتهى'
                            }) . "</span></td>";
                            echo "</tr>";
                            $counter++;
                        }
                        echo '</tbody>';
                        echo '</table>';
                        echo '</div>';
                    } else {
                        $message = "لا توجد إحتبارات حالياً";
                    }
                ?>
                <?php if ($message): ?>
                    <div class="h-100 d-flex flex-column justify-content-center align-items-center">
                        <dotlottie-player src="https://lottie.host/6acf3b9a-3c86-4c3b-abb5-11a02a1db2f4/79fjM8x5uj.json" background="transparent" speed="1" style="width: 300px; height: 250px;" loop autoplay></dotlottie-player>
                        <h4 class="text-secondary"><?php echo $message; ?></h4>
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
    <script src="../../../../assets/js/manage_quizzes.js"></script>
    <script src="../../../../assets/js/handle_teacher_notification_panel.js"></script>
</body>
</html>