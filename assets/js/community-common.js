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
    // Handle form submission with loading indicator and validation
    const form = document.getElementById('addPostForm');
    if (form) {
        form.addEventListener('submit', function (e) {
            const loadingSpinner = document.getElementById('loadingSpinner');
            const badgeInputs = document.querySelectorAll('input[name^="badges"]');
            let valid = true;

            badgeInputs.forEach(input => {
                if (input.value.trim() === '') {
                    alert('يجب إدخال اسم الشارة.');
                    valid = false;
                }
            });

            if (!valid) {
                e.preventDefault();
            } else {
                loadingSpinner.classList.remove('d-none');
            }
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
                        commonModal.show();
                        showModal('error', 'حدث خطأ أثناء حفظ المنشور.');
                        setTimeout(() => {
                            commonModal.hide();
                            messageDiv.innerHTML = '';
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Error updating bookmark status:', error);
                    commonModal.show();
                    showModal('error', 'حدث خطأ أثناء حفظ المنشور.');
                    setTimeout(() => {
                        commonModal.hide();
                        messageDiv.innerHTML = '';
                    }, 2000);
                });
        }
    });

    // --- References to common elements ---
    const editPostModalElement = document.getElementById('editPostModal');
    const commonModalElement = document.getElementById("commonModal");
    const messageDiv = document.getElementById("messageDiv");

    // Ensure modal elements exist before creating instances
    let commonModal, editPostModal;
    if (commonModalElement) {
        commonModal = new bootstrap.Modal(commonModalElement);
    } else {
        console.error("Modal element not found!");
        return;
    }
    if (editPostModalElement) {
        editPostModal = new bootstrap.Modal(editPostModalElement);
    } else {
        console.error("Edit modal element not found!");
        return;
    }

    // -- Get Attributes Of Edit Post Button --
    document.addEventListener('click', function (event) {
        const button = event.target.closest('.edit-post-btn');
        if (button) {
            const postId = button.getAttribute('data-id');
            const title = button.getAttribute('data-title');
            const content = button.getAttribute('data-content');

            document.getElementById('editPostId').value = postId;
            document.getElementById('editPostTitle').value = title;
            document.getElementById('editPostContent').value = content;
        }
    });

    // --- Edit Post ---
    const editPostModalForm = document.getElementById('editPostModal');
    if (editPostModalForm) {
        editPostModalForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const postId = document.getElementById('editPostId').value;
            const title = document.getElementById('editPostTitle').value;
            const content = document.getElementById('editPostContent').value;

            const formData = new FormData();
            formData.append('post_id', postId);
            formData.append('title', title);
            formData.append('content', content);

            const editPostBasePath = (userRole === 'teacher') ? '../../teacher/community/' : '../../student/community/';
            const editPostUrl = `${editPostBasePath}edit_post.php`;

            fetch(editPostUrl, {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        editPostModal.hide();
                        commonModal.show();
                        showModal('success', 'تم تحديث المنشور بنجاح!');
                        loadPosts('الرئيسية');
                        setTimeout(() => {
                            commonModal.hide();
                            messageDiv.innerHTML = '';
                        }, 2000);
                    } else {
                        showModal('error', 'فشل تحديث المنشور: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error updating post:', error);
                    editPostModal.hide();
                    commonModal.show();
                    showModal('error', 'حدث خطأ أثناء تحديث المنشور');
                    setTimeout(() => {
                        commonModal.hide();
                        messageDiv.innerHTML = '';
                    }, 2000);
                });
        });
    }

    // --- Show modal function ---
    function showModal(state, message) {
        let iconClass = 'fa-regular fa-circle-check text-success';
        if (state === "warning") iconClass = 'fa-solid fa-circle-exclamation text-warning';
        else if (state !== "success") iconClass = 'fa-regular fa-circle-xmark text-danger';

        messageDiv.innerHTML = `<i class="${iconClass}" style="font-size: 5rem;"></i><h6 class="mt-3">${message}</h6>`;
    }

    // --- Delete Post ---
    document.addEventListener('submit', function (event) {
        const deletePostForm = event.target.closest('#deletePostForm');
        if (deletePostForm) {
            event.preventDefault();

            const postId = deletePostForm.querySelector('#deletePostId').value;

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
                    if (data.state === 'success') {
                        loadPosts('الرئيسية');
                        commonModal.hide();
                    } else {
                        showModal(data.state, 'فشل حذف المنشور: ' + data.message);
                    }
                })
                .catch((error) => {
                    console.error('Error deleting post:', error);
                    commonModal.show();
                    showModal('error', 'حدث خطأ أثناء حذف المنشور');
                    setTimeout(() => {
                        commonModal.hide();
                        messageDiv.innerHTML = '';
                    }, 2000);
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
function loadPosts(filterType) {
    const postContainer = document.getElementById('postContainer');
    postContainer.innerHTML = '';

    const loadPostsBasePath = (userRole === 'teacher') ? '../../teacher/community/' : '../../student/community/';
    const loadPostsUrl = `${loadPostsBasePath}load_posts.php`;

    fetch(loadPostsUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `filterType=${encodeURIComponent(filterType)}`
    })
        .then(response => response.text())
        .then(data => {
            postContainer.innerHTML = data;
        })
        .catch(error => {
            console.error('Error loading posts:', error);
            commonModal.show();
            showModal('error', 'حدث خطأ أثناء تحميل المنشورات يرجى المحاولة لاحقاً.');
            setTimeout(() => {
                commonModal.hide();
                messageDiv.innerHTML = '';
            }, 2000);
        });
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