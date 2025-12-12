// إنشاء WebSocket للاتصال بالخادم
const wsUrl = `ws://${window.location.hostname}:5500`;
const socket = new WebSocket(wsUrl);

// عند فتح الاتصال بخادم WebSocket
socket.onopen = function() {
    console.log('Connected to WebSocket');
};

// استقبال الرسائل من WebSocket (أي إشعارات جديدة)
socket.onmessage = function(event) {
    const notification = JSON.parse(event.data);
    const bodyElement = document.querySelector('.body');

    // إضافة الإشعار الجديد إلى القائمة
    const newNotificationHTML = `
        <div class="notification-box row mb-3 w-100">
            <div class="content col-10 d-flex justify-content-start align-items-center">
                <p><strong>${notification.title}</strong>&nbsp;${notification.content}</p>
            </div>
        </div>
    `;

    // إدراج الإشعار الجديد في أعلى القائمة
    bodyElement.innerHTML = newNotificationHTML + bodyElement.innerHTML;

    // تحديث رمز الجرس إلى الحالة المفعلة
    const bell = document.getElementById('bell');
    if (bell) {
        bell.classList.add('fa-solid');
        bell.classList.remove('fa-regular');
    }

    // تحديث عدد الإشعارات غير المقروءة
    const unreadCountElement = document.getElementById('unreadCount');
    if (unreadCountElement) {
        unreadCountElement.textContent = parseInt(unreadCountElement.textContent || '0') + 1;
        unreadCountElement.style.display = 'block';
    }
};

// معالجة الأخطاء التي تحدث في WebSocket
socket.onerror = function(error) {
    console.error('WebSocket Error: ', error);
};

// عند إغلاق اتصال WebSocket
socket.onclose = function() {
    console.log('Disconnected from WebSocket');
};

// دالة لجلب الإشعارات عند تحميل الصفحة
function fetchNotifications() {
    fetch('../../teacher/load_home_notification.php')
        .then(response => response.json())
        .then(data => {
            const bodyElement = document.querySelector('.body');
            bodyElement.innerHTML = ''; // تفريغ المحتوى الحالي

            const bell = document.getElementById('bell');
            const unreadCountElement = document.getElementById('unreadCount');

            // إذا كانت هناك إشعارات غير مقروءة، يتم عرضها
            if (data.total > 0) {
                bodyElement.innerHTML = data.html;
                bodyElement.style.maxHeight = '280px';
                if (bell) {
                    bell.classList.add('fa-solid');
                    bell.classList.remove('fa-regular');
                }
            } else {
                bodyElement.innerHTML = data.html;
                bodyElement.style.maxHeight = 'unset';
                if (bell) {
                    bell.classList.add('fa-regular');
                    bell.classList.remove('fa-solid');
                }
            }

            // تحديث عدد الإشعارات غير المقروءة
            if (data.total > 0) {
                if (unreadCountElement) {
                    unreadCountElement.textContent = data.total;
                    unreadCountElement.style.display = 'block';
                }
            } else {
                if (unreadCountElement) {
                    unreadCountElement.style.display = 'none';
                }
            }
        })
        .catch(error => console.error("Error fetching notifications:", error));
    }

    // دالة لتحديث حالة الإشعارات إلى مقروءة
    function markNotificationsAsRead() {
        fetch('../../teacher/mark_as_read.php', { method: 'POST' })
        .then(() => {
            const unreadCountElement = document.getElementById('unreadCount');
            const bell = document.getElementById('bell');

            if (unreadCountElement) unreadCountElement.style.display = 'none';
            if (bell) {
                bell.classList.add('fa-regular');
                bell.classList.remove('fa-solid');
            }
        })
        .catch(error => console.error("Error marking notifications as read:", error));
}

// التعامل مع فتح/إغلاق لوحة الإشعارات
const readStateBtn = document.getElementById('readState');
if (readStateBtn) {
    readStateBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        const panelElement = document.querySelector('.panel');
        if (panelElement.style.display === 'block') {
            panelElement.style.display = 'none';
            markNotificationsAsRead();
        } else {
            panelElement.style.display = 'block';
            fetchNotifications();
        }
    });
}

// إغلاق اللوحة عند النقر خارجها
document.addEventListener('click', function(e) {
    const panelElement = document.querySelector('.panel');
    if (panelElement && panelElement.style.display === 'block' && !e.target.closest('.panel') && !e.target.closest('#readState')) {
        panelElement.style.display = 'none';
        markNotificationsAsRead();
    }
});

// إخفاء اللوحة وشعار الإشعارات عند تحميل الصفحة
window.onload = function() {
    const loading = document.getElementById("loading");
    if (loading) loading.style.display = "none";

    const content = document.getElementById("content");
    if (content) content.style.display = "block";

    const panel = document.querySelector('.panel');
    if (panel) panel.style.display = 'none';

    const unreadCount = document.getElementById('unreadCount');
    if (unreadCount) unreadCount.style.display = 'none';

    // جلب الإشعارات عند تحميل الصفحة
    fetchNotifications();
};