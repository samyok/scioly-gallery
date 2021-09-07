<?php
/**
 * include.php created by samy-oak-tree (2020)
 *
 * TODO Move this to partials/ and split it up into more reasonable parts -- i.e. don't mix all the auth stuff together.
 * TODO Possibly also consider abstracting the basic $user->session_begin() to a partials/ file.
 */
if (!defined('IN_PHPBB')) include "./lost.php";
include_once '../includes/message_parser.php';

function is_admin($user)
{
    // TODO return a number value depending on their group (i.e. how much power should they have?)
    $group_id = $user->data['group_id'];
    $user_id = $user->data['user_id'];
    // GROUPS
    // 5 = admins, 4 = global mods
    // 52 = gallery mods
    // 145 = mods
    return $group_id == 5 || $group_id == 145 || $group_id == 4 || $group_id == 52 || $user_id == 2 || $user_id == 46711; // samy;
}

function needs_auth()
{
    global $user;
    if ($user->data['user_id'] == ANONYMOUS) {
        echo json_encode(['success' => false, 'error' => 'You must login to do that!']);
        exit();
    }
}

function getSQLRows($sql_query)
{
    global $db;
    $sqlrslt = $db->sql_query($sql_query);
    return $db->sql_fetchrowset($sqlrslt);
}

function replaceNewLines($txt)
{
    return preg_replace('/[\r\n][\r\n ]*/i', "<br/>", $txt);
}

function getNumReports()
{
    global $user, $db;
    if (!is_admin($user)) return 0;
    else {
        $sql = 'SELECT COUNT(report_id) as count FROM gallery_reports WHERE active is not false';
        $result = $db->sql_query($sql);
        return intval($db->sql_fetchrow($result)['count']);
    }
}

function reportBox()
{
    global $user;
    if (!is_admin($user)) return '';
    $num_reports = getNumReports();
    $is_or_are = $num_reports === 1 ? 'is' : 'are';
    $plural = $num_reports === 1 ? '' : 's';
    $them_or_it = $num_reports === 1 ? 'it' : 'them';
    if ($num_reports === 0) return ''; // '<div class="report-box" style="background-color: white;"><a href="admin.php" style="color: black">There ' . $is_or_are . ' <b>' . $num_reports . ' active report' . $plural . '. </b>Click here to go to admin dashboard.</a></div>';
    else
        return '<div class="report-box"><a href="reports.php">There ' . $is_or_are . ' <b>' . $num_reports . ' active report' . $plural . '. </b>Click here to address ' . $them_or_it . '.</a></div>';
}

function adminButtons()
{
    global $user;
    if (!is_admin($user)) return '';
    $num_reports = getNumReports();
    if ($num_reports === 0) {
        return '
            <a href="reports.php" class="thinbtn" title="Add a new post">
                Reports
            </a>
            <a href="admin.php" class="thinbtn" title="Add a new post">
                Admin
            </a>';
    } else {
        return '
            <a href="reports.php" class="thinbtn" title="Add a new post">
                Reports <span class="badge" style="margin-left:5px; top:0">' . $num_reports . '</span>
            </a>
            <a href="admin.php" class="thinbtn" title="Add a new post">
                Admin
            </a>';
    }
}

function gallery_log($target_type, $target_id, $action, $reason)
{
    global $db, $user;
    $sql = 'INSERT INTO gallery_logs ' . $db->sql_build_array('INSERT', [
            'target_id' => $target_id,
            'mod_user' => $user->data['user_id'],
            'mod_username' => $user->data['username'],
            'target_type' => $target_type,
            'reason' => $reason ? $reason : '',
            'action' => $action
        ]);
    return $db->sql_query($sql);
}

function user_color_class($user)
{
    return '';
    // bernard said no to different colors but in case you ever want to enable it:
    // return is_admin($user) ? "admin" : "";
}

function parse($text, $allow_bbcode, $allow_magic_url, $allow_smilies)
{
    $p = new parse_message($text);
    return $p->format_display($allow_bbcode, $allow_magic_url, $allow_smilies, false);
}