<?php
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include_once("./include.php");
// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('');

if ($user->data['user_id'] == ANONYMOUS) {
    login_box('', $user->lang['LOGIN']);
}

$post_id = intval(request_var('p', 0));

if ($post_id > 0) {
    $sql = 'SELECT * FROM `gallery_posts` WHERE `post_id` = ' . $post_id;
    $result = $db->sql_query($sql);
    $rows = $db->sql_fetchrowset($result);
    if (mysqli_num_rows($result) == 0 || (!is_admin($user) && $rows[0]['is_hidden'])) {
        include 'lost.php';
    }
    $post = $rows[0];
} else {
    include 'lost.php';
}

$userPointRows = getSQLRows("SELECT * FROM `gallery_votes` WHERE `target_user_id` = " . $post['poster_id']);;
$userpoints = 0;
$postpoints = 0;
foreach ($userPointRows as $userPointRow) {
    $userpoints += $userPointRow['value'];
    if ($userPointRow['target_post_id'] == $post_id) {
        $postpoints += $userPointRow['value'];
    }
}

$images = getSQLRows('SELECT * FROM `gallery_images` WHERE `belongs_to_post` = ' . $post_id);

?>
<html>
<head>
    <meta property="og:title" content="Science Olympiad Student Center"/>
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="https://scioly.org/"/>
    <meta property="og:image" content="https://scioly.org/src/img/logo/logo_square.png"/>
    <meta property="og:description"
          content="A resource by and for Science Olympiad students, coaches, and alumni nationwide."/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script type="text/javascript" src="https://scioly.org/src/js/jquery.min.js"></script>
    <script type="text/javascript" src="https://scioly.org/src/js/php.js"></script>

    <link rel="stylesheet" href="fontawesome/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="./main.css">

    <link rel="shortcut icon" href="https://scioly.org/favicon.ico" type="image/x-icon">
    <link rel="icon" href="https://scioly.org/favicon.ico" type="image/x-icon">
    <title>Science Olympiad Student Center - Gallery - <?php echo $post['title']; ?></title>
    <meta property="og:title" content="Science Olympiad Student Center"/>
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="https://scioly.org"/>
    <meta property="og:image" content="https://scioly.org/src/img/logo/logo_square.png"/>
    <meta property="og:description"
          content="A resource by and for Science Olympiad students, coaches, and alumni nationwide."/>
    <!-- google analytics -->
    <!--    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-340310-3"></script>-->
    <!--    <script>window.dataLayer = window.dataLayer || [];-->

    <!--    function gtag() {-->
    <!--        dataLayer.push(arguments);-->
    <!--    }-->

    <!--    gtag('js', new Date());-->
    <!--    gtag('config', 'UA-340310-3');</script>-->
    <!-- <script type="text/javascript" src="https://scioly.org/src/js/snowstorm.js"></script> -->
    <!-- <link rel="stylesheet" type="text/css" href="https://scioly.org/src/css/lights.css"> -->
    <!-- <script type="text/javascript" src="https://scioly.org/src/js/fireworks.js"></script> -->
    <link rel="stylesheet" href="css/lightgallery.min.css">
    <link rel="stylesheet" href="./gallery.css">

    <!--    <script type="text/javascript" src="http://livejs.com/live.js"></script>-->
    <!--    <script src="https://masonry.desandro.com/masonry.pkgd.js"></script>-->
</head>
<body>
<!-- <ul class="lightrope"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul> -->
<!-- <div class="banner" style="background-color: #555555;"> -->
<div class="banner">
    <!-- <p><a href="https://scioly.org/shirts">Scioly.org t-shirts are available for a limited time! Click here for more information. Orders close Wednesday, May 8, 2019 at 11:59 PM PST.</a></p> -->
    <!-- <p><a href="https://scioly.org/fantasy">Who will win? Click here to predict event medalists and top teams for this year's national tournament. Contest closes Friday, May 31, 2019 at 10:00 AM ET.</a></p> -->
    <!-- <p><a href="https://scioly.org/nationals" style="color: #ffffff;">One page for all our nationals-related events! Click here for prediction contest, medal counts, and more!</a></p> -->
    <p><a href="https://scioly.org/forums/viewtopic.php?f=24&t=15660">Welcome to the new season! Click here to learn
            what's new.</a></p>
</div>
<div class="site-nav">
    <a href="https://scioly.org/">
        <img src="https://scioly.org/src/img/logo/logo.png">
    </a>
    <div class="hamburger" onclick="hamburger(this)">
        <div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <ul>
        <li><a href="https://scioly.org/forums">Forums</a></li>
        <li><a href="https://scioly.org/wiki">Wiki</a></li>
        <li><a href="https://scioly.org/tests">Test Exchange</a></li>
        <!-- <li><a href="https://scioly.org/gallery">Image Gallery</a></li> -->
        <!-- <li><a href="https://scioly.org/invitational">Invites</a></li> -->
        <li><a href="https://scioly.org/chat">Chat</a></li>

        <li><a href="https://scioly.org/forums/memberlist.php?mode=viewprofile&u=46711" class="button">samy-oak-tree</a>
        </li>
    </ul>
</div>
<img class="print-only" src="https://scioly.org/src/img/logo/logo_black.png" style="max-width: 150px;">

<div class="container menu-reactive" id="carousel-container">
    <div class="go-back">
        <a href="index.php">Categories</a> &lt;
        <a href="category.php?c=<?php echo $post['category']; ?>"
           class="go-back-category"><?php echo $post['category_name']; ?></a> &lt;
        <span class="truncate"><?php echo $post['title']; ?></span>
    </div>
    <div class="confirm_edits" style="display:none">
        <button id="save_edits">Save Edits</button>
        <button id="cancel_edits">Cancel Edits</button>
    </div>
    <div class="info-area">
        <div class="vote">
            <i class="fas fa-thumbs-up"></i>
            <?php echo $postpoints; ?>
            <i class="far fa-thumbs-down"></i>
        </div>
        <div class="title-area">
            <h1 class="img-title"><?php echo $post['title']; ?></h1>
            <div class="author"><a class="<?php echo $post['user_color_class'] ?>"
                                   href="https://scioly.org/forums/memberlist.php?mode=viewprofile&u=<?php echo $post['poster_id']; ?>"><?php echo $post['poster_name']; ?></a>
                (<?php echo $userpoints; ?>)
                <span class="date"><?php echo date("F d, Y", strtotime($post['date'])); ?></span>
            </div>
            <div class="action-btns">
                <div class="reply">
                    Reply
                </div>
                <div class="report">
                    Report
                </div>
                <?php
                if (is_admin($user) || $user->data['user_id'] == $post['poster_id']) {
                    ?>
                    <div class="is_editable" style="display:none">1</div>
                    <div class="edit-post">
                        Edit
                    </div>
                    <div class="delete-post">
                        Delete
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <div class="post_id" style="display:none"><?php echo $post['post_id']; ?></div>
    </div>
    <div id="carousel">
        <!--        <h3>Boomilever Title</h3>-->

        <?php
        $thumbnails = [];
        $image_uris = [];
        foreach ($images as $image) {
            if ($image['is_hidden']) continue;
            if ($image['is_youtube']) {
                ?>
                <a href="https://www.youtube.com/watch?v=<?php echo $image['image_uri']; ?>" class="video">
                    <div class="video-thumbnail">
                        <img src="https://img.youtube.com/vi/<?php echo $image['image_uri']; ?>/default.jpg" alt="">
                    </div>
                </a>
                <?php
            } else {
                ?>


                <a href="<?php echo $image['image_uri']; ?>" class="video">
                    <img src="<?php echo $image['image_uri']; ?>" alt="">
                </a>
                <?php
            }
        }
        ?>
    </div>

    <div class="description">
        <p>
            <?php
            echo preg_replace('/[\r\n][\r\n ]*/i', "<br/>", $post['description']);
            ?>
        </p>
    </div>
    <div class="comment-wrap">
        <div class="comment comment-add">
            <textarea id="comment-add-text" oninput="auto_grow(this)" placeholder="Add comment..."></textarea>
            <button class="post-reply">Post Reply</button>
        </div>
        <?php

        $comments = getSQLRows('SELECT * FROM `gallery_replies` WHERE `post_id` = ' . $post_id);
        foreach ($comments as $comment) {

            $commentUserScoreSQL = 'SELECT `value` FROM `gallery_votes` WHERE `target_user_id` = ' . $comment['poster_id'];
            $commentUserScoreRslt = $db->sql_query($commentUserScoreSQL);
            $commentUserScoreRows = $db->sql_fetchrowset($commentUserScoreRslt);
            $commentUserScoreSum = 0;
            foreach ($commentUserScoreRows as $commentUserScoreRow) {
                $commentUserScoreSum += $commentUserScoreRow['value'];
            }

            ?>
            <div class="comment">
                <p class="comment-text"><?php echo $comment['text']; ?></p>
                <div class="action-area">
                    <div class="author">
                        <a class="<?php echo $comment['user_color_class']; ?>"
                           href="https://scioly.org/forums/memberlist.php?mode=viewprofile&u=<?php echo $comment['poster_id']; ?>">
                            <?php if ($post['poster_id'] == $comment['poster_id']) {
                                ?> <i class="fas fa-camera"></i> <?php
                            }
                            echo $comment['poster_name']; ?></a>
                        (<?php echo $commentUserScoreSum; ?>)
                        <span class="date"><?php echo date("F d, Y", strtotime($comment['timestamp'])); ?></span>
                        <span class="report" title="Report" data-reply-id="<?php echo $comment['reply-id']; ?>"><i
                                    class="fas fa-exclamation-circle"></i></span>
                    </div>
                </div>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<div class="site-footer">
    <div class="tiles">
        <div>
            <h4>Connect</h4>
            <p><a href="https://scioly.org/forums/viewforum.php?f=1">Event Forums</a></p>
            <p><a href="https://scioly.org/forums/viewforum.php?f=6">Competition Forums</a></p>
            <p><a href="https://scioly.org/chat">Online Chat</a></p>
            <p><a href="https://scioly.org/watch">YouTube Channel</a></p>
        </div>
        <div>
            <h4>Learn</h4>
            <p><a href="https://scioly.org/wiki">Wiki</a></p>
            <p><a href="https://scioly.org/forums/viewforum.php?f=297">Question Marathons</a></p>
            <p><a href="https://scioly.org/tests">Test Exchange</a></p>
            <p><a href="https://scioly.org/wiki/index.php/Test_Exchange_Archive">Test Archive</a></p>
        </div>
        <div>
            <h4>Get Involved</h4>
            <p><a href="https://scioly.org/wiki/index.php/Starting_A_Science_Olympiad_Team">Starting a Team</a></p>
            <p><a href="https://scioly.org/wiki/index.php/How_to_Write_a_Practice_Test">Test Writing Guide</a></p>
            <p><a href="https://scioly.org/wiki/index.php/Category:Needs_Work">Wiki: Needs Work</a></p>
            <p><a href="https://scioly.org/wiki/index.php/Category:Student_Volunteer_Organizations">Volunteering</a></p>
        </div>
        <div>
            <h4>About</h4>
            <p><a href="https://scioly.org/about">History</a></p>
            <p><a href="https://scioly.org/rules">Site Rules</a></p>
            <p><a href="https://scioly.org/rules">Privacy Policy</a></p>
            <p><a href="https://www.facebook.com/scioly.org">Facebook Page</a></p>
        </div>
        <div>
            <h4>Disclaimer</h4>
            <p>Scioly.org is a place to ask questions and share ideas. This site is not the place to get event rules,
                official rules changes, or official clarifications.</p>
        </div>
    </div>
</div>

<style type="text/css">
    @media (max-width: 1200px) {
        .site-footer .tiles > div,
        .site-footer .tiles > a {
            width: calc(25% - 20px);
        }
    }

    @media (max-width: 800px) {
        .site-footer .tiles > div,
        .site-footer .tiles > a {
            /*width: calc(33.33% - 20px);*/
        }
    }

    @media (max-width: 700px) {
        .site-footer .tiles > div,
        .site-footer .tiles > a {
            width: calc(33.33% - 20px);
        }
    }

    @media (max-width: 500px) {
        .site-footer .tiles > div,
        .site-footer .tiles > a {
            width: calc(50% - 20px);
        }
    }

    @media (max-width: 400px) {
        .site-footer .tiles > div,
        .site-footer .tiles > a {
            width: calc(100% - 20px);
        }
    }
</style>

<!-- <div class="footer"> -->
<!-- <a href="https://www.facebook.com/scioly.org"><img src="src/img/logo/facebook.png"></a> -->
<!-- <a href="https://twitter.com/scioly_org"></a><img src="src/img/logo/twitter.png"> -->
<!-- <p>Disclaimer: Before perusing content on this site, please read our <a href="https://scioly.org/rules">site rules and privacy policy</a>. Scioly.org is <em>not</em> the place to get event rules, official rules changes, or official clarifications. Scioly.org <em>is</em> a place to ask questions and share ideas.</p> -->


<!-- </div> -->

<script src="js/lightgallery-all.min.js"></script>

<!-- lightgallery plugins -->
<!--<script src="js/lg-thumbnail.min.js"></script>-->
<!--<script src="js/lg-fullscreen.min.js"></script>-->
<script src="js/sweetalert2.all.min.js"></script>
<script src="picture.js"></script>
</body>

</html>