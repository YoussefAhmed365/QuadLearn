<?php
require '../auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['post_id'];
    $action = $_POST['action'];

    if ($action === 'bookmark') {
        // حفظ المنشور في المفضلة
        $query = "INSERT INTO saved_posts (user_id, post_id) VALUES (?, ?)";
    } elseif ($action === 'unbookmark') {
        // إزالة المنشور من المفضلة
        $query = "DELETE FROM saved_posts WHERE user_id = ? AND post_id = ?";
    }

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $postId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update bookmark status.']);
    }

    $stmt->close();
}