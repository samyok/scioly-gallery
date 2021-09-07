<?php
/**
 * add.php created by samy-oak-tree (2020)
 *
 * This page is responsible for all adding/editing of new posts.
 */
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include("./include.php");
include 'partials/template.php';
// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('');

if ($user->data['user_id'] == ANONYMOUS) {
    login_box('', $user->lang['LOGIN']);
}

$category_id = intval($request->variable('c', 0));
$edit_post = intval($request->variable('edit', 0));
$combination = $request->variable('combine', "");

$category = null;
$category_name = null;
$post = null;
$pics_array = [];
if ($edit_post) {
    // first see if post exists
    $sql = 'SELECT * FROM gallery_posts WHERE ' . $db->sql_build_array('SELECT', ['post_id' => $edit_post]);
    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);

    if (!$row) include 'lost.php';

    // post exists! check if they have perms to edit.
    $has_permission_to_edit = is_admin($user) || $user->data['user_id'] == $row['poster_id'];
    if (!$has_permission_to_edit) include 'lost.php';

    // we're good :)
    $category_name = $row['category_name'];
    $category_id = $row['category'];
    $post = $row;

    $showHidden = is_admin($user) ? '' : 'AND is_hidden is not true';

    $sql = "SELECT * FROM gallery_images WHERE belongs_to_post = $edit_post $showHidden ORDER BY image_index, image_id";
    $result = $db->sql_query($sql);
    $rows = $db->sql_fetchrowset($result);
    foreach ($rows as $row) {
        if ($row['is_youtube']) $row['video_id'] = $row['image_uri'];
        $row['isYoutube'] = $row['is_youtube'];
//        $exif = exif_read_data($row['image_uri'], 0, true);
//        foreach($exif as $key => $section){
//            foreach( $section as $name => $val){
//                echo "$section.$name = $val";
//                echo "\n";
//            }
//        }
        array_push($pics_array, $row);
    }

    $combining_post_ids = explode(',', $combination);
    if(sizeof($combining_post_ids) > 0 && is_admin($user)) {
        foreach ($combining_post_ids as $combining_post_id) {
            if(strlen($combining_post_id) === 0) continue;
            // get data, including images
            $sql = 'SELECT * FROM gallery_posts WHERE ' . $db->sql_build_array('SELECT', ['post_id' => $combining_post_id]);

            $rslt = $db->sql_query($sql);
            $combining_post_info = $db->sql_fetchrow($rslt);
            $post['title'] .= " & ". $combining_post_info['title'];
            $post['description'] .= "\n\n&\n\n". $combining_post_info['description'];



            $sql = "SELECT * FROM gallery_images WHERE belongs_to_post = $combining_post_id $showHidden ORDER BY image_index, image_id";
            $result = $db->sql_query($sql);
            $rows = $db->sql_fetchrowset($result);
            foreach ($rows as $row) {
                if ($row['is_youtube']) $row['video_id'] = $row['image_uri'];
                array_push($pics_array, $row);
            }

        }
    }
} else {
    $category = getSQLRows("SELECT `category_name` FROM `gallery_categories` WHERE `category_id` = " . $category_id);
    $category_name = $category[0]['category_name'];
}
?>
<html>
<head>
    <title>Add Post - Science Olympiad Student Center - Gallery</title>
    <?= headHTML() ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@shopify/draggable@1.0.0-beta.11/lib/sortable.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css"/>
    <link rel="stylesheet" href="https://bcdn.scioly.gallery/gallery/css/libs/toastr.min.css">
    <script src="https://bcdn.scioly.gallery/gallery/js/toastr.min.js"></script>
    <link href="https://bcdn.scioly.gallery/gallery/css/gallery.css?v=<?= time() ?>" rel="stylesheet">
    <script>
        window.PICTURES = <?= json_encode($pics_array) ?>;
        window.EDIT_POST = <?= $edit_post ?>;
        window.COMBINING = <?= json_encode($combination) ?>;
        window.IS_ADMIN = <?= is_admin($user) ? 'true' : 'false' ?>;
        window.ALL_CATEGORIES = true;
        let resizeMasonry = () => {};
    </script>
</head>
<body>
<?= navigationHTML($user) ?>
<?= reportBox() ?>
<div class="wrapper">
    <div class="container" id="post-writing-container">
        <div class="go-back">
            <a href="index.php">Categories</a> &lt;
            <a class="category_link" href="category.php?c=<?= $category_id; ?>"><span
                        class="category_name"><?= $category_name; ?></span></a> &lt;
            <?= $post ? 'Edit' : 'New'; ?> Post
            <span style="display:none" class="category_id"><?= $category_id; ?></span>
        </div>

        <div class="info-area">
            <div class="title-area">
                <textarea id="post-title" oninput="auto_grow(this)" placeholder="Title"
                          type="text"><?= $post['title']; ?></textarea>
            </div>
        </div>
        <div class="add-pics">
            <div class="pics"></div>
            <button class="add-yt-btn" href="#ex1" rel="modal:open">Add Youtube Video</button>
            or
            <input placeholder="Upload File" type="file" id="load_upload" multiple>
        </div>
        <label for="post-input">Description</label>
        <textarea id="post-input" oninput="auto_grow(this)" onclick="auto_grow(this)" class="borderless"
                  placeholder="Post Description: Describe your post here!"><?= $post['description']; ?></textarea>
        <label for="category_list">Select Category</label><br>
        <select name="possible_categories" id="category_list">
            <?php
            $sql = 'SELECT group_id, group_name FROM gallery_groups';
            $result = $db->sql_query($sql);
            $groups = $db->sql_fetchrowset($result);
            foreach ($groups as $group) {
                ?>
                <optgroup label="<?= $group['group_name']; ?>">
                    <?php
                    $sql = 'SELECT category_id, category_name FROM gallery_categories WHERE `group_id` = ' . $group['group_id'] . ' ORDER BY category_name';
                    $result = $db->sql_query($sql);
                    $rows = $db->sql_fetchrowset($result);
                    foreach ($rows as $row) {
                        ?>
                        <option value="<?= $row['category_id']; ?>"><?= $row['category_name']; ?></option>
                        <?php
                    }
                    ?>
                </optgroup>
                <?php
            }
            ?>
        </select>
        <br><br>

        <?php if ($post) { ?>

            <label for="post_date">Date</label>
            <input name="date" id="post_date" type="date"
                   value="<?= $post ? date("Y-m-d", strtotime($post['date'])) : '' ?>">


            <label for="edit-reason">Edit reason</label>
            <textarea id="edit-reason" oninput="auto_grow(this)" class="borderless"
                      placeholder="Why did you edit? (optional)"></textarea>

            <br><br>
            <button class="cancel_edits">Cancel edits</button>
            <button class="save-edit">Save edits</button>
        <?php } else { ?>
            <button class="post-reply">Post into <span class="category_name"><?= $category_name; ?></span></button>
        <?php } ?>
        <br>
        <p>BBCode is enabled! Use the 'caption' tag to add captions to your image. For example, [caption=3]This is a cool caption[/caption] would add a caption to the 3rd image in your post.</p>
    </div>
</div>
<div class="modals">
    <!-- Modal HTML embedded directly into document -->
    <div id="ex1" class="modal">
        <h4>Upload YouTube Video</h4>
        <img src="" alt="" id="youtube_video_thumbnail" class="hidden">
        <input type="text" id="youtube_modal_input"
               placeholder="YouTube Video URL (https://www.youtube.com/watch?v=ih5ts1Kzglk)">
        <div class="flex">
            <button id="load_youtube_video">Load Video</button>
            <a id="youtube_modal_close" href="#" rel="modal:close">Cancel</a>
        </div>
    </div>
</div>
<?= footerHTML() ?>
<script src="https://bcdn.scioly.gallery/gallery/js/sweetalert2.all.min.js"></script>

<script src="add.js?v=<?= time() ?>"></script>
<script src="https://dev.scioly.gallery/lib.js?v=<?= time() ?>"></script>
</body>

</html>