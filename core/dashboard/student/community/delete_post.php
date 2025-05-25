<?php
require '../auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postId = $_POST['post_id'];

    $stmt = $conn->prepare("DELETE FROM community WHERE id = ?");
    $stmt->bind_param('i', $postId);

    if ($stmt->execute()) {
        header("Location: community.php");
    } else {
        echo '<script>
                alert("حدثت مشكلة أثناء حذف المنشور");
            </script>';
    }

    $stmt->close();
    exit;
}