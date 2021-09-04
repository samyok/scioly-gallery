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
    $reason = $request->variable('reason', '', true);
    $target_id = (int)$request->variable('id', 0);
    $target_type = $request->variable('type', '');

    $VALID_TYPES = ['post', 'comment'];
    if (!in_array($target_type, $VALID_TYPES)) throw new Exception('Could not find post/comment');

    // I intentionally didn't check the length of the report because if there's a problem and a user
    // failed to fill out the report section, I still want to know.

    if ($target_type == 'post') {
        // verify post exists
        $sql = 'SELECT count(post_id) as count FROM gallery_posts WHERE ' . $db->sql_build_array('SELECT', [
                'post_id' => (int)$target_id
            ]);
        $result = $db->sql_query($sql);

        $count = $db->sql_fetchrow($result)['count'];
        if ($count < 1) throw new Exception('Post not found. ');
    } else if ($target_type == 'comment') {
        // verify comment exists
        $sql = 'SELECT count(reply_id) as count FROM gallery_replies WHERE ' . $db->sql_build_array('SELECT', [
                'reply_id' => (int)$target_id
            ]);
        $result = $db->sql_query($sql);
        $count = $db->sql_fetchrow($result)['count'];
        if ($count < 1) throw new Exception('Comment not found. ');
    }

    // create the report
    $sql = 'INSERT INTO gallery_reports ' . $db->sql_build_array('INSERT', [
            'reason_for_report' => $reason,
            'reporter_id' => (int)($user->data['user_id']),
            'reporter_name' => (string)$user->data['username'],
            'target_type' => $target_type,
            'target_id' => (int)$target_id,
            'active' => true
        ]);

    $result = $db->sql_query($sql);

    echo json_encode(['success' => true]);
    exit();
}
if ($action == "delete") {

    needs_auth();
    if (!is_admin($user)) throw new Exception("You must be an admin!");

    $id = intval($request->variable("id", 0));
    $result = getSQLRows("SELECT `report_id` FROM `gallery_reports` WHERE `report_id` = " . $id)[0];

    $sql = "UPDATE `gallery_reports` SET `active` = 0 WHERE  `report_id` = " . $id;
    $db->sql_query($sql);
    gallery_log('report', $id, 'deleted', '');
    echo json_encode(['success' => true]);
    exit();
}

