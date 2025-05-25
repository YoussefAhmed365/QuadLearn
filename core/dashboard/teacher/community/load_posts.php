<?php
require '../auth.php';
require '../../../helper_functions.php';

$filterType = $_POST['filterType'] ?? 'الرئيسية'; // تحديد الفلتر الافتراضي
$query = "";
$stmt = null;

switch ($filterType) {
    case 'الرئيسية':
        if ($data['title'] === 'teacher') {
            $query = "SELECT community.id, community.title, community.content, community.badges, community.uploaded_files, community.original_file_names, community.updated_at, users.id as publisher_id, users.first_name, users.last_name, users.account_type,
                    (SELECT COUNT(*) FROM saved_posts WHERE saved_posts.post_id = community.id AND saved_posts.user_id = ?) AS is_bookmarked
                FROM community
                JOIN users ON community.user_id = users.id
                LEFT JOIN assigned_assistants ON assigned_assistants.assistant_id = users.id AND assigned_assistants.teacher_id = ?
                LEFT JOIN assigned_students ON assigned_students.student_id = users.id AND assigned_students.teacher_id = ?
                WHERE assigned_assistants.assistant_id IS NOT NULL 
                   OR assigned_students.student_id IS NOT NULL 
                   OR community.user_id = ?  --  شرط جديد لإدراج مجتمعات المعلم
                ORDER BY community.updated_at DESC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iiii", $user_id, $user_id, $user_id, $user_id);
        } else {
            $query = "SELECT DISTINCT community.id, community.title, community.content, community.badges, community.uploaded_files, community.original_file_names, community.updated_at, users.id as publisher_id, users.first_name, users.last_name, users.account_type,
                    (SELECT COUNT(*) FROM saved_posts WHERE saved_posts.post_id = community.id AND saved_posts.user_id = ?) AS is_bookmarked
                FROM community
                JOIN users ON community.user_id = users.id
                LEFT JOIN assigned_assistants ON assigned_assistants.assistant_id = ?
                LEFT JOIN assigned_students ON assigned_students.teacher_id = assigned_assistants.teacher_id
                WHERE (community.user_id = assigned_assistants.teacher_id) -- المنشورات الخاصة بالمعلم الرئيسي
                   OR (community.user_id = assigned_students.student_id)  -- المنشورات الخاصة بالطلاب المرتبطين بالمعلم
                   OR (community.user_id = ?) -- المنشورات الخاصة بالمساعد نفسه
                ORDER BY community.updated_at DESC";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iii", $user_id, $user_id, $user_id);
        }
        break;

    case 'منشوراتك':
        $query = "
            SELECT community.id, community.user_id as publisher_id, community.title, community.content, community.badges, community.uploaded_files, community.original_file_names, community.updated_at,
            (SELECT COUNT(*) FROM saved_posts WHERE saved_posts.post_id = community.id AND saved_posts.user_id = ?) AS is_bookmarked
            FROM community
            WHERE community.user_id = ?
            ORDER BY community.updated_at DESC";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $user_id);
        break;

    case 'محفوظاتك':
        $query = "
            SELECT community.id, community.user_id as publisher_id, community.title, community.content, community.badges, community.uploaded_files, community.original_file_names, community.updated_at,
            1 AS is_bookmarked
            FROM saved_posts
            JOIN community ON saved_posts.post_id = community.id
            WHERE saved_posts.user_id = ?
            ORDER BY community.updated_at DESC";
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
        $updated_at = (!empty($row['updated_at'])) ? time_elapsed_string(date('Y-m-d H:i:s', strtotime($row['updated_at']))) : "تاريخ غير متوفر";
        $firstName = isset($row['first_name']) ? htmlspecialchars($row['first_name']) : '';
        $lastName = isset($row['last_name']) ? htmlspecialchars($row['last_name']) : '';
        $accountType = isset($row['account_type']) ? htmlspecialchars($row['account_type']) : '';
        $publisher_id = $row['publisher_id'];
        $isBookmarked = (bool) $row['is_bookmarked'];
        ?>
        <div class="post container bg-white shadow-sm rounded-4 py-4 px-5 mb-4" data-id="<?php echo $postId; ?>"
            data-title="<?php echo $title; ?>" data-content="<?php echo $content; ?>"
            data-badges="<?php echo htmlspecialchars(json_encode($badges)); ?>"
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
                <div class="files table-responsive d-flex justify-content-start align-items-center gap-2 mb-3 pb-1">
                    <?php foreach ($uploaded_files as $index => $file) { ?>
                        <div class="file bg-white shadow-sm rounded-pill px-2 py-1">
                            <a href="../../../../assets/files/<?php echo htmlspecialchars($file); ?>"
                                class="fileName d-flex justify-content-center align-items-center text-decoration-none text-black"
                                download>
                                <i
                                    class="fa-solid fa-file-pdf me-1 fs-5"></i><?php echo htmlspecialchars($original_file_names[$index]); ?>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="post-footer d-flex justify-content-between align-items-center">
                <div class="d-flex gap-2">
                    <!-- Button لتعديل المنشور -->
                    <button class="btn color-secondary edit-post-btn" data-id="<?php echo $postId; ?>"
                        data-title="<?php echo $title; ?>" data-content="<?php echo $content; ?>" data-bs-toggle="modal"
                        data-bs-target="#editPostModal">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                    <!-- Button لحذف المنشور -->
                    <button class="btn color-secondary delete-post-btn" data-delete-post="<?php echo $postId; ?>"
                        data-bs-toggle="modal" data-bs-target="#deletePostModal">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                    <!-- Delete Post Modal -->
                    <div class="modal fade" id="deletePostModal" tabindex="-1" aria-labelledby="deletePostModalLabel"
                        role="dialog">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="deletePostModalLabel">حذف المنشور</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form method="POST" id="deletePostForm">
                                    <div class="modal-body">
                                        هل أنت متأكد من رغبتك في حذف هذا المنشور؟
                                        <input type="hidden" name="post_id" id="deletePostId" value="<?php echo $postId; ?>">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-danger confirm-delete-post"
                                            data-post-id="<?php echo $postId; ?>" data-bs-dismiss="modal">حذف</button>
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
    switch ($filterType) {
        case 'الرئيسية':
            $message = "لا توجد منشورات حتى الآن";
            break;

        case 'منشوراتك':
            $message = "لم تقم بنشر أي منشورات";
            break;

        case 'محفوظاتك':
            $message = "لم تقم بحفظ منشورات بعد";
            break;

        default:
            # code...
            break;
    }
    ?>
    <div class="pt-5">
        <h3 class="text-center text-secondary"><?php echo $message; ?></h3>
    </div>
    <?php
}
$stmt->close();
?>