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
    <link rel="stylesheet" href="./gallery.css?v=<?= time()?>">
    <title>Science Olympiad Student Center - Gallery</title>
</head>
<body>

<?= navigationHTML($user); ?>
<?= reportBox() ?>

<div class="container menu-reactive">
</div>

<?= footerHTML() ?>

<script src="./moderation.js"></script>
</body>
</html>
