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

// all functions must have admin powers.
if (!is_admin($user)) throw new Exception('Unauthorized');

if ($action == 'rename') {
    // do some sql stuff
    $id = $request->variable('id', 0);
    $name = $request->variable('name', '');
    $sql = 'UPDATE gallery_categories
            SET ' . $db->sql_build_array('UPDATE', ['category_name' => $name]) . ' 
            WHERE ' . $db->sql_build_array('SELECT', ['category_id' => $id]);
    $db->sql_query($sql);
    $sql = 'UPDATE gallery_posts
            SET ' . $db->sql_build_array('UPDATE', ['category_name' => $name]) . ' 
            WHERE ' . $db->sql_build_array('SELECT', ['category' => $id]);
    $db->sql_query($sql);

    gallery_log('category', $id, 'renamed', $name);
    echo json_encode(['success' => true]);
    exit();
}
if ($action == 'move') {
    $id = $request->variable('id', 0);
    $sql = 'SELECT * FROM gallery_categories WHERE ' . $db->sql_build_array('SELECT', ['category_id' => $id]);
    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
    $new_group_id = $row['group_id'] == 1 ? 2 : 1;
    $sql = 'UPDATE gallery_categories
            SET ' . $db->sql_build_array('UPDATE', ['group_id' => $new_group_id]) . ' 
            WHERE ' . $db->sql_build_array('SELECT', ['category_id' => $id]);
    $db->sql_query($sql);
    echo json_encode(['success' => true]);
    exit();
}
if ($action == 'toggle') {
    $id = $request->variable('id', 0);
    $sql = 'SELECT * FROM gallery_categories WHERE ' . $db->sql_build_array('SELECT', ['category_id' => $id]);
    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);
    $new_hidden = !$row['hidden'];
    $sql = 'UPDATE gallery_categories
            SET ' . $db->sql_build_array('UPDATE', ['hidden' => $new_hidden]) . ' 
            WHERE ' . $db->sql_build_array('SELECT', ['category_id' => $id]);
    $db->sql_query($sql);
    echo json_encode(['success' => true]);
    exit();
}

if ($action == 'create') {
    $name = $request->variable('name', '');
    $sql = 'INSERT INTO gallery_categories ' . $db->sql_build_array('INSERT', [
            'category_name' => $name,
            'group_id' => 1,
            'hits' => 0,
            'hidden' => False
        ]);
    $db->sql_query($sql);
    echo json_encode(['success' => true]);
    exit();
}

if ($action == 'change_group_name') {
    $name = $request->variable('name', '');
    $id = $request->variable('id', 0);
    $sql = 'UPDATE gallery_groups
            SET ' . $db->sql_build_array('UPDATE', ['group_name' => $name]) . ' 
            WHERE ' . $db->sql_build_array('SELECT', ['group_id' => $id]);
    $db->sql_query($sql);
    echo json_encode(['success' => true]);
    exit();
}
