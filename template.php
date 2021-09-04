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
<div class="container menu-reactive" id="">
</div>
<?= footerHTML() ?>
<script src="js/sweetalert2.all.min.js"></script>

<script src="reports.js"></script>
</body>

</html>