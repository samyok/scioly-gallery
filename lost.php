<?php
/**
 * This page is the 'error 404' page except as a file so that I can include it whenever someone tries to hit an admin
 * endpoint without being authenticated.
 */
?>
    <html lang="en">
    <head>
        <title>Error - Science Olympiad Student Center</title>
    </head>
    <body>
    <div class="container">
        <div style="text-align: center;">
            <h1>We couldn't find the page you were looking for.</h1>
            <iframe id="youtube" width="640" height="360"
                    src="https://www.youtube.com/embed/ie4Y4Njnl5s?autoplay=1&start=34" frameborder="0" allowfullscreen
                    style="margin-top: 20px; margin-bottom: 5px; max-width: 100%;"></iframe>
        </div>
    </div>
    </body>
    </html>
<?php
exit();
?>