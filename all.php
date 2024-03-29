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

$sql = "SELECT COUNT(image_id) as num_pics FROM gallery_images WHERE is_hidden is not true";
$result = $db->sql_query($sql);
$num_pics = $db->sql_fetchrow($result)['num_pics'];

$sql = "SELECT DISTINCT(YEAR(date)) as year FROM gallery_posts WHERE is_hidden is not true ORDER BY year";
$result = $db->sql_query($sql);
$years = $db->sql_fetchrowset($result);
?>
<html lang="en">
<head>
    <?= headHTML() ?>
    <title>All Posts - Science Olympiad Student Center - Gallery</title>
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
    <script>
        if(!window.resizeMasonry) resizeMasonry = () => {};
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
                <a href="index.php">Categories</a> &lt; All Categories
            </div>
            <h1 style="display:flex;align-items: center">All Categories</h1>
            <p style="margin: 0;">Past builds most likely do not conform to this year's rules. <br>There
                are <?= $num_pics ?> images, but we want more! Click <a
                        href="add.php">here</a> to contribute.</p>
        </div>
        <div class="controls">
            <a href="add.php" class="thinbtn" title="Add a new post" id="addNewPost">
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