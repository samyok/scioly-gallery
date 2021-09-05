<?php
/**
 *
 * images.php created by samy-oak-tree (2020)
 *
 * This page is loaded in category.php. I opted to make this output raw html instead of a JSON api
 * because HTML editing was easier here than in a JS file.
 *
 */
$startTime = microtime(true);


if (!defined('IN_PHPBB')){
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
}


global $user, $auth, $request;
if (!defined("IN_PHPBB")) {
    define('IN_PHPBB', true);
    $phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
    $phpEx = substr(strrchr(__FILE__, '.'), 1);
    include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
    $user->session_begin();
    $auth->acl($user->data);
    $user->setup();
}


function exception_handler($exception)
{
    echo $exception;
    echo 'done';
    die();
}

set_exception_handler("exception_handler");


// either take the category provided (in ajax) or just use the category id (if not ajax and `include`d).

// provide overrides for gallery user pages

$typeOfImagesRequest = '';

if (defined('IS_GALLERY_USERPAGE') || $request->variable('u', 0)) {
    $pageUserID = isset($pageUserID) ? $pageUserID : $request->variable('u', 1);
    $typeOfImagesRequest = 'user';
} else if (isset($category_id) || $request->variable('c', 0)) {
    $typeOfImagesRequest = 'category';
    $category_id = isset($category_id) ? $category_id : $request->variable('c', 0);
}

$sort = isset($sort) ? $sort : $request->variable('sort', '');
$VALID_SORTS = ['new', 'views', 'score', 'comments', 'images', 'old', ''];
if (!in_array($sort, $VALID_SORTS)) {
    echo 'done';
    exit();
}

$filter = isset($filter) ? $filter : $request->variable('filter', '');
$filter = explode(',', $filter);
foreach ($filter as $key => $item) {
    $filter[$key] = intval($item);
}
$filter = '(' . join(',', $filter) . ')';
if (strlen($filter) < 6)
    $filter = '';
else
    $filter = 'AND YEAR(date) IN ' . $filter;

$NUMBER_OF_RESULTS = 30;
$page_num = intval($request->variable('page', 1));
$offset = $page_num > 0 ? ($page_num - 1) * $NUMBER_OF_RESULTS : 0;

global $db;
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

$user_id = $user->data['user_id'];
$sort_dict = [
    '' => 'ORDER BY date DESC',
    'new' => 'ORDER BY date DESC',
    'score' => 'ORDER BY score DESC',
    'views' => 'ORDER BY post_id DESC',
    'comments' => 'ORDER BY num_replies DESC',
    'images' => 'ORDER BY num_images DESC',
    'old' => 'ORDER BY date ASC'
];

$search = isset($search) ? $search : $request->variable('search', '');
$sort_string = $sort_dict[$sort];
if (strlen($search) > 0) {
    $search = 'AND MATCH(title, description) AGAINST (\'' . $db->sql_escape($search) . '\' IN NATURAL LANGUAGE MODE)';
    // don't use sort if searching :D
    $sort_string = '';
}

$sqlTypeOfRequestString = '';
if ($typeOfImagesRequest == 'user') {
    $sqlTypeOfRequestString = "p.poster_id = $pageUserID";
} else if ($typeOfImagesRequest == 'category') {
    $sqlTypeOfRequestString = "p.category = $category_id";
}

$sql = "(SELECT p.*,
       
       (SELECT Count(image_id)
        FROM gallery_images i
        WHERE p.post_id = i.belongs_to_post
           AND i.is_hidden is not true)             AS num_images,
       
       (SELECT Coalesce(Sum(v.value), 0)
        FROM gallery_votes v
        WHERE p.post_id = v.target_id)              AS score,
       
       (SELECT Coalesce(Sum(v.value), 0)
        FROM gallery_votes v
        WHERE p.post_id = v.target_id
            AND v.source_user = $user_id)           AS user_vote,
       
        (SELECT image_uri 
         FROM gallery_images i
         WHERE p.thumb_image_id = i.image_id # p.thumb_image_id = i.oid OR 
            AND i.is_hidden is not true) AS thumb_image_uri,
            
       (SELECT Count(reply_id)
        FROM gallery_replies r
        WHERE p.post_id = r.post_id 
            AND r.is_hidden is not true)            AS num_replies

        FROM gallery_posts p
        WHERE 
            $sqlTypeOfRequestString
            AND is_hidden IS NOT TRUE 
            $filter
            $search
        $sort_string
        LIMIT $NUMBER_OF_RESULTS OFFSET $offset)
";
//header("SQL: ".base64_encode($sql));
$result = $db->sql_query($sql);
$posts = $db->sql_fetchrowset($result);
?>
<?php
global $no_images;
foreach ($posts as $post) {
    $no_images = false;
    ?>
    <div class="gallery-post-container">
        <div class="gallery-post" data-post-id="<?= $post['post_id']; ?>">
            <div class="gallery-post-img">
                <div class="top-right-triangle" style="
                        position: absolute;
                        right: 0;
                        top:0;
                        color:white;
                        border-left: 75px solid transparent;
                        border-right: 75px solid rgba(0,0,0,0.7);
                        border-bottom: 75px solid transparent;
                        opacity: <?= $post['num_images'] > 1 ? 1 : 0; ?>;
                        "></div>
                <div style="opacity: <?= $post['num_images'] > 1 ? 1 : 0; ?>;
                        position: absolute; top: 0; right:0; font-size: 25px;
                        padding: 10px ; color: white;"><?= $post['num_images']; ?></div>

                <?php
                if (!strpos($post['thumb_image_uri'], "://")) {
                    ?>
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
                         color: white; font-size: 70px; background-color: rgba(0,0,0,0.7); width: 100px; height: 100px;
                         border-radius: 50px; display: flex; justify-content: center; align-items: center">
                        <i class="fa fa-youtube-play" aria-hidden="true"></i>
                    </div>
                    <img
                            src="https://img.youtube.com/vi/<?= $post['thumb_image_uri']; ?>/hqdefault.jpg?q=1"
                            alt=""
                            onload="resizeMasonry();nextImage(this)"
                            data-src="https://img.youtube.com/vi/<?= $post['thumb_image_uri']; ?>/hqdefault.jpg"
                    >
                    <?php
                } else {

                    ?>
                    <img
                            src="https://files.scioly.org/optimize?url=<?= $post['thumb_image_uri']; ?>&w=32&q=1"
                            alt=""
                            onload="resizeMasonry();nextImage(this)"
                            data-src="https://files.scioly.org/optimize?url=<?= $post['thumb_image_uri']; ?>&w=300&q=75"
                    >
                <?php } ?>
                <!--                                <img style="position: absolute; top: 0; left: 0;" alt=""-->
                <!--                                     onload="resizeMasonry();nextImage(this)"-->
                <!--                                     data-src="https://files.scioly.org/optimize?url=-->
                <?//= $post['thumb_image_uri']; ?><!--&w=300&q=75">-->
                <!--                <img-->
                <!--                        class="bkg"-->
                <!--                        src="https://bcdn.scioly.gallery/gallery/-->
                <?//= $post['thumb_image_uri']; ?><!--" alt=""-->
                <!--                        onload="resizeMasonry()">-->
            </div>
            <div class="gallery-post-info">
                <div class="post-about">
                    <div class="gallery-post-title"><?= parse($post['title'], false, false, false); ?></div>
                    <div class="gallery-post-name">
                            <span>
                        <?php if ($pageUserID) {
                            ?>
                            <a class="<?= $post['user_color_class']; ?>"
                               href="category.php?c=<?= $post['category']; ?>"><?= $post['category_name']; ?></a>
                            <?php
                        } else if ($post['poster_id'] > 0) { ?>
                            <a class="<?= $post['user_color_class']; ?>"
                               href="user.php?u=<?= $post['poster_id']; ?>"><?= $post['poster_name']; ?></a>
                            (<?= userScore($post['poster_id']); ?>)
                        <?php } else {
                            ?>Imported from old gallery <?php
                        } ?>
                            </span>
                        <span class="gallery-post-date"><?= date("M d, Y", strtotime($post['date'])); ?></span>
                    </div>
                </div>

                <?php
                $vote_classes = [
                    ($post['user_vote'] == 1 ? 'fa-thumbs-up voted' : 'fa-thumbs-o-up') . ' fa upvote',
                    ($post['user_vote'] == -1 ? 'fa-thumbs-down voted' : 'fa-thumbs-o-down') . ' fa downvote',
                ];
                ?>
                <div class="gallery-post-score">
                    <div class="comment">
                        <i class="fa fa-comments-o no-select"></i> <?= $post['num_replies']; ?>
                    </div>
                    <div class="vote-area">
                        <i class="<?= $vote_classes[0] ?>" role="button" title="Like post"></i>
                        <span class="vote-score"><?= $post['score']; ?></span>
                        <i class="<?= $vote_classes[1] ?>" role="button" title="Dislike post"></i>
                    </div>
<!--                    <div class="link">-->
<!--                        <i class="fa fa-picture-o no-select"></i>-->
<!--                        --><?//= $post['num_images']; ?>
<!--                    </div>-->
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>
