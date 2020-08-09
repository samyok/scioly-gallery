// homepage code
$(document).ready(()=>{
    $(".hamburger").on("click", ()=> {
        $(".menu-reactive").toggleClass("menu-open")
    })
    $(".img-tile").on("click", ()=>{
        location.href = "category.php";
    })
})