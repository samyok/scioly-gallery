<?php
if (!defined('IN_PHPBB')) include "../lost.php";

function headHTML($image = 'https://scioly.org/src/img/logo/logo_square.png', $title = "Science Olympiad Student Center", $desc = "A resource by and for Science Olympiad students, coaches, and alumni nationwide.", $site_name = "Scioly.org Gallery", $large_image = false)
{
    $summary_large_image_html = '<meta name="twitter:card" content="summary_large_image">';
    if (!$large_image) $summary_large_image_html = '';
    return '
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script type="text/javascript" src="https://scioly.org/src/js/jquery.min.js"></script>
    <script type="text/javascript" src="https://scioly.org/src/js/php.js"></script>

    <link rel="stylesheet" href="https://bcdn.scioly.gallery/gallery/css/font-awesome-4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="./main.css">

    <link rel="shortcut icon" href="https://scioly.org/favicon.ico" type="image/x-icon">
    <link rel="icon" href="https://scioly.org/favicon.ico" type="image/x-icon">
    <meta property="og:title" content="' . $title . '"/>
    <meta property="og:site_name" content="' . $site_name . '">
    <meta property="og:type" content="article"/>
    <meta property="og:url" content="https://scioly.org"/>
    <meta property="og:image" content="' . $image . '"/>
    ' . $summary_large_image_html . '
    <meta name="theme-color" content="#2E66B6">
    <meta property="og:description"
          content="' . $desc . '"/>
    <!-- google analytics -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-340310-3"></script>
    <script>window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag("js", new Date());
        gtag("config", "UA-340310-3");</script>
    <!-- <script type="text/javascript" src="https://scioly.org/src/js/snowstorm.js"></script> -->
    <!-- <link rel="stylesheet" type="text/css" href="https://scioly.org/src/css/lights.css"> -->
    <!-- <script type="text/javascript" src="https://scioly.org/src/js/fireworks.js"></script> -->';
}

function navigationHTML($user)
{
    return '<!-- <ul class="lightrope"><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li><li></li></ul> -->
    <!-- <div class="banner" style="background-color: #555555;"> -->
    <div class="banner">
        <!-- <p><a href="https://scioly.org/shirts">Scioly.org t-shirts are available for a limited time! Click here for more information. Orders close Wednesday, May 8, 2019 at 11:59 PM PST.</a></p> -->
        <!-- <p><a href="https://scioly.org/fantasy">Who will win? Click here to predict event medalists and top teams for this year\'s national tournament. Contest closes Friday, May 31, 2019 at 10:00 AM ET.</a></p> -->
        <!-- <p><a href="https://scioly.org/nationals" style="color: #ffffff;">One page for all our nationals-related events! Click here for prediction contest, medal counts, and more!</a></p> -->
        <p><a href="https://scioly.org/forums/viewtopic.php?f=24&t=15660">Welcome to the new season! Click here to learn
                what\'s new.</a></p>
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
    
            <li><a href="https://scioly.org/forums/memberlist.php?mode=viewprofile&u=' . $user->data['user_id'] . '"
                   class="button">' . $user->data['username'] . '</a>
            </li>
        </ul>
    </div>
    <img class="print-only" src="https://scioly.org/src/img/logo/logo_black.png" style="max-width: 150px;" alt="Print Logo">
    ';
}

function footerHTML()
{
    return '
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
    ';
}