$(document).ready(() => {
    $("#carousel").lightGallery({
        share: false
    });
    $(".truncate").each((_, el) => {
        truncate(el);
    });

    $(".hamburger").on("click", () => {
        $(".menu-reactive").toggleClass("menu-open")
    });
    if (parseInt($(".is_editable").text()) === 1) {
        $("h1.img-title").editable("click", e => {
            edited(e, "title")
        });
        $(".description p").editable({type: "textarea", action: "click"}, e => {
            edited(e, "description");
        });
        $("#cancel_edits").on("click", () => {
            let keys = Object.keys(edits.old);
            keys.forEach(key => {
                function escapeHtml(text) {
                    return text
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;")
                        .replace(/[\r\n]+/g, "<br/>")
                }

                $(edits.old[key].selector).html(escapeHtml(edits.old[key].value));
            });
            $(".confirm_edits").hide();
        })
    }
    $(".delete-post").on("click", delete_post);
    $(".edit-post").on("click", edit_gui);
});

function auto_grow(element) {
    element.style.height = "5px";
    element.style.height = (element.scrollHeight) + "px";
}

let edits = {old: {}, new: {}};

function edited(e, area) {
    if (e.value !== e.old_value) {
        $(".confirm_edits").show();
        if (!edits.old[area]) edits.old[area] = {
            value: e.old_value,
            selector: e.target.selector
        };
        edits.new[area] = e.value;
    }
    console.log(e, area);
}

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
    // Swal.fire("Any fool can use a computer :)")
    $lg = $("#carousel");
    $lg.on("onBeforeOpen.lg", event =>{
        console.log(event);
        $lg.data('lightGallery').destroy();
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
                url: "api.php?object=post&action=DELETE",
                data: {
                    post_id: $(".post_id").text()
                }
            }).then(data => {
                return data;
            })
        },
        allowOutsideClick: () => !Swal.isLoading() // while deleting don't allow outside click
    }).then((result) => {
        const {error, success} = JSON.parse(result.value);
        if (success) {
            Swal.fire('Deleted!', 'Your post has been deleted.', 'success').then(() => {
                location.href = $(".go-back-category").attr("href");
            })
        } else {
            Swal.fire("Something went wrong...", error, "error");
        }
    })
}

// jQuery.editable.js (v1.1.2 is broken, I fixed it)
// http://shokai.github.io/jQuery.editable
// (c) 2012-2015 Sho Hashimoto <hashimoto@shokai.org>
// The MIT License
// forked and patched by Samyok Nepal
(function ($) {
    var escape_html = function (str) {
        return str.replace(/</gm, "&lt;").replace(/>/gm, "&gt;")
    };
    var unescape_html = function (str) {
        return str.replace(/&lt;/gm, "<").replace(/&gt;/gm, ">")
    };
    $.fn.editable = function (event, callback) {
        if (typeof callback !== "function") callback = function () {
        };
        if (typeof event === "string") {
            var trigger = this;
            var action = event;
            var type = "input"
        } else if (typeof event === "object") {
            var trigger = event.trigger || this;
            if (typeof trigger === "string") trigger = $(trigger);
            var action = event.action || "click";
            var type = event.type || "input"
        } else {
            throw'Argument Error - jQuery.editable("click", function(){ ~~ })'
        }
        var target = this;
        var edit = {};
        edit.start = function (e) {
            trigger.unbind(action === "clickhold" ? "mousedown" : action);
            if (trigger !== target) trigger.hide();
            var old_value = (type === "textarea" ? target.html()
                .replace(/<br( \/)?>/gm, "\r\n")
                .replace(/&/g, "&")
                .replace(/&gt;/gm, ">")
                .replace(/&lt;/gm, "<") : target.text())
                .replace(/^\s+/, "").replace(/\s+$/, "");
            console.log(old_value);
            var input = type === "textarea" ? $("<textarea>") : $("<input>");
            input
                .val(old_value)
                .css("width", type === "textarea" ? "100%" : target.width() + target.height())
                .css("font-size", "100%")
                .css("margin", 0)
                .attr("id", "editable_" + new Date * 1)
                .addClass("editable");
            if (type === "textarea") input.css("height", target.height());
            var finish = function () {
                var result = input.val()
                    .replace(/^\s+/, "")
                    .replace(/\s+$/, "");
                var html = escape_html(result);
                if (type === "textarea") html = html.replace(/[\r\n]/gm, "<br />");
                target.html(html);
                callback({value: result, target: target, old_value: old_value});
                edit.register();
                if (trigger !== target) trigger.show()
            };
            input.blur(finish);
            if (type === "input") {
                input.keydown(function (e) {
                    if (e.keyCode === 13) finish()
                })
            } else if (type === "textarea") {
                input.keydown(function (e) {
                    auto_grow(e.target);
                })
            }

            target.html(input);
            input.focus()
        };
        edit.register = function () {
            if (action === "clickhold") {
                var tid = null;
                trigger.bind("mousedown", function (e) {
                    tid = setTimeout(function () {
                        edit.start(e)
                    }, 500)
                });
                trigger.bind("mouseup mouseout", function (e) {
                    clearTimeout(tid)
                })
            } else {
                trigger.bind(action, edit.start)
            }
        };
        edit.register();
        return this
    }
})(jQuery);