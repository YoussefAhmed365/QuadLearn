document.addEventListener('DOMContentLoaded', function () {
    // --- Initialize Modals ---
    const messageModalElement = document.getElementById('messageModal');
    const deleteAccountModalElement = document.getElementById('deleteAccountModal');

    let messageModal, deleteAccountModal;
    if (messageModalElement) {
        messageModal = new bootstrap.Modal(messageModalElement);
    } else {
        console.error("Modal element 'messageModal' not found.");
    }
    if (deleteAccountModalElement) {
        deleteAccountModal = new bootstrap.Modal(deleteAccountModalElement);
    } else {
        console.error("Modal element 'deleteAccountModal' not found.");
    }

    const messageDiv = document.getElementById('message');
    if (!messageDiv) {
        console.error("Message div 'message' not found.");
    }

    // --- Handle Profile Photo Upload ---
    const uploadForm = document.getElementById('uploadForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const fileInput = document.getElementById('fileToUpload');
            if (!fileInput) {
                console.error("File input 'fileToUpload' not found.");
                alert("حدث خطأ: لم يتم العثور على حقل تحميل الملف.");
                return;
            }
            const file = fileInput.files[0];

            if (!file) {
                alert("يرجى اختيار ملف.");
                return;
            }

            const img = new Image();
            const reader = new FileReader();

            reader.onload = function (e) {
                img.src = e.target.result;
                img.onload = function () {
                    if (img.width !== img.height) {
                        alert("يجب أن تكون الصورة مربعة (1:1).");
                        return;
                    }
                    if (file.size > 5 * 1024 * 1024) { // 5MB
                        alert("حجم الملف يجب أن لا يتجاوز 5 ميجابايت.");
                        return;
                    }
                    const allowedTypes = ['image/webp', 'image/jpeg', 'image/jpg', 'image/png'];
                    if (!allowedTypes.includes(file.type)) {
                        alert("نوع الملف غير مدعوم. الملفات المدعومة هي: webp, jpg, jpeg, png.");
                        return;
                    }

                    const formData = new FormData(uploadForm);
                    fetch('profile_photo_handler.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (messageDiv) {
                            messageDiv.className = data.status === 'success' ? 'alert alert-success' : 'alert alert-danger';
                            messageDiv.innerHTML = data.message;
                            messageDiv.style.display = 'block';
                        } else {
                             alert(data.message); // Fallback
                        }
                        if (data.status === 'success') {
                            // Optionally, update the image preview on the page without a full reload
                            // For simplicity, reloading to see changes:
                            setTimeout(() => window.location.reload(), 1500);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        const errorMessage = 'حدث خطأ أثناء رفع الصورة.';
                        if (messageDiv) {
                            messageDiv.className = 'alert alert-danger';
                            messageDiv.innerHTML = errorMessage;
                            messageDiv.style.display = 'block';
                        } else {
                            alert(errorMessage); // Fallback
                        }
                    });
                };
            };
            reader.readAsDataURL(file);
        });
    }

    // --- Update Account Form ---
    const updateAccountForm = document.getElementById('updateAccountForm');
    if (updateAccountForm) {
        updateAccountForm.addEventListener('submit', function (event) {
            event.preventDefault();

            let allValid = true;
            if (!validateNewUsername()) allValid = false;
            if (!validateCurrentPassword()) allValid = false;

            const newPasswordInput = document.getElementById('new_password');
            if (newPasswordInput && newPasswordInput.value !== "" && !validateNewPassword()) allValid = false;

            if (!validateNewEmail()) allValid = false;
            if (!validateNewPhone()) allValid = false;

            // Student-specific validation for Guardian Phone
            if (document.getElementById('guardianPhone')) { // Check if guardianPhone field exists
                // If guardian phone is optional overall but required if student context, adjust logic here
                // The validateGuardianPhone uses allowEmpty=true, so it passes if empty.
                // If it MUST be filled for students, this needs stronger check or make validateGuardianPhone(..., allowEmpty=false)
                if (!validateGuardianPhone()) allValid = false;
            }
            // Add validation for 'level' if it becomes required and has a validation function

            if (!allValid) {
                if (messageDiv && messageModal) {
                    messageDiv.innerHTML = `<div class="text-center p-4"><i class="fa-solid fa-circle-exclamation text-warning" style="font-size: 5rem;"></i><h6 class="mt-3">يرجى تصحيح جميع الأخطاء في النموذج.</h6></div>`;
                    messageModal.show();
                    setTimeout(() => {
                        messageModal.hide();
                        if (messageDiv) messageDiv.innerHTML = "";
                    }, 3000);
                } else {
                    alert("يرجى تصحيح الأخطاء في النموذج."); // Fallback
                }
                // Focus on first error
                const errorMessages = document.querySelectorAll('.text-danger');
                for (let msg of errorMessages) {
                    if (msg.textContent !== "" && msg.id) {
                        let fieldId = msg.id.replace('message', '');
                        fieldId = fieldId.charAt(0).toLowerCase() + fieldId.slice(1);
                        if (fieldId === "gPhone") fieldId = "guardianPhone"; // Handle specific case from ssettings.php

                        const field = document.getElementById(fieldId);
                        if (field) {
                            field.focus();
                            break;
                        }
                    }
                }
                return;
            }

            fetch('update_account_handler.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(response => response.json())
            .then(data => {
                let iconClass = 'fa-regular fa-circle-check text-success';
                if (data.status === "warning") iconClass = 'fa-solid fa-circle-exclamation text-warning';
                else if (data.status !== "success") iconClass = 'fa-regular fa-circle-xmark text-danger';

                if (messageDiv) {
                    messageDiv.innerHTML = `<div class="text-center p-4"><i class="${iconClass}" style="font-size: 5rem;"></i><h6 class="mt-3">${data.message}</h6></div>`;
                }
                if (messageModal) messageModal.show();

            })
            .catch(error => {
                console.error('Error:', error);
                if (messageDiv) {
                    messageDiv.innerHTML = `<div class="text-center p-4"><i class="fa-solid fa-circle-xmark text-danger" style="font-size: 5rem;"></i><h6 class="mt-3">حدث خطأ أثناء معالجة الطلب.</h6></div>`;
                }
                 if (messageModal) messageModal.show();
            })
            .finally(() => {
                setTimeout(() => {
                    if (messageModal) messageModal.hide();
                    if (messageDiv) messageDiv.innerHTML = "";
                    window.location.reload(); // Reload to reflect changes or clear form state
                }, 3000);
            });
        });
    }

    // --- Delete Account ---
    const currentPasswordInput = document.getElementById('password');
    const deleteAccountButton = document.getElementById('deleteAccountButton');
    const deleteAccountForm = document.getElementById('deleteAccountForm');
    const passwordConfirmInput = document.getElementById('confirmPassword'); // Hidden input in delete form

    if (deleteAccountButton && currentPasswordInput && passwordConfirmInput && deleteAccountForm) {
        deleteAccountButton.addEventListener('click', function () {
            // This button (type="button") toggles the modal via Bootstrap's data-bs-toggle attributes.
            // Its JS purpose here is to validate/prefill data for the modal.
            if (currentPasswordInput.value === "") {
                if (messageDiv && messageModal) {
                    messageDiv.innerHTML = `<div class="text-center p-4"><i class="fa-solid fa-circle-xmark text-danger" style="font-size: 5rem;"></i><h6 class="mt-3">يرجى إدخال كلمة مرور الحساب للحذف.</h6></div>`;
                    messageModal.show(); // Show message in the main modal
                    setTimeout(() => {
                        messageModal.hide();
                        if (messageDiv) messageDiv.innerHTML = "";
                    }, 3000);
                } else {
                    alert("يرجى إدخال كلمة مرور الحساب للحذف.");
                }
                // Prevent modal from showing if password is not entered, or handle inside modal
                // For now, it just shows a message and modal will still open.
                return;
            } else {
                passwordConfirmInput.value = currentPasswordInput.value; // Populate hidden field in delete modal
            }
        });

        deleteAccountForm.addEventListener('submit', function (event) {
            event.preventDefault();

            // Validate the 'confirmPassword' field (which holds the current password for deletion)
            // The validateConfirmPassword function will check its format and if it matches the main 'password' field.
            if (!validateConfirmPassword()) {
                 // Show error in the main message modal, as deleteAccountModal might be closing.
                if (messageDiv && messageModal) {
                    messageDiv.innerHTML = `<div class="text-center p-4"><i class="fa-solid fa-circle-exclamation text-warning" style="font-size: 5rem;"></i><h6 class="mt-3">كلمة المرور المقدمة للحذف غير صحيحة أو غير متطابقة.</h6></div>`;
                    if (deleteAccountModal) deleteAccountModal.hide(); // Ensure delete modal is hidden
                    messageModal.show();
                    setTimeout(() => {
                        messageModal.hide();
                        if (messageDiv) messageDiv.innerHTML = "";
                    }, 3000);
                } else {
                    alert("كلمة المرور المقدمة للحذف غير صحيحة أو غير متطابقة.");
                }
                return; // Stop submission
            }


            fetch('../../../delete_account_handler.php', {
                method: 'POST',
                body: new FormData(this) // 'this' is deleteAccountForm
            })
            .then(response => response.json())
            .then(data => {
                let iconClass = 'fa-regular fa-circle-check text-success';
                if (data.status === "warning") iconClass = 'fa-solid fa-circle-exclamation text-warning';
                else if (data.status !== "success") iconClass = 'fa-regular fa-circle-xmark text-danger';

                if (deleteAccountModal) deleteAccountModal.hide(); // Hide the confirmation modal first

                if (messageDiv) {
                    messageDiv.innerHTML = `<div class="text-center p-4"><i class="${iconClass}" style="font-size: 5rem;"></i><h6 class="mt-3">${data.message}</h6></div>`;
                }
                if (messageModal) messageModal.show(); // Show the result message modal

                if (data.status === "success") {
                    setTimeout(() => {
                        if (messageModal) messageModal.hide();
                        if (messageDiv) messageDiv.innerHTML = "";
                        window.location.href = "../../../logout.php";
                    }, 3000);
                } else {
                    setTimeout(() => {
                        if (messageModal) messageModal.hide();
                        if (messageDiv) messageDiv.innerHTML = "";
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (deleteAccountModal) deleteAccountModal.hide();
                const errorMessage = "حدث خطأ أثناء معالجة طلب الحذف.";
                if (messageDiv && messageModal) {
                    messageDiv.innerHTML = `<div class="text-center p-4"><i class="fa-solid fa-circle-xmark text-danger" style="font-size: 5rem;"></i><h6 class="mt-3">${errorMessage}</h6></div>`;
                    messageModal.show();
                    setTimeout(() => {
                        messageModal.hide();
                        if (messageDiv) messageDiv.innerHTML = "";
                    }, 3000);
                } else {
                    alert(errorMessage);
                }
            });
        });
    }

    // --- Initialize Bootstrap tooltips ---
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}); // End of DOMContentLoaded

// --- Generic Validation Function ---
// (Identical to the one in ssettings.js and tsettings.js)
function validateField(inputId, messageId, regex, errorMessage, allowEmpty = false, sanitizeRegex = null) {
    document.getElementById("resetBtn").addEventListener("click", () => {
        messageDiv.textContent = "";
    });

    const inputField = document.getElementById(inputId);
    const messageDiv = document.getElementById(messageId);

    if (!inputField) {
        // If the field itself doesn't exist (e.g. guardianPhone on teacher's page),
        // and it's allowed to be 'empty' (or non-existent), consider it valid in this context.
        // This prevents errors when script runs on a page without an optional field.
        if (allowEmpty) return true;
        console.warn(`Validation: Input field not found: ${inputId}`);
        return false; // Or true if non-existence means valid (e.g. truly optional field)
    }
    // If messageDiv is not found, validation can still proceed, but errors won't be displayed there.
    if (!messageDiv) {
        console.warn(`Validation: Message div not found: ${messageId} for input ${inputId}`);
    }

    const value = inputField.value;

    if (value === "" && allowEmpty) {
        if (messageDiv) messageDiv.textContent = "";
        return true;
    }
    if (value === "" && !allowEmpty) {
        if (messageDiv) messageDiv.textContent = "هذا الحقل مطلوب.";
        else console.error(`Message div '${messageId}' not found for required field '${inputId}'.`);
        return false;
    }

    if (!regex.test(value)) {
        if (messageDiv) messageDiv.textContent = errorMessage;
        else console.error(`Message div '${messageId}' not found for regex fail on '${inputId}'. Error: ${errorMessage}`);
        if (sanitizeRegex) {
            inputField.value = value.replace(sanitizeRegex, '');
        }
        return false;
    } else {
        if (messageDiv) messageDiv.textContent = "";
        return true;
    }
}

// --- Specific Validation Functions ---
// (All these are common or will gracefully not apply if elements are missing, due to validateField checks)

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
    if (passwordInput && passwordInput.value === "") { // Optional field
        const messageDiv = document.getElementById("messageNewPassword");
        if (messageDiv) messageDiv.textContent = "";
        return true;
    }
    const passwordRegex = /^[a-zA-Z0-9!@#$%^&.*]{8,255}$/;
    const sanitizeRegex = /[^a-zA-Z0-9!@#$%^&.*]/g;
    // allowEmpty is true here because if the field exists but is empty, it's fine. If it has content, it must be valid.
    return validateField("new_password", "messageNewPassword", passwordRegex, "كلمة المرور الجديدة يجب أن تكون بين 8 و 255 حرفًا وتحتوي فقط على أحرف إنجليزية وأرقام ورموز: !@#$%^&.*", true, sanitizeRegex);
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

// Specific to student settings (ssettings.js), but safe to include.
// It will only be effective if 'guardianPhone' and 'messageGPhone' elements exist.
function validateGuardianPhone() {
    const phoneRegex = /^[0-9]{11}$/;
    const sanitizeRegex = /[^0-9]/g;
    // allowEmpty = true as per original ssettings.js. Field is optional or might not be present.
    return validateField("guardianPhone", "messageGPhone", phoneRegex, "رقم هاتف ولي الأمر يجب أن يتكون من 11 رقمًا.", true, sanitizeRegex);
}

// For delete account password confirmation.
function validateConfirmPassword() {
    const passwordRegex = /^[a-zA-Z0-9!@#$%^&.*]{8,255}$/;
    const sanitizeRegex = /[^a-zA-Z0-9!@#$%^&.*]/g;
    // 'confirmPassword' is the ID of the hidden input in the delete form. It's populated with the current password.
    // It must not be empty for deletion.
    let isFormatValid = validateField("confirmPassword", "messageConfirmPassword", passwordRegex, "كلمة المرور يجب أن تكون بين 8 و 255 حرفًا وتحتوي فقط على أحرف إنجليزية وأرقام ورموز: !@#$%^&.*", false, sanitizeRegex);

    if (!isFormatValid) return false;

    const mainPassword = document.getElementById('password'); // The visible current password input
    const confirmPasswordInput = document.getElementById('confirmPassword'); // The hidden input being validated
    const messageDiv = document.getElementById("messageConfirmPassword");

    if (!mainPassword || !confirmPasswordInput) {
        console.error("Required password fields for confirmation not found.");
        return false;
    }

    // Check if the value in the hidden 'confirmPassword' (which should be a copy of current password) matches the current 'password' field.
    // This ensures consistency before submitting for deletion.
    if (mainPassword.value !== confirmPasswordInput.value) {
        if (messageDiv && messageDiv.textContent === "") { // Only add this error if no format error exists
            messageDiv.textContent = "كلمة المرور غير متطابقة مع كلمة المرور الحالية.";
        }
        return false;
    }
    // If they match, clear any previous mismatch message.
    if (mainPassword.value === confirmPasswordInput.value) {
        if (messageDiv && messageDiv.textContent === "كلمة المرور غير متطابقة مع كلمة المرور الحالية.") {
            messageDiv.textContent = "";
        }
    }
    return true;
}


// --- Toggle Password Visibility Function ---
// (Identical to the one in ssettings.js and tsettings.js)
function togglePasswordVisibility(passwordInputId, toggleButtonId) {
    const passwordInput = document.getElementById(passwordInputId);
    const toggleButton = document.getElementById(toggleButtonId);

    if (!passwordInput || !toggleButton) {
        console.error(`Element not found for toggle: Input: ${passwordInputId}, Button: ${toggleButtonId}`);
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

// --- Copy Identity Function ---
// (Identical to the one in ssettings.js and tsettings.js)
function copyIdentity() {
    const identityField = document.getElementById("user_id");
    const identityIcon = document.getElementById("copy-identity-icon");

    if (!identityField || !identityIcon) {
        console.error("Element not found for copy identity: user_id or copy-identity-icon");
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
                identityIcon.setAttribute('title', 'تم النسخ!'); // Fallback
            }

            setTimeout(() => {
                identityIcon.classList.remove("fa-solid", "fa-check");
                identityIcon.classList.add("fa-regular", "fa-clipboard");
                if (iconTooltipInstance) {
                    identityIcon.setAttribute('data-bs-original-title', 'نسخ المعرف');
                } else {
                    identityIcon.setAttribute('title', 'نسخ المعرف');
                }
            }, 2000);
        })
        .catch(err => {
            console.error("Failed to copy identity: ", err);
            const iconTooltipInstance = bootstrap.Tooltip.getInstance(identityIcon);
            const failMessage = 'فشل النسخ';
            if (iconTooltipInstance) {
                identityIcon.setAttribute('data-bs-original-title', failMessage);
                iconTooltipInstance.show();
            } else {
                identityIcon.setAttribute('title', failMessage);
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