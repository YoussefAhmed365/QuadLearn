document.getElementById('uploadForm').addEventListener('submit', function (event) {
    event.preventDefault(); // منع إرسال النموذج

    var fileInput = document.getElementById('fileToUpload');
    var file = fileInput.files[0];

    // تحقق مما إذا تم اختيار ملف
    if (!file) {
        alert("يرجى اختيار ملف.");
        return;
    }

    // إنشاء عنصر صورة لتحميل الملف
    var img = new Image();
    var reader = new FileReader();
    
    reader.onload = function(e) {
        img.src = e.target.result;
        img.onload = function() {
            // التحقق من الأبعاد
            if (img.width !== img.height) {
                alert("يجب أن تكون الصورة مربعة (1:1).");
                return;
            }

            // التحقق من حجم الملف
            if (file.size > 5 * 1024 * 1024) {
                alert("حجم الملف يجب أن لا يتجاوز 5 ميجابايت.");
                return;
            }

            // التحقق من نوع الملف
            var allowedTypes = ['image/webp', 'image/jpeg', 'image/jpg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                alert("نوع الملف غير مدعوم. الملفات المدعومة هي: webp, jpg, jpeg, png.");
                return;
            }

            // إذا كانت جميع التحقق ناجحة، تابع إلى رفع الصورة
            var formData = new FormData(document.getElementById('uploadForm'));

            fetch('student_profile_photo_handler.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                var messageDiv = document.getElementById('message');
                messageDiv.className = data.status === 'success' ? 'alert alert-success' : 'alert alert-danger';
                messageDiv.innerHTML = data.message;
                messageDiv.style.display = 'block'; // عرض الرسالة
            })
            .catch(error => {
                console.error('Error:', error);
            });
        };
    };
    
    reader.readAsDataURL(file);
});

function validateEnglishUsername() {
    var inputField = document.getElementById("new_username");
    var messageDiv = document.getElementById("messageUsername");
    var Regex = /^[a-zA-Z0-9!@#$%^&.*]+$/;

    if (inputField.value === "") {
        messageDiv.textContent = ""; // إذا تم مسح الحقل، لا تظهر أي رسالة
    } else if (!Regex.test(inputField.value)) {
        messageDiv.textContent = "يسمح فقط بالأحرف الإنجليزية والأرقام والرموز";
        inputField.value = inputField.value.replace(/[^a-zA-Z0-9!@#$%^&.*]/g, ''); // إزالة أي حرف غير إنجليزي
    } else {
        messageDiv.textContent = ""; // في حالة إدخال صحيح، لا تظهر أي رسالة
    }
}

function validateEnglishPassword() {
    var inputField = document.getElementById("new_password");
    var messageDiv = document.getElementById("messagePassword");
    var Regex = /[^a-zA-Z0-9!@#$%^&.*]{8,255}$/; //التحقق من وجودهم جميعا

    if (inputField.value === "") {
        messageDiv.textContent = "";
    } else if (!Regex.test(inputField.value)) {
        messageDiv.textContent = "يسمح فقط بالأحرف الإنجليزية والأرقام والرموز";
        inputField.value = inputField.value.replace(/[^a-zA-Z0-9!@#$%^&.*]/g, '');
    } else {
        messageDiv.textContent = "";
    }
}

function validateEnglishEmail() {
    var inputField = document.getElementById("new_Email");
    var messageDiv = document.getElementById("messageEmail");
    var emailRegex = /^[^\s@]+(\.[^\s@]+)*@[^\s@]+\.[^\s@]+$/;

    if (inputField.addEventListener("blur", () => {
        if (inputField.value === "") {
            messageDiv.textContent = "";
        } else if (!emailRegex.test(inputField.value)) {
            messageDiv.textContent = "عنوان البريد الإلكتروني غير صحيح";
            inputField.value = inputField.value.replace(/[^a-zA-Z0-9@.]/g, ''); // إزالة أي حرف غير مسموح به ما عدا @ و .
        } else {
            messageDiv.textContent = "";
        }
    }));
}

function validateEnglishPhone() {
    var inputField = document.getElementById("new_phone_number");
    var messageDiv = document.getElementById("messagePhone");
    var Regex = /^[0-9]+$/;

    if (inputField.value === "") {
        messageDiv.textContent = ""; // إذا تم مسح الحقل، لا تظهر أي رسالة
    } else if (!Regex.test(inputField.value)) {
        messageDiv.textContent = "يرجى كتابة أرقام فقط";
        inputField.value = inputField.value.replace(/[^0-9]/g, ''); // إزالة أي حرف غير إنجليزي
    } else {
        messageDiv.textContent = ""; // في حالة إدخال صحيح، لا تظهر أي رسالة
    }
}

function validateGuardianPhone() {
    var inputField = document.getElementById("new_guardian_phone");
    var messageDiv = document.getElementById("messageGPhone");
    var Regex = /^[0-9]+$/;

    if (inputField.value === "") {
        messageDiv.textContent = ""; // إذا تم مسح الحقل، لا تظهر أي رسالة
    } else if (!Regex.test(inputField.value)) {
        messageDiv.textContent = "يرجى كتابة أرقام فقط";
        inputField.value = inputField.value.replace(/[^0-9]/g, ''); // إزالة أي حرف غير إنجليزي
    } else {
        messageDiv.textContent = ""; // في حالة إدخال صحيح، لا تظهر أي رسالة
    }
}

function validateConfirmPassword() {
    var inputField = document.getElementById("confirmPassword");
    var messageDiv = document.getElementById("messageConfirmPassword");
    var Regex = /[^a-zA-Z0-9!@#$%^&.*]{8,255}$/; //التحقق من وجودهم جميعا

    if (inputField.value === "") {
        messageDiv.textContent = "";
    } else if (!Regex.test(inputField.value)) {
        messageDiv.textContent = "يسمح فقط بالأحرف الإنجليزية والأرقام والرموز";
        inputField.value = inputField.value.replace(/[^a-zA-Z0-9!@#$%^&.*]/g, '');
    } else {
        messageDiv.textContent = "";
    }
}

function togglePasswordVisibility() {
    var passwordInput = document.getElementById('new_password');
    var passwordToggleBtn = document.getElementById('password-toggle');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordToggleBtn.textContent = 'إخفاء';
    } else {
        passwordInput.type = 'password';
        passwordToggleBtn.textContent = 'إظهار';
    }
}

function toggleConfirmPasswordVisibility() {
    var passwordInput = document.getElementById('confirmPassword');
    var passwordToggleBtn = document.getElementById('confirm-password-toggle');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        passwordToggleBtn.textContent = 'إخفاء';
    } else {
        passwordInput.type = 'password';
        passwordToggleBtn.textContent = 'إظهار';
    }
}

function copyIdentity() {
    // Get the elements for the identity field and the copy icon.
    const identityField = document.getElementById("user_id");
    const identityIcon = document.getElementById("copy-identity-icon");

    // Check if the elements exist before proceeding.
    if (!identityField || !identityIcon) {
        console.error("Element not found: user_id or copy-identity-icon");
        return;
    }

    // Use the Clipboard API for a more modern approach to copying text.
    navigator.clipboard.writeText(identityField.value)
        .then(() => {
            // Change the icon to a checkmark to indicate success.
            identityIcon.classList.remove("fa-regular", "fa-clipboard");
            identityIcon.classList.add("fa-solid", "fa-check");

            // Set a timeout to revert the icon back to the clipboard after 2 seconds.
            setTimeout(() => {
                identityIcon.classList.remove("fa-solid", "fa-check");
                identityIcon.classList.add("fa-regular", "fa-clipboard");
            }, 2000);
        })
        .catch(err => {
            console.error("Failed to copy identity: ", err);
            // Consider providing visual feedback to the user if copying fails.
        });
}  