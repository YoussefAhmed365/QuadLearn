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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../../assets/css/ssettings.css">
    <link rel="stylesheet" href="../../../../assets/css/#default-styles.css">
    <title>الملف الشخصي | QuadLearn أدر حسابك الخاص</title>
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
            <li class="activated">
                <a href="settings.php" class="active">
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
                        <h1 class="d-none d-sm-block">الملف الشخصي</h1>
                        <nav class="navbar d-block d-sm-none">
                            <div class="container-fluid">
                                <h1>الملف الشخصي</h1>
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
                                                <a class="nav-link" aria-current="page" href="dashboard.php">الرئيسية</a>
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
                                                <a class="nav-link active" href="settings.php">الإعدادات</a>
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
                                <span class="visually-hidden">New alerts</span>
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
    
            <div class="d-flex flex-column align-items-center">
                <!-- Message Modal -->
                <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-body" id="message"></div>
                        </div>
                    </div>
                </div>

                <!-- Profile Settings -->
                <div class="container-sm bg-white rounded-4 shadow p-5" style="width: clamp(70%, 1000px, 100%);">
                    <h2>إعدادات الحساب</h2>
                    <h6>قم بإجراء تغييرات على معلوماتك الشخصية.</h6>
                    <br>

                    <div id="edit-form" class="edit-form">
                        <h4 class="mb-4">ملفك الشخصي</h4>
                        <div class="row g-4 justify-content-around">
                            <div class="col-md-6">
                                <!-- Profile Photo -->
                               <form id="uploadForm" action="profile_photo_handler.php" method="POST" enctype="multipart/form-data">
                                   <input class="form-control" type="file" id="fileToUpload" name="fileToUpload" accept="image/*" required>
                                   <button type="submit" class="btn btn-default w-100 mt-4 mb-3">حفظ</button>
                                </form>
    
                                <!-- Account ID -->
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">معرف الحساب</label>
                                    <div class="idField d-flex align-items-center" id="identityShow">
                                        <input type="text" class="form-control" id="user_id" value="<?php echo $user_id; ?>" readonly>
                                        <i class="fa-regular fa-clipboard" id="copy-identity-icon" type="button" onclick="copyIdentity()" aria-hidden="true"></i>
                                    </div>
                                </div>
                            </div>
    
                            <?php
                                $target_dir = "../../../../assets/images/profiles/";
                                $dir = (glob("$target_dir$user_id.*")) ? "$target_dir$user_id.webp" : "{$target_dir}default.png";
                            ?>
                            <img src="<?php echo $dir; ?>" alt="Profile Photo" class="rounded-circle col-md-6" style="width: clamp(200px, 200px, 100%);height: max-content;">
                        </div>
    
                        <form id="updateAccountForm" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label" for="username">اسم المستخدم <b>.</b> خاص</label>
                                <input class="form-control" type="text" id="username" name="username" value="<?php echo isset($user['username']) ? htmlspecialchars($user['username']) : ''; ?>" oninput="validateNewUsername()" required autocomplete="off">
                                <div id="messageUsername" class="text-danger mt-1"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="password">كلمة المرور الحالية</label>
                                <div class="d-flex flex-column align-items-start position-relative mb-0 gap-3">
                                    <div class="w-100">
                                        <input class="form-control" type="password" id="password" name="password" oninput="validateCurrentPassword()" required autocomplete="current-password">
                                        <i class="fa-regular fa-eye position-absolute" id="passwordToggleBtn" type="button" onclick="togglePasswordVisibility('password', 'passwordToggleBtn')" style="left: 20px; top: 12px; cursor: pointer;"></i>
                                    </div>
                                    <div id="messagePassword" class="text-danger mt-1"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="new_password">كلمة المرور الجديدة (اختياري)</label>
                                <div class="d-flex flex-column align-items-start position-relative mb-0 gap-3">
                                    <div class="w-100">
                                        <input class="form-control" type="password" id="new_password" name="new_password" oninput="validateNewPassword()" autocomplete="new-password">
                                        <i class="fa-regular fa-eye position-absolute" id="newPasswordToggleBtn" type="button" onclick="togglePasswordVisibility('new_password', 'newPasswordToggleBtn')" style="left: 20px; top: 12px; cursor: pointer;"></i>
                                    </div>
                                    <div id="messageNewPassword" class="text-danger mt-1"></div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="email">البريد الإلكتروني <b>.</b> خاص</label>
                                <input class="form-control" type="email" id="email" name="email" value="<?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?>" oninput="validateNewEmail()" required autocomplete="off">
                                <div id="messageEmail" class="text-danger mt-1"></div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="phone">رقم الهاتف <b>.</b> عام</label>
                                <input class="form-control" type="text" id="phone" name="phone" value="<?php echo isset($user['phone_number']) ? htmlspecialchars($user['phone_number']) : ''; ?>" maxlength="11" oninput="validateNewPhone()" required autocomplete="off">
                                <div id="messagePhone" class="text-danger mt-1"></div>
                            </div>
    
                            <div class="mb-3">
                                <label class="form-label" for="guardianPhone">هاتف ولي الأمر <b>.</b> عام</label>
                                <input class="form-control" type="text" id="guardianPhone" name="guardian_phone" value="<?php echo !empty($data['guardian_phone']) ? htmlspecialchars($data['guardian_phone']) : ''; ?>" maxlength="11" oninput="validateGuardianPhone()" autocomplete="off">
                                <div id="messageGPhone" class="text-danger"></div>
                            </div>
    
                            <div class="mb-3">
                                <label class="form-label" for="level">المرحلة</label>
                                <select id="level" name="level" class="form-select">
                                    <option value="first" <?php echo ($data['level'] == "first") ? 'selected' : ''; ?>>الأول الثانوي</option>
                                    <option value="second" <?php echo ($data['level'] == "second") ? 'selected' : ''; ?>>الثاني الثانوي</option>
                                    <option value="third" <?php echo ($data['level'] == "third") ? 'selected' : ''; ?>>الثالث الثانوي</option>
                                </select>
                            </div>
    
                            <div class="mb-3">
                                <label class="form-label" for="bio">الوصف <b>.</b> عام</label>
                                <textarea class="form-control" placeholder="أضف الوصف الخاص بك" id="bio" name="bio" maxlength="255" autocomplete="off" style="height: 100px;"><?php echo isset($user['bio']) ? htmlspecialchars($user['bio']) : ''; ?></textarea>
                                <div id="messageBio" class="text-danger mt-1"></div>
                            </div>

                            <div class="d-flex flex-wrap align-items-center gap-3 justify-content-between mt-4">
                                <div>
                                    <button type="reset" id="resetBtn" class="btn btn-outline-secondary rounded-pill px-4">إعادة التعيين</button>
                                    <button type="submit" class="btn btn-default rounded-pill px-4 ms-2">حفظ التغييرات</button>
                                </div>
                                <button type="button" class="btn btn-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#deleteAccountModal" id="deleteAccountButton">حذف الحساب</button>
                            </div>
                        </form>
                    
                        <form id="deleteAccountForm" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                            <input type="hidden" name="confirmPassword" id="confirmPassword">
                            <div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="deleteLabel">هل أنت متأكد من حذف الحساب؟</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>هل أنت متأكد من حذف الحساب نهائياً؟ هذا الإجراء لا يمكن التراجع عنه.</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-danger rounded-pill" data-bs-dismiss="modal">الحذف نهائياً</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://unpkg.com/@dotlottie/player-component@2.7.12/dist/dotlottie-player.mjs" type="module"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.js"></script>
    <script src="https://kit.fontawesome.com/35b8a1f8f5.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="../../../../assets/js/settings.js"></script>
    <script src="../../../../assets/js/handle_student_notification_panel.js"></script>
</body>
</html>