<?php
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include("./include.php");
include 'partials/template.php';
// Start session management

global $db, $user, $auth;

$user->session_begin();
$auth->acl($user->data);
$user->setup('');

if (!is_admin($user)) include 'lost.php';

$sql = 'SELECT * FROM gallery_reports WHERE active = true';
$result = $db->sql_query($sql);
$rows = $db->sql_fetchrowset($result);

$user_scores = [];
function userScore($user_id)
{
    global $db;
    if (isset($user_scores[$user_id])) return $user_scores[$user_id];
    else {
        $sql = 'SELECT SUM(value) as user_score FROM gallery_votes WHERE ' . $db->sql_build_array('SELECT', [
                'target_user_id' => (int)$user_id
            ]);
        $result = $db->sql_query($sql);
        $user_vote = (int)$db->sql_fetchrow($result)['user_score'];
        $user_scores[$user_id] = $user_vote;
        return $user_vote;
    }
}

?>
<html lang="en">
<head>
    <title>Reports - Science Olympiad Student Center - Gallery</title>
    <?= headHTML() ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@shopify/draggable@1.0.0-beta.11/lib/sortable.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css"/>
    <link rel="stylesheet" href="css/libs/toastr.min.css">
    <script src="js/toastr.min.js"></script>
    <link href="css/gallery.css" rel="stylesheet">
</head>
<body>
<?= navigationHTML($user) ?>
<div class="container menu-reactive" id="carousel-container">
    <div class="go-back">
        <a href="index.php">Gallery</a> &lt; Reports
    </div>
    <h1>Active Reports: <?= sizeof($rows); ?></h1>
    <p><a href="modlog.php">View modlog</a></p>
    <p><a href="admin.php">Admin Dashboard</a></p>
    <p><a href="old_reports.php">View inactive reports</a></p>
    <?php
    foreach ($rows as $row) {
        // we need to sql for each row
        if ($row['target_type'] == 'post') {
            $sql = 'SELECT * FROM gallery_posts WHERE post_id = ' . $row['target_id'];
            $result = $db->sql_query($sql);
            $post = $db->sql_fetchrow($result);
            $images = getSQLRows('
                            SELECT * FROM `gallery_images` 
                            WHERE `belongs_to_post` = ' . $post['post_id'] . ' 
                                AND is_hidden is not true 
                            ORDER BY image_index');
            $sql = 'SELECT COUNT(image_id) as count FROM `gallery_images` 
                            WHERE `belongs_to_post` = ' . $post['post_id'] . ' 
                                AND is_hidden is true';
            $result = $db->sql_query($sql);
            $num_hidden_images = (int)$db->sql_fetchrow($result)['count'];
            ?>
            <div class="report-card" id="report-<?= $row['report_id']; ?>">
                <div class="post-info">
                    <?php if ($post['is_hidden']) { ?>
                        <h3>POST HAS BEEN DELETED</h3>
                    <?php } ?>
                    <h2><?= $post['title']; ?></h2>
                    <p><?= replaceNewLines($post['description']); ?></p>
                    <p><b>Number of images:</b> <?= sizeof($images) ?> in post - <?= $num_hidden_images ?> deleted.</p>
                    <p><b>User:</b> <a href="/user.php?u=<?= $post['poster_id']; ?>"><?= $post['poster_name']; ?></a>
                        (<?= userScore($post['poster_id']) ?>)</p>
                    <p><b>Category:</b> <?= $post['category_name']; ?>
                        (<?= $post['category']; ?>-<?= $post['post_id']; ?>)</p>
                </div>
                <div class="report-info">
                    <p><b>Reported by:</b> <a
                                href="/user.php?u=<?= $row['reporter_id'] ?>"><?= $row['reporter_name']; ?></a>
                        (<?= userScore($post['poster_id']) ?>)</p>
                    <p><b>Report reason:</b> <?= $row['reason_for_report']; ?></p>
                    <p><b>Report ID:</b> <?= $row['report_id']; ?></p>
                    <p><b>Reported Time:</b> <?= $row['timestamp']; ?></p>
                </div>
                <div class="controls">
                    <div style="display: flex; flex-wrap: wrap;">
                        <?php
                        foreach ($images as $key => $image) {
                            $image_url = $image['image_uri'];
                            if ($image['is_youtube']) $image_url = 'https://img.youtube.com/vi/' . $image['image_uri'] . '/default.jpg';

                            ?>
                            <a style="align-self: center" class="<?= $image['is_youtube'] ? 'video-thumbnail' : ''; ?>"
                               href="picture.php?p=<?= $post['post_id']; ?>#lg=1&slide=<?= $key; ?>">
                                <img style="margin: 10px; height: 150px;" src="<?= $image_url ?>" alt="">
                            </a>
                            <?
                        }
                        ?>
                    </div>
                    <a class="gray-button" href="picture.php?p=<?= $post['post_id']; ?>" target="_blank">View
                        Context</a>
                    <a class="gray-button" href="add.php?edit=<?= $post['post_id']; ?>">Edit Post</a>
                    <button onclick="delete_post(<?= $post['post_id'] ?>)">Delete Post</button>
                    <button onclick="clear_report(<?= $row['report_id'] ?>)">Clear Report</button>
                </div>
            </div>
            <?php
        }
    }
    ?>
</div>
<?= footerHTML() ?>
<script src="js/sweetalert2.all.min.js"></script>

<script src="reports.js"></script>
</body>

</html>