<?php

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('');

//if ($user->data['user_id'] == ANONYMOUS) {
//    login_box('', $user->lang['LOGIN']);
//}
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

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="./main.css">

    <link rel="shortcut icon" href="https://scioly.org/favicon.ico" type="image/x-icon">
    <link rel="icon" href="https://scioly.org/favicon.ico" type="image/x-icon">
    <title>Science Olympiad Student Center - Gallery</title>
    <meta property="og:title" content="Science Olympiad Student Center"/>
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="https://scioly.org"/>
    <meta property="og:image" content="https://scioly.org/src/img/logo/logo_square.png"/>
    <meta property="og:description"
          content="A resource by and for Science Olympiad students, coaches, and alumni nationwide."/>
    <!-- google analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-340310-3"></script>
    <script>window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());
        gtag('config', 'UA-340310-3');</script>
    <!-- <script type="text/javascript" src="https://scioly.org/src/js/snowstorm.js"></script> -->
    <!-- <link rel="stylesheet" type="text/css" href="https://scioly.org/src/css/lights.css"> -->
    <!-- <script type="text/javascript" src="https://scioly.org/src/js/fireworks.js"></script> -->
    <link rel="stylesheet" href="./gallery.css">
    <script type="text/javascript" src="http://livejs.com/live.js"></script>
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

        <li><a href="https://scioly.org/forums/memberlist.php?mode=viewprofile&u=46711"
               class="button"><?php echo $user->data['username']; ?></a>
        </li>
    </ul>
</div>
<img class="print-only" src="https://scioly.org/src/img/logo/logo_black.png" style="max-width: 150px;">

<div class="container menu-reactive">
    <div class="gal-nav">
        <h1>Gallery</h1>
        <p>Welcome to the SciOly gallery! Before you post, please read the information post <a href="here">here</a>.</p>
    </div>
    <div class="gallery">

        <?php
        // find post with highest votes in category
        $sql = 'SELECT category_id, category_name, category_division FROM gallery_categories';
        global $db;
        $result = $db->sql_query($sql);
        $rows = $db->sql_fetchrowset($result);
        foreach ($rows as $row) {
            $thumbnailSQL = 'SELECT `image_uri` FROM `gallery_images` WHERE `belongs_to_category` = ' . $row['category_id'] . ' AND `is_youtube` = FALSE AND `is_hidden` = FALSE ORDER BY RAND() LIMIT 1;';
            $thumbnailRslt = $db->sql_query($thumbnailSQL);
            $thumbnailRows = $db->sql_fetchrowset($thumbnailRslt);
            $thumbnailRow = $thumbnailRows[0];
            $thumbnailImageURI = $thumbnailRow['image_uri'];
            ?>
            <a class="img-tile" href="category.php?c=<?php echo $row['category_id'];?>">
                <div class="bk-img" style="background-image: url('<?php echo $thumbnailImageURI; ?>')">
                </div>
                <div class="tile-name">
                    <?php echo $row['category_name']; ?>
                </div>
                <div class="tile-div">
                    <span class="div">
                        <?php echo $row['category_division'] ?>
                    </span>
                </div>
            </a>
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
<script src="./gallery.js"></script>
</body>

</html>
