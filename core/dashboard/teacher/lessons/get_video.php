<?php
require_once '../auth.php';

if (isset($_GET['id'])) {
    $videoId = intval($_GET['id']);

    // Prepare and execute the query to fetch video details
    $stmt = $conn->prepare("SELECT * FROM videos WHERE id = ?");
    $stmt->bind_param("i", $videoId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $video = $result->fetch_assoc();
        echo json_encode($video);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'لم يتم العثور على الفيديو']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'حدث خطأ ما، يرجى المحاولة لاحقاً']);
}

$conn->close();