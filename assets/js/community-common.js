/*
function addBadge() {
    const container = document.getElementById('badges-container');
    const newBadge = document.createElement('div');
    newBadge.classList.add('badge-item');

    const badgeIndex = container.childElementCount; // Use element order as a reference for it

    newBadge.innerHTML = `
        <div class="badges w-100">
            <input type="text" class="text-truncate px-3 py-0 badge-none w-100" name="badges[${badgeIndex}][name]" placeholder="اسم الشارة" required>
        </div>
        <div class="row mt-2 mb-3">
            <div class="col d-flex justify-content-start align-items-center gap-1">
                <div class="form-check">
                    <input class="form-check-input greenBadge" type="radio" name="badges[${badgeIndex}][color]" value="green" checked>
                </div>
                <div class="form-check">
                    <input class="form-check-input yellowBadge" type="radio" name="badges[${badgeIndex}][color]" value="yellow">
                </div>
                <div class="form-check">
                    <input class="form-check-input redBadge" type="radio" name="badges[${badgeIndex}][color]" value="red">
                </div>
            </div>
            <button type="button" class="col btn btn-danger btn-sm" onclick="this.parentNode.parentNode.remove()">حذف</button>
        </div>
    `;

    container.appendChild(newBadge);

    // Apply change event to update class directly when radio is selected
    const radios = newBadge.querySelectorAll('input[type="radio"]');
    const badgeInput = newBadge.querySelector(`input[name="badges[${badgeIndex}][name]"]`);

    radios.forEach(radio => {
        radio.addEventListener('change', function () {
            updateBadgeClass(badgeInput, radio.value);
        });
    });

    // Set the default class based on the default checked state
    updateBadgeClass(badgeInput, 'green'); // Default color is green
}

function updateBadgeClass(badgeInput, color) {
    // Remove all badge-related classes
    badgeInput.classList.remove('badge-none', 'badge-green', 'badge-yellow', 'badge-red');

    // Add the appropriate class based on the color
    if (color === 'green') {
        badgeInput.classList.add('badge-green');
    } else if (color === 'yellow') {
        badgeInput.classList.add('badge-yellow');
    } else if (color === 'red') {
        badgeInput.classList.add('badge-red');
    }
}

document.addEventListener("DOMContentLoaded", function () {
    // --- References to common elements ---
    const addPostModalElement = document.getElementById('addPostModal');
    const editPostModalElement = document.getElementById('editPostModal');
    const commonModalElement = document.getElementById("commonModal");
    const subjectModalElement = document.getElementById("subjectModal");
    const messageDiv = document.getElementById("messageDiv");

    // Ensure modal elements exist before creating instances
    let commonModal, addPostModal, editPostModal, subjectModal;
    if (commonModalElement) {
        commonModal = new bootstrap.Modal(commonModalElement);
    } else {
        console.error("Modal element not found!");
        return;
    }
    if (addPostModalElement) {
        addPostModal = new bootstrap.Modal(addPostModalElement);
    } else {
        console.error("Add post modal element not found!");
        return;
    }
    if (editPostModalElement) {
        editPostModal = new bootstrap.Modal(editPostModalElement);
    } else {
        console.error("Edit modal element not found!");
        return;
    }
    if (subjectModalElement) {
        subjectModal = new bootstrap.Modal(subjectModalElement);
    }

    // --- Show messages modal function ---
    function showModal(state, message) {
        let iconClass = 'fa-regular fa-circle-check text-success';
        let interval = 2000;

        if (state === "warning") {
            iconClass = 'fa-solid fa-circle-exclamation text-warning';
            interval = 2000;
        } else if (state !== "success") {
            iconClass = 'fa-regular fa-circle-xmark text-danger';
            interval = 5000;
        }

        messageDiv.innerHTML = `<i class="${iconClass}" style="font-size: 5rem;"></i><h6 class="mt-3">${message}</h6>`;
        commonModal.show();

        setTimeout(() => {
            commonModal.hide();
            messageDiv.innerHTML = '';
        }, interval);
    }

    // Handle form submission with loading indicator and validation
    const form = document.getElementById('addPostForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const loadingSpinner = document.getElementById('loadingSpinner');
            loadingSpinner.classList.remove('d-none');
            const badgeInputs = document.querySelectorAll('input[name^="badges"]');
            let validSubmision = true;

            badgeInputs.forEach(input => {
                if (input.value.trim() === '') {
                    alert('يجب إدخال اسم الشارة.');
                    validSubmision = false;
                }
            });

            if (validSubmision) {
                const addPostBasePath = (userRole == 'teacher') ? '../../teacher/community/' : '../../student/community/';
                const addPostUrl = `${addPostBasePath}add_post.php`;
                fetch(addPostUrl, {
                    method: 'POST',
                    body: new FormData(form)
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status == 'success') {
                            addPostModal.hide();
                            showModal(data.status, data.message);
                            loadPosts('الرئيسية');
                        } else if (data.status == 'warning') {
                            addPostModal.hide();
                            showModal(data.status, data.message);
                            addPostModal.show();
                        }
                    })
                    .catch(error => {
                        console.error('Error adding post:', error);
                        addPostModal.hide();
                        showModal('error', error);
                    })
            }

            loadingSpinner.classList.add('d-none');
            validSubmision = false;
        });
    }

    loadPosts('الرئيسية');

    // -- Handle Saving Post --
    document.addEventListener('click', function (event) {
        const button = event.target.closest('.bookmark-post-btn');
        if (button) {
            const postId = button.getAttribute('data-post-id');
            const isBookmarked = button.classList.contains('bookmarked');
            const savePostBasePath = (userRole === 'teacher') ? '../../teacher/community/' : '../../student/community/';
            const savePostUrl = `${savePostBasePath}save_post.php`;

            fetch(savePostUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `post_id=${encodeURIComponent(postId)}&action=${isBookmarked ? 'unbookmark' : 'bookmark'}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Change Button State
                        button.classList.toggle('bookmarked');
                        button.querySelector('i').classList.toggle('text-warning', !isBookmarked);
                    } else {
                        console.error('Failed to update bookmark status:', data.message);
                        showModal('error', 'حدث خطأ أثناء حفظ المنشور.');
                    }
                })
                .catch(error => {
                    console.error('Error updating bookmark status:', error);
                    showModal('error', 'حدث خطأ أثناء حفظ المنشور.');
                });
        }
    });

    // -- Get Attributes Of Edit Post Button --
    document.addEventListener('click', function (event) {
        const button = event.target.closest('.edit-post-btn');
        if (button) {
            // Clear previous values
            document.getElementById('editPostForm').reset();
            document.getElementById('editExistingFiles').innerHTML = '';
            document.getElementById('editBadgesContainer').innerHTML = '';

            // Get post data
            const postId = button.getAttribute('data-id');
            const title = button.getAttribute('data-title');
            const content = button.getAttribute('data-content');
            const originalFiles = button.getAttribute('data-original-files') || '[]';
            const serverFiles = button.getAttribute('data-server-files') || '[]';
            const badges = button.getAttribute('data-badges') || '[]';

            // Parse files and badges
            let originalFilesArr = [];
            let serverFilesArr = [];
            let originalFilesWithServer = [];
            try {
                originalFilesArr = JSON.parse(originalFiles);
                serverFilesArr = JSON.parse(serverFiles);
                originalFilesWithServer = originalFilesArr.map((file, idx) => ({
                    original: file,
                    server: serverFilesArr[idx] || ''
                }));
            } catch (e) {
                originalFilesWithServer = [];
            }

            // Set form values
            document.getElementById('editPostId').value = postId;
            document.getElementById('editPostTitle').value = title;
            document.getElementById('editPostContent').value = content;
            document.getElementById('hiddenExistingFilesInput').value = JSON.stringify(originalFilesWithServer.map(f => f.original));
            document.getElementById('hiddenExistingServerFiles').value = JSON.stringify(originalFilesWithServer.map(f => f.server));

            // Render existing files
            const editExistingFiles = document.getElementById('editExistingFiles');
            editExistingFiles.innerHTML = '';
            if (originalFilesWithServer.length > 0) {
                originalFilesWithServer.forEach((fileObj, idx) => {
                    const file = fileObj.original;
                    const fileExtension = file.split('.').pop().toLowerCase();
                    let fileIcon = 'fa-file';
                    if (['jpg', 'jpeg', 'png', 'webp'].includes(fileExtension)) fileIcon = 'fa-file-image';
                    else if (['pdf'].includes(fileExtension)) fileIcon = 'fa-file-pdf';
                    else if (['doc', 'docx', 'txt'].includes(fileExtension)) fileIcon = 'fa-file-word';
                    else if (['xlsx', 'csv'].includes(fileExtension)) fileIcon = 'fa-file-excel';
                    else if (['mp4', 'avi', 'mov', 'mkv', 'webm'].includes(fileExtension)) fileIcon = 'fa-file-video';
                    else if (['pptx', 'zip', 'rar'].includes(fileExtension)) fileIcon = 'fa-file-archive';

                    const fileItem = document.createElement('div');
                    fileItem.className = 'd-flex justify-content-between align-items-center mb-2 bg-white p-2 rounded border-1';
                    fileItem.innerHTML = `
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid ${fileIcon} fs-4"></i>
                            <span class="text-truncate">${file}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" data-file-idx="${idx}" title="حذف الملف">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    `;
                    // Delete file handler
                    fileItem.querySelector('button').addEventListener('click', function () {
                        // Remove from UI
                        fileItem.remove();
                        // Remove from arrays
                        originalFilesWithServer.splice(idx, 1);
                        // Update hidden inputs
                        document.getElementById('hiddenExistingFilesInput').value = JSON.stringify(originalFilesWithServer.map(f => f.original));
                        document.getElementById('hiddenExistingServerFiles').value = JSON.stringify(originalFilesWithServer.map(f => f.server));
                    });
                    editExistingFiles.appendChild(fileItem);
                });
            }

            // Render badges
            const editBadgesContainer = document.getElementById('editBadgesContainer');
            editBadgesContainer.innerHTML = '';
            let badgesArr = [];
            try {
                badgesArr = JSON.parse(badges);
            } catch (e) {
                badgesArr = [];
            }
            badgesArr.forEach((badge, idx) => {
                // Support both old and new badge structure
                let badgeName = badge.name && typeof badge.name === 'object' ? badge.name.name : badge.name || '';
                let badgeColor = badge.name && typeof badge.name === 'object' ? badge.name.color : badge.color || 'green';

                const badgeDiv = document.createElement('div');
                badgeDiv.className = 'badge-item';
                badgeDiv.innerHTML = `
                    <div class="badges w-100">
                        <input type="text" class="text-truncate px-3 py-0 badge-${badgeColor} w-100" name="badges[${idx}][name]" value="${badgeName}" placeholder="اسم الشارة" required>
                    </div>
                    <div class="row mt-2 mb-3">
                        <div class="col d-flex justify-content-start align-items-center gap-1">
                            <div class="form-check">
                                <input class="form-check-input greenBadge" type="radio" name="badges[${idx}][color]" value="green" ${badgeColor === 'green' ? 'checked' : ''}>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input yellowBadge" type="radio" name="badges[${idx}][color]" value="yellow" ${badgeColor === 'yellow' ? 'checked' : ''}>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input redBadge" type="radio" name="badges[${idx}][color]" value="red" ${badgeColor === 'red' ? 'checked' : ''}>
                            </div>
                        </div>
                        <button type="button" class="col btn btn-danger btn-sm">حذف</button>
                    </div>
                `;
                // Color radio change event
                badgeDiv.querySelectorAll('input[type="radio"]').forEach(radio => {
                    radio.addEventListener('change', function () {
                        updateBadgeClass(badgeDiv.querySelector('input[type="text"]'), radio.value);
                    });
                });
                // Set initial class
                updateBadgeClass(badgeDiv.querySelector('input[type="text"]'), badgeColor);
                // Delete badge
                badgeDiv.querySelector('button').addEventListener('click', function () {
                    badgeDiv.remove();
                });
                editBadgesContainer.appendChild(badgeDiv);
            });
        }
    });

    // --- Edit Post Form Submission ---
    const editPostForm = document.getElementById('editPostForm');
    if (editPostForm) {
        editPostForm.addEventListener('submit', function (event) {
            event.preventDefault();

            // Show loading spinner
            const spinner = document.getElementById('editPostLoadingSpinner');
            spinner && spinner.classList.remove('d-none');

            // Gather form data
            const formData = new FormData(editPostForm);

            // Add badges
            const badges = [];
            document.querySelectorAll('#editBadgesContainer .badge-item').forEach((badgeDiv, idx) => {
                const nameInput = badgeDiv.querySelector('input[type="text"]');
                const colorInput = badgeDiv.querySelector('input[type="radio"]:checked');
                if (nameInput && colorInput) {
                    badges.push({
                        name: nameInput.value,
                        color: colorInput.value
                    });
                }
            });
            formData.delete('badges[]');
            formData.append('badges', JSON.stringify(badges));

            // Add files to delete
            let filesToDelete = [];
            try {
                const originalFiles = JSON.parse(document.getElementById('hiddenExistingFilesInput').defaultValue || '[]');
                const currentFiles = JSON.parse(document.getElementById('hiddenExistingFilesInput').value || '[]');
                filesToDelete = originalFiles.filter(f => !currentFiles.includes(f));
            } catch (e) {
                filesToDelete = [];
            }
            formData.append('files_to_delete', JSON.stringify(filesToDelete));

            // Send request
            const editPostBasePath = (userRole === 'teacher') ? '../../teacher/community/' : '../../student/community/';
            const editPostUrl = `${editPostBasePath}edit_post.php`;

            fetch(editPostUrl, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    spinner && spinner.classList.add('d-none');
                    if (data.status === 'success') {
                        editPostModal.hide();
                        showModal('success', data.message || 'تم تحديث المنشور');
                        loadPosts('الرئيسية');
                    } else if (data.status === 'warning') {
                        editPostModal.hide();
                        showModal('warning', data.message || 'تم التحديث مع تحذير');
                        loadPosts('الرئيسية');
                    } else {
                        editPostModal.hide();
                        showModal('error', data.message || 'فشل تحديث المنشور');
                        editPostModal.show();
                    }
                })
                .catch(error => {
                    spinner && spinner.classList.add('d-none');
                    console.error('Error updating post:', error);
                    editPostModal.hide();
                    showModal('error', 'حدث خطأ أثناء تحديث المنشور');
                    editPostModal.show();
                });
        });
    }

    // --- Delete Post ---
    document.addEventListener('submit', function (event) {
        // Match any form with id starting with deletePostForm_
        const deletePostForm = event.target.closest('form[id^="deletePostForm_"]');
        if (deletePostForm) {
            event.preventDefault();

            // Get the post_id from the hidden input
            const postIdInput = deletePostForm.querySelector('input[name="post_id"]');
            if (!postIdInput) {
                showModal('error', 'لم يتم العثور على معرف المنشور.');
                return;
            }
            const postId = postIdInput.value;

            const formData = new FormData();
            formData.append('post_id', postId);

            const deletePostBasePath = (userRole === 'teacher') ? '../../teacher/community/' : '../../student/community/';
            const deletePostUrl = `${deletePostBasePath}delete_post.php`;

            fetch(deletePostUrl, {
                method: 'POST',
                body: formData,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.state === 'success' || data.success) {
                        showModal('success', 'تم حذف المنشور بنجاح!');
                        loadPosts('الرئيسية');
                    } else {
                        showModal(data.state || 'error', 'فشل حذف المنشور: ' + (data.message || ''));
                    }
                })
                .catch((error) => {
                    console.error('Error deleting post:', error);
                    showModal('error', 'حدث خطأ أثناء حذف المنشور');
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
});

const searchInput = document.getElementById('searchInput');
if (searchInput) {
    searchInput.addEventListener('keyup', function () {
        const searchValue = this.value.toLowerCase();
        const posts = document.querySelectorAll('.post');

        posts.forEach(post => {
            const title = post.getAttribute('data-title').toLowerCase();
            const content = post.getAttribute('data-content').toLowerCase();
            const badges = JSON.parse(post.getAttribute('data-badges')).map(b => b.name).join(' ').toLowerCase();
            const uploadedFiles = JSON.parse(post.getAttribute('data-uploaded-files')).join(' ').toLowerCase();

            if (title.includes(searchValue) || content.includes(searchValue) || badges.includes(searchValue) || uploadedFiles.includes(searchValue)) {
                post.style.display = 'block';
            } else {
                post.style.display = 'none';
            }
        });
    });
}

// -- Load Posts By Filter --
async function loadPosts(filterType) {
    const postContainer = document.getElementById('postContainer');
    postContainer.innerHTML = '';

    const loadPostsBasePath = (userRole === 'teacher') ? '../../teacher/community/' : '../../student/community/';
    const loadPostsUrl = `${loadPostsBasePath}load_posts.php`;
    try {
        const response = await fetch(loadPostsUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `filterType=${encodeURIComponent(filterType)}`
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.text();

        postContainer.innerHTML = data;
    } catch (error) {
        console.error('Error loading posts:', error);
        showModal('error', 'حدث خطأ أثناء تحميل المنشورات يرجى المحاولة لاحقاً.');
    };
}

async function loadPosts(filterType) {
    const postContainer = document.getElementById('postContainer');
    if (!postContainer) {
        console.error('Error: postContainer element not found.');
        showModal('error', 'عنصر عرض المنشورات غير موجود. يرجى إبلاغ الدعم.');
        return; // الخروج من الوظيفة إذا لم يتم العثور على العنصر
    }

    postContainer.innerHTML = '<div class="spinner-border spinner-border-sm spinner-border-default" role="status"><span class="visually-hidden">جاري تحمل المنشورات...</span></div>';

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
        
        postContainer.innerHTML = '';
        postContainer.innerHTML = data;

    } catch (error) {
        console.error('Failed to load posts:', error);
        let userErrorMessage = 'حدث خطأ أثناء تحميل المنشورات يرجى المحاولة لاحقاً.';

        if (error instanceof TypeError) {
            userErrorMessage = 'خطأ في الشبكة: يرجى التحقق من اتصالك بالإنترنت.';
        } else if (error.message.startsWith('HTTP error!')) {
            userErrorMessage = `خطأ في الخادم (${error.message.split(' ')[3]}): ${error.message.split('Message: ')[1] || 'يرجى المحاولة لاحقاً.'}`;
        }
        
        showModal('error', userErrorMessage);
    }
}

// -- Get Filter Type Of Active Button
document.querySelectorAll('.listItem button').forEach(button => {
    button.addEventListener('click', function () {
        document.querySelectorAll('.listItem').forEach(item => {
            item.classList.remove('active');
        });

        this.closest('.listItem').classList.add('active');

        let filterType = this.querySelector('span').textContent;

        loadPosts(filterType);
    });
});
*/


// --- New Javascript Code [Working But Need More Review] ---
function addBadge(context = 'add') {
    const containerId = context === 'edit' ? 'editBadgesContainer' : 'badges-container';
    const container = document.getElementById(containerId);
    if (!container) {
        console.error(`Badge container '${containerId}' not found.`);
        return;
    }

    const badgeItemCount = container.querySelectorAll('.badge-item').length;
    const newBadgeDiv = document.createElement('div');
    newBadgeDiv.className = 'badge-item mb-3'; // mb-3 for spacing

    // Using unique names for inputs to avoid conflicts if add/edit modals are complex
    // The backend will receive a JSON string, so exact input names are less critical for FormData collection via JS.
    newBadgeDiv.innerHTML = `
        <div class="input-group input-group-sm mb-1">
            <input type="text" class="form-control badge-input-name" placeholder="اسم الشارة" required>
            <button type="button" class="btn btn-outline-danger remove-badge-btn" title="حذف الشارة">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <div class="badge-color-options row mt-2 mb-3">
            <div class="col form-check">
                <input class="form-check-input greenBadge" type="radio" name="badge_color_${context}_${badgeItemCount}" value="green" checked>
            </div>
            <div class="col form-check">
                <input class="form-check-input yellowBadge" type="radio" name="badge_color_${context}_${badgeItemCount}" value="yellow">
            </div>
            <div class="col form-check">
                <input class="form-check-input redBadge" type="radio" name="badge_color_${context}_${badgeItemCount}" value="red">
            </div>
        </div>
    `;

    container.appendChild(newBadgeDiv);

    const badgeNameInput = newBadgeDiv.querySelector('.badge-input-name');
    const colorRadios = newBadgeDiv.querySelectorAll(`input[name="badge_color_${context}_${badgeItemCount}"]`);

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
    // Add border and text color for better visual feedback
    switch (color) {
        case 'green':
            badgeNameInputElement.classList.add('border-success');
            // badgeNameInputElement.classList.add('text-success'); // Optional: if you want text color too
            break;
        case 'yellow':
            badgeNameInputElement.classList.add('border-warning');
            // badgeNameInputElement.classList.add('text-warning');
            break;
        case 'red':
            badgeNameInputElement.classList.add('border-danger');
            // badgeNameInputElement.classList.add('text-danger');
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
            alert(message); // Fallback if modal elements are not found
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
            messageDiv.innerHTML = ''; // Clear content after hiding
        }, autoHideTimeout);
    }

    // --- Add Post Form Submission ---
    const addPostForm = document.getElementById('addPostForm');
    if (addPostForm) {
        addPostForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const loadingSpinner = document.getElementById('loadingSpinner');
            loadingSpinner.classList.remove('d-none');

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
                } else if (nameInput && nameInput.value.trim() !== '') {
                     // If name is there but color somehow not, default or skip
                    console.warn("Badge name found but color not selected, skipping or defaulting:", nameInput.value);
                }
            });
            formData.append('badges', JSON.stringify(badges)); // Add badges as JSON

            // Clear individual badge fields from FormData if they were added by name attribute, to avoid conflict
            // This step might be redundant if inputs don't have 'name' attribute that FormData picks up.
            // For safety, ensure badge inputs used for collection (like .badge-input-name) don't have conflicting 'name' attributes.

            const addPostBasePath = (typeof userRole !== 'undefined' && userRole === 'teacher') ? '../../teacher/community/' : '../../student/community/';
            const addPostUrl = `${addPostBasePath}add_post.php`;

            fetch(addPostUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                loadingSpinner.classList.add('d-none');
                if (data.status === 'success' || data.status === 'warning') {
                    if(addPostModalInstance) addPostModalInstance.hide();
                    addPostForm.reset(); // Reset form
                    document.getElementById('badges-container').innerHTML = ''; // Clear dynamically added badges
                    showModal(data.status, data.message);
                    loadPosts(document.querySelector('.listItem.active span') ? document.querySelector('.listItem.active span').textContent.trim() : 'الرئيسية');
                } else { // error
                    showModal('error', data.message || 'فشل إضافة المنشور.');
                }
            })
            .catch(error => {
                loadingSpinner.classList.add('d-none');
                console.error('Error adding post:', error);
                showModal('error', 'حدث خطأ فادح أثناء إضافة المنشور.');
            });
        });
    }


    // --- Populate Edit Post Modal & Handle File Deletion Marking ---
    let filesMarkedForDeletion = []; // Holds original names of files to be deleted

    document.addEventListener('click', function (event) {
        const button = event.target.closest('.edit-post-btn');
        if (button) {
            filesMarkedForDeletion = []; // Reset for this modal instance
            const editForm = document.getElementById('editPostForm');
            if(editForm) editForm.reset(); // Reset form fields
            
            document.getElementById('editExistingFiles').innerHTML = '<p class="text-muted small">جاري تحميل الملفات...</p>';
            document.getElementById('editBadgesContainer').innerHTML = ''; // Clear badges

            const postId = button.getAttribute('data-id');
            const title = button.getAttribute('data-title');
            const content = button.getAttribute('data-content');
            const originalFilesStr = button.getAttribute('data-original-files') || '[]';
            const serverFilesStr = button.getAttribute('data-server-files') || '[]';
            const badgesStr = button.getAttribute('data-badges') || '[]';

            document.getElementById('editPostId').value = postId;
            document.getElementById('editPostTitle').value = title;
            document.getElementById('editPostContent').value = content;

            let originalFilesArr = [];
            let serverFilesArr = [];
            try {
                originalFilesArr = JSON.parse(originalFilesStr);
                serverFilesArr = JSON.parse(serverFilesStr);
            } catch (e) { console.error("Error parsing file data for edit:", e); }

            const editExistingFilesContainer = document.getElementById('editExistingFiles');
            editExistingFilesContainer.innerHTML = ''; // Clear previous
            if (originalFilesArr.length > 0) {
                originalFilesArr.forEach((originalName, idx) => {
                    const serverName = serverFilesArr[idx] || originalName; // Fallback
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
                        // Visually mark as deleted or remove
                        this.closest('.d-flex').classList.add('text-decoration-line-through', 'opacity-50');
                        this.disabled = true; // Disable button after marking
                         // fileItemDiv.remove(); // Or simply remove from UI
                    });
                    editExistingFilesContainer.appendChild(fileItemDiv);
                });
            } else {
                editExistingFilesContainer.innerHTML = '<p class="text-muted small">لا توجد ملفات مرفقة حالياً.</p>';
            }

            // Render badges for edit
            const editBadgesContainer = document.getElementById('editBadgesContainer');
            editBadgesContainer.innerHTML = ''; // Clear previous
            try {
                const badgesArr = JSON.parse(badgesStr);
                if (badgesArr && badgesArr.length > 0) {
                    badgesArr.forEach((badge, idx_badge) => {
                        const badgeName = badge.name || '';
                        const badgeColor = badge.color || 'green';

                        const badgeItemCount = editBadgesContainer.querySelectorAll('.badge-item').length;
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
                                    <input class="form-check-input" type="radio" name="badge_color_edit_${idx_badge}" value="green" ${badgeColor === 'green' ? 'checked' : ''}>
                                    <label class="form-check-label badge bg-success text-white p-1">أخضر</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="badge_color_edit_${idx_badge}" value="yellow" ${badgeColor === 'yellow' ? 'checked' : ''}>
                                    <label class="form-check-label badge bg-warning text-dark p-1">أصفر</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="badge_color_edit_${idx_badge}" value="red" ${badgeColor === 'red' ? 'checked' : ''}>
                                    <label class="form-check-label badge bg-danger text-white p-1">أحمر</label>
                                </div>
                            </div>
                        `;
                        editBadgesContainer.appendChild(newBadgeDiv);

                        const currentBadgeNameInput = newBadgeDiv.querySelector('.badge-input-name');
                        const currentColorRadios = newBadgeDiv.querySelectorAll(`input[name="badge_color_edit_${idx_badge}"]`);
                        updateBadgeClass(currentBadgeNameInput, badgeColor);
                        currentColorRadios.forEach(radio => {
                            radio.addEventListener('change', function() { updateBadgeClass(currentBadgeNameInput, this.value); });
                        });
                        newBadgeDiv.querySelector('.remove-badge-btn').addEventListener('click', function() { newBadgeDiv.remove(); });
                    });
                }
            } catch (e) { console.error("Error parsing badges data for edit:", e); }
        }
    });

    // --- Edit Post Form Submission ---
    const editPostForm = document.getElementById('editPostForm');
    if (editPostForm) {
        editPostForm.addEventListener('submit', function (event) {
            event.preventDefault();
            const spinner = document.getElementById('editPostLoadingSpinner');
            if(spinner) spinner.classList.remove('d-none');

            const formData = new FormData(editPostForm); // Contains title, content, post_id, new_uploaded_files[]

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
                } else { // error
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
    // This uses event delegation on the document for dynamically created delete forms/modals
    document.addEventListener('submit', function(event) {
        const deleteForm = event.target.closest('form[id^="deletePostForm_"]');
        if (deleteForm) {
            event.preventDefault();
            const postId = deleteForm.querySelector('input[name="post_id"]').value;
            const deleteModalElement = document.getElementById(`deletePostModal_${postId}`);
            let deleteModalInstance = null;
            if (deleteModalElement) {
                 deleteModalInstance = bootstrap.Modal.getInstance(deleteModalElement);
                 if (!deleteModalInstance) { // If not already instanced
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
                if (deleteModalInstance) deleteModalInstance.hide(); // Hide the specific delete modal

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
            const posts = document.querySelectorAll('#postContainer .post'); // Target posts within the container

            posts.forEach(post => {
                const title = (post.getAttribute('data-title') || '').toLowerCase();
                const content = (post.getAttribute('data-content') || '').toLowerCase();
                let badgesText = '';
                try {
                    const badgesArray = JSON.parse(post.getAttribute('data-badges') || '[]');
                    badgesText = badgesArray.map(b => b.name).join(' ').toLowerCase();
                } catch (e) {/*ignore parse error for search*/}

                let filesText = '';
                 try {
                    const filesArray = JSON.parse(post.getAttribute('data-original-files') || '[]');
                    filesText = filesArray.join(' ').toLowerCase();
                } catch (e) {/*ignore parse error for search*/}


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
                    icon.classList.toggle('text-warning', !isBookmarked); // Add text-warning if now bookmarked
                    // Optionally, show a small success message or just visual feedback
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
        // Consider calling showModal here if it's available globally
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
        postContainer.innerHTML = data; // Populate with fetched posts
        if (data.trim() === '' || data.includes("لا توجد منشورات")) { // Check if the response indicates no posts
             // The PHP script should ideally return a clear message when no posts are found
        }

    } catch (error) {
        console.error('Failed to load posts:', error);
        let userErrorMessage = 'حدث خطأ أثناء تحميل المنشورات. يرجى المحاولة لاحقاً.';
        if (error instanceof TypeError) { // Network error
            userErrorMessage = 'خطأ في الشبكة: يرجى التحقق من اتصالك بالإنترنت.';
        } else if (error.message && error.message.startsWith('HTTP error!')) {
            // Try to extract a more specific message if available from the server error
            const match = error.message.match(/Message: (.*)/);
            const detail = match && match[1] ? match[1] : 'يرجى المحاولة لاحقاً.';
            userErrorMessage = `خطأ في الخادم: ${detail}`;
        }
        postContainer.innerHTML = `<div class="alert alert-danger text-center">${userErrorMessage}</div>`;
        // showModal('error', userErrorMessage); // Assuming showModal is globally accessible and appropriate here
    }
}
