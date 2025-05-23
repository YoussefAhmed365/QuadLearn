document.addEventListener("DOMContentLoaded", function () {
    // --- Initialize Modals ---
    const messageModalElement = document.getElementById('messageModal');
    const deleteAccountModalElement = document.getElementById('deleteAccountModal');

    let messageModal, deleteAccountModal;
    if (messageModalElement) {
        messageModal = new bootstrap.Modal(messageModalElement);
    } else {
        console.error("Modal element not found");
        return;
    }
    if (deleteAccountModalElement) {
        deleteAccountModal = new bootstrap.Modal(deleteAccountModalElement);
    } else {
        console.error("Delete modal element not found");
        return;
    }

    const messageDiv = document.getElementById('message');

    // --- Handle Profile Photo Upload ---
    document.getElementById('uploadForm').addEventListener('submit', function (event) {
        event.preventDefault(); // منع إرسال النموذج

        let fileInput = document.getElementById('fileToUpload');
        let file = fileInput.files[0];

        // تحقق مما إذا تم اختيار ملف
        if (!file) {
            alert("يرجى اختيار ملف.");
            return;
        }

        // إنشاء عنصر صورة لتحميل الملف
        let img = new Image();
        let reader = new FileReader();

        reader.onload = function (e) {
            img.src = e.target.result;
            img.onload = function () {
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
                let allowedTypes = ['image/webp', 'image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    alert("نوع الملف غير مدعوم. الملفات المدعومة هي: webp, jpg, jpeg, png.");
                    return;
                }

                // إذا كانت جميع التحقق ناجحة، تابع إلى رفع الصورة
                let formData = new FormData(document.getElementById('uploadForm'));

                fetch('profile_photo_handler.php', {
                    method: 'POST',
                    body: formData
                })
                    .then(response => response.json())
                    .then(data => {
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

    // --- Update Account Form ---
    document.getElementById('updateAccountForm').addEventListener('submit', function (event) {
        event.preventDefault();

        fetch('update_account_handler.php', {
            method: 'POST',
            body: new FormData(this)
        })
            .then(response => response.json())
            .then(data => {
                let iconClass = 'fa-regular fa-circle-check text-success';
                if (data.status === "warning") iconClass = 'fa-solid fa-circle-exclamation text-warning';
                else if (data.status !== "success") iconClass = 'fa-regular fa-circle-xmark text-danger';

                messageDiv.innerHTML = `<div class="text-center p-4"><i class="${iconClass}" style="font-size: 5rem;"></i><h6 class="mt-3">${data.message}</h6></div>`;
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.innerHTML = `<div class="text-center p-4"><i class="fa-solid fa-circle-xmark text-danger" style="font-size: 5rem;"></i><h6 class="mt-3">حدث خطأ أثناء معالجة الطلب.</h6></div>`;
            })
            .finally(() => {
                messageModal.show();
                setTimeout(() => {
                    messageModal.hide();
                    messageDiv.innerHTML = "";
                }, 3000);
            });
    });

    // --- Delete Account ---
    const currentPasswordInput = document.getElementById('password');
    const deleteAccountButton = document.getElementById('deleteAccountButton');
    const deleteAccountForm = document.getElementById('deleteAccountForm');
    const passwordConfirmInput = document.getElementById('confirmPassword');

    deleteAccountButton.addEventListener('click', function () {
        if (currentPasswordInput && currentPasswordInput.value === "") {
            messageDiv.innerHTML = `<div class="text-center p-4"><i class="fa-solid fa-circle-xmark text-danger" style="font-size: 5rem;"></i><h6 class="mt-3">يرجى إدخال كلمة مرور الحساب للحذف.</h6></div>`;
            messageModal.show();
            setTimeout(() => {
                messageModal.hide();
                messageDiv.innerHTML = "";
            }, 3000);
            return;
        } else {
            passwordConfirmInput.value = currentPasswordInput.value;
        }
    });

    deleteAccountForm.addEventListener('submit', function (event) {
        event.preventDefault();

        fetch('../../../delete_account_handler.php', {
            method: 'POST',
            body: new FormData(this)
        })
            .then(response => response.json())
            .then(data => {
                let iconClass = 'fa-regular fa-circle-check text-success';
                if (data.status === "warning") iconClass = 'fa-solid fa-circle-exclamation text-warning';
                else if (data.status !== "success") iconClass = 'fa-regular fa-circle-xmark text-danger';
                messageDiv.innerHTML = `<div class="text-center p-4"><i class="${iconClass}" style="font-size: 5rem;"></i><h6 class="mt-3">${data.message}</h6></div>`;
                deleteAccountModal.hide();
                if (data.status === "success") {
                    messageModal.show();
                    setTimeout(() => {
                        messageModal.hide();
                        messageDiv.innerHTML = "";
                        window.location.href = "../../../logout.php";
                    }, 3000);
                } else {
                    messageModal.show();
                    setTimeout(() => {
                        messageModal.hide();
                        messageDiv.innerHTML = "";
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageDiv.innerHTML = `<div class="text-center p-4"><i class="fa-solid fa-circle-xmark text-danger" style="font-size: 5rem;"></i><h6 class="mt-3">حدث خطأ أثناء معالجة الطلب.</h6></div>`;
                messageModal.show();
                setTimeout(() => {
                    messageModal.hide();
                    messageDiv.innerHTML = "";
                }, 3000);
            });
    });
});

function validateField(inputId, messageId, regex, errorMessage, allowEmpty = false, sanitizeRegex = null) {
    const inputField = document.getElementById(inputId);
    const messageDiv = document.getElementById(messageId);

    if (!inputField || !messageDiv) {
        console.error(`Element not found: ${!inputField ? inputId : ''} ${!messageDiv ? messageId : ''}`);
        return false;
    }

    const value = inputField.value;

    if (value === "" && allowEmpty) {
        messageDiv.textContent = "";
        return true;
    }
    if (value === "" && !allowEmpty) {
        messageDiv.textContent = "هذا الحقل مطلوب.";
        return false;
    }

    if (!regex.test(value)) {
        messageDiv.textContent = errorMessage;
        if (sanitizeRegex) {
            inputField.value = value.replace(sanitizeRegex, '');
        }
        return false;
    } else {
        messageDiv.textContent = "";
        return true;
    }
}

// --- Specific Validation Functions ---
function validateNewUsername() {
    const usernameRegex = /^[a-zA-Z0-9!@#$%^&.*]+$/;
    const sanitizeRegex = /[^a-zA-Z0-9!@#$%^&.*]/g;
    return validateField("username", "messageUsername", usernameRegex, "يسمح فقط بالأحرف الإنجليزية والأرقام والرموز: !@#$%^&.*", false, sanitizeRegex);
}

function validateCurrentPassword() {
    const passwordRegex = /^[a-zA-Z0-9!@#$%^&.*]{8,255}$/;
    const sanitizeRegex = /[^a-zA-Z0-9!@#$%^&.*]/g;
    return validateField("password", "messagePassword", passwordRegex, "كلمة المرور يجب أن تكون بين 8 و 255 حرفًا وتحتوي فقط على أحرف إنجليزية وأرقام ورموز: !@#$%^&.*", false, sanitizeRegex);
}

function validateNewPassword() {
    const passwordInput = document.getElementById("new_password");
    if (passwordInput && passwordInput.value === "") {
        const messageDiv = document.getElementById("messageNewPassword");
        if (messageDiv) messageDiv.textContent = "";
        return true;
    }
    const passwordRegex = /^[a-zA-Z0-9!@#$%^&.*]{8,255}$/;
    const sanitizeRegex = /[^a-zA-Z0-9!@#$%^&.*]/g;
    return validateField("new_password", "messageNewPassword", passwordRegex, "كلمة المرور يجب أن تكون بين 8 و 255 حرفًا وتحتوي فقط على أحرف إنجليزية وأرقام ورموز: !@#$%^&.*", true, sanitizeRegex);
}

function validateNewEmail() {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return validateField("email", "messageEmail", emailRegex, "عنوان البريد الإلكتروني غير صحيح.", false);
}

function validateNewPhone() {
    const phoneRegex = /^[0-9]{11}$/;
    const sanitizeRegex = /[^0-9]/g;
    return validateField("phone", "messagePhone", phoneRegex, "رقم الهاتف يجب أن يتكون من 11 رقمًا.", false, sanitizeRegex);
}

function validateConfirmPassword() {
    // Validate format
    const passwordRegex = /^[a-zA-Z0-9!@#$%^&.*]{8,255}$/;
    const sanitizeRegex = /[^a-zA-Z0-9!@#$%^&.*]/g;
    let isFormatValid = validateField("confirmPassword", "messageConfirmPassword", passwordRegex, "كلمة المرور يجب أن تكون بين 8 و 255 حرفًا وتحتوي فقط على أحرف إنجليزية وأرقام ورموز: !@#$%^&.*", false, sanitizeRegex);

    if (!isFormatValid) return false; // Stop if format is wrong

    // Check if it matches the main password for deletion
    const mainPassword = document.getElementById('password'); // Current password field
    const confirmPasswordInput = document.getElementById('confirmPassword');
    const messageDiv = document.getElementById("messageConfirmPassword");

    if (mainPassword && confirmPasswordInput && messageDiv && mainPassword.value !== confirmPasswordInput.value && confirmPasswordInput.value !== "") {
        if (messageDiv.textContent === "") { // Only add this error if no format error exists
            messageDiv.textContent = "كلمة المرور غير متطابقة مع كلمة المرور الحالية.";
        }
        return false; // Passwords do not match
    }
    // If format is valid and (either passwords match or main password check is not applicable here), clear message if it was for mismatch
    // This part might need refinement based on exact logic for when to clear mismatch message
    // For now, if format is valid and they match (or confirm is empty and format is ok), it's good.
    if (mainPassword && confirmPasswordInput && messageDiv && mainPassword.value === confirmPasswordInput.value) {
        if (messageDiv.textContent === "كلمة المرور غير متطابقة مع كلمة المرور الحالية.") {
            messageDiv.textContent = ""; // Clear mismatch message if they now match
        }
    }
    return true; // Format is valid, and if applicable, passwords match
}

// --- Toggle Password Visibility Function ---
function togglePasswordVisibility(passwordInputId, toggleButtonId) {
    const passwordInput = document.getElementById(passwordInputId);
    const toggleButton = document.getElementById(toggleButtonId);

    if (!passwordInput || !toggleButton) {
        console.error(`Element not found for toggle: ${!passwordInput ? passwordInputId : ''} ${!toggleButton ? toggleButtonId : ''}`);
        return;
    }

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleButton.classList.remove('fa-eye');
        toggleButton.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleButton.classList.remove('fa-eye-slash');
        toggleButton.classList.add('fa-eye');
    }
}

// --- Copy Identity Function (Updated) ---
function copyIdentity() {
    const identityField = document.getElementById("user_id"); // Reads from the visible input field with id="user_id"
    const identityIcon = document.getElementById("copy-identity-icon"); // This is the tooltip target

    if (!identityField || !identityIcon) {
        console.error("Element not found: user_id or copy-identity-icon");
        return;
    }

    navigator.clipboard.writeText(identityField.value)
        .then(() => {
            identityIcon.classList.remove("fa-regular", "fa-clipboard");
            identityIcon.classList.add("fa-solid", "fa-check");

            const iconTooltipInstance = bootstrap.Tooltip.getInstance(identityIcon);
            if (iconTooltipInstance) {
                identityIcon.setAttribute('data-bs-original-title', 'تم النسخ!');
                iconTooltipInstance.show();
            } else {
                // Fallback if Bootstrap's JS hasn't initialized the tooltip yet or it was destroyed
                // This typically shouldn't happen if Bootstrap JS is loaded and tooltips are initialized.
                identityIcon.setAttribute('title', 'تم النسخ!'); // Set title directly
                // Manually show a temporary message if tooltip instance is not available
                console.warn("Tooltip instance not found for copy icon. Set title attribute directly.");
            }

            setTimeout(() => {
                identityIcon.classList.remove("fa-solid", "fa-check");
                identityIcon.classList.add("fa-regular", "fa-clipboard");
                if (iconTooltipInstance) {
                    identityIcon.setAttribute('data-bs-original-title', 'نسخ المعرف');
                    // iconTooltipInstance.hide(); // Optionally hide if it doesn't auto-hide
                } else {
                    identityIcon.setAttribute('title', 'نسخ المعرف');
                }
            }, 2000);
        })
        .catch(err => {
            console.error("Failed to copy identity: ", err);
            const iconTooltipInstance = bootstrap.Tooltip.getInstance(identityIcon);
            if (iconTooltipInstance) {
                identityIcon.setAttribute('data-bs-original-title', 'فشل النسخ');
                iconTooltipInstance.show();
            } else {
                identityIcon.setAttribute('title', 'فشل النسخ');
            }
            setTimeout(() => {
                if (iconTooltipInstance) {
                    identityIcon.setAttribute('data-bs-original-title', 'نسخ المعرف');
                } else {
                    identityIcon.setAttribute('title', 'نسخ المعرف');
                }
            }, 2000);
        });
}

// --- Form Submission Handling ---
document.getElementById('updateAccountForm').addEventListener('submit', function (event) {
    let allValid = true;
    // Call all validation functions and logical AND their results
    if (!validateNewUsername()) allValid = false;
    if (!validateCurrentPassword()) allValid = false; // Current password is required
    // New password is optional, but if filled, it must be valid
    if (document.getElementById('new_password').value !== "" && !validateNewPassword()) allValid = false;
    if (!validateNewEmail()) allValid = false;
    if (!validateNewPhone()) allValid = false;
    // Add validation for bio if needed, e.g., validateField('bio', 'messageBio', /.+/, "الوصف مطلوب", true);

    if (!allValid) {
        event.preventDefault();
        // Find first field with an error message and focus it (optional UX improvement)
        const errorMessages = document.querySelectorAll('.text-danger');
        for (let msg of errorMessages) {
            if (msg.textContent !== "") {
                const fieldId = msg.id.replace('message', '').toLowerCase();
                const field = document.getElementById(fieldId);
                if (field) {
                    field.focus();
                    break;
                }
            }
        }
        // Using a more subtle notification than alert, e.g. a general message area on the form
        // For now, keeping the alert for simplicity.
        const generalMessageArea = document.getElementById('formGeneralMessage'); // Assuming you add such an element
        if (generalMessageArea) {
            generalMessageArea.textContent = "يرجى تصحيح الأخطاء في النموذج.";
            generalMessageArea.className = 'alert alert-danger mt-3';
        } else {
            alert("يرجى تصحيح الأخطاء في النموذج.");
        }
    }
});

document.getElementById('deleteAccountForm').addEventListener('submit', function (event) {
    if (!validateConfirmPassword()) { // This now also checks if it matches current password
        event.preventDefault();
    }
    // Server MUST re-validate the password for deletion.
});

// Initialize Bootstrap tooltips
// This ensures that any element with data-bs-toggle="tooltip" gets a tooltip.
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})