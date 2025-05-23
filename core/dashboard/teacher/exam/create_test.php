<!-- test.php -->
<?php
require '../auth.php';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>إنشاء إختبار جديد | QuadLearn</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" integrity="sha384-nU14brUcp6StFntEOOEBvcJm4huWjB0OcIeQ3fltAfSmuZFrkAif0T+UtNGlKKQv" crossorigin="anonymous">
        <link rel="stylesheet" href="../../../../assets/css/create_test.css">
        <link rel="stylesheet" href="../../../../assets/css/#default-styles.css">
    </head>
    <body class="modals">
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
        <header class="d-flex justify-content-between align-items-center bg-white shadow-sm p-3 rounded-4 position-fixed z-1">
            <button type="button" id="externalSubmit" class="btn btn-primary" onclick="submitForm()">إرسال</button>
        </header>
        <div class="form-container container d-flex flex-column mt-5">
            <div class="d-flex flex-column align-items-center justify-content-center mt-5 p-2">
                <form id="testForm" action="process_form.php" method="POST" class="w-100">
                    <div class="bg-white mb-4 p-4 rounded-4 shadow-sm">
                        <input type="text" id="testTitle" name="title" class="form-control mb-3" placeholder="نموذج غير معنون" required>
                        <textarea id="testDescription" name="description" class="form-control" style="resize: none;" placeholder="وصف النموذج" required></textarea>
                        <div class="bg-white mb-4 p-4 rounded-4 shadow-sm">
                            <label for="testLevel" class="form-label">المرحلة</label>
                            <select id="testLevel" name="level" class="form-select" required>
                                <option value="first">الأول الثانوي</option>
                                <option value="second">الثاني الثانوي</option>
                                <option value="third">الثالث الثانوي</option>
                            </select>
                        </div>
                    </div>
                    <div id="questionsContainer">
                    </div>
                    <button type="button" id="addQuestion" class="btn btn-primary">إضافة سؤال</button><br><br>
                </form>
            </div>
        </div>
    </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/35b8a1f8f5.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
    <script src="../../../../assets/js/create_test.js"></script>
    </body>
</html>