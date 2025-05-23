document.addEventListener("DOMContentLoaded", function () {
    loadNotifications();

    // عند تقديم نموذج إضافة الإشعار
    const notificationForm = document.getElementById("notificationForm");
    if (notificationForm) {
        notificationForm.addEventListener("submit", async function (event) {
            event.preventDefault();
            const formData = new FormData(notificationForm);
            await handleNotification('add', formData);
        });
    }

    // دالة لحذف الإشعار
    window.deleteNotification = async function (notificationId) {
        if (confirm("هل أنت متأكد من أنك تريد حذف هذا الإشعار؟")) {
            const formData = new FormData();
            formData.append('id', notificationId);
            formData.append('action', 'delete');
            await handleNotification('delete', formData);
        }
    };

    // معالجة إضافة وحذف الإشعارات
    async function handleNotification(action, data) {
        try {
            if (data instanceof FormData) {
                data.append('action', action);
            }
            const response = await fetch("../../teacher/notifications/notification_handler.php", {
                method: 'POST',
                body: data instanceof FormData ? data : new URLSearchParams(data)
            });

            const result = await response.json();
            showAlert(result.message, result.success);
            if (result.success) {
                loadNotifications();
                if (action === 'add' && notificationForm) {
                    notificationForm.reset();
                }
            }
        } catch (error) {
            showAlert("حدث خطأ أثناء معالجة الإشعار.", false);
        }
    }

    // تحميل الإشعارات
    async function loadNotifications() {
        try {
            const response = await fetch("../../teacher/notifications/notification_handler.php", {
                method: 'GET',
            });
            const result = await response.json();

            if (result.success) {
                if (result.notifications.length > 0) {
                    displayNotifications(result.notifications);
                } else {
                    showNoNotifications();
                }
            } else {
                showAlert(result.message, false);
            }
        } catch (error) {
            showAlert("حدث خطأ أثناء تحميل الإشعارات.", false);
        }
    }

    // عرض الإشعارات في القائمة
    function displayNotifications(notifications) {
        const notificationsList = document.getElementById("notificationsList");
        if (!notificationsList) return;

        notificationsList.innerHTML = ""; // تفريغ القائمة
        notifications.forEach(function (notification) {
            const notificationItem = document.createElement("div");
            notificationItem.className = "notification-item bg-white my-3 py-3 px-4 rounded shadow-sm d-flex justify-content-between align-items-center";
            notificationItem.innerHTML = `
                <div class="d-flex align-items-center">
                    <img src="${notification.dir}" alt="Profile Photo" class="rounded-circle me-3" style="height: 64px;">
                    <div>
                        <h6><strong>${notification.title} - </strong> ${notification.content}</h6>
                        <strong>${notification.first_name} ${notification.last_name}</strong>
                    </div>
                </div>
                <button onclick="deleteNotification(${notification.id})" class="btn btn-danger">حذف</button>
            `;
            notificationsList.appendChild(notificationItem);
        });
    }

    // عرض رسالة إذا لم توجد إشعارات
    function showNoNotifications() {
        const notificationsList = document.getElementById("notificationsList");
        if (!notificationsList) return;

        notificationsList.innerHTML = `
            <div class="h-100 d-flex flex-column justify-content-center align-items-center">
                <dotlottie-player src="https://lottie.host/a2ad8216-e6e7-43ab-834b-deb6eb734753/NxW5MqXOtg.json" background="transparent" speed="1" style="width: 300px; height: 300px;" loop autoplay></dotlottie-player>
                <h4 class="text-secondary">لا توجد إشعارات حالياً</h4>
            </div>
        `;
    }

    // عرض تنبيه
    function showAlert(message, success) {
        const alertContainer = document.getElementById("alertContainer");
        if (!alertContainer) return;

        const alertType = success ? 'alert-success' : 'alert-danger';
        const alertElement = document.createElement("div");
        alertElement.className = `alert ${alertType} show fade`;
        alertElement.setAttribute("role", "alert");
        alertElement.textContent = message;

        alertContainer.innerHTML = ""; // إزالة التنبيهات السابقة
        alertContainer.appendChild(alertElement);

        setTimeout(() => {
            if (alertElement) {
                alertElement.remove();
            }
        }, 3000);
    }
});