<?php
if(!defined('IN_PHPBB')) include "./lost.php";

function is_admin($user)
{
    $group_id = $user->data['group_id'];
    $user_id = $user->data['user_id'];
    return $group_id == 4;// || $user_id == 2 || $user_id == 46711;
}
function needs_auth() {
    global $user;
    if($user->data['user_id'] == ANONYMOUS){
        echo "403 Unauthorized";
        exit();
    }
}
function getSQLRows($sql_query)
{
    global $db;
    $sqlrslt = $db->sql_query($sql_query);
    return $db->sql_fetchrowset($sqlrslt);
}
