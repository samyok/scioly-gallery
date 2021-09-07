<?php
/**
 * category.php created by samy-oak-tree (2019)
 *
 * This page does the bare-bones templating for category.php, most of the complex stuff is just loaded from images.php.
 */
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include 'include.php';
include 'partials/template.php';
// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

//
//if ($user->data['user_id'] == ANONYMOUS) {
//    login_box('', $user->lang['LOGIN']);
//}

$category_id = intval($request->variable('c', 0));
$category_id = (int)$category_id;
$category_name = '';
if ($category_id > 0) {
    $sql = 'SELECT * FROM `gallery_categories` WHERE ' . $db->sql_build_array('SELECT', ['category_id' => $category_id]);
    $result = $db->sql_query($sql);
    if (mysqli_num_rows($result) == 0) {
        include 'lost.php';
    }

    $rows = $db->sql_fetchrowset($result);
    $category = $rows[0];
    $category_name = $rows[0]['category_name'];
} else {
    include 'lost.php';
}
$sql = "SELECT COUNT(image_id) as num_pics FROM gallery_images WHERE is_hidden is not true AND belongs_to_category = $category_id";
$result = $db->sql_query($sql);
$num_pics = $db->sql_fetchrow($result)['num_pics'];

$sql = "SELECT DISTINCT(YEAR(date)) as year FROM gallery_posts WHERE is_hidden is not true and category = $category_id ORDER BY year";
$result = $db->sql_query($sql);
$years = $db->sql_fetchrowset($result);
?>
<html lang="en">
<head>
    <?= headHTML(null, $category_name, "Submissions on the Scioly.org Gallery do not necessarily follow current rules for the event. Always refer to the Rules Manual!") ?>
    <title><?= $category_name; ?> - Science Olympiad Student Center - Gallery</title>
    <link rel="stylesheet" href="https://bcdn.scioly.gallery/gallery/css/libs/toastr.min.css">
    <script src="https://bcdn.scioly.gallery/gallery/js/toastr.min.js"></script>
    <link rel="stylesheet" href="https://bcdn.scioly.gallery/gallery/css/libs/lightgallery.min.css">

    <script src="https://bcdn.scioly.gallery/gallery/js/masonry.pkgd.min.js"></script>
    <script src="https://bcdn.scioly.gallery/gallery/js/bodyScrollLock.js?a"></script>
    <script src="voting.js?a"></script>
    <script>
        window.CATEGORY_ID = <?= $category_id; ?>;
        if (typeof resizeMasonry === "undefined") window.resizeMasonry = () => {
        }
    </script>
    <link rel="stylesheet" href="css/gallery.css">
    <style>
        .lg-outer {
            width: 100%;
            height: calc(100% - 50px);
        }

        .lg-sub-html {
            bottom: 50px;
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
            background-color: #0D4473;
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
    <script>
        function nextImage(e) {
            // let unloadedYet = Array.from(document.querySelectorAll('[data-src]')).filter(img => img.src !== img.attributes['data-src']);
            // if(!unloadedYet.length) return console.log('done loading everything :)');
            // console.log(unloadedYet[0].src, unloadedYet[0].attributes['data-src']);
            // let img = unloadedYet[0];
            let srcUrl = new URL(e.src)
            if (+srcUrl.searchParams.get('q') === 1) e.src = e.getAttribute('data-src');
        }
    </script>
</head>
<body>
<?= navigationHTML($user) ?>
<div class="container gallery-category-container menu-reactive">
    <div id="gallery-width" style="width:100%;height:0px;"></div>
    <div class="gallery-control" style="margin-bottom: 15px; width: 100%">
        <div class="gal-nav">
            <div class="go-back">
                <a href="index.php">Categories</a> &lt; <?= $category_name ?>
            </div>
            <h1 style="display:flex;align-items: center"><?= $category_name ?> <a
                        href="https://scioly.org/wiki/index.php/<?= $category_name ?>" target="_blank"
                        style="color:black; font-size: .7em; margin-left: 10px;"><i class="fa fa-external-link"
                                                                                    aria-hidden="true"></i></a></h1>
            <p style="margin: 0;">Submissions do not necessarily follow current rules for the event. Always refer to the <a href="https://www.soinc.org/rules-2022" target="_blank">Rules Manual</a>. <br>There
                are <?= $num_pics ?> images in
                this<?= $category['hidden'] ? ' <b>hidden</b> ' : ' ' ?>album, but we want more! Click <a
                        href="add.php?c=<?= $category_id; ?>">here</a> to contribute.</p>
        </div>
        <div class="controls">
            <a href="add.php?c=<?= $category_id ?>" class="thinbtn" title="Add a new post" id="addNewPost">
                New Post <i class="fa fa-pencil" style="margin-left: 5px"></i>
            </a>
            <?= adminButtons() ?>
            <div class="tags">
                <?php
                foreach ($years as $year) {
                    ?>
                    <div class="tag"><?= $year['year']; ?></div>
                    <?php
                }
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
         * the magical images.php loading.
         */
        include 'images.php'
        ?>
    </div>
    <div class="no-more-images" style="text-align: center;<?= $no_images ? '' : 'display:none;' ?>">
        There aren't any images in this category. Feel free to upload one!
    </div>
    <div class="combine-posts thinbtn" style="display: none">Combine Posts</div>
</div>

<div id="carousel" style="display: none"></div>
<div id="lightroom_banner" class="container" style="display: none">
    <a class="button" href="picture.php?p=3222">View post</a>
</div>
<?= footerHTML() ?>
<script src="https://bcdn.scioly.gallery/gallery/js/lightgallery-all.min.js"></script>
<script src="category.js?v=<?= microtime() ?>"></script>
</body>

</html>