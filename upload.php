<?php

set_exception_handler(function ($exception) {
    echo(json_encode(array(
        'success' => false,
        'message' => $exception->getMessage()
    )));
    exit();
});


$fileToUpload = $_FILES["fileToUpload"];

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), true);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
global $auth;
$auth->acl($user->data);
$user->setup();

global $db;

if ($user->data['user_id'] == ANONYMOUS) {
    throw new Exception("E_NOT_LOGGED_IN");
}

$target_dir = "img/uploads/";

function getSQLRows($sql_query)
{
    global $db;
    $sqlrslt = $db->sql_query($sql_query);
    return $db->sql_fetchrowset($sqlrslt);
}

$uploadOk = true;
$imageFileType = strtolower(pathinfo(basename($fileToUpload["name"]), PATHINFO_EXTENSION));
$images_from_user = count(getSQLRows("SELECT * FROM `gallery_images` WHERE `belongs_to_user` = " . $user->data['user_id']));
$target_file = $target_dir . $user->data['username'] . "-" . $images_from_user . "-" . time() . "." . $imageFileType;
// Check if image file is a actual image or fake image

if (isset($_POST["submit"])) {
    $check = getimagesize($fileToUpload["tmp_name"]);
    if ($check !== false) {
        throw new Exception ("File is an image - " . $check["mime"] . ".");
        $uploadOk = true;
    } else {
        throw new Exception ("File is not an image.");
        $uploadOk = false;
    }
}
// Check if file already exists
if (file_exists($target_file)) {
    throw new Exception ("Sorry, file already exists. " . $target_file);
    $uploadOk = false;
}
// Check file size
if ($fileToUpload["size"] > 5000000) {
    throw new Exception ("Sorry, your file is too large.");
    $uploadOk = false;
}
// Allow certain file formats
if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
    && $imageFileType != "gif") {
    throw new Exception ("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
    $uploadOk = false;
}
// Check if $uploadOk is set to false by an error
if ($uploadOk == false) {
    throw new Exception ("Sorry, your file was not uploaded.");
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($fileToUpload["tmp_name"], $target_file)) {
        // do sql command
        $target_file = $db->sql_escape($target_file);
        $user_id = $db->sql_escape($user->data['user_id']);

        $img_ary = array(
            'image_uri' => $target_file,
            'belongs_to_category' => false,
            'belongs_to_post' => false,
            'belongs_to_user' => (int)$user_id
        );

        $sql = 'INSERT INTO `gallery_images` ' . $db->sql_build_array('INSERT', $img_ary);

        $db->sql_query($sql);
        $image_id = $db->sql_nextid();
        echo json_encode([
            "success" => true,
            "image_id" => $image_id,
            "image_uri" => $target_file
        ]);
    } else {
        throw new Exception ("Sorry, there was an error uploading your file.");
    }
}
