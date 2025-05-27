<?php
// Get posts according to specific subject and teacher.
require '../auth.php';
require '../../../helper_functions.php';

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $subject = ['subjectName'];
    $teacher_id = ['teacherId'];

    // هنا استخدم الاتصال بقاعدة البيانات لجلب المنشورات الخاصة بالموضوع والمعلم.
    $stmt = $conn->prepare("SELECT c.id, c.title, c.content, c.badges, c.uploaded_files, c.original_file_names, c.updated_at, u.id AS publisher_id, u.first_name, u.last_name, u.account_type,
                                (SELECT COUNT(*) FROM saved_posts AS sp WHERE sp.post_id = c.id AND sp.user_id = ?) AS is_bookmarked
                            FROM community AS c
                            JOIN users AS u ON c.user_id = u.id
                            WHERE c.user_id = ?
                                OR c.user_id IN (
                                    SELECT assistant_id
                                    FROM assigned_assistants
                                    WHERE teacher_id = ?
                                )
                                OR c.user_id IN (
                                    SELECT student_id
                                    FROM assigned_students
                                    WHERE teacher_id = ?
                                )
                            ORDER BY c.updated_at DESC");
    $stmt->bind_param("iiii", $user_id, $teacher_id, $teacher_id, $teacher_id);
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
                                    case "teacher":
                                        echo '<i class="fa-solid fa-graduation-cap"></i>';
                                        break;
                                    default:
                                        echo '<i class="fa-solid fa-user"></i>';
                                        break;
                                }
                                ?>
                                <!-- <img class="rounded-4 border border-4 border-white" src="../../../../assets/images/<?php echo $data["subject"]; ?>.webp" alt="subject"> -->
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
                                $badgeName = isset($badge["name"]["name"]) ? htmlspecialchars($badge["name"]["name"]) : "اسم غير متوفر";
                                $badgeColor = isset($badge["name"]["color"]) ? htmlspecialchars($badge["name"]["color"]) : "badge-default";
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
                        <?php
                        if ($publisher_id === $user_id) {
                            echo '<button class="btn color-secondary edit-post-btn" data-edit-post="<?php echo $postId; ?>">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                                <button class="btn color-secondary delete-post-btn" data-delete-post="<?php echo $postId; ?>">
                                    <i class="fa-solid fa-trash"></i>
                                </button>';
                        }
                        ?>
                        <!-- إضافة أو إزالة المنشور من المفضلة -->
                        <button class="btn color-secondary bookmark-post-btn <?php echo $isBookmarked ? "bookmarked" : ""; ?>"
                            data-post-id="<?php echo $postId; ?>">
                            <i class="fa-solid fa-bookmark <?php echo $isBookmarked ? "text-warning" : ""; ?>"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<h6 class='text-center text-secondary'>لم يقم هذا المعلم بإضافة منشورات بعد</h6>";
    }
    $stmt->close();
}