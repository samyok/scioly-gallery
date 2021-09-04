/**
 * gallery.js created by samy-oak-tree (2019)
 *
 * Does the basic JS needed on / or /index.php
 */
$(document).ready(() => {
    $(".hamburger").on("click", () => {
        $(".menu-reactive").toggleClass("menu-open")
    })
    $(".img-tile").on("click", () => {
        location.href = "category.php";
    });

    // https://www.w3schools.com/jquery/jquery_filters.asp
    $("#searchbar").on("keyup", function () {
        let value = $(this).val().toLowerCase();
        $(".img-tile").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });

    // change background images of tiles after page load. This is not very important at all.
    $("[data-bkg-src]").fadeTo(0,0)
    setTimeout(() => {
        Array.from(document.querySelectorAll('[data-bkg-src]'))
            .forEach(elem => elem.style.backgroundImage = `url('${elem.getAttribute('data-bkg-src')}')`)

        setTimeout(() => {
            console.log('logggy');
            $("[data-bkg-src]").fadeTo(0,1)
        }, 0)
    }, 0)
})