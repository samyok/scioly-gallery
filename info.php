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
header('Content-Type: application/json');

// @TODO move these values to a sekret file?
$algo = "sha512";
$secret = "insert secret here";
$time = microtime(true) * 1000 >> 0;
$username = $user->data['username'];
$text = $username . ':' . $time;
$token = base64_encode(hash_hmac($algo, $text, $secret, true));
echo json_encode([
    "username" => $username,
    "time" => $time,
    "token" => $token
]);
