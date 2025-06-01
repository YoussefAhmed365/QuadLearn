document.addEventListener("DOMContentLoaded", () => {

    // --- Check if Bootstrap is loaded ---
    if (typeof bootstrap === 'undefined' || typeof bootstrap.Modal === 'undefined') {
        console.error("Bootstrap JS or Modal component not loaded!");
        // disable buttons that rely on Bootstrap modals
        const uploadButton = document.getElementById("uploadButton");
        if (uploadButton) uploadButton.disabled = true;
        return; // Stop script execution if Bootstrap is not available
    }

    // --- References to common elements ---
    const uploadDegreeModalElement = document.getElementById("uploadDegreeModal");
    const uploadStatusModalElement = document.getElementById("uploadStatusModal");
    const deleteConfirmationModalElement = document.getElementById('deleteConfirmationModal');
    const deleteAllStudentsModalElement = document.getElementById('deleteAllStudentsConfirmation');
    const addAssistantModalElement = document.getElementById('addAssistantModal');
    const degreesModalElement = document.getElementById('degreesModal');

    // Ensure modal elements exist before creating instances
    let uploadDegreeModal, uploadStatusModal, deleteConfirmationModal, deleteAllStudentsModal, addAssistantModal, degreesModal;
    if (uploadDegreeModalElement) {
        uploadDegreeModal = new bootstrap.Modal(uploadDegreeModalElement);
    } else {
        console.error("Upload Degree modal element not found!");
        return; // Critical modal is missing
    }
    if (uploadStatusModalElement) {
        uploadStatusModal = new bootstrap.Modal(uploadStatusModalElement);
    } else {
        console.error("Status modal element not found!");
        return; // Critical modal is missing
    }
    if (deleteConfirmationModalElement) {
        deleteConfirmationModal = new bootstrap.Modal(deleteConfirmationModalElement);
    } else {
        console.error("Delete confirmation modal element not found!");
        return; // Critical modal is missing
    }
    if (deleteAllStudentsModalElement) {
        deleteAllStudentsModal = new bootstrap.Modal(deleteAllStudentsModalElement);
    } else {
        console.error("Delete All Students modal element not found!");
        return; // Critical modal is missing
    }
    if (addAssistantModalElement) {
        addAssistantModal = new bootstrap.Modal(addAssistantModalElement);
    } else {
        console.error("Add Assistant modal element not found!");
        return; // Critical modal is missing
    }
    if (degreesModalElement) {
        degreesModal = new bootstrap.Modal(degreesModalElement);
    } else {
        console.error("Degrees modal modal element not found!");
        return; // Critical modal is missing
    }


    // References for Upload Degree Modal
    const FileInput = document.getElementById("excelFile");
    const useFileNameCheckbox = document.getElementById("useFileName");
    const examTitleInput = document.getElementById("examTitle");
    const uploadForm = document.getElementById("uploadDegreeForm");
    const messageDiv = document.getElementById("uploadStatus"); // Used for both upload and delete status

    // References for Delete Confirmation Modal
    const nameToDeleteElement = document.getElementById('nameToDelete');
    const idToDeleteInput = document.getElementById('idToDelete'); // Hidden input for ID
    const itemTypeToDeleteElement = document.getElementById('itemTypeToDelete'); // Span for item type (الطالب/المساعدين)
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    // References for Search Inputs and Tables
    const studentSearchInput = document.getElementById('studentSearchInput');

    // --- Helper function for debouncing search input ---
    function debounce(func, delay) {
        let timer;
        return function (...args) {
            clearTimeout(timer);
            timer = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // --- Function to update the table content via AJAX ---
    async function updateTable(tableId, searchValue = "") { // Default searchValue to empty string
        let scriptUrl = '';
        const tableContainer = document.getElementById(tableId);

        if (!tableContainer) {
            console.error(`Table container with ID '${tableId}' not found.`);
            return;
        }

        if (tableId === 'studentTable') {
            scriptUrl = `show-students-table.php?searchInput=${encodeURIComponent(searchValue)}`;
        } else if (tableId === 'assistantContainer') {
            scriptUrl = `show-assistants-table.php`;
        } else {
            console.error("Invalid table ID for updateTable:", tableId);
            return;
        }

        try {
            const response = await fetch(scriptUrl);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}, when fetching ${scriptUrl}`);
            }
            tableContainer.innerHTML = await response.text();
        } catch (error) {
            console.error(`Error updating ${tableId}:`, error);
            if (messageDiv && uploadStatusModal) {
                messageDiv.innerHTML = `<div class="alert alert-danger" role="alert">فشل تحديث الجدول (${tableId}): ${error.message}</div>`;
                uploadStatusModal.show();
            } else {
                alert(`فشل تحديث الجدول (${tableId}): ${error.message}`);
            }
        }
    }

    // --- Event listeners for Search Inputs ---
    if (studentSearchInput) {
        studentSearchInput.addEventListener(
            "input",
            debounce(function () {
                updateTable('studentTable', this.value);
            }, 300)
        );
    }

    // --- Upload Degree Modal Logic ---
    if (uploadForm && FileInput && useFileNameCheckbox && examTitleInput && uploadDegreeModal && uploadStatusModal && messageDiv) {
        const updateExamTitleInput = () => {
            const useFileName = useFileNameCheckbox.checked;
            const fileName = FileInput.files.length > 0 ? FileInput.files[0].name : "";
            if (useFileName) {
                examTitleInput.value = fileName;
                examTitleInput.setAttribute("disabled", "true");
                examTitleInput.removeAttribute("required");
            } else {
                examTitleInput.removeAttribute("disabled");
            }
        };
        useFileNameCheckbox.addEventListener("change", updateExamTitleInput);
        FileInput.addEventListener("change", updateExamTitleInput);
        updateExamTitleInput(); // Initial call

        // --- Handle form degrees submission ---
        uploadForm.addEventListener("submit", async (event) => {
            event.preventDefault();
            messageDiv.innerHTML = '<div class="d-flex align-items-center justify-content-center p-3 mx-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mb-0 ms-2">جاري رفع الملف...</p></div>';
            uploadStatusModal.show();
            const formData = new FormData(uploadForm);

            if (useFileNameCheckbox.checked) {
                formData.set('examTitle', FileInput.files.length > 0 ? FileInput.files[0].name : "");
            }

            try {
                const response = await fetch("upload_test_degree.php", {
                    method: 'POST',
                    body: formData,
                    headers: { "X-Requested-With": "XMLHttpRequest" },
                });
                uploadDegreeModal.hide();
                const data = await response.json();
                if (!response.ok) {
                    throw new Error(data.message || `HTTP error! Status: ${response.status}`);
                }

                let iconClass = 'fa-regular fa-circle-check text-success';
                if (data.status === "warning") iconClass = 'fa-solid fa-circle-exclamation text-warning';
                else if (data.status !== "success") iconClass = 'fa-regular fa-circle-xmark text-danger';

                messageDiv.innerHTML = `<div class="text-center p-4"><i class="${iconClass}" style="font-size: 5rem;"></i><h6 class="mt-3">${data.message || 'حدث خطأ غير معروف.'}</h6></div>`;

                if (data.status === "success") {
                    setTimeout(() => {
                        uploadStatusModal.hide();
                        updateTable('studentTable', studentSearchInput ? studentSearchInput.value : ''); // Update table
                        // window.location.reload(); // Simpler for now
                    }, 2000);
                } else {
                    setTimeout(() => { uploadStatusModal.hide(); }, 5000); // Keep modal longer for errors/warnings
                }
            } catch (error) {
                console.error("Upload Error:", error);
                uploadDegreeModal.hide();
                messageDiv.innerHTML = `<div class="text-center p-4"><i class="fa-regular fa-circle-xmark text-danger" style="font-size: 5rem;"></i><h6 class="mt-3">${error.message || 'حدث خطأ غير متوقع أثناء الرفع.'}</h6></div>`;
                setTimeout(() => { uploadStatusModal.hide(); }, 5000);
            }
        });
    }


    // --- Delete Confirmation Modal Logic ---

    if (deleteConfirmationModalElement) {
        deleteConfirmationModalElement.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget;
            if (!button) return;

            const itemId = button.getAttribute('data-id');
            const itemName = button.getAttribute('data-name');
            const itemType = button.getAttribute('data-type'); // 'الطالب' or 'المساعدين'

            if (nameToDeleteElement) nameToDeleteElement.textContent = itemName || 'العنصر المحدد';
            if (idToDeleteInput) idToDeleteInput.value = itemId || '';
            if (itemTypeToDeleteElement) itemTypeToDeleteElement.textContent = itemType || ''; // Store type
        });
    }


    if (confirmDeleteBtn && idToDeleteInput && itemTypeToDeleteElement && uploadStatusModal && messageDiv) {
        confirmDeleteBtn.addEventListener('click', async () => {
            const id = idToDeleteInput.value;
            const type = itemTypeToDeleteElement.textContent; // Retrieve stored type

            if (!id || !type) {
                console.error("ID or Type is missing for deletion.");
                messageDiv.innerHTML = `<div class="alert alert-danger text-center" role="alert">خطأ: معرف أو نوع العنصر مفقود.</div>`;
                uploadStatusModal.show();
                setTimeout(() => uploadStatusModal.hide(), 3000);
                return;
            }

            let deleteUrl = '';
            let tableToUpdate = ''; // This is the ID of the DIV container for the table content

            if (type === 'الطالب') {
                deleteUrl = 'delete-student.php';
                tableToUpdate = 'studentTable'; // The ID of the div that contains the student table HTML
            } else if (type === 'المساعدين') {
                deleteUrl = 'delete-assistant.php';
                tableToUpdate = 'assistantContainer'; // The ID of the div that contains the assistants cards HTML
            } else {
                console.error("Unknown item type for deletion:", type);
                messageDiv.innerHTML = `<div class="alert alert-danger text-center" role="alert">نوع العنصر غير معروف للحذف: ${type}</div>`;
                uploadStatusModal.show();
                setTimeout(() => uploadStatusModal.hide(), 3000);
                return;
            }

            if (deleteConfirmationModal) deleteConfirmationModal.hide();

            messageDiv.innerHTML = '<div class="d-flex align-items-center justify-content-center p-3"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mb-0 ms-2">جاري الحذف...</p></div>';
            uploadStatusModal.show();

            try {
                const formData = new FormData();
                formData.append('id', id);

                const response = await fetch(deleteUrl, {
                    method: 'POST',
                    body: formData,
                    headers: { "X-Requested-With": "XMLHttpRequest" },
                });

                const data = await response.json(); // Always try to parse JSON

                if (!response.ok) { // Check HTTP status after parsing
                    throw new Error(data.message || `HTTP error! Status: ${response.status}`);
                }


                if (data.status === 'success') {
                    messageDiv.innerHTML = `<div class="text-center p-4"><i class="fa-regular fa-circle-check text-success" style="font-size: 5rem;"></i><h6 class="mt-3">${data.message || `تم حذف ${type} بنجاح.`}</h6></div>`;

                    // Attempt to remove the item from the DOM directly
                    let itemElement;
                    if (tableToUpdate === 'studentTable') {
                        // student rows are <tr> with data-id
                        itemElement = document.querySelector(`#${tableToUpdate} tr[data-id="${id}"]`);
                    } else if (tableToUpdate === 'assistantContainer') {
                        updateTable(tableToUpdate, ""); // Reload the table if direct removal fails
                        // assistant items are <div class="col" data-id="...">
                        // Ensure that show-assistants-table.php adds data-id to the .col div
                        const container = document.getElementById(tableToUpdate); // e.g., <div id="assistantContainer">
                        if (container) {
                            itemElement = container.querySelector(`.col[data-id="${id}"]`);
                        }
                    }

                    if (itemElement) {
                        itemElement.remove();
                    } else {
                        // Fallback: Reload the specific table's content if direct removal fails or is complex
                        console.warn(`Could not find item with ID ${id} in ${tableToUpdate} for direct removal. Refreshing table.`);
                        let currentSearchValue = '';
                        if (tableToUpdate === 'studentTable' && studentSearchInput) {
                            currentSearchValue = studentSearchInput.value;
                        }
                        // Add similar for assistant search if it exists
                        updateTable(tableToUpdate, currentSearchValue);
                    }
                    // Update counts if they are displayed on the page
                    if (type === 'الطالب' && document.querySelector('.student-number h4')) {
                        const studentCountEl = document.querySelector('.student-number h4');
                        let currentCount = parseInt(studentCountEl.textContent.split(':')[1].trim());
                        if (!isNaN(currentCount) && currentCount > 0) studentCountEl.textContent = `عدد الطلاب: ${currentCount - 1}`;
                    } // Add similar for assistant count if displayed


                } else { // data.status is 'error' or something else
                    messageDiv.innerHTML = `<div class="text-center p-4"><i class="fa-regular fa-circle-xmark text-danger" style="font-size: 5rem;"></i><h6 class="mt-3">${data.message || `حدث خطأ أثناء حذف ${type}.`}</h6></div>`;
                }

            } catch (error) {
                console.error("Delete Error:", error);
                messageDiv.innerHTML = `<div class="text-center p-4"><i class="fa-regular fa-circle-xmark text-danger" style="font-size: 5rem;"></i><h6 class="mt-3">${error.message || 'حدث خطأ غير متوقع أثناء محاولة الحذف.'}</h6></div>`;
            } finally {
                // Hide status modal after a delay, regardless of outcome
                setTimeout(() => {
                    if (uploadStatusModal) uploadStatusModal.hide();
                    updateTable(tableToUpdate); // Just refresh the specific table
                }, 3000);
            }
        });
    }

    // --- Delete All Students Logic ---
    const confirmDeleteAllBtn = document.getElementById('confirmDeleteAllBtn');

    confirmDeleteAllBtn.addEventListener('click', () => {
        fetch('delete-all-students.php', {
            method: 'POST',
            headers: { "X-Requested-With": "XMLHttpRequest" },
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    messageDiv.innerHTML = `<div class="text-center p-4"><i class="fa-regular fa-circle-check text-success" style="font-size: 5rem;"></i><h6 class="mt-3">${data.message || 'تم حذف جميع الطلاب.'}</h6></div>`;
                    updateTable('studentTable'); // Refresh the student table
                } else {
                    messageDiv.innerHTML = `<div class="text-center p-4"><i class="fa-regular fa-circle-xmark text-danger" style="font-size: 5rem;"></i><h6 class="mt-3">${data.message || 'حدث خطأ أثناء حذف جميع الطلاب.'}</h6></div>`;
                }
            })
            .catch(error => {
                console.error('Error deleting all students:', error);
                messageDiv.innerHTML = `<div class="text-center p-4"><i class="fa-regular fa-circle-xmark text-danger" style="font-size: 5rem;"></i><h6 class="mt-3">${error.message || 'حدث خطأ غير متوقع أثناء حذف جميع الطلاب.'}</h6></div>`;
            })
            .finally(() => {
                uploadStatusModal.show();
                setTimeout(() => {
                    if (uploadStatusModal) uploadStatusModal.hide();
                    updateTable('studentTable'); // Refresh the student table
                }, 3000);
            });
    });

    // --- Event Delegation for Delete Buttons in Tables ---
    // This is more robust for dynamically updated tables.
    document.body.addEventListener('click', function (event) {
        const deleteButton = event.target.closest('.delete-btn');

        if (deleteButton) {
            event.preventDefault(); // Prevent default if it's a link styled as a button

            const itemId = deleteButton.getAttribute('data-id');
            const itemName = deleteButton.getAttribute('data-name');
            const itemType = deleteButton.getAttribute('data-type'); // 'الطالب' or 'المساعدين'

            if (itemId && itemName && itemType && deleteConfirmationModal) {
                // Populate the confirmation modal (this will also trigger 'show.bs.modal')
                // and then show it.
                // The 'show.bs.modal' listener will handle populating the fields.
                deleteConfirmationModal.show(deleteButton); // Pass the button as relatedTarget
            } else {
                console.error("Delete button is missing required data attributes (data-id, data-name, data-type) or modal is missing.");
                if (messageDiv && uploadStatusModal) {
                    messageDiv.innerHTML = `<div class="alert alert-danger text-center" role="alert">خطأ: بيانات الحذف غير مكتملة أو النافذة المنبثقة للحذف غير موجودة.</div>`;
                    uploadStatusModal.show();
                    setTimeout(() => { uploadStatusModal.hide(); }, 3000);
                }
            }
        }
    });

    // --- Add Assistant Modal ---
    const addAssistantForm = document.getElementById('addAssistantForm');
    addAssistantForm.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(addAssistantForm);

        messageDiv.innerHTML = '<div class="d-flex align-items-center justify-content-center p-3 mx-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mb-0 ms-2">جاري إضافة المساعد...</p></div>';
        addAssistantModal.hide();
        uploadStatusModal.show(); // Show the modal to display the loading message

        try {
            const response = await fetch('add-assistant.php', {
                method: 'POST',
                body: formData,
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.message || `HTTP error! Status: ${response.status}`);
            }

            let iconClass = 'fa-regular fa-circle-check text-success';
            if (data.status === "warning") iconClass = 'fa-solid fa-circle-exclamation text-warning';
            else if (data.status !== "success") iconClass = 'fa-regular fa-circle-xmark text-danger';

            messageDiv.innerHTML = `<div class="text-center p-4"><i class="${iconClass}" style="font-size: 5rem;"></i><h6 class="mt-3">${data.message || 'حدث خطأ غير معروف.'}</h6></div>`;

            if (data.status === 'success') {
                addAssistantForm.reset(); // Clear the form
                messageDiv.innerHTML = `<div class="text-center p-4"><i class="${iconClass}" style="font-size: 5rem;"></i><h6 class="mt-3">${data.message}</h6></div>`;
                addAssistantModal.hide();
                uploadStatusModal.show();
                setTimeout(() => {
                    updateTable('assistantContainer'); // Refresh the assistant table
                    addAssistantModal.show();
                    uploadStatusModal.hide(); // Hide the modal after a delay
                }, 2000);
            } else if (data.status === 'warning') {
                messageDiv.innerHTML = `<div class="text-center p-4"><i class="${iconClass}" style="font-size: 5rem;"></i><h6 class="mt-3">${data.message}</h6></div>`;
                addAssistantModal.hide();
                uploadStatusModal.show();
                setTimeout(() => {
                    addAssistantModal.show();
                    uploadStatusModal.hide();
                }, 5000); // Keep modal longer for warnings
            } else {
                messageDiv.innerHTML = `<div class="text-center p-4"><i class="${iconClass}" style="font-size: 5rem;"></i><h6 class="mt-3">${data.message}</h6></div>`;
                addAssistantModal.hide();
                uploadStatusModal.show();
                setTimeout(() => {
                    addAssistantModal.show();
                    uploadStatusModal.hide();
                }, 5000); // Keep modal longer for warnings
            }

        } catch (error) {
            console.error('Error adding assistant:', error);
            messageDiv.innerHTML = `<div class="text-center p-4"><i class="fa-regular fa-circle-xmark text-danger" style="font-size: 5rem;"></i><h6 class="mt-3">${error.message || 'حدث خطأ غير متوقع أثناء إضافة المساعد.'}</h6></div>`;
            setTimeout(() => {
                addAssistantModal.show();
                uploadStatusModal.hide();
            }, 5000); // Keep modal longer for warnings
            addAssistantModal.hide();
            uploadStatusModal.show();
        }
    });
    // --- Initial Load for tables if needed ---
    // Example: updateTable('studentTable', studentSearchInput ? studentSearchInput.value : '');
    // updateTable('assistantContainer'); // Load assistants initially

    // --- Show Students Degrees ---
    const viewDegreesBtn = document.getElementById('viewDegreesBtn');
    const degreesContainer = document.getElementById('degreesContainer');

    async function loadDegreesReport(testId = "") {
        if (!degreesContainer) return;
        degreesContainer.innerHTML = '<div class="d-flex align-items-center justify-content-center p-5"><div class="spinner-border text-primary" role="status"></div><p class="mb-0 ms-3 fs-5">جاري تحميل التقرير...</p></div>';
        const formData = new FormData();
        if (testId) formData.append('test_id', testId);

        try {
            const response = await fetch('degrees_report.php', {
                method: 'POST',
                headers: { "X-Requested-With": "XMLHttpRequest" },
                body: formData
            });
            const data = await response.json();
            if (data.status === 'success') {
                degreesContainer.innerHTML = data.pageContent;
                // Attach change event to dropdown
                const select = document.getElementById('degreeReport_test_id_selector');
                if (select) {
                    select.onchange = () => loadDegreesReport(select.value);
                }
            } else {
                degreesContainer.innerHTML = `<div class="alert alert-warning text-center m-3">${data.message || 'تعذر تحميل التقرير.'}</div>`;
            }
        } catch (error) {
            degreesContainer.innerHTML = `<div class="alert alert-danger text-center m-3">خطأ في تحميل التقرير: ${error.message}</div>`;
        }
    }

    if (viewDegreesBtn && degreesModal) {
        viewDegreesBtn.addEventListener('click', () => {
            degreesModal.show();
            loadDegreesReport();
        });
    }
});