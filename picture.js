/**
 * picture.php created by samy-oak-tree (2020)
 *
 * TODO rename to post.php/post.js
 * Manage the JS for 'view one post' page.
 */
$(document).ready(() => {
    let lg = $("#carousel").lightGallery({
        share: false
    });

    lg.on('onBeforeClose.lg', () => {
        if (!location.href.includes("picture.php")) location.reload();
    })

    $(".truncate").each((_, el) => {
        truncate(el);
    });

    $(".description .post-caption").on("click", ev => {
        console.log(ev.target)
        let img = Array.from($("a"))
            .filter(a => a && a.getAttribute("data-sub-html")?.endsWith("#" + ev.target.id));
        if (img && img[0]) img[0].click()
    })


    $(".delete-post").on("click", delete_post);
    $(".restore-post").on("click", restore_post);
    $(".edit-post").on("click", edit_gui);
    $(".post-reply").on("click", post_reply);
    if (location.hash && !location.hash.includes("lg")) { // exclude lightgallery
        document.querySelector(window.location.hash)?.classList.add('selected');
    }

    let currentReport = {
        type: null,
        id: null,
    }
    $("#send_report").on('click', () => {
        $("#send_report").attr('disabled', true);
        $.post("api.php?object=report&action=create", {
            object: 'report',
            action: 'create',
            type: currentReport.type,
            id: currentReport.id,
            reason: $("#report-reason").val()
        }, data => {
            $("#send_report").attr('disabled', false);
            if (!data.success) return toastr.error(data.error);
            toastr.success('Submitted report');
            $("#report-area").hide(250);
            $("#report-reason").val("");
        })
    })


    $(".comment-info.date").on('click', (event) => {
        let parentTag = event.target.closest('.comment-info');
        let reply_id = $(parentTag).attr('data-reply-id');
        location.hash = '#c' + reply_id;
        if (location.hash) {
            $(".comment").removeClass("selected");
            document.querySelector(window.location.hash).classList.add('selected');
        }
    })

    $(".delete").on('click', (event) => {
        console.log(event);
        let commentID = $(event.currentTarget).attr('data-reply-id');
        if (commentID) {
            delete_comment(commentID);
        } else {
            console.log({commentID});
        }
    })

    $(".restore").on('click', (event) => {
        console.log(event);
        let commentID = $(event.currentTarget).attr('data-reply-id');
        if (commentID) {
            restore_comment(commentID);
        } else {
            console.log({commentID});
        }
    })

    $(".report").on('click', (event) => {
        let commentID = $(event.currentTarget).attr('data-reply-id');
        if (commentID) {
            $("#report-area").insertAfter($('#c' + commentID)).show(250);
            currentReport = {type: 'comment', id: commentID}
        } else {
            $("#report-area").insertAfter($('.info-area')).show(250);
            currentReport = {type: 'post', id: window.POST_ID}
        }
    })
    $("i").on('click', event => {
        if (Voting.isIconAVoteButton(event.target)) {
            Voting.callback(event.target, window.POST_ID)
        }
    })
    $(".hamburger").on("click", () => {
        $(".menu-reactive").toggleClass("menu-open")
    });
});

function auto_grow(element) {
    element.style.height = "5px";
    element.style.height = (element.scrollHeight) + "px";
}

let edits = {old: {}, new: {}};


function truncate(el) {
    let elem = $(el), text = elem.text(), i = 0;
    let ending = "";
    while (el.offsetWidth > 200) {
        ending = "...";
        elem.text(text.substr(0, text.length - (++i)))
    }
    elem.text(elem.text() + ending);
}

function edit_gui() {
    location.href = `add.php?edit=${window.POST_ID}`;
}

function post_reply() {
    $("#comment-add-text, .post-reply").addClass("loading").attr("disabled", true);

    $.post('api.php', {
        object: 'comment',
        action: 'create',
        content: $("#comment-add-text").val(),
        parent_post: window.POST_ID
    }, data => {
        $("#comment-add-text, .post-reply").removeClass("loading").attr("disabled", false);
        if (data.success) return location.reload();
        else toastr.error(data.error)
    })
}

function delete_post() {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you really want to permanently delete this post?",
        showConfirmButton: true,
        confirmButtonText: "Yes, delete it.",
        confirmButtonColor: '#aa0000',
        showCancelButton: true,
        cancelButtonText: "Cancel",
        cancelButtonColor: '#555555',
        reverseButtons: true,
        allowEnterKey: false,
        type: "warning",
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                method: "POST",
                url: "api.php",
                data: {
                    object: 'post',
                    action: 'DELETE',
                    post_id: window.POST_ID
                }
            }).then(data => {
                return data;
            })
        },
        allowOutsideClick: () => !Swal.isLoading() // while deleting don't allow outside click
    }).then((result) => {
        if (result.dismiss) return;

        const {success, error} = result;
        if (!error) {
            Swal.fire('Deleted!', 'Your post has been deleted.', 'success').then(() => {
                location.href = $(".go-back-category").attr("href");
            })
        } else {
            Swal.fire("Something went wrong...", error, "error");
        }
    })
}

function restore_post() {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you really want to restore this post?",
        showConfirmButton: true,
        confirmButtonText: "Yes, restore.",
        confirmButtonColor: '#208300',
        showCancelButton: true,
        cancelButtonText: "Cancel",
        cancelButtonColor: '#d7d7d7',
        reverseButtons: true,
        allowEnterKey: false,
        type: "warning",
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                method: "POST",
                url: "api.php",
                data: {
                    object: 'post',
                    action: 'RESTORE',
                    post_id: window.POST_ID
                }
            }).then(data => {
                return data;
            })
        },
        allowOutsideClick: () => !Swal.isLoading() // while deleting don't allow outside click
    }).then((result) => {
        if (result.dismiss) return;

        const {success, error} = result;
        if (!error) {
            Swal.fire('Restored!', 'Your post has been restored.', 'success').then(() => {
                location.reload();
            })
        } else {
            Swal.fire("Something went wrong...", error, "error");
        }
    })
}

function restore_comment(n) {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you really want to restore this comment?",
        showConfirmButton: true,
        confirmButtonText: "Yes, restore.",
        confirmButtonColor: '#208300',
        showCancelButton: true,
        cancelButtonText: "Cancel",
        cancelButtonColor: '#acacac',
        reverseButtons: true,
        allowEnterKey: false,
        type: "warning",
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                method: "POST",
                url: "api.php",
                data: {
                    object: 'comment',
                    action: 'restore',
                    reply_id: n
                }
            }).then(data => {
                return data;
            })
        },
        allowOutsideClick: () => !Swal.isLoading() // while deleting don't allow outside click
    }).then((result) => {
        if (result.dismiss) return;

        const {success, error} = result;
        if (!error) {
            Swal.fire('Restored!', 'Your comment has been restored.', 'success').then(() => {
                location.reload();
            })
        } else {
            Swal.fire("Something went wrong...", error, "error");
        }
    })
}

function delete_comment(n) {
    Swal.fire({
        title: "Are you sure?",
        text: "Do you really want to permanently delete this comment?",
        showConfirmButton: true,
        confirmButtonText: "Yes, delete it.",
        confirmButtonColor: '#aa0000',
        showCancelButton: true,
        cancelButtonText: "Cancel",
        cancelButtonColor: '#555555',
        reverseButtons: true,
        allowEnterKey: false,
        type: "warning",
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return $.ajax({
                method: "POST",
                url: "api.php",
                data: {
                    object: 'comment',
                    action: 'DELETE',
                    reply_id: n
                }
            }).then(data => {
                return data;
            })
        },
        allowOutsideClick: () => !Swal.isLoading() // while deleting don't allow outside click
    }).then((result) => {
        if (result.dismiss) return;
        const {success, error} = result;
        if (!error) {
            Swal.fire('Deleted!', 'Your comment has been deleted.', 'success').then(() => {
                location.reload();
            })
        } else {
            Swal.fire("Something went wrong...", error, "error");
        }
    })
}