function loadVideos() {
    fetch('../../teacher/lessons/load_videos.php')
        .then(response => response.json())
        .then(videos => {
            const lists = {
                first: document.getElementById('firstVideoList'),
                second: document.getElementById('secondVideoList'),
                third: document.getElementById('thirdVideoList'),
            };

            const containers = {
                first: document.getElementById('firstVideos'),
                second: document.getElementById('secondVideos'),
                third: document.getElementById('thirdVideos'),
            };

            // تنظيف القوائم
            Object.values(lists).forEach(list => (list.innerHTML = ''));

            // تعبئة الفيديوهات في القوائم
            videos.forEach(video => {
                const videoItem = createThumbnailItem(video);
                if (lists[video.level]) {
                    lists[video.level].appendChild(videoItem);
                }
            });

            // إظهار أو إخفاء الحاويات بناءً على وجود فيديوهات
            Object.keys(lists).forEach(level => {
                const list = lists[level];
                const container = containers[level];
                if (list.children.length === 0) {
                    container.style.display = 'none';
                } else {
                    container.style.display = 'block';
                }
            });

            initializeSwiper(); // إعادة تهيئة Swiper
        })
        .catch(error => {
            console.error('Error loading videos:', error);
            showErrorModal('حدث خطأ أثناء تحميل الفيديوهات. حاول مجدداً في وقت لاحق.');
        });
}

/**
 * إنشاء عنصر الصورة المصغرة للفيديو
 * @param {Object} video
 * @returns {HTMLElement}
 */
function createThumbnailItem(video) {
    const videoItem = document.createElement('div');
    if (video.picture == null) {
        video.picture = "default.png";
    }
    let thumbnailPath = (video.thumbnail == "") ? video.thumbnail = "../../../../assets/images/" + video.subject + ".webp" : "../../../../assets/videos/" + video.level + "/" + video.thumbnail;
    videoItem.className = 'swiper-slide bg-white p-3 shadow-sm d-flex flex-column justify-content-start align-items-center h-100';
    videoItem.innerHTML = `
            <div class="thumbnail-container position-relative">
                <img src="${thumbnailPath}" class="video-thumbnail rounded-3 w-100">
                <button class="play-button btn btn-light rounded-circle position-absolute d-flex justify-content-center align-items-center" data-video-id="${video.id}" data-video-src="../../../../assets/videos/${video.level}/${video.fileName}">
                    <i class="fas fa-play"></i>
                </button>
            </div>
            <h6 class="video-name mt-2 text-truncate w-100 text-start">${video.name}
                <br>
                <div class="d-flex justify-content-start align-items-center mt-1">
                    <img class="rounded-circle me-1" src="../../../../assets/images/profiles/${video.picture}" alt="${video.first_name}">
                    <small class="text-secondary">${video.first_name} ${video.last_name}</small>
                </div>
            </h6>
            <div class="d-flex justify-content-center align-items-end gap-2 w-100">
                <button class="btn btn-default w-100 edit-video" data-video-id="${video.id}">تعديل</button>
                <button class="delete-video btn btn-danger w-100" data-video-id="${video.id}">حذف</button>
            </div>
        `;
    return videoItem;
}

/**
 * إظهار مودال الخطأ
 * @param {string} message
 */
function showErrorModal(message) {
    const modalContainer = document.getElementById('modals');
    modalContainer.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <button type="button" class="btn-close" onclick="this.closest('.modal').remove()"></button>
                    </div>
                    <div class="modal-body">
                        <p>${message}</p>
                    </div>
                </div>
            </div>
        `;
    const modal = new bootstrap.Modal(modalContainer);
    modal.show();
    modalContainer.addEventListener('hidden.bs.modal', () => {
        modalContainer.innerHTML = ''; // تنظيف المودال بعد الإغلاق
    }, { once: true });
}

function initializeSwiper() {
    new Swiper('.swiper', {
        slidesPerView: 'auto',
        spaceBetween: 10,
        direction: 'horizontal',
        loop: true,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },
    });
}

document.addEventListener('DOMContentLoaded', () => {
    loadVideos();

    /**
     * عرض مودال الفيديو وتحميل الفيديو عند الحاجة
     * @param {string} videoSrc
     */
    function showVideoModal(videoSrc) {
        const modalBody = document.querySelector('#videoModal .modal-body');
        modalBody.innerHTML = `
            <video controls autoplay class="w-100">
                <source src="${videoSrc}" type="video/mp4">
                متصفحك لا يدعم تشغيل الفيديو.
            </video>
        `;

        const videoModal = new bootstrap.Modal(document.getElementById('videoModal'));
        videoModal.show();

        // تنظيف الفيديو عند إغلاق المودال
        const modalElement = document.getElementById('videoModal');
        modalElement.addEventListener('hidden.bs.modal', () => {
            modalBody.innerHTML = ''; // إزالة الفيديو
        }, { once: true });
    }

    /**
     * إظهار مودال التعديل
     * @param {string} videoSrc
     */
    function showEditModal(video) {
        if (!video || !video.level || !video.fileName) {
            console.error('Invalid video object:', video);
            showModalMessage('حدث خطأ أثناء جلب بيانات الفيديو.', 'error');
            return;
        }

        const modalEdit = document.getElementById('editVideoModal');
        modalEdit.innerHTML = `
            <div class="modal-dialog modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header border-0">
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form id="editForm">
                    <div class="mb-3">
                      <label for="editVideoName" class="form-label">عنوان الفيديو</label>
                      <input type="text" class="form-control" id="editVideoName" name="videoName" value="${video.name}" required>
                    </div>
                    <div class="mb-3">
                      <label for="editThumbnail" class="form-label">صورة مصغرة</label>
                      <input type="file" class="form-control" id="editThumbnail" name="thumbnail" accept="image/.jpg,.jpeg,.png,.webp">
                    </div>
                    <div class="mb-3">
                      <label for="editlevel" class="form-label">المرحلة</label>
                      <select class="form-select" id="editlevel" name="level" required>
                        <option value="first" ${video.level === 'first' ? 'selected' : ''}>الأول الثانوي</option>
                        <option value="second" ${video.level === 'second' ? 'selected' : ''}>الثاني الثانوي</option>
                        <option value="third" ${video.level === 'third' ? 'selected' : ''}>الثالث الثانوي</option>
                      </select>
                    </div>
                    <input type="hidden" id="editVideoSrc" name="videoSrc" value="../../../../assets/videos/${video.level}/${video.fileName}">
                    <input type="hidden" name="videoId" value="${video.id}">
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                  <button type="button" class="btn btn-default" onclick="saveChanges()">حفظ التغييرات</button>
                </div>
              </div>
            </div>
        `;
        const editModal = new bootstrap.Modal(modalEdit);
        editModal.show();
    }

    // التعامل مع زر تشغيل الفيديو
    document.addEventListener('click', e => {
        if (e.target.closest('.play-button')) {
            const button = e.target.closest('.play-button');
            const videoSrc = button.getAttribute('data-video-src');
            showVideoModal(videoSrc);
        }
    });

    // التعامل مع زر التعديل
    document.addEventListener('click', e => {
        if (e.target.closest('.edit-video')) {
            const button = e.target.closest('.edit-video');
            const videoId = button.getAttribute('data-video-id');
            fetch(`get_video.php?id=${videoId}`)
                .then(response => response.json())
                .then(video => {
                    showEditModal(video);
                })
                .catch(error => {
                    console.error('Error fetching video:', error);
                    showModalMessage('حدث خطأ أثناء جلب بيانات الفيديو.', 'error');
                });
        }
    });

    // رفع الفيديو مع شريط التقدم
    const uploadForm = document.getElementById('uploadForm');
    const progressBar = document.getElementById('uploadProgress');

    uploadForm.addEventListener('submit', e => {
        e.preventDefault();

        const formData = new FormData(uploadForm);
        const xhr = new XMLHttpRequest();

        xhr.open('POST', '../../teacher/lessons/upload.php', true);

        // تحديث شريط التقدم
        xhr.upload.addEventListener('progress', e => {
            if (e.lengthComputable) {
                const percentComplete = (e.loaded / e.total) * 100;
                progressBar.style.width = `${percentComplete}%`;
                progressBar.setAttribute('aria-valuenow', percentComplete.toFixed(0));
            }
        });

        // معالجة الاستجابة بعد رفع الفيديو
        xhr.onload = () => {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    showModalMessage(response.message, response.status === 'success' ? 'success' : 'error');
                    if (response.status === 'success') {
                        loadVideos(); // إعادة تحميل الفيديوهات بعد النجاح
                        uploadForm.reset();
                        progressBar.style.width = '0%';
                        const uploadModal = bootstrap.Modal.getInstance(document.getElementById('uploadModal'));
                        uploadModal.hide();
                    }
                } catch {
                    showModalMessage('حدث خطأ غير متوقع.', 'error');
                }
            } else {
                showModalMessage('حدث خطأ أثناء رفع الفيديو.', 'error');
            }
        };

        xhr.onerror = () => {
            showModalMessage('حدث خطأ أثناء رفع الفيديو.', 'error');
        };

        xhr.send(formData);
    });

    // حذف الفيديو عند النقر على زر الحذف
    document.addEventListener('click', e => {
        if (e.target.classList.contains('delete-video')) {
            const videoId = e.target.dataset.videoId;
            showConfirmationModal('هل أنت متأكد من أنك تريد حذف هذا الفيديو؟', () => {
                fetch('../../teacher/lessons/delete_video.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${videoId}`,
                })
                    .then(response => response.json())
                    .then(data => {
                        showModalMessage(data.message, data.status === 'success' ? 'success' : 'error');
                        if (data.status === 'success') loadVideos();
                    })
                    .catch(error => {
                        console.error('Error deleting video:', error);
                        showModalMessage('حدث خطأ أثناء حذف الفيديو.', 'error');
                    });
            });
        }
    });
});

/**
 * Display a modal message
 * @param {string} message - The message to display
 * @param {string} type - The type of message ('success' or 'error')
 */
function showModalMessage(message, type = 'success') {
    const modalContainer = document.getElementById('modals');
    modalContainer.innerHTML = `
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header ${type === 'success' ? 'bg-success text-white' : 'bg-danger text-white'}">
                <h5 class="modal-title">${type === 'success' ? 'نجاح' : 'خطأ'}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>${message}</p>
            </div>
        </div>
    </div>
    `;
    const modal = new bootstrap.Modal(modalContainer);
    modal.show();
    modalContainer.addEventListener('hidden.bs.modal', () => {
        modalContainer.innerHTML = ''; // تنظيف المودال بعد الإغلاق
    }, { once: true });
}

/**
 * Display a confirmation modal
 * @param {string} message - The confirmation message
 * @param {Function} onConfirm - The callback function to execute if the user confirms
 */
function showConfirmationModal(message, onConfirm) {
    const modalContainer = document.getElementById('modals');
    if (!modalContainer) {
        console.error('Modal container with id "modals" not found in the DOM.');
        return;
    }

    modalContainer.innerHTML = `
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>${message}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-danger" id="confirmButton">تأكيد</button>
            </div>
        </div>
    </div>
    `;
    const modal = new bootstrap.Modal(modalContainer);
    modal.show();
    
    // Add event listener for the confirm button
    document.getElementById('confirmButton').addEventListener('click', () => {
        modalContainer.innerHTML = ''; // Remove the modal
        onConfirm(); // Execute the callback function
    });
    
    modalContainer.addEventListener('hidden.bs.modal', () => {
        modalContainer.innerHTML = ''; // تنظيف المودال بعد الإغلاق
    }, { once: true });
}

function saveChanges() {
    const editForm = document.getElementById('editForm');
    const formData = new FormData(editForm);

    fetch('../../teacher/lessons/edit_video.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            showModalMessage(data.message, data.status === 'success' ? 'success' : 'error');
            if (data.status === 'success') {
                loadVideos(); // Reload videos to reflect changes
                const editModal = bootstrap.Modal.getInstance(document.getElementById('editVideoModal'));
                editModal.hide();
            }
        })
        .catch(error => {
            console.error('Error editing video:', error);
            showModalMessage('حدث خطأ أثناء تعديل الفيديو.', 'error');
        });
}