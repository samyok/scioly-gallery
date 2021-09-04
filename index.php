<?php

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include 'include.php';
include 'partials/template.php';

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('');
?>
<html lang="en">
<head>
    <?= headHTML(); ?>
    <link rel="stylesheet" href="css/gallery.css?v=<?= time() ?>">
    <title>Science Olympiad Student Center - Gallery</title>
</head>
<body>

<?= navigationHTML($user); ?>

<div class="container menu-reactive">
    <div class="gallery-control" style="padding: 10px 5px">
        <div class="gal-nav">
            <h1>Gallery</h1>
            <p style="margin: 0">Welcome to the Scioly.org gallery! Before you post, please read the information <a
                        href="here">here</a>.</p>
            <p style="margin: 0">There are <?php
                $sql = 'SELECT count(image_id) as count FROM gallery_images WHERE is_hidden is not true';
                $result = $db->sql_query($sql);
                echo $db->sql_fetchrow($result)['count'];
                ?> pictures and videos.</p>
        </div>
        <div class="controls">
            <a href="add.php" class="thinbtn" title="Add a new post">
                New Post <i class="fa fa-pencil" style="margin-left: 5px"></i>
            </a>
            <?= adminButtons() ?>
            <div class="searchbar">
                <input type="text" placeholder="Search..." class="search" id="searchbar">
                <button style="border-radius:0" id="search_btn"><i class="fa fa-search"></i></button>
            </div>
        </div>
    </div>

    <div class="gallery">
        <?php
        global $db;

        $sql = 'SELECT group_id, group_name FROM gallery_groups';
        $result = $db->sql_query($sql);
        $groups = $db->sql_fetchrowset($result);
        foreach ($groups as $key => $group) {
            ?>

            <h2 class="gallery-section">
                <?= $group['group_name']; ?>
            </h2>

            <?php
            $sql = 'SELECT category_id, category_name FROM gallery_categories WHERE `group_id` = ' . $group['group_id'] . ' AND hidden != 1 ORDER BY category_name';
            $result = $db->sql_query($sql);
            $rows = $db->sql_fetchrowset($result);
            foreach ($rows as $row) {
                $thumbnailSQL = 'SELECT `image_uri` FROM `gallery_images` WHERE `belongs_to_category` = ' . $row['category_id'] . ' AND `is_youtube` is not true AND `is_hidden` is not true ORDER BY RAND() LIMIT 1;';
                $thumbnailRslt = $db->sql_query($thumbnailSQL);
                $thumbnailRows = $db->sql_fetchrowset($thumbnailRslt);
                $thumbnailRow = $thumbnailRows[0];
                $thumbnailImageURI = $thumbnailRow['image_uri'];
                ?>
                <a class="img-tile tile-section-<?= $key ?>" href="category.php?c=<?= $row['category_id']; ?>">

                    <div class="bk-img"
                         style="filter: blur(5px);"
                         data-bkg-src="https://cdn.scioly.gallery/optimize?url=<?php echo $thumbnailImageURI; ?>&w=300&q=75">
                    </div>
                    <div class="bk-img">
                    </div>
                    <div class="tile-name">
                        <span class="name"><?= $row['category_name']; ?></span>
<!--                        <i class="material-icons">fingerprint</i>-->
                    </div>
                </a>
                <?php
            }
        }
        ?>
    </div>
</div>

<?= footerHTML() ?>

<script src="./gallery.js?V=<?= microtime(); ?>"></script>
</body>
</html>
