$(document).ready(function () {
    // دالة عامة لإرسال طلب AJAX
    function sendAjaxRequest(url, method, data, successCallback, errorCallback) {
        $.ajax({
            type: method || 'POST',
            url: url,
            data: data,
            dataType: 'json',
            success: function(response) {
                if (typeof response === 'object') {
                    successCallback(response);
                } else {
                    handleError(null, 'Invalid response', 'Response is not JSON');
                }
            },
            error: function(xhr, status, error) {
                handleError(xhr, status, error);
                if (errorCallback) errorCallback(xhr, status, error);
            }
        });
    }

    // استدعاء البحث عن المعلم
    $('#searchForm').submit(function (event) {
        event.preventDefault();
        const formData = $(this).serialize();
        sendAjaxRequest('search_teacher_ajax.php', 'POST', formData, handleSearchResponse);
    });

    // التعامل مع استجابة البحث
    function handleSearchResponse(response) {
        const teacherInfoDiv = $('#teacherInfo');
        let teacherInfoHtml = '';

        // التعامل مع الحالات المختلفة للاستجابة
        if (response.status === 'invalidIdLength') {
            teacherInfoHtml = `<div class="alert alert-info">${response.requestMessage || 'حدث خطأ غير معروف'}</div>`;
        } else if (response.status === 'assigned') {
            teacherInfoHtml = `<div class="alert alert-info">هذا المعلم مضاف بالفعل.</div>`;
        } else if (response.status === 'exist') {
            teacherInfoHtml = `
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">${response.subject || 'مادة غير محددة'}</h5>
                        <p class="card-text">${response.firstName} ${response.lastName}</p>
                        <p class="card-text"><strong>حالة الطلب:</strong> ${response.requestStatus || 'غير معروفة'}</p>
                        <p class="card-text"><strong>رسالة الطلب:</strong> ${response.requestMessage || 'لا توجد رسالة'}</p>
                    </div>
                </div>`;
        } else if (response.status === 'assistant') {
            teacherInfoHtml = `
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">مساعد معلم</h5>
                        <p class="card-text">لا يمكن إرسال طلب إلى معلم مساعد ولاكن يمكن الإلتحاق بمعلم المادة</p>
                        <svg xmlns="http://www.w3.org/2000/svg" class="d-none">
                            <symbol id="check-circle-fill" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </symbol>
                        </svg>
                        <div class="alert alert-primary d-flex align-items-center" role="alert">
                            <svg class="bi flex-shrink-0 me-2" role="img" aria-label="Info:"><use xlink:href="#info-fill"/></svg>
                            <div>${response.requestMessage || 'معلم غير معروف'}</div>
                        </div>
                    </div>
                </div>`;
        } else if (response.status === 'success') {
            teacherInfoHtml = `
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">${response.subject || 'مادة غير محددة'}</h5>
                        <p class="card-text">${response.firstName} ${response.lastName} - ${response.title === 'teacher' ? 'معلم' : 'مساعد'}</p>
                        <button type="button" id="joinTeacherBtn" class="btn btn-default" data-teacher-id="${response.teacher_id}">تقديم طلب</button>
                    </div>
                </div>`;
        } else {
            teacherInfoHtml = `<div class="alert alert-danger">${response.message || 'لم يتم العثور على معلم بالمعرف المدخل.'}</div>`;
        }

        // تحديث محتوى div لعرض معلومات المعلم
        teacherInfoDiv.html(teacherInfoHtml);
    }

    // إرسال طلب للانضمام إلى المعلم
    $(document).on('click', '#joinTeacherBtn', function () {
        var teacherId = $(this).data('teacher-id');

        if (!teacherId) {
            alert('معرف المعلم غير صالح');
            return;
        }

        // تعطيل الزر لمنع التكرار
        $(this).prop('disabled', true);

        sendAjaxRequest('add_teacher.php', 'POST', { teacherId: teacherId }, function(response) {
            handleJoinResponse(response, '#joinTeacherBtn');
        });
    });

    // التعامل مع استجابة إضافة المعلم
    function handleJoinResponse(response, buttonSelector) {
        var teacherInfoDiv = $('#teacherInfo');
        var alertType = response.status === 'success' ? 'alert-success' : 'alert-danger';
        teacherInfoDiv.html(`<div class="alert ${alertType}">${response.message}</div>`);

        // إعادة تفعيل الزر في حالة الفشل
        if (response.status !== 'success') {
            $(buttonSelector).prop('disabled', false);
        }
    }

    // حذف المعلم من القائمة
    $(document).on('click', '.delete-teacher-btn', function () {
        var teacherId = $(this).data('teacher-id');

        if (!teacherId) {
            alert('معرف المعلم غير صالح');
            return;
        }

        sendAjaxRequest('delete-assigned-teacher.php', 'POST', { teacher_id: teacherId }, handleDeleteResponse);
    });

    // التعامل مع استجابة حذف المعلم
    function handleDeleteResponse(response) {
        var deleteTeacherDiv = $('#deleteTeacher');
        var alertType = response.status === 'success' ? 'alert-success' :
                        response.status === 'warning' ? 'alert-warning' : 'alert-danger';
        deleteTeacherDiv.html(`<div class="alert ${alertType}">${response.message}</div>`);

        if (response.status === 'success') {
            setTimeout(function() {
                location.reload();
            }, 3000);
        }
    }

    // دالة للتعامل مع الأخطاء العامة في الطلبات
    function handleError(xhr, status, error) {
        const teacherInfoDiv = $('#teacherInfo');
        let errorMessage = 'حدث خطأ أثناء معالجة الطلب.';

        if (status === 'timeout') {
            errorMessage = 'انتهت المهلة الزمنية للطلب.';
        } else if (status === 'error' || status === 'parsererror') {
            errorMessage = 'حدث خطأ أثناء استجابة الخادم.';
        } else if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
            errorMessage = xhr.responseJSON.message;
        }

        teacherInfoDiv.html(`<div class="alert alert-danger">${errorMessage}</div>`);
    }
});

// استمع لفتح كل مودال
document.querySelectorAll('.modal-body').forEach(function(modal) {
    modal.addEventListener('shown.bs.modal', function() {
        // احصل على معرف المعلم من المودال المفتوح
        const teacherId = modal.getAttribute("data-teacher-id");
        
        // حدد الصفحات
        const page1 = document.getElementById("page1-" + teacherId);
        const page2 = document.getElementById("page2-" + teacherId);
        
        // قم بضبط القيم الصحيحة
        page1.style.transform = "translateX(0)";
        page2.style.transform = "translateX(-120%)"; // اجعل الصفحة 2 خارج العرض
    });
});

// إضافة أحداث الزر لحذف المعلم والعودة
document.querySelectorAll(".deletePage").forEach(function (deleteBtn) {
    deleteBtn.addEventListener("click", function () {
        const teacherId = deleteBtn.getAttribute("data-teacher-id");
        const page1 = document.getElementById("page1-" + teacherId);
        const page2 = document.getElementById("page2-" + teacherId);

        page1.style.transform = "translateX(120%)"; // إخفاء الصفحة 1
        page2.style.transform = "translateX(0)"; // عرض الصفحة 2
    });
});

document.querySelectorAll(".homePage").forEach(function (homeBtn) {
    homeBtn.addEventListener("click", function () {
        const teacherId = homeBtn.getAttribute("data-teacher-id");
        const page1 = document.getElementById("page1-" + teacherId);
        const page2 = document.getElementById("page2-" + teacherId);

        page2.style.transform = "translateX(-120%)"; // إخفاء الصفحة 2
        page1.style.transform = "translateX(0)"; // عرض الصفحة 1
    });
});