<?php
/**
 * picture.php created by samy-oak-tree (2020)
 *
 * TODO rename to post.php/post.js
 * the 'single post' view
 */
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include_once("./include.php");
include 'partials/template.php';
// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('');

//if ($user->data['user_id'] == ANONYMOUS) {
//    login_box('', $user->lang['LOGIN']);
//}

$post_id = intval($request->variable('p', 0));

if ($post_id > 0) {
    $sql = 'SELECT * FROM `gallery_posts` WHERE `post_id` = ' . $post_id;
    $result = $db->sql_query($sql);
    $rows = $db->sql_fetchrowset($result);
    if (mysqli_num_rows($result) == 0 || (!is_admin($user) && $rows[0]['is_hidden'])) {
        include 'lost.php';
    }
    $post = $rows[0];
} else {
    include 'lost.php';
}

$userPointRows = getSQLRows("SELECT * FROM `gallery_votes` WHERE `target_user_id` = " . $post['poster_id']);;

$images = getSQLRows('SELECT * FROM `gallery_images` WHERE `belongs_to_post` = ' . $post_id . ' ORDER BY image_index');

function imageHTML($images)
{
    $thumbnails = [];
    $image_uris = [];
    foreach ($images as $img_key => $image) {
        if ($image['is_hidden'] && !is_admin($user)) continue;
        if ($image['is_youtube']) {
            ?>
            <a href="https://www.youtube.com/watch?v=<?= $image['image_uri']; ?>"
               data-sub-html="#caption-slide-<?= $img_key + 1 ?>"
               class="video <?= $image['is_hidden'] ? 'deleted' : '' ?>">
                <div class="video-thumbnail">
                    <img class="bkg"
                         src="https://img.youtube.com/vi/<?= $image['image_uri']; ?>/hqdefault.jpg" alt="">
                </div>
            </a>
            <?php
        } else {
            ?>
            <a data-sub-html="#caption-slide-<?= $img_key + 1 ?>"
               href="<?= $image['image_uri']; ?>" class="<?= $image['is_hidden'] ? 'deleted' : '' ?>">
                <img class="bkg"
                     src="<?= $image['image_uri']; ?>" alt="post image">
            </a>
            <?php
        }
    }
}

if ($request->variable('images_only', false)) {
    echo "<div class='pics'>";
    imageHTML($images);
    echo "</div>";
    echo parse($post['description'], true, true, true);
    exit();
}
/* get score for the post */
$sql = 'SELECT SUM(value) as score, SUM(IF(source_user = ' . $user->data['user_id'] . ', value, 0)) as user_vote FROM gallery_votes WHERE ' . $db->sql_build_array('SELECT', [
        'target_id' => $post_id,
        'type' => 'post'
    ]);
$result = $db->sql_query($sql);
$vote = $db->sql_fetchrow($result);

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

// basically an int typecast
$vote['score'] += 0;
$vote_classes = [
    ($vote['score'] == 1 ? 'fa-thumbs-up voted' : 'fa-thumbs-o-up') . ' fa upvote',
    ($vote['score'] == -1 ? 'fa-thumbs-down voted' : 'fa-thumbs-o-down') . ' fa downvote',
];
$header_image_uri = $images[0]['image_uri'];
if ($images[0]['is_youtube']) {
    $header_image_uri = "https://img.youtube.com/vi/" . $images[0]['image_uri'] . "/hqdefault.jpg";
}
?>
<html>
<head>
    <title><?= $post['title']; ?> - Science Olympiad Student Center - Gallery</title>
    <?= headHTML($header_image_uri, "&quot;" . trim($post['title']) . "&quot; (" . sizeof($images) . ") by " . trim($post['poster_name']), trim($post['description']), $post['category_name'] . " - Scioly.org Gallery", true) ?>
    <link rel="stylesheet" href="css/libs/lightgallery.min.css">
    <link rel="stylesheet" href="css/libs/toastr.min.css">
    <script src="js/toastr.min.js"></script>
    <link rel="stylesheet" href="css/gallery.css?v=<?= time(); ?>">
    <script src="voting.js?a"></script>
    <!--    <script type="text/javascript" src="http://livejs.com/live.js"></script>-->
    <!--    <script src="https://masonry.desandro.com/masonry.pkgd.js"></script>-->
    <script>window.POST_ID = <?php echo $post_id; ?>;</script>
</head>
<body>
<?= navigationHTML($user); ?>
<?= reportBox() ?>
<div class="container menu-reactive" id="carousel-container">
    <div class="go-back">
        <a href="index.php">Categories</a> &lt;
        <a href="category.php?c=<?= $post['category']; ?>"
           class="go-back-category"><?= $post['category_name']; ?></a> &lt;
        <span class="truncate"><?= parse($post['title'], false, false, false); ?></span>
    </div>
    <div class="confirm_edits" style="display:none">
        <button id="save_edits">Save Edits</button>
        <button id="cancel_edits">Cancel Edits</button>
    </div>
    <?php if ($post['is_hidden']) { ?>
        <h1>Post has been deleted. Only admins can see this page.</h1>
    <?php } ?>
    <div class="info-area">
        <div class="vote-area">
            <i class="<?= $vote_classes[0] ?>" role="button" title="Like post"></i>
            <span class="vote-score"><?= $vote['score']; ?></span>
            <i class="<?= $vote_classes[1] ?>" role="button" title="Dislike post"></i>
        </div>
        <div class="title-area">
            <h1 class="img-title"><?= parse($post['title'], false, false, false); ?></h1>
            <div class="author">
                <?php if ($post['poster_id'] == 0) {
                    echo "";
                } else { ?>
                    <a class="<?= $post['user_color_class'] ?>"
                       href="user.php?u=<?= $post['poster_id']; ?>"><?= $post['poster_name']; ?></a>
                    (<?= userScore($post['poster_id']); ?>)
                <?php } ?>
                <span class="date"><?= date("F d, Y", strtotime($post['date'])); ?></span>
            </div>
            <div class="action-btns">
                <div class="reply">
                    Reply
                </div>
                <?php
                if (is_admin($user) || $user->data['user_id'] == $post['poster_id']) {

                    if (!$post['is_hidden']) {
                        ?>
                        <div class="is_editable" style="display:none">1</div>
                        <div class="edit-post">
                            Edit
                        </div>
                        <div class="delete-post">
                            Delete
                        </div>
                        <?php
                    }
                }
                if (is_admin($user) && $post['is_hidden'] == true) {
                    ?>
                    <div style="flex-grow: 1"></div>

                    <div class="restore-post">
                        Restore
                    </div>
                    <?php
                }
                ?>

                <div class="report">
                    Report
                </div>
            </div>
        </div>
    </div>

    <div id="report-area">

        <label for="report-reason">Report reason</label>
        <textarea id="report-reason" oninput="auto_grow(this)"
                  placeholder="Typically only rule-breaking posts are removed!"></textarea>
        <button id="send_report">Send Report</button>
    </div>
    <div id="carousel">
        <?php
        $thumbnails = [];
        $image_uris = [];
        foreach ($images as $img_key => $image) {
            if ($image['is_hidden'] && !is_admin($user)) continue;
            if ($image['is_youtube']) {
                ?>
                <a href="https://www.youtube.com/watch?v=<?= $image['image_uri']; ?>"
                   data-sub-html=".description #caption-slide-<?= $img_key + 1 ?>"
                   class="video <?= $image['is_hidden'] ? 'deleted' : '' ?>">
                    <div class="video-thumbnail">
                        <img class="bkg <?= sizeof($images) === 1 ? "only-image" : "" ?>"
                             src="https://img.youtube.com/vi/<?= $image['image_uri']; ?>/hqdefault.jpg" alt="">
                    </div>
                </a>
                <?php
            } else {
                ?>
                <a href="<?= $image['image_uri']; ?>"
                   data-sub-html=".description #caption-slide-<?= $img_key + 1 ?>"
                   class="<?= $image['is_hidden'] ? 'deleted' : '' ?>">
                    <img class="bkg <?= sizeof($images) === 1 ? "only-image" : "" ?>" src="<?= $image['image_uri']; ?>"
                         alt="">
                </a>
                <?php
            }
        }
        ?>
    </div>

    <div class="description">
        <?= parse($post['description'], true, true, true); ?>
    </div>
    <div class="comment-wrap">
        <div class="comment-add">
            <textarea id="comment-add-text" oninput="auto_grow(this)" placeholder="Add comment..."></textarea>
            <button class="post-reply" style="margin: 10px 0;">Post Reply</button>
        </div>
        <?php
        $is_hidden_string = 'is_hidden is not true and';
        if (is_admin($user)) $is_hidden_string = '';
        $comments = getSQLRows("SELECT * FROM `gallery_replies` WHERE $is_hidden_string `post_id` =  $post_id");
        foreach ($comments as $comment) {
            $comment_text = parse($comment['text'], true, true, true);
            ?>
            <div class="comment <?= $comment['is_hidden'] ? 'hidden' : '' ?>" id="c<?= $comment['reply_id']; ?>">
                <p class="comment-text"><?= $comment['is_hidden'] ? '<b>This comment has been deleted.</b><br>' . $comment_text : $comment_text; ?></p>
                <div class="action-area">
                    <div class="author">
                        <a class="<?= $comment['user_color_class']; ?>"
                           href="user.php?u=<?= $comment['author_id']; ?>">
                            <?php if ($post['poster_id'] == $comment['author_id']) {
                                ?> <i class="fa fa-camera"></i> <?php
                            }
                            echo $comment['author_name']; ?></a>
                        <span class="comment-info">(<?php echo userScore($comment['author_id']); ?>)</span>
                        <span class="comment-info date"
                              data-reply-id="<?= $comment['reply_id'] ?>"><?= date("F d, Y h:m A", strtotime($comment['timestamp'])); ?></span>
                        <?php if (is_admin($user) && !$comment['is_hidden']) { ?>
                            <span class="comment-info delete right" data-reply-id="<?= $comment['reply_id'] ?>">
                                Delete</span>
                        <?php } else if (is_admin($user) && $comment['is_hidden']) { ?>
                            <span class="comment-info restore right" data-reply-id="<?= $comment['reply_id'] ?>">
                                Restore</span>
                        <?php } else { ?>
                            <span class="comment-info report right"
                                  data-reply-id="<?= $comment['reply_id'] ?>">Report</span>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<?= footerHTML() ?>
<script src="https://bcdn.scioly.gallery/gallery/js/lightgallery-all.min.js"></script>
<script src="https://bcdn.scioly.gallery/gallery/js/sweetalert2.all.min.js"></script>
<script src="picture.js?v=<?= time(); ?>"></script>
</body>

</html>