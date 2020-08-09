<?php
// verify that it's being called from api.php and not directly
// mimic slow internet and add some buffering to test loader
sleep(1);
if (!defined('IN_PHPBB')) include '../lost.php';

try {
    /**
     * I'm mimicking REST-like behavior, but it's not the greatest. :)
     * GET gets post information, no auth required.
     * POST creates a new post (auth required)
     * PATCH edits the post contents (auth + admin/posting user required)
     * PUT edits the image contents (admin required)
     * DELETE deletes the post (hides it, auth required + admin/original user required)
     */
    if ($action == "GET") { // get post info
        $post_id = intval($request->variable("post_id", 0));
        if ($post_id < 1) throw new Exception("Post ID must be greater than 0.");
        $result = getSQLRows("SELECT `category`, `post_id`, `poster_id`, `poster_name`, `title`, `description`, `date`, `thumb_image_id`, `last_edited_by`, `num_times_edited`, `edit_reason`, `user_color_class` FROM `gallery_posts` WHERE `post_id` = " . $post_id)[0];
        $res = new stdClass();
        $res->success = true;
        $res->data = $result;
        echo json_encode($res);
    }

    if ($action == "POST") { // add post
        needs_auth();

        $title = $request->variable('title', "");
        if (strlen($title) < 8) throw new Exception("Your title needs to be at least 8 characters long.");

        $desc = $request->variable('desc', "");
        if (strlen(preg_replace('/[\r\n][\r\n ]*/i', "", $desc)) < 20) throw new Exception("Your description needs to be at least 20 characters long.");

        $user_color_class = is_admin($user) ? "admin" : "";

        $category_id = intval($request->variable('category', 0));
        global $db;

        $categories = getSQLRows("SELECT `category_id`, `category_name` FROM `gallery_categories` WHERE `category_id`=" . $db->sql_escape($category_id));
        if (count($categories) !== 1) throw new Exception("Invalid category.");
        $category_name = $categories[0]['category_name'];

//        foreach($images as $image){
//            $image
//        }
        $images = $request->variable('images', "[]");
        $images = json_decode(html_entity_decode($images));
        if (count($images) < 1) throw new Exception("You need at least one image!");
        if ($images[0]->isYoutube) {
            throw new Exception("Your first image cannot be a Video!");
        }

        $sql = "INSERT INTO `gallery_posts` (`category`, `category_name`, `poster_id`, `poster_name`, `title`, `description`, `date`, `thumb_image_id`, `user_color_class`) VALUES (" .
            (int)$category_id . ", '" .
            $db->sql_escape($category_name) . "', " .
            (int)$user->data['user_id'] . ", '" .
            $db->sql_escape($user->data['username']) . "', '" .
            $db->sql_escape($title) . "', '" .
            $db->sql_escape($desc) . "', CURRENT_TIMESTAMP, " .
            (int)$images[0]->image_id . ", '" .
            $db->sql_escape($user_color_class) . "')";

        $db->sql_query($sql);
        $new_post_id = $db->sql_nextid();


        foreach ($images as $image) {
            if ($image->isYoutube) {
                // sql insert to images
                $sql = 'INSERT INTO `gallery_images` ' . $db->sql_build_array('INSERT', array(
                        'image_uri' => $db->sql_escape($image->video_id),
                        'is_youtube' => 1,
                        'belongs_to_post' => (int)$new_post_id,
                        'belongs_to_category' => (int)$category_id,
                        'belongs_to_user' => (int)$user->data['user_id']
                    ));
                $db->sql_query($sql);
            } else {
                // attach image to post

                $getImages = array(
                    'belongs_to_user' => (int)$user->data['user_id'],
                    'image_id' => (int)$image->image_id
                );
                $updateImages = array(
                    'belongs_to_post' => (int)$new_post_id,
                    'belongs_to_category' => (int)$category_id
                );
                $db->sql_query('UPDATE `gallery_images` SET ' . $db->sql_build_array('UPDATE', $updateImages) . ' WHERE ' . $db->sql_build_array('SELECT', $getImages));
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
        $sql = "UPDATE `gallery_posts` SET `is_hidden` = 1 WHERE `gallery_posts`.`post_id` = " . $post_id;
        $db->sql_query($sql);
        $sql = "UPDATE `gallery_images` SET `is_hidden` = 1 WHERE `belongs_to_post` = " . $post_id;
        $db->sql_query($sql);
        $success = new stdClass();
        $success->success = true;
        $success->message = "Post #" . $post_id . " has been deleted.";
        echo json_encode($success);
    }
} catch (Exception $exception) {
    $errorObj = new stdClass();
    $errorObj->success = false;
    $errorObj->error = $exception->getMessage();
    echo json_encode($errorObj);
}
