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
    <link rel="stylesheet" href="../../../../assets/css/subject_files.css">
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
                <li class="activated">
                    <a href="subject_files.php" class="active">
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
        <main class="position-relative">
            <div id="toast-container" class="toast-container position-fixed"></div>
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
                                                <a class="nav-link" href="../main/dashboard.php">الرئيسية</a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link active" aria-current="page" href="subject_files.php">المحتوى الدراسي</a>
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
                <main class="bg-white w-100 p-4 rounded-4">
                    <div id="drop-zone" class="drop-zone">
                        <div id="upload-container" class="upload-container rounded-4 d-flex justify-content-center align-items-center flex-column p-5 mb-4">
                            <form id="upload-form" action="upload-file.php" method="POST" enctype="multipart/form-data">
                                <input type="file" id="file-input" name="uploaded_files[]" style="display: none;" multiple>
                            </form>
                            <div class="d-flex justify-content-center align-items-center mb-4">
                                <div class="file-icon rounded border border-secondary-subtle position-relative">
                                    <div class="upload-icon position-absolute">
                                        <!-- أيقونة الرفع -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="white" class="bi bi-cloud-upload" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M4.406 1.342A5.53 5.53 0 0 1 8 0c2.69 0 4.923 2 5.166 4.579C14.758 4.804 16 6.137 16 7.773 16 9.569 14.502 11 12.687 11H10a.5.5 0 0 1 0-1h2.688C13.979 10 15 8.988 15 7.773c0-1.216-1.02-2.228-2.313-2.228h-.5v-.5C12.188 2.825 10.328 1 8 1a4.53 4.53 0 0 0-2.941 1.1c-.757.652-1.153 1.438-1.153 2.055v.448l-.445.049C2.064 4.805 1 5.952 1 7.318 1 8.785 2.23 10 3.781 10H6a.5.5 0 0 1 0 1H3.781C1.708 11 0 9.366 0 7.318c0-1.763 1.266-3.223 2.942-3.593.143-.863.698-1.723 1.464-2.383"/>
                                            <path fill-rule="evenodd" d="M7.646 4.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V14.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-center align-items-center flex-column">
                                <span class="text-secondary">
                                    <button class="btn p-0 text-decoration-underline text-secondary" type="button" onclick="triggerFileInput()">انقر لاختيار ملفات</button>&nbsp;أو اسحب وأفلت هنا
                                </span>
                                <small class="text-secondary">أقصى حجم للملف 20 MB.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-4">
                        <div class="col-md">
                            <h5>الملفات المرفوعة</h5>
                            <h6 class="text-secondary">قم بإدارة الملفات المرفوعة من قبلك والمعلمين المساعدين لهذه المادة</h6>
                        </div>
                        <div class="col-md d-flex justify-content-end align-items-center">
                            <form id="searchFiles" method="POST">
                                <div class="input-group flex-nowrap">
                                    <span class="input-group-text" id="addon-wrapping"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></span>
                                    <input type="search" id="searchInput" class="form-control" placeholder="البحث عن..." aria-describedby="addon-wrapping" autofocus="">
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="bg-light d-flex justify-content-start align-items-center p-3 rounded mb-4">
                        <div class="bg-white rounded">
                            <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                                <input type="radio" class="btn-check" name="btnradio" id="showAll" value="showAll">
                                <label class="btn btn-outline-light text-black" for="showAll">عرض الكل</label>
    
                                <input type="radio" class="btn-check" name="btnradio" id="ownFiles" value="ownFiles">
                                <label class="btn btn-outline-light text-black" for="ownFiles">ملفاتك</label>
    
                                <input type="radio" class="btn-check" name="btnradio" id="ascending" value="ascending">
                                <label class="btn btn-outline-light text-black" for="ascending">ترتيب حسب الأقدم</label>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <td class="text-truncate">
                                        <input type="checkbox" class="form-check-input" id="selectAllCheckbox">
                                    </td>
                                    <td class="text-truncate">اسم الملف</td>
                                    <td class="text-truncate">تاريخ النشر</td>
                                    <td class="text-truncate">تاريخ التعديل</td>
                                    <td>الناشر</td>
                                    <td class="text-truncate">
                                        <button class="btn btn-sm w-100 text-primary fw-medium" id="deleteAllFiles" data-bs-toggle="modal" data-bs-target="#deleteAllModal" style="visibility: hidden;">حذف الملفات المحددة</button>
                                    </td>
                                </tr>
                            </thead>
                            <tbody id="filesContent">
                            </tbody>
                        </table>
                        <button class="show-all btn btn-light w-100 mb-3 justify-content-center align-items-center" id="showAllFiles">عرض الكل</button>
                        <!-- Modal لتأكيد الحذف -->
                        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header border border-0">
                                        <h5 class="modal-title" id="deleteModalLabel">تأكيد الحذف</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        هل أنت متأكد أنك تريد حذف هذا الملف؟
                                    </div>
                                    <div class="modal-footer border border-0">
                                        <button type="button" class="btn btn-danger col" id="confirmDeleteBtn">حذف</button>
                                        <button type="button" class="btn btn-secondary col" data-bs-dismiss="modal">إلغاء</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal لتعديل اسم الملف -->
                        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel">تعديل اسم الملف</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form id="editFileForm">
                                        <div class="modal-body">
                                            <div class="input-group mb-3">
                                                <input type="text" class="form-control" id="fileNameInput" name="file_name" required>
                                                <span class="input-group-text" id="fileExtensionInput"></span>
                                                <input type="hidden" id="fileIdInput" name="file_id">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-default col">حفظ التغييرات</button>
                                            <button type="button" class="btn btn-secondary col" data-bs-dismiss="modal">إلغاء</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <script src="https://kit.fontawesome.com/35b8a1f8f5.js" crossorigin="anonymous"></script>
    <script src="../../../../assets/js/subject_files.js"></script>
    <script src="../../../../assets/js/handle_teacher_notification_panel.js"></script>
</body>
</html>