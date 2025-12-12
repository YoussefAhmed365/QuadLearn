// --- New Javascript Code ---

// Define userRole globally if not already defined (This should ideally come from the backend/page context)
// For safety, we can check if it's undefined before using it.

function addBadge(context = 'add') {
    const containerId = context === 'edit' ? 'editBadgesContainer' : 'badges-container';
    const container = document.getElementById(containerId);
    if (!container) {
        console.error(`Badge container '${containerId}' not found.`);
        return;
    }

    // Using a timestamp + random number to ensure uniqueness even if elements are deleted
    const uniqueId = Date.now() + '_' + Math.floor(Math.random() * 1000);

    const newBadgeDiv = document.createElement('div');
    newBadgeDiv.className = 'badge-item mb-3';

    newBadgeDiv.innerHTML = `
        <div class="input-group input-group-sm mb-1">
            <input type="text" class="form-control badge-input-name" placeholder="اسم الشارة" required>
            <button type="button" class="btn btn-outline-danger remove-badge-btn" title="حذف الشارة">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <div class="badge-color-options row mt-2 mb-3">
            <div class="col form-check">
                <input class="form-check-input greenBadge" type="radio" name="badge_color_${uniqueId}" value="green" checked>
            </div>
            <div class="col form-check">
                <input class="form-check-input yellowBadge" type="radio" name="badge_color_${uniqueId}" value="yellow">
            </div>
            <div class="col form-check">
                <input class="form-check-input redBadge" type="radio" name="badge_color_${uniqueId}" value="red">
            </div>
        </div>
    `;

    container.appendChild(newBadgeDiv);

    const badgeNameInput = newBadgeDiv.querySelector('.badge-input-name');
    const colorRadios = newBadgeDiv.querySelectorAll(`input[name="badge_color_${uniqueId}"]`);

    updateBadgeClass(badgeNameInput, 'green'); // Apply initial class

    colorRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateBadgeClass(badgeNameInput, this.value);
        });
    });

    newBadgeDiv.querySelector('.remove-badge-btn').addEventListener('click', function() {
        newBadgeDiv.remove();
    });
}

/**
 * Updates the visual class of a badge name input field.
 * @param {HTMLElement} badgeNameInputElement - The input element for the badge name.
 * @param {string} color - The selected color ('green', 'yellow', 'red').
 */
function updateBadgeClass(badgeNameInputElement, color) {
    if (!badgeNameInputElement) return;
    badgeNameInputElement.classList.remove('border-success', 'border-warning', 'border-danger', 'text-success', 'text-warning', 'text-danger');

    switch (color) {
        case 'green':
            badgeNameInputElement.classList.add('border-success');
            break;
        case 'yellow':
            badgeNameInputElement.classList.add('border-warning');
            break;
        case 'red':
            badgeNameInputElement.classList.add('border-danger');
            break;
    }
}


document.addEventListener("DOMContentLoaded", function () {
    const addPostModalElement = document.getElementById('addPostModal');
    const editPostModalElement = document.getElementById('editPostModal');
    const commonModalElement = document.getElementById("commonModal");
    const subjectModalElement = document.getElementById("subjectModal");
    const messageDiv = document.getElementById("messageDiv");

    let commonModal, addPostModalInstance, editPostModalInstance, subjectModal;
    if (commonModalElement) commonModal = new bootstrap.Modal(commonModalElement);
    if (addPostModalElement) addPostModalInstance = new bootstrap.Modal(addPostModalElement);
    if (editPostModalElement) editPostModalInstance = new bootstrap.Modal(editPostModalElement);
    if (subjectModalElement) subjectModal = new bootstrap.Modal(subjectModalElement);

    function showModal(state, message) {
        if (!commonModal || !messageDiv) {
            alert(message);
            return;
        }
        let iconClass = 'fa-regular fa-circle-check text-success fa-3x';
        let autoHideTimeout = 2500;

        if (state === "warning") {
            iconClass = 'fa-solid fa-circle-exclamation text-warning fa-3x';
            autoHideTimeout = 4000;
        } else if (state === "error") {
            iconClass = 'fa-regular fa-circle-xmark text-danger fa-3x';
            autoHideTimeout = 5000;
        }
        messageDiv.innerHTML = `<div class="text-center p-3"><i class="${iconClass}"></i><h6 class="mt-3">${message}</h6></div>`;
        commonModal.show();
        setTimeout(() => {
            commonModal.hide();
            messageDiv.innerHTML = '';
        }, autoHideTimeout);
    }

    // --- Add Post Form Submission ---
    const addPostForm = document.getElementById('addPostForm');
    if (addPostForm) {
        addPostForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const loadingSpinner = document.getElementById('loadingSpinner');
            if (loadingSpinner) loadingSpinner.classList.remove('d-none');

            const formData = new FormData(addPostForm);
            const badges = [];
            document.querySelectorAll('#badges-container .badge-item').forEach((badgeDiv) => {
                const nameInput = badgeDiv.querySelector('.badge-input-name');
                const colorInput = badgeDiv.querySelector('input[type="radio"]:checked');
                if (nameInput && nameInput.value.trim() !== '' && colorInput) {
                    badges.push({
                        name: nameInput.value.trim(),
                        color: colorInput.value
                    });
                }
            });
            formData.append('badges', JSON.stringify(badges));

            const addPostBasePath = (typeof userRole !== 'undefined' && userRole === 'teacher') ? '../../teacher/community/' : '../../student/community/';
            const addPostUrl = `${addPostBasePath}add_post.php`;

            fetch(addPostUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (loadingSpinner) loadingSpinner.classList.add('d-none');
                if (data.status === 'success' || data.status === 'warning') {
                    if(addPostModalInstance) addPostModalInstance.hide();
                    addPostForm.reset();
                    const badgeContainer = document.getElementById('badges-container');
                    if (badgeContainer) badgeContainer.innerHTML = '';
                    showModal(data.status, data.message);
                    loadPosts(document.querySelector('.listItem.active span') ? document.querySelector('.listItem.active span').textContent.trim() : 'الرئيسية');
                } else {
                    showModal('error', data.message || 'فشل إضافة المنشور.');
                }
            })
            .catch(error => {
                if (loadingSpinner) loadingSpinner.classList.add('d-none');
                console.error('Error adding post:', error);
                showModal('error', 'حدث خطأ فادح أثناء إضافة المنشور.');
            });
        });
    }


    // --- Populate Edit Post Modal & Handle File Deletion Marking ---
    let filesMarkedForDeletion = [];

    document.addEventListener('click', function (event) {
        const button = event.target.closest('.edit-post-btn');
        if (button) {
            filesMarkedForDeletion = [];
            const editForm = document.getElementById('editPostForm');
            if(editForm) editForm.reset();

            const editFilesContainer = document.getElementById('editExistingFiles');
            if (editFilesContainer) editFilesContainer.innerHTML = '<p class="text-muted small">جاري تحميل الملفات...</p>';
            
            const editBadgesContainer = document.getElementById('editBadgesContainer');
            if (editBadgesContainer) editBadgesContainer.innerHTML = '';

            const postId = button.getAttribute('data-id');
            const title = button.getAttribute('data-title');
            const content = button.getAttribute('data-content');
            const originalFilesStr = button.getAttribute('data-original-files') || '[]';
            const serverFilesStr = button.getAttribute('data-server-files') || '[]';
            const badgesStr = button.getAttribute('data-badges') || '[]';

            const editPostId = document.getElementById('editPostId');
            if (editPostId) editPostId.value = postId;

            const editPostTitle = document.getElementById('editPostTitle');
            if (editPostTitle) editPostTitle.value = title;

            const editPostContent = document.getElementById('editPostContent');
            if (editPostContent) editPostContent.value = content;

            let originalFilesArr = [];
            let serverFilesArr = [];
            try {
                originalFilesArr = JSON.parse(originalFilesStr);
                serverFilesArr = JSON.parse(serverFilesStr);
            } catch (e) { console.error("Error parsing file data for edit:", e); }

            if (editFilesContainer) {
                editFilesContainer.innerHTML = '';
                if (originalFilesArr.length > 0) {
                    originalFilesArr.forEach((originalName, idx) => {
                        const fileExtension = originalName.split('.').pop().toLowerCase();
                        let fileIcon = 'fa-file';
                        if (['jpg', 'jpeg', 'png', 'webp'].includes(fileExtension)) fileIcon = 'fa-file-image';
                        else if (['pdf'].includes(fileExtension)) fileIcon = 'fa-file-pdf';
                        else if (['doc', 'docx'].includes(fileExtension)) fileIcon = 'fa-file-word';
                        else if (['xlsx', 'csv'].includes(fileExtension)) fileIcon = 'fa-file-excel';
                        else if (['pptx'].includes(fileExtension)) fileIcon = 'fa-file-powerpoint';
                        else if (['mp4', 'avi', 'mov', 'mkv', 'webm'].includes(fileExtension)) fileIcon = 'fa-file-video';
                        else if (['zip', 'rar'].includes(fileExtension)) fileIcon = 'fa-file-archive';


                        const fileItemDiv = document.createElement('div');
                        fileItemDiv.className = 'd-flex justify-content-between align-items-center mb-1 p-2 border rounded bg-light';
                        fileItemDiv.innerHTML = `
                            <div class="d-flex align-items-center gap-2 text-truncate" style="max-width: calc(100% - 40px);">
                                <i class="fa-solid ${fileIcon} fs-5 text-secondary"></i>
                                <span class="text-truncate small" title="${originalName}">${originalName}</span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger delete-existing-file-btn" data-original-name="${originalName}" title="حذف هذا الملف">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        `;
                        fileItemDiv.querySelector('.delete-existing-file-btn').addEventListener('click', function () {
                            const fileToDeleteOriginal = this.getAttribute('data-original-name');
                            if (!filesMarkedForDeletion.includes(fileToDeleteOriginal)) {
                                filesMarkedForDeletion.push(fileToDeleteOriginal);
                            }
                            this.closest('.d-flex').classList.add('text-decoration-line-through', 'opacity-50');
                            this.disabled = true;
                        });
                        editFilesContainer.appendChild(fileItemDiv);
                    });
                } else {
                    editFilesContainer.innerHTML = '<p class="text-muted small">لا توجد ملفات مرفقة حالياً.</p>';
                }
            }

            if (editBadgesContainer) {
                editBadgesContainer.innerHTML = '';
                try {
                    const badgesArr = JSON.parse(badgesStr);
                    if (badgesArr && badgesArr.length > 0) {
                        badgesArr.forEach((badge, idx_badge) => {
                            const badgeName = badge.name || '';
                            const badgeColor = badge.color || 'green';

                            const uniqueId = Date.now() + '_' + Math.floor(Math.random() * 1000) + '_' + idx_badge;

                            const newBadgeDiv = document.createElement('div');
                            newBadgeDiv.className = 'badge-item mb-3';
                            newBadgeDiv.innerHTML = `
                                <div class="input-group input-group-sm mb-1">
                                    <input type="text" class="form-control badge-input-name" value="${badgeName}" placeholder="اسم الشارة" required>
                                    <button type="button" class="btn btn-outline-danger remove-badge-btn" title="حذف الشارة">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </div>
                                <div class="badge-color-options">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="badge_color_edit_${uniqueId}" value="green" ${badgeColor === 'green' ? 'checked' : ''}>
                                        <label class="form-check-label badge bg-success text-white p-1">أخضر</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="badge_color_edit_${uniqueId}" value="yellow" ${badgeColor === 'yellow' ? 'checked' : ''}>
                                        <label class="form-check-label badge bg-warning text-dark p-1">أصفر</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="badge_color_edit_${uniqueId}" value="red" ${badgeColor === 'red' ? 'checked' : ''}>
                                        <label class="form-check-label badge bg-danger text-white p-1">أحمر</label>
                                    </div>
                                </div>
                            `;
                            editBadgesContainer.appendChild(newBadgeDiv);

                            const currentBadgeNameInput = newBadgeDiv.querySelector('.badge-input-name');
                            const currentColorRadios = newBadgeDiv.querySelectorAll(`input[name="badge_color_edit_${uniqueId}"]`);
                            updateBadgeClass(currentBadgeNameInput, badgeColor);
                            currentColorRadios.forEach(radio => {
                                radio.addEventListener('change', function() { updateBadgeClass(currentBadgeNameInput, this.value); });
                            });
                            newBadgeDiv.querySelector('.remove-badge-btn').addEventListener('click', function() { newBadgeDiv.remove(); });
                        });
                    }
                } catch (e) { console.error("Error parsing badges data for edit:", e); }
            }
        }
    });

    // --- Edit Post Form Submission ---
    const editPostForm = document.getElementById('editPostForm');
    if (editPostForm) {
        editPostForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const spinner = document.getElementById('editPostLoadingSpinner');
            if(spinner) spinner.classList.remove('d-none');

            const formData = new FormData(editPostForm);

            const badgesToSubmit = [];
            document.querySelectorAll('#editBadgesContainer .badge-item').forEach((badgeDiv) => {
                const nameInput = badgeDiv.querySelector('.badge-input-name');
                const colorInput = badgeDiv.querySelector('input[type="radio"]:checked');
                if (nameInput && nameInput.value.trim() !== '' && colorInput) {
                    badgesToSubmit.push({
                        name: nameInput.value.trim(),
                        color: colorInput.value
                    });
                }
            });
            formData.append('badges', JSON.stringify(badgesToSubmit));
            formData.append('files_to_delete', JSON.stringify(filesMarkedForDeletion));

            const editPostBasePath = (typeof userRole !== 'undefined' && userRole === 'teacher') ? '../../teacher/community/' : '../../student/community/';
            const editPostUrl = `${editPostBasePath}edit_post.php`;

            fetch(editPostUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(spinner) spinner.classList.add('d-none');
                if (data.status === 'success' || data.status === 'warning') {
                    if(editPostModalInstance) editPostModalInstance.hide();
                    showModal(data.status, data.message);
                     loadPosts(document.querySelector('.listItem.active span') ? document.querySelector('.listItem.active span').textContent.trim() : 'الرئيسية');
                } else {
                    showModal('error', data.message || 'فشل تحديث المنشور.');
                }
            })
            .catch(error => {
                if(spinner) spinner.classList.add('d-none');
                console.error('Error updating post:', error);
                showModal('error', 'حدث خطأ فادح أثناء تحديث المنشور.');
            });
        });
    }

    // --- Delete Post Confirmation and Submission ---
    document.addEventListener('submit', function(event) {
        const deleteForm = event.target.closest('form[id^="deletePostForm_"]');
        if (deleteForm) {
            event.preventDefault();
            const postIdInput = deleteForm.querySelector('input[name="post_id"]');
            if (!postIdInput) return;
            const postId = postIdInput.value;
            const deleteModalElement = document.getElementById(`deletePostModal_${postId}`);
            let deleteModalInstance = null;
            if (deleteModalElement) {
                 deleteModalInstance = bootstrap.Modal.getInstance(deleteModalElement);
                 if (!deleteModalInstance) {
                    deleteModalInstance = new bootstrap.Modal(deleteModalElement);
                 }
            }


            const formData = new FormData();
            formData.append('post_id', postId);

            const deletePostBasePath = (typeof userRole !== 'undefined' && userRole === 'teacher') ? '../../teacher/community/' : '../../student/community/';
            const deletePostUrl = `${deletePostBasePath}delete_post.php`;

            fetch(deletePostUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (deleteModalInstance) deleteModalInstance.hide();

                if (data.state === 'success' || data.success || data.status === 'success') {
                    showModal('success', data.message || 'تم حذف المنشور بنجاح!');
                    loadPosts(document.querySelector('.listItem.active span') ? document.querySelector('.listItem.active span').textContent.trim() : 'الرئيسية');
                } else {
                    showModal('error', data.message || 'فشل حذف المنشور.');
                }
            })
            .catch(error => {
                if (deleteModalInstance) deleteModalInstance.hide();
                console.error('Error deleting post:', error);
                showModal('error', 'حدث خطأ أثناء حذف المنشور.');
            });
        }
    });


    // --- Load Posts By Filter (Initial load and on filter change) ---
    const initialFilter = document.querySelector('.listItem.active span');
    loadPosts(initialFilter ? initialFilter.textContent.trim() : 'الرئيسية');

    document.querySelectorAll('.tabs .listItem button').forEach(button => {
        button.addEventListener('click', function () {
            document.querySelectorAll('.tabs .listItem').forEach(item => item.classList.remove('active'));
            this.closest('.listItem').classList.add('active');
            const filterType = this.querySelector('span').textContent.trim();
            loadPosts(filterType);
        });
    });

    // --- Search Functionality ---
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function () {
            const searchValue = this.value.toLowerCase().trim();
            const posts = document.querySelectorAll('#postContainer .post');

            posts.forEach(post => {
                const title = (post.getAttribute('data-title') || '').toLowerCase();
                const content = (post.getAttribute('data-content') || '').toLowerCase();
                let badgesText = '';
                try {
                    const badgesArray = JSON.parse(post.getAttribute('data-badges') || '[]');
                    badgesText = badgesArray.map(b => b.name).join(' ').toLowerCase();
                } catch (e) {}

                let filesText = '';
                 try {
                    const filesArray = JSON.parse(post.getAttribute('data-original-files') || '[]');
                    filesText = filesArray.join(' ').toLowerCase();
                } catch (e) {}


                if (title.includes(searchValue) || content.includes(searchValue) || badgesText.includes(searchValue) || filesText.includes(searchValue)) {
                    post.style.display = 'block';
                } else {
                    post.style.display = 'none';
                }
            });
        });
    }
    // --- Save/Bookmark Post ---
    document.addEventListener('click', function (event) {
        const button = event.target.closest('.bookmark-post-btn');
        if (button) {
            const postId = button.getAttribute('data-post-id');
            const isBookmarked = button.classList.contains('bookmarked');
            const savePostBasePath = (typeof userRole !== 'undefined' && userRole === 'teacher') ? '../../teacher/community/' : '../../student/community/';
            const savePostUrl = `${savePostBasePath}save_post.php`;

            fetch(savePostUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded'},
                body: `post_id=${encodeURIComponent(postId)}&action=${isBookmarked ? 'unbookmark' : 'bookmark'}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.classList.toggle('bookmarked');
                    const icon = button.querySelector('i');
                    icon.classList.toggle('text-warning', !isBookmarked);
                } else {
                    showModal('error', data.message || 'فشل تحديث حالة الحفظ.');
                }
            })
            .catch(error => {
                console.error('Error updating bookmark:', error);
                showModal('error', 'خطأ في تحديث حالة الحفظ.');
            });
        }
    });

    // --- Open Specific Subject Posts ---
    document.addEventListener('click', function (event) {
        const subjectBtn = event.target.closest('.subjectBtn');
        if (subjectBtn) {
            event.preventDefault();

            const subjectName = subjectBtn.getAttribute('data-subject');
            const teacherId = subjectBtn.getAttribute('data-teacher-id');
            const Content = document.getElementById("modalContent");

            fetch("get_specific_subject_posts.php", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `subjectName=${encodeURIComponent(subjectName)}&teacherId=${encodeURIComponent(teacherId)}`
            })
                .then(response => response.text())
                .then(data => {
                    if (Content) {
                        Content.innerHTML = data;
                        subjectModal.show();
                    }
                })
                .catch(error => {
                    console.error('Error loading subject posts:', error);
                    showModal('error', 'حدث خطأ أثناء تحميل منشورات المادة.');
                });
        }
    });
}); // End of DOMContentLoaded


/**
 * Loads posts based on the selected filter type.
 * @param {string} filterType - The type of filter to apply (e.g., 'الرئيسية', 'منشوراتك').
 */
async function loadPosts(filterType) {
    const postContainer = document.getElementById('postContainer');
    if (!postContainer) {
        console.error('Error: postContainer element not found.');
        return;
    }

    postContainer.innerHTML = `
        <div class="d-flex justify-content-center align-items-center p-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">جاري تحميل المنشورات...</span>
            </div>
            <strong class="ms-2">جاري تحميل المنشورات...</strong>
        </div>`;

    const loadPostsBasePath = (typeof userRole !== 'undefined' && userRole === 'teacher')
                              ? '../../teacher/community/'
                              : '../../student/community/';
    const loadPostsUrl = `${loadPostsBasePath}load_posts.php`;

    try {
        const formData = new FormData();
        formData.append('filterType', filterType);

        const response = await fetch(loadPostsUrl, {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            const errorText = await response.text();
            throw new Error(`HTTP error! Status: ${response.status}. Message: ${errorText || 'No additional error message provided.'}`);
        }

        const data = await response.text();
        postContainer.innerHTML = data;


    } catch (error) {
        console.error('Failed to load posts:', error);
        let userErrorMessage = 'حدث خطأ أثناء تحميل المنشورات. يرجى المحاولة لاحقاً.';
        if (error instanceof TypeError) {
            userErrorMessage = 'خطأ في الشبكة: يرجى التحقق من اتصالك بالإنترنت.';
        } else if (error.message && error.message.startsWith('HTTP error!')) {
            const match = error.message.match(/Message: (.*)/);
            const detail = match && match[1] ? match[1] : 'يرجى المحاولة لاحقاً.';
            userErrorMessage = `خطأ في الخادم: ${detail}`;
        }
        postContainer.innerHTML = `<div class="alert alert-danger text-center">${userErrorMessage}</div>`;
    }
}
