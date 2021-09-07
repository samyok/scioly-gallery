<?php
// verify that it's being called from api.php and not directly
// mimic slow internet and add some buffering to test loader
// TODO: match comment.php
if (!defined('IN_PHPBB')) include '../lost.php';


function exception_handler($exception)
{
    echo json_encode(['success' => false, 'error' => $exception->getMessage()]);
    die();
}

set_exception_handler("exception_handler");


/**
 * I'm mimicking REST-like behavior, but it's not the greatest. :)
 * GET gets post information, no auth required.
 * POST creates a new post (auth required)
 * PATCH edits the post contents (auth + admin/posting user required)
 * PUT edits the image contents (admin required)
 * DELETE deletes the post (hides it, auth required + admin/original user required)
 */
global $action;
if ($action == "GET") { // get post info
    $post_id = intval($request->variable("post_id", 0));
    if ($post_id < 1) throw new Exception("Post ID must be greater than 0.");
    $result = getSQLRows("SELECT `category`, `post_id`, `poster_id`, `poster_name`, `title`, `description`, `date`, `thumb_image_id`, `num_times_edited`, `edit_reason`, `user_color_class` FROM `gallery_posts` WHERE `post_id` = " . $post_id)[0];
    $res = new stdClass();
    $res->success = true;
    $res->data = $result;
    echo json_encode($res);
}

if ($action == "POST") { // add/edit post
    needs_auth();

    // if we're combining images, make sure that we're admin
    $combination = $request->variable('combining', "[]");
    $combination = json_decode(html_entity_decode($combination));
//    var_dump($combination);
    if (sizeof($combination) > 0) {
        if (!is_admin($user)) {
            throw new Exception("You must be an admin to combine posts");
        }
    }

    $title = $request->variable('title', "", true);
    if (strlen($title) < 8) throw new Exception("Your title needs to be at least 8 characters long.");
    if (strlen($title) > 500) throw new Exception("Your title must be at most 500 characters long. (currently " . strlen($title) . " chars)");

    $desc = $request->variable('desc', "", true);
    $cleanDesc = preg_replace('/[\r\n][\r\n ]*/i', "", $desc);
    if (strlen($cleanDesc) < 20) throw new Exception("Your description needs to be at least 20 characters long.");
    if (strlen($cleanDesc) > 3000) throw new Exception("Your description must be at most 3000 characters long. (currently " . strlen($cleanDesc) . " chars)");


    $category_id = intval($request->variable('category', 0));
    global $db;
//    if ($category_id != 42) throw new Exception("Hey! Don't do things in categories other than Memez!! 👿");

    $categories = getSQLRows("SELECT `category_id`, `category_name` FROM `gallery_categories` WHERE `category_id`=" . $db->sql_escape($category_id));
    if (count($categories) !== 1) throw new Exception("Invalid category.");
    $category_name = $categories[0]['category_name'];

//        foreach($images as $image){
//            $image
//        }
    $images = $request->variable('images', "[]");
    $images = json_decode(html_entity_decode($images));
    if (count($images) < 1) throw new Exception("You need at least one image!");
//    if ($images[0]->isYoutube) {
//        throw new Exception("Your first image cannot be a Video!");
//    }

    // if we're editing, we need to make sure that they have perms to edit.
    $post_id = $request->variable('post_id', 0);
    if ($post_id > 0) {
        $sql = 'SELECT poster_id from gallery_posts WHERE ' . $db->sql_build_array('SELECT', [
                'post_id' => $post_id
            ]);
        $result = $db->sql_query($sql);
        $row = $db->sql_fetchrow($result);
        // first make sure that the post exists!
        if (!$row) throw new Exception('Post doesn\'t exist!');

        // then make sure we have perms
        $has_perms = is_admin($user) || $user->data['user_id'] == $row['poster_id'];
        if (!$has_perms) throw new Exception('You don\'t have permission to edit this post!');

        $edit_reason = $request->variable('edit_reason', "");
        $date = $request->variable('date', time());
        // update post
        gallery_log('post', $post_id, 'edited', $edit_reason);
        $sql = 'UPDATE gallery_posts 
            SET  ' . $db->sql_build_array('UPDATE', [
                'category' => (int)$category_id,
                'category_name' => $category_name,
                'title' => $title,
                'description' => $desc,
                'edit_reason' => $edit_reason,
                'last_edited_time' => date('Y-m-d H:i:s'),
                'thumb_image_id' => $images[0]->image_id,
                'user_color_class' => user_color_class($user),
                'date' => date('Y-m-d H:i:s', $date)
            ]) . '
            WHERE post_id = ' . (int)$post_id;

    } else {

        $sql = 'INSERT INTO `gallery_posts`' . $db->sql_build_array('INSERT', [
                'category' => (int)$category_id,
                'category_name' => $category_name,
                'poster_id' => (int)$user->data['user_id'],
                'poster_name' => $user->data['username'],
                'title' => $title,
                'description' => $desc,
                'date' => date('Y-m-d H:i:s'),
                'thumb_image_id' => $images[0]->image_id,
                'user_color_class' => user_color_class($user)
            ]);
    }

    $db->sql_query($sql);
    $new_post_id = $post_id > 0 ? $post_id : $db->sql_nextid();
    // hide all the images first. then add them back later.
    $db->sql_query('
        UPDATE `gallery_images` 
        SET ' . $db->sql_build_array('UPDATE', ['is_hidden' => true]) . ' 
        WHERE ' . $db->sql_build_array('SELECT', ['belongs_to_post' => $new_post_id]));

    foreach ($images as $index => $image) {

        if (!$image->image_id) { //
            // sql insert to images
            // if it doesn't have an ID it's a youtube video
            $sql = 'INSERT INTO `gallery_images` ' . $db->sql_build_array('INSERT', [
                    'image_uri' => $db->sql_escape($image->video_id),
                    'is_youtube' => 1,
                    'belongs_to_post' => (int)$new_post_id,
                    'belongs_to_category' => (int)$category_id,
                    'belongs_to_user' => (int)$user->data['user_id'],
                    'image_index' => $index,
                    'is_hidden' => false
                ]);
            $db->sql_query($sql);
            if ($index === 0) {
                // update post with thumbnail
                $thumbnail_id = $db->sql_nextid();
                $sql = 'UPDATE `gallery_posts` SET ' . $db->sql_build_array('UPDATE', array(
                        'thumb_image_id' => $thumbnail_id
                    )) . ' WHERE ' . $db->sql_build_array('SELECT', array(
                        'post_id' => (int)$new_post_id
                    ));
                $db->sql_query($sql);
            }

        } else {
            // find if such an id exists
            $sql = 'SELECT * from gallery_images WHERE ' . $db->sql_build_array('SELECT', [
                    'oid' => $image->image_id
                ]). ' OR ' . $db->sql_build_array('SELECT', [
                    'image_id' => $image->image_id
                ]);
            $result = $db->sql_query($sql);
            $row = $db->sql_fetchrow($result);

            if ($row) { // update row
                // attach image to post
                $getImages = array(
                    'image_id' => (int)$row["image_id"]
                );
                $updateImages = array(
                    'belongs_to_post' => (int)$new_post_id,
                    'belongs_to_category' => (int)$category_id,
                    'image_index' => $index,
                    'is_hidden' => !!$image->is_hidden
                );
                $db->sql_query('UPDATE `gallery_images` SET ' . $db->sql_build_array('UPDATE', $updateImages) . ' WHERE ' . $db->sql_build_array('SELECT', $getImages));

                if ($index === 0) {
                    // update post with thumbnail
                    $thumbnail_id = $db->sql_nextid();
                    $sql = 'UPDATE `gallery_posts` SET ' . $db->sql_build_array('UPDATE', [
                            'thumb_image_id' => (int)$row["image_id"]
                        ]) . ' WHERE ' . $db->sql_build_array('SELECT', [
                            'post_id' => (int)$new_post_id
                        ]);
                    $db->sql_query($sql);
                }
            } else { // insert row
                // insert image into db
                $sql = 'INSERT INTO `gallery_images` ' . $db->sql_build_array('INSERT', array(
                        'image_uri' => $db->sql_escape($image->image_uri),
                        'is_youtube' => 0,
                        'belongs_to_post' => (int)$new_post_id,
                        'belongs_to_category' => (int)$category_id,
                        'belongs_to_user' => (int)$user->data['user_id'],
                        'image_index' => $index,
                        'is_hidden' => false,
                        'oid' => $image->image_id
                    ));

                $db->sql_query($sql);

                if ($index === 0) {
                    // update post with thumbnail
                    $thumbnail_id = $db->sql_nextid();
                    $sql = 'UPDATE `gallery_posts` SET ' . $db->sql_build_array('UPDATE', array(
                            'thumb_image_id' => $thumbnail_id
                        )) . ' WHERE ' . $db->sql_build_array('SELECT', array(
                            'post_id' => (int)$new_post_id
                        ));
                    $db->sql_query($sql);
                }
            }

        }
    }

    if (sizeof($combination) > 0) {
        foreach ($combination as $cpost_id) {
            $sql = "UPDATE `gallery_posts` SET `is_hidden` = 1 WHERE `gallery_posts` . `post_id` = " . $cpost_id;
            $db->sql_query($sql);
        }
    }
    $success = new stdClass();
    $success->success = true;
    $success->message = "Posted successfully!";
    $success->post_id = $new_post_id;
    echo json_encode($success);
    exit();
//        $sql = "CREATE"
//        $request->variable('')
}

if ($action == "PATCH") {
    // TODO authentication
    // TODO edit post
}

if ($action == "PUT") {
    // TODO auth
    // TODO edit images
}

if ($action == "DELETE") {
    // MARKER auth
    $post_id = intval($request->variable("post_id", 0));
    if ($post_id < 1) throw new Exception("Post ID must be greater than 0.");
    $result = getSQLRows("SELECT `poster_id` FROM `gallery_posts` WHERE `post_id` = " . $post_id)[0];
    $author_id = $result['poster_id'];
    if (!is_admin($user) && $user->data['user_id'] !== $author_id) {
        throw new Exception("Could not delete--you don't have enough permissions!");
    }
    // delete (hide) the post
    // (just mark it as hidden)
    $sql = "UPDATE `gallery_posts` SET `is_hidden` = 1 WHERE `gallery_posts` . `post_id` = " . $post_id;
    $db->sql_query($sql);
    $sql = "UPDATE `gallery_images` SET `is_hidden` = 1 WHERE `belongs_to_post` = " . $post_id;
    $db->sql_query($sql);
    $success = new stdClass();
    $success->success = true;
    $success->message = "Post #" . $post_id . " has been deleted.";
    gallery_log('post', $post_id, 'deleted', '');
    echo json_encode($success);
}
if ($action == "RESTORE") {
    // MARKER auth
    $post_id = intval($request->variable("post_id", 0));
    if ($post_id < 1) throw new Exception("Post ID must be greater than 0.");
    $result = getSQLRows("SELECT `poster_id` FROM `gallery_posts` WHERE `post_id` = " . $post_id)[0];
    $author_id = $result['poster_id'];
    if (!is_admin($user)) {
        throw new Exception("Could not restore--you don't have enough permissions!");
    }
    // delete (hide) the post
    // (just mark it as hidden)
    $sql = "UPDATE `gallery_posts` SET `is_hidden` = 0 WHERE `gallery_posts` . `post_id` = " . $post_id;
    $db->sql_query($sql);
    $sql = "UPDATE `gallery_images` SET `is_hidden` = 0 WHERE `belongs_to_post` = " . $post_id;
    $db->sql_query($sql);
    $success = new stdClass();
    $success->success = true;
    $success->message = "Post #" . $post_id . " has been restored.";
    gallery_log('post', $post_id, 'restored', '');
    echo json_encode($success);
}
