<?php
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('');

//if ($user->data['user_id'] == ANONYMOUS) {
//    login_box('', $user->lang['LOGIN']);
//}
include_once("./include.php");


$action = request_var("action", "");
$object = request_var("object", "");

//echo $action . ":" . $object . "\n";

switch($object){
    case "post":
        include 'api/post.php';
        break;
}