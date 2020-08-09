if (window.attachEvent) {
    window.attachEvent('onload', runOnload);
} else {
    if (window.onload) {
        const currentOnload = window.onload;
        window.onload = function (evt) {
            currentOnload(evt);
            runOnload(evt);
        };
    } else {
        window.onload = runOnload;
    }
}

const masonry_opts = {
    itemSelector: '.gallery-post-container',
    columnWidth: 410
};

function runOnload() {
    $('.gallery-category').show().masonry(masonry_opts)
    $(".hamburger").on("click", () => {
        $(".menu-reactive").toggleClass("menu-open")
    })
    $(".add-new-post").on("click", () => {
        location.href = "add.php?c=";
    })
    resizeMasonry();
    $(window).resize(resizeMasonry);

    $(".tag").on("click", (event) => {
        $(event.target).toggleClass("active");
    })
    $(".gallery-post").on("click", event => {
        let postId = $(event.target).closest(".gallery-post").attr("data-post-id");
        switch ($(event.target).prop("tagName")) {
            case "I":
                if ($(event.target).hasClass("fa-link")) {
                    let tempInput = $("<input/>");
                    tempInput.appendTo("body").val(location.href.replace(/category.+/g, "picture.php?p=" + postId)).select();
                    document.execCommand("copy");
                    tempInput.remove();
                    alert("Copied the link");
                }
                break;
            case "IMG":
                location.href = "picture.php?p=" + postId + "#lg=1&slide=0";
                break;
            default:
                location.href = "picture.php?p=" + postId;
        }
    })
    let sortbtn = $(".sort");
    sortbtn.on("click", () => {
        if (sortbtn.text().includes("Top")) {
            sortbtn.html("<i class=\"fab fa-hotjar\"></i> New\n")
        } else {
            sortbtn.html("<i class=\"fas fa-trophy\"></i> Top")
        }
    })
}

function resizeMasonry() {
    if (window.innerWidth > 450) {
        let curWidth = $(".gallery-category-container").innerWidth();
        let goodWidth = curWidth > 410 ? (Math.floor((curWidth - 40) / 410) * 410) + "px" : "100%";
        $(".gallery-category").css("width", goodWidth).masonry(masonry_opts);
        $(".gallery-post").css("width", "400px");
    } else {
        $(".gallery-category").masonry('destroy');
        $(".gallery-post").css("width", "100%");
    }
}