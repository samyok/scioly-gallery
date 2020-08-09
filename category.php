<?php

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

//
//if ($user->data['user_id'] == ANONYMOUS) {
//    login_box('', $user->lang['LOGIN']);
//}

$category_id = intval($request->variable('c', 0));
if ($category_id > 0) {
    $sql = 'SELECT * FROM `gallery_categories` WHERE `category_id` = ' . $category_id;
    $result = $db->sql_query($sql);
    if (mysqli_num_rows($result) == 0) {
        include 'lost.php';
    }

    $rows = $db->sql_fetchrowset($result);
    $category_name = $rows[0]['category_name'];
    $category_division = $rows[0]['category_division'];
} else {
    include 'lost.php';
}
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
    <title>Science Olympiad Student Center - Gallery - <?php echo $category_name; ?></title>
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
    <link rel="stylesheet" href="gallery.css">
    <script src="https://masonry.desandro.com/masonry.pkgd.js"></script>
</head>
<body>
<!-- <ul class="lightrope"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul> -->
<!-- <div class="banner" style="background-color: #555555;"> -->
<!--
<?php
var_dump($user->data);
?>
-->
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
<div class="container gallery-category-container menu-reactive">
    <div class="gal-nav">
        <div class="go-back">
            <a href="index.php">Categories</a> &lt; <?php echo $category_name ?>
        </div>
        <h1><?php echo $category_name ?></h1>
        <input id="searchBar" type="text" placeholder="Search the category">
        <div class="tags">
            <div class="add-new-container">
                <a class="add-new-post" href="add.php?c=<?php echo $category_id; ?>">
                    <i class="fas fa-plus"></i> Add post
                </a>
            </div>
            Filter:
            <div class="tag">2013</div>
            <div class="tag">2014</div>
            <div class="tag">2018</div>
            <div class="tag active">2019</div>

            <div class="sort-container">Sorting by
                <span class="sort">
                    <i class="fas fa-trophy"></i> Top
                </span>
            </div>

        </div>
    </div>
    <div class="gallery-category">
        <?php
        $sql = 'SELECT * FROM `gallery_posts` WHERE `is_hidden` is null AND `category` = ' . $category_id;
        $result = $db->sql_query($sql);
        $rows = $db->sql_fetchrowset($result);
        foreach ($rows as $row) {
            ?>
            <div class="gallery-post-container">
                <div class="gallery-post" data-post-id="<?php echo $row['post_id']; ?>">
                    <div class="gallery-post-img">
                        <img src="<?php
                        $imgSql = 'SELECT * FROM `gallery_images` WHERE `image_id` =' . $row['thumb_image_id'];
                        $imgresult = $db->sql_query($imgSql);
                        $imgSqlRow = $db->sql_fetchrowset($imgresult);
                        if ($imgSqlRow[0]['is_youtube']) {
                            echo 'https://img.youtube.com/vi/' . $imgSqlRow[0]['image_uri'] . '/default.jpg';
                        } else {
                            echo $imgSqlRow[0]['image_uri'];
                        }
                        ?>" alt="">
<!--                        --><?php
//                        $imgSql = 'SELECT * FROM `gallery_image` WHERE `belongs_to_post` =' . $row['post_id'];
//                        $imgresult = $db->sql_query($imgSql);
//                        if (mysqli_num_rows($imgresult) > 1) { ?>
<!--                            <div class="many-images"><i class="fas fa-images"></i></div>-->
<!--                        --><?php //} ?>
                    </div>
                    <div class="gallery-post-info">
                        <div class="post-about">
                            <div class="gallery-post-title"><?php echo $row['title']; ?></div>
                            <div class="gallery-post-name">
                                <a class="<?php echo $row['user_color_class'];?>" href="https://scioly.org/forums/memberlist.php?mode=viewprofile&u=<?php echo $row['poster_id'];?>"><?php echo $row['poster_name']; ?></a>
                                <span class="gallery-post-date"><?php echo date("M d, Y", strtotime($row['date'])); ?></span>
                            </div>
                        </div>
                        <?php
                        // GET COMMENTS AND POST SCORE
                        $postScoreSQL = 'SELECT `value` FROM gallery_votes WHERE `target_post_id` = ' . $row['post_id'];
                        $postScoreRslt = $db->sql_query($postScoreSQL);
                        $postScoreRows = $db->sql_fetchrowset($postScoreRslt);
                        $postScoreSum = 0;
                        foreach($postScoreRows as $postScoreRow) {
                            $postScoreSum += $postScoreRow['value'];
                        }

                        $commentNumberSQL = 'SELECT * FROM `gallery_replies` WHERE `is_hidden` is null AND `post_id` = ' . $row['post_id'];
                        $commentNumberRslt = $db->sql_query($commentNumberSQL);
                        $commentNumber = mysqli_num_rows($commentNumberRslt);
                        ?>
                        <div class="gallery-post-score">
                            <div class="comment">
                                <i class="fas fa-comment-dots"></i> <?php echo $commentNumber; ?>
                            </div>
                            <div class="thumbs-up">
                                <i class="fas fa-thumbs-up"></i> <?php echo $postScoreSum; ?> <i class="far fa-thumbs-down"></i>
                            </div>
                            <div class="link">
                                <i class="fas fa-link"></i>
                            </div>
                        </div>
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
<script src="category.js"></script>
</body>

</html>