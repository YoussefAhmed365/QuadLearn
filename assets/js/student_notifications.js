let filterType = 'عرض الكل';

document.addEventListener("DOMContentLoaded", function () {
    loadNotifications(filterType);
});

// دالة عامة لتقديم طلبات fetch
async function sendFetchRequest(url, method, body = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        }
    };
    if (body) options.body = JSON.stringify(body);
    
    try {
        const response = await fetch(url, options);
        return await response.json();
    } catch (error) {
        console.error(`Fetch error (${url}):`, error);
        throw error;
    }
}

// تعيين الإشعار كمقروء
function markNotificationAsRead(notificationId) {
    sendFetchRequest('mark_notification_as_read.php', 'POST', { notificationId })
        .then(data => {
            if (data.success) {
                loadNotifications();
            } else {
                console.error("Server error:", data.message);
            }
        })
        .catch(error => console.error("Fetch error:", error));
}

// تحميل الإشعارات
function loadNotifications(filterType = 'عرض الكل') {
    const notificationContainer = document.getElementById('notificationContainer');
    notificationContainer.innerHTML = "";

    fetch('load_notifications.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `filterType=${encodeURIComponent(filterType)}`
    })
    .then(response => response.text())
    .then(data => {
        notificationContainer.innerHTML = data;
    })
    .catch(error => console.error('Error loading notifications:', error));
}

// التعامل مع الفلاتر
document.querySelectorAll('.subjectItem button').forEach(button => {
    button.addEventListener('click', function () {
        document.querySelectorAll('.subjectItem').forEach(item => item.classList.remove('active'));
        this.closest('.subjectItem').classList.add('active');
        filterType = this.textContent;
        loadNotifications(filterType);
    });
});

// إعادة تحميل الإشعارات عند التحديث
document.getElementById('update').addEventListener('click', () => loadNotifications(filterType));

// التعامل مع تحديد وحذف الإشعارات
document.addEventListener("DOMContentLoaded", function() {
    const checkAll = document.getElementById('checkAll');
    const deleteButton = document.getElementById('delete');

    function getCheckboxes() {
        return document.querySelectorAll('table td.check input[type="checkbox"]');
    }

    // إظهار أو إخفاء زر الحذف
    function toggleButtonVisibility() {
        const anyChecked = Array.from(getCheckboxes()).some(checkbox => checkbox.checked);
        deleteButton.style.display = anyChecked ? 'block' : 'none';
    }

    // تحديث حالة "تحديد الكل"
    function updateCheckAllStatus() {
        const allChecked = Array.from(getCheckboxes()).every(checkbox => checkbox.checked);
        checkAll.checked = allChecked;
        toggleButtonVisibility();
    }

    // الاستجابة لتحديد أو إلغاء تحديد "الكل"
    checkAll.addEventListener('click', () => {
        const checkboxes = getCheckboxes();
        checkboxes.forEach(checkbox => checkbox.checked = checkAll.checked);
        toggleButtonVisibility();
    });

    // الاستجابة لتغييرات التحديد في كل checkbox
    document.addEventListener('change', (event) => {
        if (event.target.matches('table td.check input[type="checkbox"]')) {
            updateCheckAllStatus();
        }
    });

    // حذف الإشعارات المختارة
    deleteButton.addEventListener('click', function() {
        const selectedNotifications = Array.from(getCheckboxes())
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.closest('tr').getAttribute('data-id'));

        if (selectedNotifications.length > 0) {
            sendFetchRequest('delete_notifications.php', 'POST', { notificationIds: selectedNotifications })
                .then(data => {
                    if (data.success) {
                        loadNotifications(filterType);
                    } else {
                        alert('فشل في حذف الإشعارات: ' + data.message);
                    }
                })
                .catch(error => alert('حدث خطأ أثناء محاولة حذف الإشعارات.'));
        } else {
            alert('لم يتم تحديد أي إشعارات للحذف.');
        }
    });

    // عرض تفاصيل الإشعار عند الضغط عليه
    document.addEventListener('click', function(event) {
        const notification = event.target.closest("#notification");
        if (notification) {
            const notificationId = notification.getAttribute("data-id");
            const title = notification.getAttribute("data-title");
            const content = notification.getAttribute("data-content");
            const time = notification.getAttribute("data-time");
            const publisherName = notification.getAttribute("data-firstName") + ' ' + notification.getAttribute("data-lastName");

            document.getElementById('notificationContainer').classList.add('d-none');
            document.getElementById('notificationDetails').classList.remove('d-none');

            document.getElementById('notificationTitle').innerText = title;
            document.getElementById('notificationContent').innerText = content;
            document.getElementById('notificationTime').innerText = time;
            document.getElementById('notificationPublisher').innerText = 'من: ' + publisherName;

            if (notification.classList.contains('unread')) {
                markNotificationAsRead(notificationId);
            }
        }
    });

    // زر العودة لعرض جميع الإشعارات
    document.getElementById('backToTable').addEventListener('click', function() {
        document.getElementById('notificationDetails').classList.add('d-none');
        document.getElementById('notificationContainer').classList.remove('d-none');
        loadNotifications(filterType);
    });
});