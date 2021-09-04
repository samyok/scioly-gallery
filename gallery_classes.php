<?php

if (!defined('IN_PHPBB') || !defined('IN_GALLERY')) {
    header("Location: /gallery/");
    die();
}

class Post
{
    /**
     * Post constructor. Creates a new post.
     * If trying to retrieve a post, use Post.get($id) instead.
     * @param $post
     */
    public $category;
    public $category_name;
    public $poster_id;
    public $poster_name;
    public $title;
    public $description;
    public $date;
    public $thumb_image_id;
    public $last_edited_time;
    public $num_times_edited;
    public $edit_reason;
    public $user_color_class;
    public $is_hidden;
    public $id;

    function __construct($post)
    {
        // @todo add property checking (probably using regex)
        $this->category = $post["category"];
        $this->category_name = $post["category_name"];
        $this->poster_id = $post["poster_id"];
        $this->poster_name = $post["poster_name"];
        $this->title = $post["title"];
        $this->description = $post["description"];
        $this->date = $post["date"];
        $this->thumb_image_id = $post["thumb_image_id"];
        $this->last_edited_time = $post["last_edited_time"];
        $this->num_times_edited = $post["num_times_edited"];
        $this->edit_reason = $post["edit_reason"];
        $this->user_color_class = $post["user_color_class"];
        $this->is_hidden = $post["is_hidden"];
    }

    static function get($id, $db)
    {
        if (!is_integer($id)) include 'lost.php';
        $sql = "SELECT * FROM gallery_posts WHERE post_id = $id";
        $result = $db->sql_query($sql);
        if (mysqli_num_rows($result) == 0) include 'lost.php';

        $rows = $db->sql_fetchrowset($result);

        return new Post($rows[0]);
    }

    /**
     * reports the post.
     */
    function report()
    {
    }

    /**
     * Saves post to db.
     */
    function save()
    {
        $sql = "UPDATE gallery_posts SET WHERE post_id = $this->id";
    }
}

class Category
{
    function __construct($opts)
    {
    }

    static function get($id)
    {
    }
}

class Notification
{
    static function create($id)
    {
    }
}
