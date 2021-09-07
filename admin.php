<?php
/**
 * admin.php created by samy-oak-tree (2020)
 *
 * Admin Dashboard -- do all sorts of ad-hoc admin tasks that are necessary for the long-term maintenance.
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
$user->setup('');
if (!is_admin($user)) include './lost.php';

?>
<html lang="en">
<head>
    <?= headHTML(); ?>
    <link rel="stylesheet" href="css/gallery.css">
    <title>Science Olympiad Student Center - Gallery</title>
    <style>
        .hidden {
            display: none;
            padding: 20px;
            background-color: #efefef;
        }

        .opened h2 {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<?= navigationHTML($user); ?>
<?= reportBox() ?>

<div class="container menu-reactive"  id="carousel-container">
    <div class="go-back">
        <a href="index.php">Gallery</a> &lt; Admin
    </div>
    <h1>Admin Dashboard</h1>
    <p><a href="modlog.php">View modlog</a></p>
    <p><a href="reports.php">Reports</a></p>
    <p><a href="old_reports.php">View inactive reports</a></p>
    <a href="#categories"><h2 class="collapsible">Categories</h2></a>
    <div class="hidden" id="categories">
        <button onclick="addEvent()">Add Event</button>
        <?php
        global $db;

        $sql = 'SELECT group_id, group_name FROM gallery_groups';
        $result = $db->sql_query($sql);
        $groups = $db->sql_fetchrowset($result);
        foreach ($groups as $group) {
            ?>
            <div>
                <h2 class="group" group-id="<?= $group['group_id'] ?>" contenteditable="true"
                    style="background-color: rgba(0,0,0,0.05); padding: 10px 5px">
                    <?= $group['group_name']; ?>
                </h2>
            </div>
            <?php

            $sql = 'SELECT * FROM gallery_categories WHERE `group_id` = ' . $group['group_id'] . ' ORDER BY category_name';
            $result = $db->sql_query($sql);
            $rows = $db->sql_fetchrowset($result);
            foreach ($rows as $row) {
                ?>
                <div style="display: flex; align-items: center; justify-content: center" class="category">
                    <a href="category.php?c=<?= $row['category_id'] ?>"
                       target="_blank"
                       style="width: 25px; text-align: center;"><?= $row['category_id'] ?></a>
                    <button onclick="moveCategory(<?= $row['category_id'] ?>)" style="margin-right: 5px">Move</button>
                    <button onclick="toggleCategory(<?= $row['category_id'] ?>)"><?= $row['hidden'] ? 'Show' : 'Hide' ?></button>
                    <p
                            class="category-name"
                            category-id="<?= $row['category_id']; ?>"
                            style="background-color: rgba(0,0,0,0.05); flex-grow: 1; padding: 5px; margin:0 5px; text-decoration: <?= $row['hidden'] ? 'line-through' : 'none' ?>"
                            contenteditable="true"
                    ><?= $row['category_name'] ?></p>
                </div>
                <?php
            }
        }
        ?>
    </div>
</div>

<?= footerHTML() ?>

<script src="./moderation.js"></script>
</body>
</html>
