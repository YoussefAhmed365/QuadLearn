document.getElementById("pageContent").style.display = "none";
window.onload = () => {
    document.getElementById("loading").style.display = "none";
    document.getElementById("pageContent").style.display = "block";
};

$(document).ready(function() {
    function submitForm(formId, alertContainerId) {
        $(formId).on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: 'signup_handler.php',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $(alertContainerId).html(
                        `<div class="alert alert-${response.type}" role="alert">${response.message}</div>`
                    );
                    if (response.type === 'warning') {
                        setTimeout(() => {
                            if(formId == "teacher") {
                                prevFlip.click();
                            } else {
                                sprevFlip.click();
                            }
                            prevPage.click();
                        }, 2000);
                    }
                    if (response.type === 'success') {
                        setTimeout(() => {
                            window.location.href = response.redirect;
                        }, 2000); // تأخير للتوجيه بمدة 2 ثانية
                    }
                },                
                error: function() {
                    $(alertContainerId).html(
                        `<div class="alert alert-danger" role="alert">حدث خطأ، حاول مرة أخرى</div>`
                    );
                }
            });
        });
    }

    // استدعاء الدالة للنموذجين
    submitForm('#teacher-form', '#alert-container-teacher');
    submitForm('#student-form', '#alert-container-student');
});

document.addEventListener("DOMContentLoaded", function () {
    // التعريف بالمتغيرات
    const username = document.getElementById("username");
    const email = document.getElementById("email");
    const password = document.getElementById("password");
    const first_name = document.getElementById("first_name");
    const last_name = document.getElementById("last_name");
    const account_type = document.getElementById("account_type");
    const gender = document.getElementById("gender");
    const nextPage = document.getElementById("nextPage");
    const prevPage = document.getElementById("prevPage");
    const nextFlip = document.getElementById("nextFlip");
    const prevFlip = document.getElementById("prevFlip");
    const sprevFlip = document.getElementById("sprevFlip");
    const page1 = document.getElementById("page1");
    const page2 = document.getElementById("page2");
    const flip1 = document.getElementById("flip1");
    const flip2 = document.getElementById("flip2");
    const flip3 = document.getElementById("flip3");
    const img = document.getElementById("img");

    // تمرير القيم للنموذج الأول
    const sentusername = document.getElementById("sent_username");
    const sentemail = document.getElementById("sent_email");
    const sentpassword = document.getElementById("sent_password");

    // تمرير القيم للنموذج الأخير
    const hiddenUsername = document.getElementById("hidden_username");
    const hiddenEmail = document.getElementById("hidden_email");
    const hiddenPassword = document.getElementById("hidden_password");
    const hiddenFirst_Name = document.getElementById("hidden_first_name");
    const hiddenLast_Name = document.getElementById("hidden_last_name");
    const hiddenAccount_Type = document.getElementById("hidden_account_type");
    const hiddenGender = document.getElementById("hidden_gender");

    const shiddenUsername = document.getElementById("shidden_username");
    const shiddenEmail = document.getElementById("shidden_email");
    const shiddenPassword = document.getElementById("shidden_password");
    const shiddenFirst_Name = document.getElementById("shidden_first_name");
    const shiddenLast_Name = document.getElementById("shidden_last_name");
    const shiddenAccount_Type = document.getElementById("shidden_account_type");
    const shiddenGender = document.getElementById("shidden_gender");

    // التحقق من البريد الإلكتروني
    function validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(String(email).toLowerCase());
    }

    // التحقق من اسم المستخدم
    function validateUsername(username) {
        const re = /^[a-zA-Z0-9.]+$/; // يسمح فقط بالأحرف الإنجليزية والأرقام
        return re.test(username);
    }

    // التحقق من كلمة المرور
    function validatePassword(password) {
        const re = /^[a-zA-Z0-9.!@#$%^&*()]+$/; // يسمح بالأحرف الإنجليزية والأرقام وبعض الرموز
        return re.test(password);
    }

    // إضافة أو إزالة كلاس "is-valid" فقط أثناء الكتابة
    function setValidClass(element, isValid) {
        if (isValid) {
            element.classList.remove('is-invalid');
            element.classList.add('is-valid');
        } else {
            element.classList.remove('is-valid');
        }
    }

    // إضافة كلاس "is-invalid" فقط بعد فقد التركيز
    function setInvalidClass(element, isValid) {
        if (!isValid) {
            element.classList.add('is-invalid');
        }
    }

    // التحقق من البريد الإلكتروني
    function handleEmailValidation() {
        const emailValue = email.value;
        const isValidEmail = validateEmail(emailValue);
        setValidClass(email, isValidEmail); // إضافة كلاس is-valid أثناء الكتابة فقط إذا كانت صحيحة
    
        // إخفاء الرسالة أثناء الكتابة إذا كان البريد صحيح
        const emailErrorSpan = document.getElementById('email-error');
        if (isValidEmail) {
            emailErrorSpan.textContent = '';
            emailErrorSpan.style.display = 'none';
        }
    }
    
    // إضافة كلاس is-invalid وظهور الرسالة عند فقد التركيز فقط إذا كانت المدخلات خاطئة
    email.addEventListener('blur', function() {
        const isValidEmail = validateEmail(email.value);
        setInvalidClass(email, isValidEmail); // إضافة كلاس is-invalid عند فقد التركيز إذا كانت خاطئة
    
        // عرض الرسالة عند فقدان التركيز إذا كان البريد غير صالح
        const emailErrorSpan = document.getElementById('email-error');
        if (email.value == "") {
            emailErrorSpan.textContent = 'يرجى تقديم عنوان بريد صالح';
            emailErrorSpan.style.display = 'block';
        } else if (!isValidEmail) {
            emailErrorSpan.textContent = 'عنوان البريد غير صالح';
            emailErrorSpan.style.display = 'block';
        }
    });
    
    // إضافة span لعرض خطأ البريد الإلكتروني
    const emailErrorSpan = document.createElement('span');
    emailErrorSpan.id = 'email-error';
    emailErrorSpan.className = 'text-danger';
    emailErrorSpan.style.display = 'none'; // إخفاء الرسالة بشكل افتراضي
    email.parentNode.appendChild(emailErrorSpan);

    // التعامل مع الأحداث أثناء الكتابة (input) وعند فقدان التركيز (blur)
    username.addEventListener('input', function() {
        const isValidUsername = validateUsername(username.value) && username.value.length >= 8;
        setValidClass(username, isValidUsername);
    });

    username.addEventListener('blur', function() {
        const isValidUsername = validateUsername(username.value) && username.value.length >= 8;
        setInvalidClass(username, isValidUsername);
    });

    // التعامل مع الأحداث
    email.addEventListener('input', handleEmailValidation);

    password.addEventListener('input', function() {
        const isValidPassword = validatePassword(password.value) && password.value.length >= 8;
        setValidClass(password, isValidPassword);
    });

    password.addEventListener('blur', function() {
        const isValidPassword = validatePassword(password.value) && password.value.length >= 8;
        setInvalidClass(password, isValidPassword);
    });

    // الانتقال للصفحة التالية
    nextPage.addEventListener("click", function (event) {
        let hasError = false;

        // التحقق من اسم المستخدم
        if (!validateUsername(username.value) || username.value.length < 8) {
            setInvalidClass(username, false);
            username.focus();
            hasError = true;
        } else {
            setValidClass(username, true);
        }

        // التحقق من البريد الإلكتروني
        if (!validateEmail(email.value)) {
            setInvalidClass(email, false);
            handleEmailValidation();
            email.focus();
            hasError = true;
        } else {
            setValidClass(email, true);
        }

        // التحقق من كلمة المرور
        if (!validatePassword(password.value) || password.value.length < 8) {
            setInvalidClass(password, false);
            password.focus();
            hasError = true;
        } else {
            setValidClass(password, true);
        }

        if (hasError) {
            event.preventDefault(); // منع الانتقال إذا كانت هناك أخطاء
        } else {
            // الانتقال للصفحة التالية
            sentusername.value = username.value;
            sentemail.value = email.value;
            sentpassword.value = password.value;

            page1.style.opacity = "0";
            page1.style.pointerEvents = "none";
            img.style.left = "50%";
            page2.style.opacity = "1";
            page2.style.pointerEvents = "all";

            setTimeout(() => {
                flip2.classList.remove("d-none");
                flip2.classList.add("d-flex");
                flip2.style.opacity = "0";
                flip2.style.pointerEvents = "none";
                flip2.style.left = "150%";

                flip3.classList.remove("d-none");
                flip3.classList.add("d-flex");
                flip3.style.opacity = "0";
                flip3.style.pointerEvents = "none";
                flip3.style.left = "150%";
            }, 1000);
        }
    });

    prevPage.addEventListener("click", function () {
        if (flip2.classList.contains("d-flex") || flip3.classList.contains("d-flex")) {
            flip2.classList.remove("d-flex");
            flip2.classList.add("d-none");
            flip3.classList.remove("d-flex");
            flip3.classList.add("d-none");
        }

        page1.style.opacity = "1";
        page1.style.pointerEvents = "all";
        img.style.left = "0";
        page2.style.opacity = "0";
        page2.style.pointerEvents = "none";
    });

    // التحقق من الأسماء العربية
    function validateArabicName(name) {
        const re = /^[\u0621-\u064A\s]+$/; // يسمح فقط بالأحرف العربية والمسافات
        return re.test(name);
    }

    // إضافة أو إزالة كلاس "is-valid" فقط أثناء الكتابة
    function setValidClass(element, isValid) {
        if (isValid) {
            element.classList.remove('is-invalid');
            element.classList.add('is-valid');
        } else {
            element.classList.remove('is-valid');
        }
    }

    // إضافة كلاس "is-invalid" فقط بعد فقد التركيز
    function setInvalidClass(element, isValid) {
        if (!isValid) {
            element.classList.add('is-invalid');
        }
    }

    // التعامل مع الأحداث أثناء الكتابة (input) وعند فقدان التركيز (blur)
    first_name.addEventListener('input', function() {
        const isValidFirstName = validateArabicName(first_name.value) && first_name.value.length >= 2;
        setValidClass(first_name, isValidFirstName);
    });

    first_name.addEventListener('blur', function() {
        const isValidFirstName = validateArabicName(first_name.value) && first_name.value.length >= 2;
        setInvalidClass(first_name, isValidFirstName);
    });

    last_name.addEventListener('input', function() {
        const isValidLastName = validateArabicName(last_name.value) && last_name.value.length >= 2;
        setValidClass(last_name, isValidLastName);
    });

    last_name.addEventListener('blur', function() {
        const isValidLastName = validateArabicName(last_name.value) && last_name.value.length >= 2;
        setInvalidClass(last_name, isValidLastName);
    });

    account_type.addEventListener('input', function() {
        account_type.value !== "";
    });

    account_type.addEventListener('blur', function() {
        account_type.value !== "";
    });

    gender.addEventListener('input', function() {
        gender.value !== "";
    });

    gender.addEventListener('blur', function() {
        gender.value !== "";
    });

    // الانتقال للصفحة التالية عند النقر على "nextFlip"
    nextFlip.addEventListener("click", function (event) {
        let hasError = false;

        // التحقق من الاسم الأول
        if (!validateArabicName(first_name.value) || first_name.value.length < 2) {
            setInvalidClass(first_name, false);
            first_name.focus();
            hasError = true;
        } else {
            setValidClass(first_name, true);
        }

        // التحقق من الاسم الأخير
        if (!validateArabicName(last_name.value) || last_name.value.length < 2) {
            setInvalidClass(last_name, false);
            last_name.focus();
            hasError = true;
        } else {
            setValidClass(last_name, true);
        }

        // التأكد من اختيار نوع الحساب
        if (account_type.value === "") {
            setInvalidClass(account_type, false);
            account_type.focus();
            hasError = true;
        } else {
            account_type.classList.remove('is-invalid');
        }

        // التأكد من اختيار الجنس
        if (gender.value === "") {
            setInvalidClass(gender, false);
            gender.focus();
            hasError = true;
        } else {
            gender.classList.remove('is-invalid');
        }

        if (hasError) {
            event.preventDefault(); // منع الانتقال إذا كانت هناك أخطاء
        } else {
            // الانتقال بناءً على نوع الحساب
            if (account_type.value === "teacher") {
                hiddenUsername.value = sentusername.value;
                hiddenEmail.value = sentemail.value;
                hiddenPassword.value = sentpassword.value;
                hiddenFirst_Name.value = first_name.value;
                hiddenLast_Name.value = last_name.value;
                hiddenAccount_Type.value = account_type.value;
                hiddenGender.value = gender.value;

                flip1.style.opacity = "0";
                flip1.style.pointerEvents = "none";
                flip1.style.left = "150%";
                flip2.style.opacity = "1";
                flip2.style.pointerEvents = "all";
                flip2.style.left = "50%";
            } else if (account_type.value === "student") {
                shiddenUsername.value = sentusername.value;
                shiddenEmail.value = sentemail.value;
                shiddenPassword.value = sentpassword.value;
                shiddenFirst_Name.value = first_name.value;
                shiddenLast_Name.value = last_name.value;
                shiddenAccount_Type.value = account_type.value;
                shiddenGender.value = gender.value;

                flip1.style.opacity = "0";
                flip1.style.left = "150%";
                flip1.style.pointerEvents = "none";
                flip3.style.opacity = "1";
                flip3.style.pointerEvents = "all";
                flip3.style.left = "50%";
            }
        }
    });

    sprevFlip.addEventListener("click", function () {
        flip1.style.opacity = "1";
        flip1.style.pointerEvents = "all";
        flip1.style.left = "50%";
        flip3.style.opacity = "0";
        flip3.style.pointerEvents = "none";
        flip3.style.left = "150%";
    });

    prevFlip.addEventListener("click", function () {
        flip1.style.opacity = "1";
        flip1.style.pointerEvents = "all";
        flip1.style.left = "50%";
        flip2.style.opacity = "0";
        flip2.style.pointerEvents = "none";
        flip2.style.left = "150%";
    });

    var input = document.getElementById("password");
    var icon = document.getElementById("show-password-icon");

    // وظيفة لتغيير موضع الأيقونة بناءً على الحالة
    function updateIconPosition() {
        if (input.classList.contains("is-valid") || input.classList.contains("is-invalid")) {
            icon.style.left = "30px"; // موضع للأيقونة في حال وجود is-valid أو is-invalid
        } else {
            icon.style.left = "10px"; // الموضع الافتراضي
        }
    }

    // الاستماع للأحداث لتغيير الأيقونة
    input.addEventListener('input', function() {
        updateIconPosition();
    });

    // التحقق عند فقدان التركيز من الحقل
    input.addEventListener('blur', function() {
        updateIconPosition();
    });

    // تغيير عرض كلمة المرور عند النقر على الأيقونة
    icon.addEventListener("click", function () {
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-regular", "fa-eye");
            icon.classList.add("fa-solid", "fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-solid", "fa-eye-slash");
            icon.classList.add("fa-regular", "fa-eye");
        }
    });

    // تحديث موضع الأيقونة عند تحميل الصفحة لأول مرة
    updateIconPosition();

    if (window.innerWidth <= 576) {
        page2.classList.remove("d-flex");
        page2.classList.add("d-none");
        flip2.classList.remove("d-flex");
        flip2.classList.add("d-none");
        flip3.classList.remove("d-flex");
        flip3.classList.add("d-none");
        
        nextPage.addEventListener("click", function (event) {
            let hasError = false;
            let alertContainer = document.getElementById('alert-container-page1');
            alertContainer.innerHTML = ''; // مسح التنبيهات القديمة
    
            // التحقق من اسم المستخدم
            if (!validateUsername(username.value)) {
                showAlert('alert-container-page1', 'warning', 'الرجاء إدخال اسم مستخدم يحتوي على أحرف إنجليزية وأرقام فقط.');
                username.focus();
                hasError = true;
            } else if (username.value.length < 8) {
                showAlert('alert-container-page1', 'warning', 'الرجاء إدخال اسم مستخدم يحتوي على 8 أحرف على الأقل.');
                username.focus();
                hasError = true;
            }
    
            // التحقق من البريد الإلكتروني
            if (!validateEmail(email.value)) {
                showAlert('alert-container-page1', 'warning', 'الرجاء إدخال بريد إلكتروني صالح.');
                email.focus();
                hasError = true;
            }
    
            // التحقق من كلمة المرور
            if (!validatePassword(password.value)) {
                showAlert('alert-container-page1', 'warning', 'الرجاء إدخال كلمة مرور تحتوي على أحرف إنجليزية وأرقام ورموز مسموح بها فقط.');
                password.focus();
                hasError = true;
            } else if (password.value.length < 8) {
                showAlert('alert-container-page1', 'warning', 'الرجاء إدخال كلمة مرور تحتوي على 8 أحرف على الأقل.');
                password.focus();
                hasError = true;
            }
    
            if (hasError) {
                event.preventDefault(); // منع الانتقال إذا كانت هناك أخطاء
            } else {
                // الانتقال للصفحة التالية
                sentusername.value = username.value;
                sentemail.value = email.value;
                sentpassword.value = password.value;
    
                page1.style.opacity = "0";
                page1.style.pointerEvents = "none";
                page2.style.opacity = "1";
                page2.style.pointerEvents = "all";
    
                setTimeout(() => {
                    page1.classList.remove("d-flex");
                    page1.classList.add("d-none");
                    page2.classList.remove("d-none");
                    page2.classList.add("d-flex");
                }, 500);
            }
        });

        prevPage.addEventListener("click", function () {
            page1.style.opacity = "1";
            page1.style.pointerEvents = "all";
            page2.style.opacity = "0";
            page2.style.pointerEvents = "none";

            setTimeout(() => {
                page2.classList.remove("d-flex");
                page2.classList.add("d-none");
                page1.classList.remove("d-none");
                page1.classList.add("d-flex");
            }, 500);
        });

        nextFlip.addEventListener("click", function (event) {
            let hasError = false;
            let alertContainer = document.getElementById('alert-container-page2');
            alertContainer.innerHTML = ''; // مسح التنبيهات القديمة
    
            // التحقق من الاسم الأول
            if (!validateArabicName(first_name.value)) {
                showAlert('alert-container-page2', 'warning', 'الرجاء إدخال الاسم الأول باللغة العربية فقط.');
                first_name.focus();
                hasError = true;
            } else if (first_name.value.length < 2) {
                showAlert('alert-container-page2', 'warning', 'الرجاء إدخال الاسم الأول مكون من حرفين على الأقل.');
                first_name.focus();
                hasError = true;
            }
    
            // التحقق من الاسم الأخير
            if (!validateArabicName(last_name.value)) {
                showAlert('alert-container-page2', 'warning', 'الرجاء إدخال الاسم الأخير باللغة العربية فقط.');
                last_name.focus();
                hasError = true;
            } else if (last_name.value.length < 2) {
                showAlert('alert-container-page2', 'warning', 'الرجاء إدخال الاسم الأخير مكون من حرفين على الأقل.');
                last_name.focus();
                hasError = true;
            }
    
            // التأكد من اختيار نوع الحساب والجنس
            if (account_type.value === "") {
                showAlert('alert-container-page2', 'warning', 'الرجاء اختيار نوع الحساب.');
                account_type.focus();
                hasError = true;
            }
    
            if (gender.value === "") {
                showAlert('alert-container-page2', 'warning', 'الرجاء اختيار الجنس.');
                gender.focus();
                hasError = true;
            }
    
            if (hasError) {
                event.preventDefault();
            } else {
                // الانتقال بناءً على نوع الحساب
                if (account_type.value === "teacher") {
                    hiddenUsername.value = sentusername.value;
                    hiddenEmail.value = sentemail.value;
                    hiddenPassword.value = sentpassword.value;
                    hiddenFirst_Name.value = first_name.value;
                    hiddenLast_Name.value = last_name.value;
                    hiddenAccount_Type.value = account_type.value;
                    hiddenGender.value = gender.value;
    
                    flip1.style.opacity = "0";
                    flip1.style.pointerEvents = "none";
                    flip2.style.opacity = "1";
                    flip2.style.pointerEvents = "all";

                    setTimeout(() => {
                        flip1.classList.remove("d-flex");
                        flip1.classList.add("d-none");
                        flip2.classList.remove("d-none");
                        flip2.classList.add("d-flex");
                    }, 500);
                } else if (account_type.value === "student") {
                    shiddenUsername.value = sentusername.value;
                    shiddenEmail.value = sentemail.value;
                    shiddenPassword.value = sentpassword.value;
                    shiddenFirst_Name.value = first_name.value;
                    shiddenLast_Name.value = last_name.value;
                    shiddenAccount_Type.value = account_type.value;
                    shiddenGender.value = gender.value;
    
                    flip1.style.opacity = "0";
                    flip1.style.pointerEvents = "none";
                    flip3.style.opacity = "1";
                    flip3.style.pointerEvents = "all";

                    setTimeout(() => {
                        flip1.classList.remove("d-flex");
                        flip1.classList.add("d-none");
                        flip2.classList.remove("d-none");
                        flip2.classList.add("d-flex");
                    }, 500);
                }
            }
        });
        
        prevFlip.addEventListener("click", function () {
            flip1.style.opacity = "1";
            flip1.style.pointerEvents = "all";
            flip2.style.opacity = "0";
            flip2.style.pointerEvents = "none";

            setTimeout(() => {
                flip2.classList.remove("d-flex");
                flip2.classList.add("d-none");
                flip1.classList.remove("d-none");
                flip1.classList.add("d-flex");
            }, 500);
        });

        sprevFlip.addEventListener("click", function () {
            flip1.style.opacity = "1";
            flip1.style.pointerEvents = "all";
            flip3.style.opacity = "0";
            flip3.style.pointerEvents = "none";

            setTimeout(() => {
                flip3.classList.remove("d-flex");
                flip3.classList.add("d-none");
                flip1.classList.remove("d-none");
                flip1.classList.add("d-flex");
            }, 500);
        });
    }
});