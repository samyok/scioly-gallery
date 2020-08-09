<?php
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include("./include.php");
// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('');

if ($user->data['user_id'] == ANONYMOUS) {
    login_box('', $user->lang['LOGIN']);
}

$category_id = intval(request_var('c', 0));
$category = getSQLRows("SELECT `category_name` FROM `gallery_categories` WHERE `category_id` = ".$category_id);
$category_name = $category[0]['category_name'];
?>
<html>
<head>
    <meta content="Science Olympiad Student Center" property="og:title"/>
    <meta content="website" property="og:type"/>
    <meta content="https://scioly.org/" property="og:url"/>
    <meta content="https://scioly.org/src/img/logo/logo_square.png" property="og:image"/>
    <meta content="A resource by and for Science Olympiad students, coaches, and alumni nationwide."
          property="og:description"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <script src="https://scioly.org/src/js/jquery.min.js" type="text/javascript"></script>
    <script src="https://scioly.org/src/js/php.js" type="text/javascript"></script>

    <link href="fontawesome/css/all.min.css" rel="stylesheet">
    <link href="./main.css" rel="stylesheet" type="text/css">

    <link href="https://scioly.org/favicon.ico" rel="shortcut icon" type="image/x-icon">
    <link href="https://scioly.org/favicon.ico" rel="icon" type="image/x-icon">
    <title>Science Olympiad Student Center - Gallery</title>
    <meta content="Science Olympiad Student Center" property="og:title"/>
    <meta content="website" property="og:type"/>
    <meta content="https://scioly.org" property="og:url"/>
    <meta content="https://scioly.org/src/img/logo/logo_square.png" property="og:image"/>
    <meta content="A resource by and for Science Olympiad students, coaches, and alumni nationwide."
          property="og:description"/>
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
    <!--    <link rel="stylesheet" href="css/lightgallery.min.css">-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-modal/0.9.1/jquery.modal.min.css"/>
    <link rel="stylesheet" href="css/toastr.min.css">
    <link href="./gallery.css" rel="stylesheet">

    <!--    <script type="text/javascript" src="http://livejs.com/live.js"></script>-->
    <!--    <script src="https://masonry.desandro.com/masonry.pkgd.js"></script>-->
    <script src="js/toastr.min.js"></script>
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

        <li><a class="button" href="https://scioly.org/forums/memberlist.php?mode=viewprofile&u=46711">samy-oak-tree</a>
        </li>
    </ul>
</div>
<img class="print-only" src="https://scioly.org/src/img/logo/logo_black.png" style="max-width: 150px;">

<div class="container" id="post-writing-container">
    <div class="go-back">
        <a href="index.html">Categories</a> &lt;
        <a href="category.php?c=<?php echo $category_id; ?>"><?php echo $category_name; ?></a> &lt;
        New Post
        <span style="display:none" class="category_id"><?php echo $category_id; ?></span>
    </div>
    <div class="info-area">
        <div class="title-area">
            <textarea id="post-title" oninput="auto_grow(this)" placeholder="Title" type="text"></textarea>
        </div>
    </div>
    <div class="add-pics">
        <div class="pics"></div>
        <button class="add-yt-btn" href="#ex1" rel="modal:open">Add Youtube Video</button>
        or
        <input placeholder="Upload File" type="file" id="load_upload">
    </div>
    <textarea id="post-input" oninput="auto_grow(this)" placeholder="Post Description"></textarea>
    <textarea id="category_id" style="visibility:hidden"><?php echo $category_id; ?></textarea>
    <button class="post-reply">Post into <?php echo $category_name; ?></button>
</div>
<div class="modals">
    <!-- Modal HTML embedded directly into document -->
    <div id="ex1" class="modal">
        <h4>Upload YouTube Video</h4>
        <img src="" alt="" id="youtube_video_thumbnail" class="hidden">
        <input type="text" id="youtube_modal_input"
               placeholder="YouTube Video URL (https://www.youtube.com/watch?v=ih5ts1Kzglk)">
        <div class="flex">
            <button id="load_youtube_video">Load Video</button>
            <a id="youtube_modal_close" href="#" rel="modal:close">Cancel</a>
        </div>
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

<script></script>
<!--<script src="js/lg-thumbnail.min.js"></script>-->
<!--<script src="js/lg-fullscreen.min.js"></script>-->
<script src="js/sweetalert2.all.min.js"></script>

<script src="add.js"></script>
</body>

</html>