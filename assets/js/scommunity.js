function addBadge() {
    const container = document.getElementById('badges-container');
    const newBadge = document.createElement('div');
    newBadge.classList.add('badge-item');

    const badgeIndex = container.childElementCount; // استخدام عدد العناصر كمؤشر فريد لكل شارة

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
    const form = document.getElementById('add-post-form');
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
});

document.getElementById('searchInput').addEventListener('keyup', function () {
    const searchValue = this.value.toLowerCase();
    const posts = document.querySelectorAll('.post');

    posts.forEach(post => {
        const title = post.getAttribute('data-title').toLowerCase();
        const content = post.getAttribute('data-content').toLowerCase();
        const badges = JSON.parse(post.getAttribute('data-badges')).map(b => b.name).join(' ').toLowerCase(); // تعديل حسب هيكل الشارات
        const uploadedFiles = JSON.parse(post.getAttribute('data-uploaded-files')).join(' ').toLowerCase();

        if (title.includes(searchValue) || content.includes(searchValue) || badges.includes(searchValue) || uploadedFiles.includes(searchValue)) {
            post.style.display = 'block'; // إظهار المنشور
        } else {
            post.style.display = 'none'; // إخفاء المنشور
        }
    });
});

document.addEventListener('DOMContentLoaded', () => {
    let postIdToDelete = null;
    let postIdToEdit = null;

    // حذف المنشور
    document.addEventListener('click', (event) => {
        if (event.target.closest('.delete-post-btn')) {
            postIdToDelete = event.target.closest('.delete-post-btn').dataset.deletePost;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            deleteModal.show();
        }
    });

    const deleteBtn = document.getElementById('deleteBtn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', () => {
            if (postIdToDelete) {
                sendAjaxRequest('student_delete_post.php', 'POST', { post_id: postIdToDelete }, handleDeleteResponse);
                const deleteModal = bootstrap.Modal.getInstance(document.getElementById('deleteConfirmModal'));
                deleteModal.hide();
            }
        });
    }

    // تعديل المنشور
    document.addEventListener('click', (event) => {
        if (event.target.closest('.edit-post-btn')) {
            postIdToEdit = event.target.closest('.edit-post-btn').dataset.editPost;

            const postElement = event.target.closest('.post');
            const title = postElement.dataset.title;
            const content = postElement.dataset.content;
            const badges = JSON.parse(postElement.dataset.badges || '[]');
            const uploadedFiles = JSON.parse(postElement.dataset.uploadedFiles || '[]');

            populateEditModal(postIdToEdit, title, content, badges, uploadedFiles);
        }
    });

    const saveEditBtn = document.getElementById("saveEditBtn");
    if (saveEditBtn) {
        saveEditBtn.addEventListener('click', () => {
            if (postIdToEdit) {
                const editForm = document.getElementById('editPostForm');
                const updatedData = {
                    post_id: postIdToEdit,
                    title: editForm.querySelector('#editTitle').value,
                    content: editForm.querySelector('#editContent').value,
                    badges: Array.from(editForm.querySelectorAll('.edit-badge')).map(badge => badge.value),
                    uploaded_files: [] // Add logic if needed to handle file uploads
                };

                sendAjaxRequest('student_edit_post.php', 'POST', updatedData, handleEditResponse);
                const editModal = bootstrap.Modal.getInstance(document.getElementById('editPostModal'));
                editModal.hide();
            }
        });
    }

    // استجابة الحذف
    function handleDeleteResponse(response) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${response.status === 'success' ? 'alert-success' : 'alert-danger'}`;
        alertDiv.textContent = response.message;
        document.getElementById('deletePostContainer').appendChild(alertDiv);

        if (response.status === 'success') {
            setTimeout(() => location.reload(), 2000);
        }
    }

    // استجابة التعديل
    function handleEditResponse(response) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert ${response.status === 'success' ? 'alert-success' : 'alert-danger'}`;
        alertDiv.textContent = response.message;
        document.getElementById('editPostContainer').appendChild(alertDiv);

        if (response.status === 'success') {
            setTimeout(() => location.reload(), 2000);
        }
    }

    // ملء بيانات التعديل في النموذج
    function populateEditModal(postId, title, content, badges, uploadedFiles) {
        const editForm = document.getElementById('editPostForm');
        editForm.querySelector('#editTitle').value = title;
        editForm.querySelector('#editContent').value = content;

        const badgeContainer = editForm.querySelector('#editBadges');
        badgeContainer.innerHTML = ''; // مسح المحتوى الحالي
        badges.forEach(badge => {
            const badgeInput = document.createElement('input');
            badgeInput.type = 'text';
            badgeInput.className = 'form-control edit-badge';
            badgeInput.value = badge.name;
            badgeContainer.appendChild(badgeInput);
        });

        // عرض الملفات المحملة إن لزم الأمر
        const filesContainer = editForm.querySelector('#editUploadedFiles');
        filesContainer.innerHTML = '';
        uploadedFiles.forEach(file => {
            const fileItem = document.createElement('div');
            fileItem.textContent = file;
            filesContainer.appendChild(fileItem);
        });

        const editModal = new bootstrap.Modal(document.getElementById('editPostModal'));
        editModal.show();
    }

    // دالة إرسال الطلب باستخدام Fetch
    async function sendAjaxRequest(url, method, data, successCallback) {
        try {
            const response = await fetch(url, {
                method: method || 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error('خطأ في الاستجابة من الخادم.');
            }

            const responseData = await response.json();

            if (responseData && typeof responseData === 'object') {
                successCallback(responseData);
            } else {
                throw new Error('الاستجابة ليست بتنسيق JSON صالح.');
            }
        } catch (error) {
            handleError(error);
        }
    }

    // التعامل مع الأخطاء
    function handleError(error) {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger';
        alertDiv.textContent = error.message || 'حدث خطأ أثناء معالجة الطلب.';
        document.body.appendChild(alertDiv);
    }
});

document.addEventListener('DOMContentLoaded', function () {
    // تحميل المنشورات الافتراضية عند تحميل الصفحة
    loadPosts('الرئيسية');

    // إضافة Event Listener على زر حفظ المنشور
    document.addEventListener('click', function (event) {
        const button = event.target.closest('.bookmark-post-btn');
        if (button) {
            const postId = button.getAttribute('data-post-id');
            const isBookmarked = button.classList.contains('bookmarked');

            // إرسال طلب AJAX لحفظ أو إزالة المنشور من المفضلة
            fetch('student_save_post.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `post_id=${encodeURIComponent(postId)}&action=${isBookmarked ? 'unbookmark' : 'bookmark'}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // تغيير حالة الزر بناءً على الرد من الخادم
                        button.classList.toggle('bookmarked');
                        button.querySelector('i').classList.toggle('text-warning', !isBookmarked);
                    } else {
                        console.error('Failed to update bookmark status:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error updating bookmark status:', error);
                });
        }
    });
});

// دالة تحميل المنشورات بناءً على نوع الفلتر
function loadPosts(filterType) {
    const postContainer = document.getElementById('postContainer');
    postContainer.innerHTML = ''; // إفراغ المحتويات السابقة

    fetch('student_load_posts.php', {
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
        });
}

document.querySelectorAll('.listItem button').forEach(button => {
    button.addEventListener('click', function () {
        // إزالة class active من جميع الأزرار
        document.querySelectorAll('.listItem').forEach(item => {
            item.classList.remove('active');
        });

        // إضافة class active للزر الذي تم الضغط عليه
        this.closest('.listItem').classList.add('active');

        // جلب نوع الفلتر بناءً على النص
        let filterType = this.querySelector('span').textContent;

        // إرسال طلب AJAX بناءً على الفلتر المختار
        loadPosts(filterType);
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const exampleModal = document.getElementById('exampleModal');

    exampleModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // الزر الذي تم النقر عليه
        const subject = button.getAttribute('data-subject'); // الحصول على الموضوع
        const teacherId = button.getAttribute('data-teacher-id'); // الحصول على معرف المعلم

        // هنا يمكنك إضافة طلب AJAX لجلب المنشورات
        fetch('get_posts.php?subject=' + encodeURIComponent(subject) + '&teacher_id=' + teacherId)
            .then(response => response.text())
            .then(data => {
                document.getElementById('modal-content').innerHTML = data; // تحديث محتوى المودال
            })
            .catch(error => console.error('Error:', error));
    });
});

document.addEventListener('DOMContentLoaded', function () {
    // إضافة Event Listener على زر التعديل
    document.addEventListener('click', function (event) {
        const button = event.target.closest('.edit-post-btn');
        if (button) {
            // جلب القيم المخزنة في data-attributes
            const postId = button.getAttribute('data-id');
            const title = button.getAttribute('data-title');
            const content = button.getAttribute('data-content');

            // تعبئة الحقول بالقيم المستخرجة من data-attributes
            document.getElementById('editPostId').value = postId;
            document.getElementById('editPostTitle').value = title;
            document.getElementById('editPostContent').value = content;

            // التأكد من أن القيم تم تحميلها بشكل صحيح في الكونسول
            console.log('Post ID:', postId);
            console.log('Title:', title);
            console.log('Content:', content);
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    // إضافة Event Listener على زر حفظ التعديلات في المودال
    document.getElementById('editPostModal').addEventListener('submit', function (event) {
        event.preventDefault(); // منع إرسال النموذج بشكل تقليدي

        // جلب القيم من الحقول
        const postId = document.getElementById('editPostId').value;
        const title = document.getElementById('editPostTitle').value;
        const content = document.getElementById('editPostContent').value;

        // إرسال البيانات عبر AJAX
        const formData = new FormData();
        formData.append('post_id', postId);
        formData.append('title', title);
        formData.append('content', content);

        // تنفيذ طلب AJAX
        fetch('student_edit_post.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // تحديث الصفحة أو إغلاق المودال بناءً على الرد
                    alert('تم تحديث المنشور بنجاح!');
                    // يمكنك هنا إعادة تحميل المنشورات أو تحديث المحتوى
                    loadPosts('الرئيسية');  // هنا نقوم بإعادة تحميل المنشورات بعد التعديل

                    // إغلاق المودال
                    const modal = new bootstrap.Modal(document.getElementById('editPostModal'));
                    modal.hide();
                } else {
                    alert('فشل تحديث المنشور: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error updating post:', error);
                alert('حدث خطأ أثناء تحديث المنشور');
            });
    });
});