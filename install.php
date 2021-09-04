<html>
<head>
    <title>Gallery Installer</title>
</head>
<body>
<pre>
<?php
// This file is to create the db tables. This file should be deleted after use.

/**
 * This file creates the db files to allow for one-command docker installation.
 *
 * THIS FILE SHOULD BE DELETED AFTER USE
 *
 * Gallery written by Samyok Nepal from 2019-2020. Contact me at samyok@samyok.us if something goes wrong :D
 */
$C = "✔️";
$c = "✔";
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

global $db;
global $request;
$drop = $request->variable('drop', 0);
if($drop){
    $sql = "SELECT CONCAT( 'DROP TABLE ', GROUP_CONCAT(table_name) , ';' )
    AS statement FROM information_schema.tables
    WHERE table_name LIKE 'gallery_%';";
    $result = $db->sql_query($sql);
    $result = $db->sql_fetchrowset($result);
    $result = $result[0]['statement'];
    echo "😐 executing $result <br>";
    $result = $db->sql_query($result);
    echo "$C dropped all tables 😎<br>";
}

$sql = 'CREATE TABLE IF NOT EXISTS `gallery_categories` (
    `category_id` MEDIUMINT NOT NULL AUTO_INCREMENT,
    `category_name` TEXT,
    `hits` MEDIUMINT DEFAULT 0,
    `group_id` MEDIUMINT DEFAULT 1,
    `hidden` BOOL DEFAULT FALSE,
    `info` varchar(255),
    PRIMARY KEY (category_id))';
$result = $db->sql_query($sql);
echo "$C created gallery_categories if not exists: $result <br>";

$sql = "DELETE FROM gallery_categories";
$result = $db->sql_query($sql);
echo "    $C deleted all categories: $result <br>";


$sql = 'CREATE TABLE IF NOT EXISTS `gallery_posts` (
    `post_id` MEDIUMINT NOT NULL AUTO_INCREMENT,
    `category` MEDIUMINT NOT NULL,
    `category_name` TEXT,
    `poster_id` MEDIUMINT NOT NULL,
    `poster_name` TEXT NOT NULL,
    `title` TEXT NOT NULL,
    `description` TEXT,
    `date` DATETIME NOT NULL,
    `thumb_image_id` MEDIUMINT,
    `last_edited_time` DATETIME,
    `num_times_edited` DATETIME,
    `edit_reason` TEXT,
    `user_color_class` TEXT,
    `is_hidden` BOOLEAN,
    PRIMARY KEY (post_id)
)';
$result = $db->sql_query($sql);
echo "$C created gallery_posts if not exists: $result <br>";

$sql = 'ALTER TABLE `phpbb`.`gallery_posts` ADD FULLTEXT `search` (`title`, `description`)';
$result = $db->sql_query($sql);
echo "    $C Created search indices <br>";
$sql = 'CREATE TABLE IF NOT EXISTS `gallery_images` (
    `image_id` MEDIUMINT NOT NULL AUTO_INCREMENT,
    `image_uri` TEXT,
    `is_youtube` BOOLEAN,
    `belongs_to_post` MEDIUMINT NOT NULL,
    `belongs_to_category` MEDIUMINT NOT NULL,
    `belongs_to_user` MEDIUMINT NOT NULL,
    `is_hidden` BOOLEAN,
    `image_index` MEDIUMINT NOT NULL DEFAULT 0,
    PRIMARY KEY (image_id)
)';
$result = $db->sql_query($sql);
echo "$C created gallery_images if not exists: $result <br>";

$sql = 'create table if not exists gallery_votes
(
	value int null,
	target_user_id mediumint not null,
	target_id mediumint not null,
	source_ip text not null,
	source_user mediumint not null,
	timestamp datetime not null,
	type text not null,
	vote_id MEDIUMINT NOT NULL AUTO_INCREMENT,
	primary key (vote_id)
);';
$result = $db->sql_query($sql);
echo "$C created gallery_votes if not exists: $result <br>";

$sql = 'create table if not exists gallery_replies
(
    `reply_id` MEDIUMINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `text` TEXT NOT NULL,
    `author_id` MEDIUMINT NOT NULL,
    `author_name` TEXT NOT NULL,
    `post_id` MEDIUMINT NOT NULL,
    `timestamp` DATETIME NOT NULL,
    `last_edited_time` DATETIME,
    `num_times_edited` DATETIME,
    `edit_reason` DATETIME,
    `user_color_class` TEXT,
    `is_hidden` BOOLEAN
);';
$result = $db->sql_query($sql);
echo "$C created gallery_replies if not exists: $result <br>";

$sql = 'CREATE TABLE IF NOT EXISTS `gallery_reports` (
    `report_id` MEDIUMINT NOT NULL AUTO_INCREMENT,
    `reporter_id` MEDIUMINT,
    `reporter_name` TEXT,
    `target_id` INT,
    `target_type` TEXT,
    `reason_for_report` TEXT,
    `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `active` BOOL DEFAULT FALSE,
    PRIMARY KEY (report_id))';
$result = $db->sql_query($sql);
echo "$C created gallery_reports if not exists: $result <br>";
$sql = 'CREATE TABLE IF NOT EXISTS `gallery_groups` (
    `group_id` MEDIUMINT NOT NULL AUTO_INCREMENT,
    `group_name` TEXT,
    PRIMARY KEY (group_id))';
$result = $db->sql_query($sql);
echo "$C created gallery_groups if not exists: $result <br>";
if($drop) {

    $default_categories = [
        "Recent Events",
        "Archive"
    ];

    foreach($default_categories as $name){
        $sql = "INSERT INTO gallery_groups (group_name) VALUES ('$name')";
        $result = $db->sql_query($sql);
        echo "    $C group $name created: $result <br>";
    }

}
$sql = 'CREATE TABLE IF NOT EXISTS `gallery_logs` (
    `log_id` MEDIUMINT NOT NULL AUTO_INCREMENT,
    `mod_user` MEDIUMINT,
    `mod_username` TEXT,
    `target_type` TEXT,
    `action` TEXT,
    `target_id` text,
    `reason` TEXT,
    `timestamp` DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (log_id))';
$result = $db->sql_query($sql);
echo "$C created gallery_logs if not exists: $result <br>";

?>
</pre>
</body>
</html>
