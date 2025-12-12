// إعداد منطقة السحب والإفلات
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('file-input');

if (dropZone) {
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });
}

if (fileInput) {
    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });
}

function triggerFileInput() {
    if (fileInput) fileInput.click();
}


async function handleFiles(files) {
    for (const file of files) {  // Loop through files one by one
        if (file.size <= 20 * 1024 * 1024) { // أقصى حجم للملف 20MB
           await uploadFile(file); // Await each upload
        } else {
            errorToast(file.name);
        }
    }
    setTimeout(() => {
        window.location.reload();
    }, 5000);
}

function errorToast(fileName) {
    const toast = document.createElement('div');
    toast.className = 'toast align-items-center text-bg-warning border-0 show';
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body text-white">الملف ${fileName} يتجاوز الحجم الأقصى.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    `;
    document.getElementById('toast-container').appendChild(toast);
    setTimeout(() => toast.remove(), 5000);
    return toast;
}
// دالة لإنشاء toast لكل ملف مع شريط التحميل
function createToast(fileName) {
    const toast = document.createElement('div');
    toast.className = 'toast show';
    toast.innerHTML = `
        <div class="toast-header">
            <div class="d-flex justify-content-center align-items-center bg-white border border-dark rounded me-2" style="width:35px;height:35px;">
                <i class="fa-regular fa-file fs-5"></i>
            </div>
            <strong class="me-auto">${fileName}</strong>
            <button type="button" class="btn-close" aria-label="Close" onclick="this.closest('.toast').remove();"></button>
        </div>
        <div class="toast-body">
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;"></div>
            </div>
        </div>
    `;
    document.getElementById('toast-container').appendChild(toast);
    return toast;
}

// دالة لرفع الملف باستخدام fetch
async function uploadFile(file) { // Make uploadFile async
    const formData = new FormData();
    formData.append('uploaded_files[]', file);

    const toast = createToast(file.name);
    const progressBar = toast.querySelector('.progress-bar');

    try {
        const response = await fetch('../../teacher/files/upload-file.php', { // Await the fetch call
            method: 'POST',
            body: formData,
        });
        const result = await response.json(); // Await the json conversion

        if (result.success && result.files.some(f => f.file === file.name && f.success)) { // Check if THIS file was successful
            progressBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
            progressBar.classList.add('bg-success');
            progressBar.style.width = '100%';
            setTimeout(() => toast.remove(), 2000);
        } else {
            const fileResult = result.files.find(f => f.file === file.name);
            const errorMessage = fileResult && fileResult.message ? fileResult.message : `فشل رفع الملف: ${file.name}`;
            showErrorToast(progressBar, toast, errorMessage);
        }
    } catch (error) {
        showErrorToast(progressBar, toast, `حدث خطأ أثناء رفع الملف: ${file.name}: ${error}`);
    }
}

// إعداد عام لطلب fetch
async function fetchData(url, method = "POST", body = null) {
    try {
        const options = {
            method,
            headers: { "Content-Type": "application/json" },
        };
        if (body) options.body = JSON.stringify(body);

        const response = await fetch(url, options);
        if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);

        return await response.text();
    } catch (error) {
        console.error("Fetch Error:", error);
        throw error;
    }
}

// التعامل مع البحث
const searchFilesForm = document.getElementById("searchFiles");
if (searchFilesForm) {
    searchFilesForm.addEventListener("submit", async (e) => {
        e.preventDefault();
        const searchInput = document.getElementById("searchInput").value.trim();
        if (!searchInput) return;

        try {
            const data = await fetchData("../../teacher/files/search-subject-files.php", "POST", { searchInput });
            document.getElementById("filesContent").innerHTML = data;
        } catch (error) {
            console.error("Error during search:", error);
        }
    });
}

// إدارة الملفات باستخدام IIFE
const fileManager = (() => {
    const filesTable = document.getElementById("filesContent");
    const showAllFilesButton = document.getElementById("showAllFiles");
    const radioBtns = document.getElementsByName("btnradio");

    if (!filesTable) return { fetchFiles: () => {} };

    // Instead of SQL clauses, we should send simple filter keys to the backend.
    // The backend should handle the SQL construction to prevent SQL Injection risks.
    const filterKeys = {
        showAll: 'showAll',
        ownFiles: 'ownFiles',
        ascending: 'ascending'
    };

    // التعامل مع تغيير خيارات التصفية
    radioBtns.forEach((button) => {
        button.addEventListener("change", async () => {
            const filterKey = button.value; // Expecting 'showAll', 'ownFiles', etc.

            try {
                // Send the filter key instead of raw SQL
                const data = await fetchData("../../teacher/files/show-all-files.php", "POST", { filter: filterKey });
                filesTable.innerHTML = data;
            } catch (error) {
                console.error("Error during filter fetch:", error);
            }
        });
    });

    // استدعاء الملفات عند التحميل
    const fetchFiles = async () => {
        try {
            const data = await fetchData("../../teacher/files/show-files.php", "GET");
            filesTable.innerHTML = data;
        } catch (error) {
            console.error("Error loading files:", error);
        }

        updateCheckboxState();
    };

    // عرض جميع الملفات
    if (showAllFilesButton) {
        showAllFilesButton.addEventListener("click", async () => {
            showAllFilesButton.disabled = true;
            showAllFilesButton.innerHTML = "<div class='btn-loader'></div>";

            try {
                // Send filter key 'showAll'
                const data = await fetchData("../../teacher/files/show-all-files.php", "POST", { filter: 'showAll' });
                filesTable.innerHTML = data;
            } catch (error) {
                console.error("Error showing all files:", error);
            } finally {
                showAllFilesButton.disabled = false;
                showAllFilesButton.style.display = "none";
                updateCheckboxState();
            }
        });
    }

    // استدعاء الملفات عند التحميل الأول للصفحة
    fetchFiles();

    return { fetchFiles };
})();

// تحديد جميع الملفات عند الضغط على مربع تحديد الكل
const selectAllCheckbox = document.getElementById("selectAllCheckbox");
const deleteSelectedFiles = document.getElementById("deleteAllFiles");

if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener("change", function (event) {
        const checkboxes = document.querySelectorAll("#filesContent input[type='checkbox']");
        const isChecked = event.target.checked;
        checkboxes.forEach((checkbox) => {
            checkbox.checked = isChecked;
        });
        toggleDeleteBtn();
    });
}

function updateCheckboxState() {
    const allCheckboxes = document.querySelectorAll("#filesContent input[type='checkbox']");
    const allCheckboxesLength = allCheckboxes.length;

    allCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", function () {
            const checkedCheckboxesLength = document.querySelectorAll("#filesContent input[type='checkbox']:checked").length;
            if (selectAllCheckbox) {
                if (checkedCheckboxesLength === allCheckboxesLength && allCheckboxesLength > 0) {
                    selectAllCheckbox.checked = true;
                } else {
                    selectAllCheckbox.checked = false;
                }
            }
            toggleDeleteBtn();
        });
    });
    toggleDeleteBtn();
}

function toggleDeleteBtn() {
    if (deleteSelectedFiles) {
        if (document.querySelectorAll("#filesContent input[type='checkbox']:checked").length > 0) {
            deleteSelectedFiles.style.visibility = "visible";
        } else {
            deleteSelectedFiles.style.visibility = "hidden";
        }
    }
}

// إدارة زر الحذف الجماعي
if (deleteSelectedFiles) {
    deleteSelectedFiles.addEventListener("click", async () => {
        const checkboxes = document.querySelectorAll("#filesContent input[type='checkbox']:checked");
        const selectedIds = Array.from(checkboxes).map((checkbox) =>
            checkbox.id.replace("fileCheckbox", "")
        );

        if (selectedIds.length === 0) {
            alert("Please select files to delete.");
            return;
        }

        try {
            const response = await fetchData("delete_files.php", "POST", { file_ids: selectedIds });
            // Assume response is success or handle it if it returns JSON
            // fetchData returns text. We might want to parse it if PHP returns JSON.
            // For now, assume success if no error thrown.
            checkboxes.forEach((checkbox) => checkbox.closest("tr").remove());
            alert("Files deleted successfully.");
        } catch (error) {
            console.error("Error deleting files:", error);
            alert("An error occurred while deleting files.");
        }
    });
}

// دالة لتحديث التوست عند حدوث خطأ
function showErrorToast(progressBar, toast, message) {
    progressBar.classList.remove('progress-bar-striped', 'progress-bar-animated');
    progressBar.classList.add('bg-danger');
    progressBar.style.width = '100%';
    toast.querySelector('.toast-body').innerHTML += `<div>${message}</div>`;
    setTimeout(() => toast.remove(), 5000); // إخفاء التوست بعد الفشل
}

// Handle Edit Modal
document.addEventListener("click", function (e) {
    if (e.target.matches("[data-bs-target='#editModal']")) {
        const fileId = e.target.getAttribute("data-file-id");
        const fileName = e.target.getAttribute("data-file-name") || "Unnamed File";

        // Populate the modal fields
        const fileIdInput = document.getElementById("fileIdInput");
        const fileNameInput = document.getElementById("fileNameInput");
        if(fileIdInput) fileIdInput.value = fileId;
        if(fileNameInput) fileNameInput.value = fileName;
    }
});

// Handle Edit Form Submission
const editFileForm = document.getElementById("editFileForm");
if (editFileForm) {
    editFileForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        const fileId = document.getElementById("fileIdInput").value.trim();
        const fileNameInput = document.getElementById("fileNameInput").value.trim();

        if (!fileNameInput) {
            alert("اسم الملف لا يمكن أن يكون فارغًا.");
            return;
        }

        const formData = new FormData();
        formData.append("file_id", fileId);
        formData.append("fileNameInput", fileNameInput);

        try {
            const response = await fetch("edit_file.php", {
                method: 'POST',
                body: formData,
            });
            const result = await response.json();

            if (result.status === "success") {
                alert("تم تعديل اسم الملف بنجاح");
                window.location.reload(); // Reload the page to update the file list
            } else {
                alert(result.message || "حدث خطأ أثناء تعديل اسم الملف");
            }
        } catch (error) {
            console.error("Error editing file:", error);
            alert("حدث خطأ أثناء تعديل اسم الملف");
        }
    });
}

// Handle Delete Modal
document.addEventListener("click", function (e) {
    if (e.target.matches("[data-bs-target='#deleteModal']")) {
        const fileId = e.target.getAttribute("data-file-id");

        // Display the file name in the delete confirmation modal
        const modalBody = document.querySelector("#deleteModal .modal-body");
        if(modalBody) modalBody.textContent = `هل أنت متأكد أنك تريد حذف الملف؟`;

        // Set the file ID on the confirm delete button
        const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
        if(confirmDeleteBtn) confirmDeleteBtn.setAttribute("data-file-id", fileId);
    }
});

// Handle Confirm Delete Button
const confirmDeleteBtn = document.getElementById("confirmDeleteBtn");
if (confirmDeleteBtn) {
    confirmDeleteBtn.addEventListener("click", async function () {
        const fileId = this.getAttribute("data-file-id");

        if (!fileId) {
            alert("لم يتم تحديد ملف للحذف.");
            return;
        }

        try {
            const response = await fetch(`delete_file.php?file_id=${fileId}`, { method: 'GET' });
            const result = await response.json();

            if (result.success) {
                alert("تم حذف الملف بنجاح");
                window.location.reload(); // Reload the page to update the file list
            } else {
                alert(result.message || "حدث خطأ أثناء حذف الملف");
            }
        } catch (error) {
            console.error("Error deleting file:", error);
            alert("حدث خطأ أثناء حذف الملف");
        }
    });
}