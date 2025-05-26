<?php
// Load all posts.
require '../auth.php';
require '../../../helper_functions.php';

$images = ["jpg", "jpeg", "png", "webp"];
$word = ["doc", "docx"];
$excel = ["csv", "xlsx"];
$video = ["mp4", "avi", "mov", "mkv", "webm"];

$filterType = $_POST['filterType'] ?? 'الرئيسية';
$query = "";
$stmt = null;

switch ($filterType) {
    case 'الرئيسية':
        $query = "SELECT DISTINCT
                      c.id,
                      c.title,
                      c.content,
                      c.badges,
                      c.uploaded_files,
                      c.original_file_names,
                      c.updated_at,
                      u.id AS publisher_id,
                      u.first_name,
                      u.last_name,
                      u.account_type,
                      IF(sp.id IS NOT NULL, 1, 0) AS is_bookmarked
                  FROM
                      community c
                  JOIN
                      users u ON c.user_id = u.id
                  LEFT JOIN
                      saved_posts sp ON sp.post_id = c.id AND sp.user_id = ?
                  WHERE
                      c.user_id = ?
                      OR c.user_id IN (
                          SELECT teacher_id
                          FROM assigned_teachers
                          WHERE student_id = ?
                      )
                      OR c.user_id IN (
                          SELECT aa.assistant_id
                          FROM assigned_assistants aa
                          INNER JOIN assigned_teachers at ON aa.teacher_id = at.teacher_id
                          WHERE at.student_id = ?
                      )
                      OR (
                          c.user_id IN (
                              SELECT ast.student_id
                              FROM assigned_students ast
                              INNER JOIN assigned_teachers at ON ast.teacher_id = at.teacher_id
                              WHERE at.student_id = ?
                          )
                          AND c.user_id != ?
                      )
                  ORDER BY
                      c.updated_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iiiiii", $user_id, $user_id, $user_id, $user_id, $user_id, $user_id);
        break;

    case 'منشوراتك':
        $query = "SELECT
                      c.id,
                      c.user_id AS publisher_id,
                      c.title,
                      c.content,
                      c.badges,
                      c.uploaded_files,
                      c.original_file_names,
                      c.updated_at,
                      u.first_name,
                      u.last_name,
                      u.account_type,
                      IF(sp.id IS NOT NULL, 1, 0) AS is_bookmarked -- Check if the post is bookmarked by the current student
                  FROM
                      community c -- Alias community as c
                  JOIN
                      users u ON c.user_id = u.id -- Join community to users to get publisher details
                  LEFT JOIN
                      saved_posts sp ON sp.post_id = c.id AND sp.user_id = ? -- Check if bookmarked by the student (Param 1)
                  WHERE
                      c.user_id = ? -- Filter for posts by the student themselves (Param 2)
                  ORDER BY
                      c.updated_at DESC";
        $stmt = $conn->prepare($query);
        // Bind parameters: student_id for bookmark check and student's own posts.
        $stmt->bind_param("ii", $user_id, $user_id);
        break;

    case 'محفوظاتك':
        // Query to get posts that the current student has bookmarked.
        $query = "SELECT
                      c.id,
                      c.user_id AS publisher_id,
                      c.title,
                      c.content,
                      c.badges,
                      c.uploaded_files,
                      c.original_file_names,
                      c.updated_at,
                      u.first_name,
                      u.last_name,
                      u.account_type,
                      1 AS is_bookmarked -- Always 1 for saved posts
                  FROM
                      saved_posts sp -- Alias saved_posts as sp
                  JOIN
                      community c ON sp.post_id = c.id -- Join saved_posts to community
                  JOIN
                      users u ON c.user_id = u.id -- Join community to users to get publisher details
                  WHERE
                      sp.user_id = ? -- Filter for posts saved by the student (Param 1)
                  ORDER BY
                      c.updated_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        break;

    default:
        echo "<p>نوع الفلتر غير صالح.</p>";
        exit;
}

$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $postId = htmlspecialchars($row['id']);
        $title = isset($row['title']) ? htmlspecialchars($row['title']) : '';
        $content = isset($row['content']) ? htmlspecialchars($row['content']) : '';
        $badges = !empty($row['badges']) ? json_decode($row['badges'], true) : [];
        $uploaded_files = !empty($row['uploaded_files']) ? json_decode($row['uploaded_files'], true) : [];
        $original_file_names = !empty($row['original_file_names']) ? json_decode($row['original_file_names'], true) : [];
        $updated_at = (!empty($row['updated_at'])) ? time_elapsed_string($row['updated_at']) : "تاريخ غير متوفر";
        $firstName = isset($row['first_name']) ? htmlspecialchars($row['first_name']) : '';
        $lastName = isset($row['last_name']) ? htmlspecialchars($row['last_name']) : '';
        $accountType = isset($row['account_type']) ? htmlspecialchars($row['account_type']) : '';
        $publisher_id = $row['publisher_id'];
        $isBookmarked = (bool) $row['is_bookmarked'];
        ?>
        <div class="post container bg-white shadow-sm rounded-4 py-4 px-5 mb-4" data-title="<?php echo $title; ?>"
            data-content="<?php echo $content; ?>" data-badges="<?php echo htmlspecialchars(json_encode($badges)); ?>"
            data-uploaded-files="<?php echo htmlspecialchars(json_encode($original_file_names)); ?>">
            <div class="title">
                <?php
                $target_dir = "../../../../assets/images/profiles/";
                $dir = (glob("$target_dir$publisher_id.*")) ? "$target_dir$publisher_id.webp" : "{$target_dir}default.png";
                ?>
                <h2><?php echo $title; ?></h2>
            </div>
            <div class="row mb-4 gap-3">
                <div class="col-sm contact  d-flex align-items-center justify-content-start">
                    <div class="photo position-relative">
                        <img class="rounded-4" src="<?php echo $dir; ?>" alt="profile">
                        <div
                            class="subject d-flex justify-content-center align-items-center position-absolute rounded-4 border border-4 border-white">
                            <?php
                            switch ($accountType) {
                                case 'teacher':
                                    echo '<i class="fa-solid fa-graduation-cap"></i>';
                                    break;
                                default:
                                    echo '<i class="fa-solid fa-user"></i>';
                                    break;
                            }
                            ?>
                        </div>
                    </div>
                    <div class="text-truncate name d-flex flex-column ms-4">
                        <h6 class="mb-0"><?php echo htmlspecialchars("$firstName $lastName"); ?></h6>
                        <h6 class="mb-0"><small><?php echo $updated_at; ?></small></h6>
                    </div>
                </div>
                <?php if (!empty($badges) && is_array($badges)) { ?>
                    <div class="col-sm badges row overflow-x-scroll flex-nowrap pb-1 d-flex align-items-center justify-content-end">
                        <?php foreach ($badges as $badge) {
                            $badgeName = isset($badge['name']['name']) ? htmlspecialchars($badge['name']['name']) : "اسم غير متوفر";
                            $badgeColor = isset($badge['name']['color']) ? htmlspecialchars($badge['name']['color']) : "badge-default";
                            ?>
                            <div class="text-truncate col-auto ms-2 px-3 py-0 badge-<?php echo $badgeColor; ?>">
                                <?php echo $badgeName; ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <p class="content"><?php echo nl2br($content); ?></p>
            <?php if (!empty($uploaded_files) && is_array($uploaded_files) && !empty($original_file_names) && is_array($original_file_names)) { ?>
                <div class="files overflow-x-scroll d-flex justify-content-start align-items-center gap-2 mb-3 pb-1">
                    <?php
                    foreach ($uploaded_files as $index => $file) {
                        $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                        if (in_array($fileExtension, $video)) {
                            $icon = "fa-file-video";
                        } elseif (in_array($fileExtension, $word)) {
                            $icon = "fa-file-word";
                        } elseif (in_array($fileExtension, $excel)) {
                            $icon = "fa-file-excel";
                        } elseif (in_array($fileExtension, $images)) {
                            $icon = "fa-image";
                        } elseif ($fileExtension == "pdf") {
                            $icon = "fa-file-pdf";
                        } else {
                            $icon = "fa-file";
                        }
                    ?>
                        <div class="file bg-white shadow-sm rounded-pill px-2 py-1">
                            <a href="../../../../assets/files/<?php echo htmlspecialchars($file); ?>"
                                class="fileName d-flex justify-content-center align-items-center text-decoration-none text-black"
                                download>
                                <i
                                    class="fa-solid <?php echo $icon; ?> me-1 fs-5"></i><?php echo htmlspecialchars($original_file_names[$index]); ?>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="post-footer d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2">
                    <?php
                    if ($publisher_id === $user_id) {
                        ?>
                        <!-- Button لتعديل المنشور -->
                        <button class="btn color-secondary edit-post-btn" data-id="<?php echo $postId; ?>"
                            data-title="<?php echo $title; ?>" data-content="<?php echo $content; ?>" data-bs-toggle="modal"
                            data-bs-target="#editPostModal">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <!-- Button لحذف المنشور -->
                        <button class="btn color-secondary delete-post-btn" data-delete-post="<?php echo $postId; ?>"
                            data-bs-toggle="modal" data-bs-target="#deletePostModal_<?php echo $postId; ?>">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                        <?php
                    }
                    ?>
                    <!-- Delete Post Modal -->
                    <div class="modal fade" id="deletePostModal_<?php echo $postId; ?>" tabindex="-1"
                        aria-labelledby="deletePostModalLabel_<?php echo $postId; ?>" role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deletePostModalLabel_<?php echo $postId; ?>">حذف المنشور</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" id="deletePostForm_<?php echo $postId; ?>">
                                    <div class="modal-body">
                                        هل أنت متأكد من رغبتك في حذف هذا المنشور؟
                                        <input type="hidden" name="post_id" value="<?php echo $postId; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-danger confirm-delete-post"
                                            data-bs-dismiss="modal">حذف</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- إضافة أو إزالة المنشور من المفضلة -->
                    <button class="btn color-secondary bookmark-post-btn <?php echo $isBookmarked ? 'bookmarked' : ''; ?>"
                        data-post-id="<?php echo $postId; ?>">
                        <i class="fa-solid fa-bookmark <?php echo $isBookmarked ? 'text-warning' : ''; ?>"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php
    }
} else {
    $message = "";
    $message = match ($filterType) {
        'الرئيسية' => "لا توجد منشورات حتى الآن",
        'منشوراتك' => "لم تقم بنشر أي منشورات",
        'محفوظاتك' => "لم تقم بحفظ منشورات بعد",
        default => "توجد مشكلة في تحميل المنشورات حاول مرة لاحقاً",
    };
    ?>
    <div class="pt-5">
        <h3 class="text-center text-secondary"><?php echo $message; ?></h3>
    </div>
    <?php
}
$stmt->close();
?>