// إنشاء WebSocket للاتصال بالخادم
const socket = new WebSocket('ws://127.0.0.1:5500');

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
    document.getElementById('bell').classList.add('fa-solid');
    document.getElementById('bell').classList.remove('fa-regular');

    // تحديث عدد الإشعارات غير المقروءة
    const unreadCountElement = document.getElementById('unreadCount');
    unreadCountElement.textContent = parseInt(unreadCountElement.textContent) + 1;
    unreadCountElement.style.display = 'block';
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
    fetch('../../student/load_student_notification.php')
        .then(response => response.json())
        .then(data => {
            const bodyElement = document.querySelector('.body');
            bodyElement.innerHTML = ''; // تفريغ المحتوى الحالي

            // إذا كانت هناك إشعارات غير مقروءة، يتم عرضها
            if (data.total > 0) {
                bodyElement.innerHTML = data.html;
                bodyElement.style.maxHeight = '280px';
                document.getElementById('bell').classList.add('fa-solid');
                document.getElementById('bell').classList.remove('fa-regular');
            } else {
                bodyElement.innerHTML = data.html;
                bodyElement.style.maxHeight = 'unset';
                document.getElementById('bell').classList.add('fa-regular');
                document.getElementById('bell').classList.remove('fa-solid');
            }
            
            // تحديث عدد الإشعارات غير المقروءة
            const unreadCountElement = document.getElementById('unreadCount');
            if (data.total > 0) {
                unreadCountElement.textContent = data.total;
                unreadCountElement.style.display = 'block';
            } else {
                unreadCountElement.style.display = 'none';
            }
        })
        .catch(error => console.error("Error fetching notifications:", error));
    }
    
    // دالة لتحديث حالة الإشعارات إلى مقروءة
    function markNotificationsAsRead() {
        fetch('../../student/mark_as_read_student.php', { method: 'POST' })
        .then(() => {
            document.getElementById('unreadCount').style.display = 'none';
            document.getElementById('bell').classList.add('fa-regular');
            document.getElementById('bell').classList.remove('fa-solid');
        })
        .catch(error => console.error("Error marking notifications as read:", error));
}

// التعامل مع فتح/إغلاق لوحة الإشعارات
document.getElementById('readState').addEventListener('click', function(e) {
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

// إغلاق اللوحة عند النقر خارجها
document.addEventListener('click', function(e) {
    const panelElement = document.querySelector('.panel');
    if (panelElement.style.display === 'block' && !e.target.closest('.panel') && !e.target.closest('#readState')) {
        panelElement.style.display = 'none';
        markNotificationsAsRead();
    }
});

// إخفاء اللوحة وشعار الإشعارات عند تحميل الصفحة
window.onload = function() {
    document.getElementById("loading").style.display = "none";
    document.getElementById("content").style.display = "block";

    document.querySelector('.panel').style.display = 'none';
    document.getElementById('unreadCount').style.display = 'none';

    // جلب الإشعارات عند تحميل الصفحة
    fetchNotifications();
};