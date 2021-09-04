<?php
/**
 * vote.php created by samy-oak-tree (2020)
 *
 * TODO: this should be moved to an api/ :eyes: 👀
 */
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include('./include.php');
// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('');

header('Content-Type: application/json');
function exception_handler($exception)
{
    echo json_encode(['success' => false, 'error' => $exception->getMessage()]);
    die();
}

set_exception_handler("exception_handler");


if ($user->data['user_id'] == ANONYMOUS) {
    echo json_encode(['success' => false, 'error' => 'You must be logged in!']);
    die();
}
// first get id and type of object
$VALID_OBJECT_TYPES = ['post', 'comment'];
$VALID_VOTING_OPTIONS = ['up', 'down', 'cancel'];
$vote_option = strtolower($request->variable('vote', ''));
$object_type = strtolower($request->variable('type', ''));
$target_id = $request->variable('id', -1);
if (
    $request->is_set_post('type') &&
    in_array($object_type, $VALID_OBJECT_TYPES) &&
    $request->is_set_post('vote') &&
    in_array($vote_option, $VALID_VOTING_OPTIONS) &&
    $request->is_set_post('id') &&
    is_integer($target_id)
) {
    global $db;

    if ($vote_option === "up") $vote_option = 1;
    else if ($vote_option === "down") $vote_option = -1;
    else $vote_option = 0;
    // delete all other votes anyway, we're going to be adding the correct one later.
    $uid = $user->data['user_id'];
    // select syntax is same as DELETE syntax
    $sql = 'DELETE FROM gallery_votes WHERE ' . $db->sql_build_array('SELECT', [
            'target_id' => $target_id,
            'type' => $object_type,
            'source_user' => $uid
        ]);
    $db->sql_query($sql);
    // get post or comment user
    if ($object_type == "post") {
        $sql = 'SELECT poster_id as user_id, category FROM gallery_posts WHERE ' . $db->sql_build_array('SELECT', [
                'post_id' => $target_id
            ]);
    } else throw new Exception("We don't support those pesky comments yet 👿");

    $result = $db->sql_query($sql);
    $row = $db->sql_fetchrow($result);

    if($row['category'] != 42) throw new Exception("Hey! Don't do things in categories other than Memez!! 👿");

    $target_user_id = $row['user_id'];

    if ($vote_option != 0) {
        $sql = "INSERT INTO gallery_votes" . $db->sql_build_array('INSERT', [
                'value' => $vote_option,
                'target_user_id' => $target_user_id,
                'target_id' => $target_id,
                'source_user' => $uid,
                'source_ip' => $request->server("REMOTE_ADDR"),
                'timestamp' => date_create()->format('Y-m-d H:i:s'),
                'type' => $object_type
            ]);
    }
    $db->sql_query($sql);

    $sql = 'SELECT SUM(value) as score, SUM(IF(source_user = ' . $user->data['user_id'] . ', value, 0)) as user_vote FROM gallery_votes WHERE ' . $db->sql_build_array('SELECT', [
            'target_id' => $target_id,
            'type' => $object_type
        ]);
    $row = $db->sql_fetchrow($db->sql_query($sql));

    echo json_encode([
        'success' => true,
        'post_id' => $target_id,
        'vote_count' => (int)$row['score'],
        'user_vote' => (int)$row['user_vote']
    ]);
    die();
} else {
    echo json_encode(['success' => false, 'error' => 'Something went wrong in your request.']);
    die();
}