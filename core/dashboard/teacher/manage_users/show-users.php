<?php
// show-users.php
require '../auth.php';

$sqlStudents = "SELECT COUNT(*) AS studentCount FROM assigned_students WHERE teacher_id = $user_id";
$resultStudents = $conn->query($sqlStudents);
$rowStudents = $resultStudents->fetch_assoc();
$studentCount = $rowStudents['studentCount'];

$sqlAssistants = "SELECT COUNT(*) AS assistantCount FROM assigned_assistants WHERE teacher_id = $user_id";
$resultAssistants = $conn->query($sqlAssistants);
$rowAssistants = $resultAssistants->fetch_assoc();
$assistantCount = $rowAssistants['assistantCount'];
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
    <link rel="stylesheet" href="../../../../assets/css/show-users.css">
    <link rel="stylesheet" href="../../../../assets/css/#default-styles.css">
    <title>إدارة الأعضاء | QuadLearn</title>
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
            <li class="activated">
                <a href="show-users.php" class="active">
                    <div class="icon">
                        <i class="fa-solid fa-user-group"></i>
                    </div>
                    <span class="item">إدارة الأعضاء</span>
                </a>
            </li>
            <li>
                <a href="../requests/requests.php">
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
                        <h1 class="d-none d-sm-block">إدارة الأعضاء</h1>
                        <nav class="navbar d-block d-sm-none">
                            <div class="container-fluid">
                                <h1>إدارة الأعضاء</h1>
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
                                                <a class="nav-link active" href="show-users.php">إدارة الأعضاء</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="../requests/requests.php">الطلبات</a>
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
            
            <!-- Tabs -->
            <?php
            if($title == "معلم") {
                ?>
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="student-tab" data-bs-toggle="tab" data-bs-target="#student-content" type="button" role="tab" aria-controls="student-content" aria-selected="true">الطلاب</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="assistant-tab" data-bs-toggle="tab" data-bs-target="#assistant-content" type="button" role="tab" aria-controls="assistant-content" aria-selected="false">المساعدين</button>
                        </li>
                    </ul>
                <?php
            }
            ?>
            <div class="tab-content mt-5 bg-white rounded-4 shadow p-4" id="myTabContent">
                <div class="tab-pane fade show active" id="student-content" role="tabpanel" aria-labelledby="student-tab">
                    <div class="container">
                        <h2>سجل الطلاب</h2>
                    </div>
                    <br>
                    <div class="container mb-3">
                        <div class="student-number">
                            <h4>عدد الطلاب: <?php echo $studentCount; ?></h4>
                        </div>
                        <div class="row justify-content-between g-4">
                            <div class="col-sm input-group flex-nowrap">
                                <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></span>
                                <input type="search" id="studentSearchInput" class="form-control" placeholder="البحث عن..." aria-describedby="addon-wrapping" autofocus="">
                            </div>
                            <div class="col-sm">
                                <?php if ($studentCount > 0) : ?>
                                    <div class="row gx-3">
                                        <div class="col-sm-4">
                                            <button class="btn btn-default w-100" id="addDegreeBtn" data-bs-toggle="modal" data-bs-target="#uploadDegreeModal">إضافة درجات إختبار</button>
                                        </div>
                                        <div class="col-sm-4">
                                            <button class="btn btn-default w-100" id="viewDegreesBtn">سجل الدرجات</button>
                                        </div>
                                        <div class="col-sm-4">
                                            <button class="btn btn-danger w-100" id="deleteAllStudents" data-bs-toggle="modal" data-bs-target="#deleteAllStudentsConfirmation">حذف جميع الطلاب</button>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div id="studentTable" class="table-container">
                        <?php include 'show-students-table.php'; ?>
                    </div>
                </div>

                <div class="tab-pane fade" id="assistant-content" role="tabpanel" aria-labelledby="assistant-tab">
                    <div class="container">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2>سجل المدرسين المساعدين</h2>
                            <button class="btn btn-default" data-bs-toggle="modal" data-bs-target="#addAssistantModal">إضافة</button>
                        </div>
                    </div>
                    <br>
                    <div id="assistantContainer">
                        <?php include 'show-assistants-table.php'; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modals -->
        <div class="modal fade" id="uploadDegreeModal" tabindex="-1" aria-labelledby="uploadDegreeModalLabel">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h1 class="modal-title fs-5" id="uploadDegreeModalLabel">رفع ملف الدرجات</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" id="uploadDegreeForm" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="excelFile" class="form-label">اختر الملف</label>
                                <input type="file" class="form-control" name="excelFile" id="excelFile" accept=".xlsx" required>
                                <small class="text-danger">*قم بتضمين ملف .xlsx فقط</small>
                            </div>
                            <div class="mb-3">
                                <label for="examTitle" class="form-label">عنوان الإختبار</label>
                                <input type="text" class="form-control" name="examTitle" id="examTitle">
                                <input type="checkbox" name="useFileName" id="useFileName">
                                <span class="text-black">استخدام عنوان الملف بدلاً من ذلك</span>
                            </div>
                            <button type="submit" class="btn btn-default" id="uploadButton">رفع الملف</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Degrees -->
        <div class="modal fade" id="uploadStatusModal" tabindex="-1" aria-labelledby="uploadStatusModalLabel">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body d-flex flex-column justify-content-center align-items-center gap-3" id="uploadStatus">
                        </div>
                </div>
            </div>
        </div>

        <!-- Delete Specific Assistant or Student -->
        <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteConfirmationModalLabel">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="deleteConfirmationModalLabel">تأكيد الحذف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        هل أنت متأكد أنك تريد حذف: <strong id="nameToDelete"></strong>؟
                        <input type="hidden" id="idToDelete">
                        <span id="itemTypeToDelete" style="display: none;"></span>
                    </div>
                    <div class="modal-footer d-flex justify-content-center align-items-center border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="min-width: 100px;">إلغاء</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn" style="min-width: 100px;">حذف</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete All Students -->
        <div class="modal fade" id="deleteAllStudentsConfirmation" tabindex="-1" aria-labelledby="deleteAllStudentsConfirmationLabel">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="deleteAllStudentsConfirmationLabel">تأكيد الحذف</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        هل أنت متأكد أنك تريد حذف جميع الطلاب؟
                        <input type="hidden" id="idToDelete">
                        <span id="itemTypeToDelete" style="display: none;"></span>
                    </div>
                    <div class="modal-footer d-flex justify-content-center align-items-center border-0">
                        <button type="button" class="btn btn-secondary"  style="min-width: 100px;">إلغاء</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteAllBtn" data-bs-dismiss="modal" style="min-width: 100px;">حذف</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Assistant -->
        <div class="modal fade" id="addAssistantModal" tabindex="-1" aria-labelledby="addAssistantModalLabel">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="addAssistantModalLabel">إضافة معلم مساعد جديد</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" id="addAssistantForm">
                            <div class="mb-3">
                                <label for="assistantIdInput" class="form-label">معرف المعلم المساعد</label>
                                <input type="number" name="assistantId" id="assistantIdInput" class="form-control" placeholder="أدخل الرقم التعريفي للمساعد" required>
                            </div>
                            <button type="submit" class="btn btn-default w-100">
                                <i class="fa-solid fa-user-plus me-1"></i> إضافة المساعد
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <script src="https://kit.fontawesome.com/35b8a1f8f5.js" crossorigin="anonymous"></script>
    <script src="../../../../assets/js/show_users.js"></script>
    <script src="../../../../assets/js/handle_teacher_notification_panel.js"></script>
</body>
</html>