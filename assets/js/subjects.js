const filesContainer = document.getElementById("filesContainer");

function showFiles(limit) {
    return new Promise((resolve, reject) => {
        fetch("show-files.php", {
            method: 'POST',
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ limit })
        })
        .then(response => {
            if (!response.ok) {
                reject(new Error("Network response was not ok"));
                return;
            }
            return response.text();
        })
        .then(data => {
            filesContainer.innerHTML = data;
            // After rendering, attach event listener if button exists
            const showAllBtn = document.getElementById("showAllBtn");
            if (showAllBtn) {
                showAllBtn.addEventListener("click", function () {
                    showAllBtn.disabled = true;
                    showAllBtn.innerHTML = "<div class='btn-loader'></div>";
                    showFiles("") // Remove limit
                    .then(() => {
                        showAllBtn.style.display = "none";
                    })
                    .catch(error => {
                        console.error("Error showing files:", error);
                        showAllBtn.innerHTML = "عرض الكل";
                        showAllBtn.disabled = false;
                    });
                });
            }
            resolve();
        })
        .catch(error => {
            reject(error);
        });
    });
}

// Initial load with limit 4
showFiles("LIMIT 4");