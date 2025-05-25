document.addEventListener("DOMContentLoaded", function () {
    const main = document.getElementById("main");
    const list = document.getElementById("subjectList");
    
    if (!main || !list) {
        console.error("Required DOM elements not found.");
        return;
    }
    
    loadVideos();

    let filterType = ""; // التصنيف الحالي
    const radioBtns = document.getElementsByName("subjectFilter"); // أزرار التصنيف
    
    // إضافة مستمع للأزرار
    radioBtns.forEach(button => {
        button.addEventListener("change", function () {
            filterType = this.value !== "showAll" ? this.value : ""; // تحديث التصنيف
            loadVideos(filterType); // استدعاء تحميل الفيديوهات
        });
    });
    
    // تحميل الفيديوهات وعرضها في الصفحة
    function loadVideos(filterType = "") {
        // رسالة تحميل مؤقتة
        main.innerHTML = `
            <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">جاري التحميل...</span>
            </div>
        `;
    
        // إرسال الطلب إلى الواجهة الخلفية
        fetch("load_videos.php", {
            method: 'POST',
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({
                filterType: filterType, // التصنيف المحدد
                level: "Grade 12" // يمكنك تحديث المستوى حسب الحاجة
            }),
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(videos => {
            // تنظيف الحاوية القديمة
            main.innerHTML = "";
    
            if (Array.isArray(videos) && videos.length > 0) {
                // عرض الفيديوهات
                videos.forEach(video => {
                    const videoItem = createVideoCard(video);
                    main.appendChild(videoItem);
                });
            } else {
                // رسالة عند عدم وجود فيديوهات
                main.innerHTML = '<h4 class="col-12 text-center">لا توجد دروس حتى الآن</h4>';
            }
        })
        .catch(error => {
            console.error("Error loading videos:", error);
            // رسالة عند حدوث خطأ
            main.innerHTML = '<h4 class="col-12 text-danger text-center">حدث خطأ أثناء تحميل الفيديوهات. حاول مجدداً لاحقاً.</h4>';
        });
    }

    // إنشاء بطاقة فيديو
    function createVideoCard(video) {
        video.picture = video.picture || "default.png";
        const defaultThumbnails = {
            Arabic: "../../../../assets/images/Arabic.webp",
            English: "../../../../assets/images/English.webp",
            French: "../../../../assets/images/French.webp",
            Spanish: "../../../../assets/images/Spanish.webp",
            German: "../../../../assets/images/German.webp",
            Italian: "../../../../assets/images/Italian.webp",
            Physics: "../../../../assets/images/Physics.webp",
            Chemistry: "../../../../assets/images/Chemistry.webp",
            Biology: "../../../../assets/images/Biology.webp",
            Geology: "../../../../assets/images/Geology.webp",
            Mathematics: "../../../../assets/images/Mathematics.webp",
            Philosophy: "../../../../assets/images/Philosophy.webp",
            History: "../../../../assets/images/History.webp",
            Geography: "../../../../assets/images/Geography.webp"
        };
        video.thumbnail = video.thumbnail || defaultThumbnails[video.subject] || "default_thumbnail.webp";
        
        const translatedSubjects = {
            Arabic: "اللغة العربية",
            English: "اللغة الإنجليزية",
            French: "اللغة الفرنسية",
            Spanish: "اللغة الإسبانية",
            German: "اللغة الألمانية",
            Italian: "اللغة الإيطالية",
            Physics: "الفيزياء",
            Chemistry: "الكيمياء",
            Biology: "الأحياء",
            Geology: "الجيولوجيا",
            Mathematics: "الرياضيات",
            Philosophy: "الفلسفة وعلم النفس",
            History: "التاريخ",
            Geography: "الجغرافيا"
        };

        const videoItem = document.createElement('div');
        videoItem.className = 'col-md-3 col-sm-6';
        videoItem.innerHTML = `
            <div class="card p-2 rounded-4 w-100 h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="position-relative mb-2">
                        <img src="../../../../assets/videos/${video.level}/${video.thumbnail}" alt="${video.name}" class="card-img-top rounded-4">
                        <button class="play-button btn btn-light rounded-circle position-absolute d-flex justify-content-center align-items-center" data-video-id="${video.id}" data-video-src="../../../../assets/videos/${video.level}/${video.fileName}">
                            <i class="fas fa-play" aria-hidden="true"></i>
                        </button>
                    </div>
                    <h5 class="card-title">${video.name}</h5>
                </div>
                <div>
                    <h6 class="card-subtitle mb-1 text-body-secondary d-flex justify-content-start align-items-center">
                        <img src="../../../../assets/images/profiles/${video.picture}" alt="${video.first_name}" class="card-profile rounded-circle me-2">
                        <span>${video.first_name} ${video.last_name}</span>
                    </h6>
                    <h6 class="card-subtitle mb-2 text-body-secondary">${translatedSubjects[video.subject]}</h6>
                </div>
            </div>
        `;
        return videoItem;
    }

    // تشغيل الفيديو عند النقر على زر التشغيل
    document.addEventListener('click', e => {
        if (e.target.closest('.play-button')) {
            const button = e.target.closest('.play-button');
            const videoSrc = button.getAttribute('data-video-src');
            showVideoModal(videoSrc);
        }
    });

    // عرض مودال الفيديو
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
});