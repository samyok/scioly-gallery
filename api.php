<?php
/**
 * api.php created by samy-oak-tree
 *
 * This page coalesces all the different JSON apis under api/.
 */
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

global $request;
global $user;
global $auth;

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('');

//if ($user->data['user_id'] == ANONYMOUS) {
//    login_box('', $user->lang['LOGIN']);
//}
include_once("./include.php");


$action = $request->variable("action", "");
$object = $request->variable("object", "");

//echo $action . ":" . $object . "\n";

switch($object){
    case 'post':
        include 'api/post.php';
        break;
    case 'comment':
        include 'api/comment.php';
        break;
    case 'report':
        include 'api/report.php';
        break;
    case 'category':
        include 'api/category.php';
        break;
}