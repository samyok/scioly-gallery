<?php
define('IN_PHPBB', true);
define('IS_GALLERY_USERPAGE', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include("./include.php");
include 'partials/template.php';
// Start session management

global $db, $user, $auth, $request;

$user->session_begin();
$auth->acl($user->data);
$user->setup('');


$pageUserID = $request->variable('u', 1);

$user_loader = new \phpbb\user_loader($db, $phpbb_root_path, $phpEx, USERS_TABLE);
$avatar = $user_loader->get_avatar($pageUserID, true);

$pageUser = $user_loader->get_user($pageUserID);

if ($pageUser['user_id'] <= 1) include "./lost.php";

function pageUserScore($user_id)
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

$pageUserScore = pageUserScore($pageUserID);

?>
<html lang="en">
<head>
    <title><?=$pageUser['username']?> - Gallery - Scioly.org</title>
    <?= headHTML("https://scioly.org/forums/download/file.php?avatar=".$pageUser['user_avatar'], $pageUser['username']." on Gallery", "See what posts " .$pageUser['username']." has made on the Gallery!", null, true) ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@shopify/draggable@1.0.0-beta.11/lib/sortable.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css"/>
    <link rel="stylesheet" href="css/libs/toastr.min.css">
    <script src="js/toastr.min.js"></script>
    <link href="css/gallery.css" rel="stylesheet">
    <link rel="stylesheet" href="css/libs/lightgallery.min.css">
    <script>
        window.PAGE_USER_ID = <?= $pageUserID ?>;
    </script>
    <script src="js/masonry.pkgd.min.js"></script>
    <script src="js/bodyScrollLock.js?a"></script>
    <script src="voting.js?a"></script>
    <style>
        .lg-outer {
            width: 100%;
            height: calc(100% - 50px);
        }

        .lg-outer.lg-pull-caption-up.lg-thumb-open .lg-sub-html {
            bottom: 150px;
        }

        .lg-outer .lg-inner {
            height: calc(100% - 50px);
            margin-top: 50px;
        }

        #lightroom_banner {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 50px;
            min-height: 0;
            background-color: #555;
            color: #ffffff;
            z-index: 2000;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 0;
        }

        #lightroom_banner a {
            margin: 0
        }
    </style>
    <script src="category.js"></script>
    <script>
        function nextImage(e) {
            // let unloadedYet = Array.from(document.querySelectorAll('[data-src]')).filter(img => img.src !== img.attributes['data-src']);
            // if(!unloadedYet.length) return console.log('done loading everything :)');
            // console.log(unloadedYet[0].src, unloadedYet[0].attributes['data-src']);
            // let img = unloadedYet[0];
            let srcUrl = new URL(e.src)
            console.log({params: srcUrl.searchParams.toString(), src: e.src});
            if (+srcUrl.searchParams.get('q') === 1) e.src = e.getAttribute('data-src');
        }
    </script>
</head>
<body>
<?= navigationHTML($user) ?>
<div class="container gallery-category-container menu-reactive">
    <div id="gallery-width" style="width:100%;height:0px;"></div>
    <?php
    /** Username, Points, Links to other pages **/
    ?>
    <div style="display: flex; align-content: center; margin-bottom: 10px;">
        <?php if ($avatar) { ?>
            <div style="margin-right: 15px;">
                <?= $avatar ?>
                <script>
                    $(".avatar").attr("src", $(".avatar").attr("src").replace("..", "/forums"))
                </script>
            </div>
        <?php } ?>
        <div style="display: flex; justify-content: center; flex-direction: column; align-items: center">
            <h1 style="margin-top: 0;">
                <?= $pageUser['username'] ?>
            </h1>
            <p style="margin: 0;"><b><?= $pageUserScore ?> point<?= $pageUserScore != 1 ? 's' : '' ?></b></p>
            <p style="margin: 0;"><a
                        href="https://scioly.org/forums/memberlist.php?mode=viewprofile&u=<?= $pageUser['user_id'] ?>">Forums</a>
                -
                <a href="https://scioly.org/wiki/index.php/User:<?= $pageUser['username'] ?>">Wiki</a></p>
        </div>
    </div>
    <div class="gallery-control" style="margin-bottom: 15px; width: 100%">
        <div class="controls">
            <?= adminButtons() ?>
            <div class="tags">
                <?php
                //                foreach ($years as $year) {
                //                    ?>
                <!--                    <div class="tag">--><? //= $year['year']; ?><!--</div>-->
                <!--                    --><?php
                //                }
                ?>
            </div>
            <div class="searchbar">
                <label for="sort_by"></label><select name="sort_by" id="sort_by">
                    <option value="new">Newest</option>
                    <option value="old">Oldest</option>
                    <option value="score">Most Votes</option>
                    <option value="comments">Most Comments</option>
                    <option value="images">Most Images/Videos</option>
                    <!--                    <option value="views">Most Views</option>-->
                </select>
                <input type="text" placeholder="Search..." class="search" id="searchbar">
                <button style="margin: 0; border-radius:0" id="search_btn"><i class="fa fa-search"></i></button>
<!--                <button id="tabular_switch" style="margin-left: 5px"><i class="fa fa-th"></i></button>-->
            </div>
        </div>
    </div>
    <div class="gallery-category" style="align-self: center">
        <?php
        $no_images = true;
        /**
         * the 'magical' images.php loading.
         */
        include 'images.php'
        ?>
    </div>
    <div class="no-more-images" style="text-align: center;<?= $no_images ? '' : 'display:none;' ?>">
        There aren't any images in this category. Feel free to upload one!
    </div>
</div>

<div id="carousel" style="display: none"></div>
<div id="lightroom_banner" class="container" style="display: none">
    <a class="button" href="picture.php?p=3222">View post</a>
</div>
<?= footerHTML() ?>
<script src="js/lightgallery-all.min.js"></script>
</body>

</html>