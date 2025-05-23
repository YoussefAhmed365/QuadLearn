<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="assets/images/favicon-16x16.ico" sizes="16x16" type="image/x-icon">
    <link rel="icon" href="assets/images/favicon-32x32.ico" sizes="32x32" type="image/x-icon">
    <link rel="icon" href="assets/images/favicon-48x48.ico" sizes="48x48" type="image/x-icon">
    <link rel="apple-touch-icon" href="assets/images/apple-touch-icon-180x180.ico" sizes="180x180">
    <title>إسترجاع الحساب | QuadLearn</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="assets/css/account-recovery.css">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center vh-100">
        <form action="recovery-handler.php" method="POST" class="bg-white rounded-4 px-4 pt-2 pb-5 shadow row gy-4 w-50">
            <div class="col-8">
                <h5>هل نسيت كلمة المرور؟</h5>
                <h6 class="text-secondary">أدخل بريدك الإلكتروني المسجل لتعيينها فوراً.</h6>
            </div>
            <div class="col-4 d-flex flex-row-reverse justify-content-start align-items-center gap-2 mt-0">
                <img src="assets/images/logo.svg" alt="QuadLearn" style="width: 35px;">
                <h5>QuadLearn</h5>
            </div>
            <div class="col-12">
                <div class="form-floating mb-2 mt-3">
                    <input type="email" name="email" class="form-control" id="floatingInput" placeholder="name@example.com">
                    <label for="floatingInput">البريد الإلكتروني</label>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-default btn-lg fs-6 w-100">تعيين كلمة المرور</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/35b8a1f8f5.js" crossorigin="anonymous"></script>
    <script src="assets/js/account-recovery.js"></script>
</body>
</html>