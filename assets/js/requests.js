(function() {
    // Open modal and update fields
    function openModal(studentId, studentName) {
        document.getElementById('modalStudentId').value = studentId;
        document.getElementById('modalStudentName').value = studentName;

        var myModal = new bootstrap.Modal(document.getElementById('requestModal'), {
            keyboard: false
        });
        myModal.show();
    }

    // Toggle message area visibility
    document.getElementById('messageCheck').addEventListener('change', function() {
        document.getElementById('messageArea').style.display = this.checked ? 'block' : 'none';
    });

    // Save changes and send updated request
    document.getElementById('saveChanges').addEventListener('click', function() {
        const studentId = document.getElementById('modalStudentId').value;
        const status = document.querySelector('input[name="requestStatus"]:checked').value;
        const message = document.getElementById('messageCheck').checked ? document.getElementById('Textarea1').value : null;

        const data = { student_id: studentId, status, message };

        sendRequest('update-request.php', data)
            .then(response => handleResponse(response, studentId, status))
            .catch(error => console.error('Error:', error));
    });

    // Function to send requests using fetch
    function sendRequest(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        }).then(response => response.json());
    }

    // Handle response from the server
    function handleResponse(data, studentId, status) {
        const messageDiv = document.querySelector('.message');
        messageDiv.innerHTML = '';

        const alertBox = document.createElement('div');
        alertBox.className = `alert alert-${data.status === 'success' ? 'success' : 'danger'}`;
        alertBox.textContent = data.response;
        messageDiv.appendChild(alertBox);

        const modal = bootstrap.Modal.getInstance(document.getElementById('requestModal'));
        modal.hide();

        updateStatusInTable(studentId, status);

        setTimeout(() => alertBox.remove(), 3000);
    }

    // Update the status in the main table
    function updateStatusInTable(studentId, status) {
        const badgeElement = document.querySelector(`tr[data-student-id="${studentId}"] .badge`);
        badgeElement.textContent = translateStatus(status);
        badgeElement.className = `badge text-bg-${status}`;
    }

    // Translate status to Arabic
    function translateStatus(status) {
        return { 'Accepted': 'مقبول', 'Pending': 'معلَّق', 'Rejected': 'مرفوض' }[status] || 'غير معروف';
    }

    // Search requests dynamically
    document.getElementById('searchInput').addEventListener('input', function() {
        const query = this.value.trim();

        if (query.length === 0) {
            document.getElementById('requestTable').innerHTML = ''; // Hide table if input is empty
            return;
        }

        sendRequest('search_requests.php', { query })
            .then(response => {
                document.getElementById('requestTable').innerHTML = response || '<p class="text-center">لا توجد نتائج.</p>';
            })
            .catch(error => console.error('Error:', error));
    });

    window.openModal = openModal;
})();