<?php

// verify that it's being called from api.php and not directly
// mimic slow internet and add some buffering to test loader
if (!defined('IN_PHPBB')) include '../lost.php';

header('Content-Type: application/json');

function exception_handler($exception)
{
    echo json_encode(['success' => false, 'error' => $exception->getMessage()]);
    die();
}

set_exception_handler("exception_handler");

global $action;
global $request;
global $db;
global $user;

$action = strtolower($action);
if ($action == 'create') {
    needs_auth();
    $content = $request->variable('content', '', true);
    $parent_post = $request->variable('parent_post', 0);
    if (strlen($content) < 6) throw new Exception("Comments must have more than 6 characters.");
    if (strlen($content) > 500) throw new Exception("Comments must have no more than 500 characters (currently " . strlen($content) . ")");

    // verify post exists
    $sql = 'SELECT count(post_id) as count, category FROM gallery_posts WHERE ' . $db->sql_build_array('SELECT', [
            'post_id' => (int)$parent_post
        ]);
    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
    $count = $row['count'];
    if ($count < 1) throw new Exception('Post not found.');
    if($row['category'] != 42) throw new Exception("Hey! Don't do things in categories other than Memez!! 👿");
    // create the comment
    $sql = 'INSERT INTO gallery_replies ' . $db->sql_build_array('INSERT', [
            'text' => $content,
            'author_id' => (int)($user->data['user_id']),
            'user_color_class' => user_color_class($user),
            'author_name' => (string)($user->data['username']),
            'post_id' => (int)$parent_post,
            'timestamp' => date('Y-m-d H:i:s')
        ]);

    $result = $db->sql_query($sql);

    echo json_encode(['success' => true]);
    die();
}
// todo: edit
if ($action == "delete") {

    $reply_id = intval($request->variable("reply_id", 0));
    $result = $db->sql_query("SELECT `reply_id`, post_id FROM `gallery_replies` WHERE `reply_id` = " . $reply_id);
    $comment = $db->sql_fetchrow($result);
    if (!is_admin($user) || !$comment['reply_id']) {
        throw new Exception("Could not delete--you don't have enough permissions!");
    }

    $sql = "UPDATE `gallery_replies` SET `is_hidden` = 1 WHERE `reply_id` = " . $reply_id;
    $db->sql_query($sql);
    gallery_log('comment', $comment['post_id'] . '#c' . $comment['reply_id'], 'deleted', '');
    echo json_encode(['success' => true]);
    exit();
}


if ($action == "restore") {

    $reply_id = intval($request->variable("reply_id", 0));
    $result = $db->sql_query("SELECT `reply_id`, post_id FROM `gallery_replies` WHERE `reply_id` = " . $reply_id);
    $comment = $db->sql_fetchrow($result);
    if (!is_admin($user) || !$comment['reply_id']) {
        throw new Exception("Could not delete--you don't have enough permissions!");
    }

    $sql = "UPDATE `gallery_replies` SET `is_hidden` = 0 WHERE `reply_id` = " . $reply_id;
    $db->sql_query($sql);
    gallery_log('comment', $comment['post_id'] . '#c' . $comment['reply_id'], 'restored', '');
    echo json_encode(['success' => true]);
    exit();
}

