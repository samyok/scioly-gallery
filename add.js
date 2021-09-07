/**
 * add.js created by samy-oak-tree (2020)
 *
 * This JS file handles all the JS for add.php -- the post where adding and editing of new images happens.
 */
let Toast;
$(document).ready(() => {
    setTimeout(() => {
        auto_grow(document.querySelector("#post-input"));
    }, 500)
    $("#category_list").on('change', e => {
        $(".category_name").text($("#category_list option:selected").text());
        $(".category_link").attr("href", "category.php?c=" + $("#category_list").val());
    }).val(window.CATEGORY_ID).trigger('change');
    Toast = Swal.mixin({
        toast: true,
        position: 'middle',
        showConfirmButton: false,
        timer: 3000
    })

    $(".add-yt-btn").on("click", () => {
        // if (PICTURES.length < 1) {
        //     $(".add-yt-btn").attr("disabled", true);
        //     Toast.fire({
        //         type: "warning",
        //         title: "You cannot upload a YouTube video first!"
        //     });
        //     return;
        // }
        $("#ex1").modal({
            escapeClose: false,
            clickClose: false,
            showClose: false
        });
    });

    // Get youtube ID from the various possible types.
    // More info here: https://stackoverflow.com/a/27728417
    const ytrx = /^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/;
    $("#load_youtube_video").on("click", () => {
        try {
            let yt_input = $("#youtube_modal_input");
            let vid_id = yt_input.val().match(ytrx);
            console.log(vid_id);
            $(imageTemplate(`https://img.youtube.com/vi/${vid_id[1]}/hqdefault.jpg`, vid_id[1])).appendTo($(".pics"));
            // reattach the sortable drag and drop listeners
            reattachDNDListeners()
            PICTURES.push({
                isYoutube: true,
                video_id: vid_id[1]
            })
            yt_input.val("");
            $("#youtube_modal_close").click();
        } catch (e) {
            Toast.fire({
                type: 'warning',
                title: 'Video not found'
            });
        }
    });

    $("#load_upload").change(async (event) => {
        event.target.setAttribute('disabled', true);
        document.querySelector(".post-reply").setAttribute("disabled", true);
        Swal.showLoading({
            allowOutsideClick: false,
            allowEscapeKey: false
        });
        try {
            await readURL(event.target)
        } catch (e) {
            console.log(e);
            Swal.update({
                type: "error",
                title: "Oh no!",
                text: e.toString(),
                showConfirmButton: true
            })
        } finally {
            Swal.hideLoading();
            $("#load_upload").val("")
            event.target.removeAttribute('disabled')
            document.querySelector(".post-reply").removeAttribute("disabled");
        }
    });

    $(".post-reply, .save-edit").on("click", () => {
        Swal.fire({
            title: "Posting...",
            allowOutsideClick: () => !Swal.isLoading()
        });
        Swal.showLoading();
        submit_post()
    });
    $(".cancel_edits").on("click", () => {
        console.log(location.href = `picture.php?p=${window.EDIT_POST}`)
    });

    showImages();
})

function showImages() {
    // this runs on init to put the pictures in the correct place.
    $(".pics").html("");
    PICTURES.forEach(picture => {

        // force an integer, otherwise "0" reads true
        let is_hidden = picture.is_hidden >> 0;

        if (+picture.isYoutube) { // the + turns it into an integer, otherwise "0" would return true :/
            // we want image urls on youtube videos instead of image id. :|
            $(imageTemplate(`https://img.youtube.com/vi/${picture.image_uri}/hqdefault.jpg`, picture.image_uri, is_hidden))
                .appendTo($(".pics"));
        } else {
            $(imageTemplate(picture.image_uri, picture.image_id, is_hidden))
                .appendTo($(".pics"));
        }
    })
    // attach drag and drop listeners

    reattachDNDListeners()

}

const imageTemplate = (src, image_id, is_hidden) => `
    <div class="picture ${is_hidden ? 'deleted' : ''}" data-image-id="${image_id}">
        <img src="${src}" class="bkg" alt="Picture to be uploaded"/>
        <div class="delete-image">
            ${is_hidden ? 'Restore' : 'Delete'}
        </div>
    </div>`;

async function readURL(input) {
    if (input.files && input.files[0]) {
        for (let i = 0; i < input.files.length; i++) {
            let file = input.files[i];
            console.log(file.size);
            if (file.size > 5000000) throw "Please upload an image smaller than 5mb";

        }
        let data = await Bucket.upload({}, input);
        console.log(data);
        for (let i = 0; i < data.length; i++) {


            let imageuri = Bucket.imageURI({id: data[i].oid, name: 'image', extension: data[i].extension});
            $(imageTemplate(imageuri, data[i].oid)).appendTo($(".pics"));
            // reattach the sortable drag and drop listeners
            reattachDNDListeners()
            Toast.fire({
                type: 'success',
                title: 'Image uploaded successfully!'
            })
            PICTURES.push({
                isYoutube: false,
                image_id: data[i].oid,
                image_uri: imageuri
            });
        }

        // let reader = new FileReader();
        //
        // reader.onload = function (e) {
        //     console.log(e);
        //     let fd = new FormData();
        //     fd.append('fileToUpload', file);
        //
        //     $.ajax({
        //         url: "upload.php",
        //         type: "post",
        //         data: fd,
        //         contentType: false,
        //         processData: false,
        //         success: res => {
        //             res = JSON.parse(res);
        //             if (!res.success) {
        //                 Toast.fire({
        //                     type: 'error',
        //                     title: res.message
        //                 });
        //                 return;
        //             }
        //             $(".add-yt-btn").attr("disabled", false);
        //         }
        //     })
        // }
        //
        // reader.readAsDataURL(input.files[0]);


    } else {
        console.log("no")
        console.log(input);
    }
}

function auto_grow(element) {
    element.style.height = "5px";
    element.style.height = (element.scrollHeight) + "px";
}

let sortable = null;

function reattachDNDListeners() {
    if (sortable && sortable.destroy) sortable.destroy();
    sortable = new Sortable.default(document.querySelectorAll('.pics'), {
        draggable: '.picture'
    });

    sortable.on('sortable:start', (a) => {
        console.log('sortable:start', a)
        let {height, width} = a.data.dragEvent.originalSource.querySelector('img');
        if (!!a.data.dragEvent.originalSource.classList.contains('deleted')) {
            width = Math.min(500, 50 * width / height);
        } else {
            width = Math.min(500, 200 * width / height);
        }
        a.data.dragEvent.source.querySelector('img').style.width = width + 'px';
    });
    sortable.on('sortable:sort', (a) => console.log('sortable:sort', a));
    sortable.on('sortable:sorted', (a) => console.log('sortable:sorted', a));
    sortable.on('sortable:stop', (...args) => {
        console.log('sortable:stop', ...args);
        on_sort_finish(...args);

    });

    // reattach image delete links
    $(".delete-image")
        .off("click")
        .on("click", delete_image)
}

function on_sort_finish(event) {
    console.log(event);
    let newOrder = [];
    let imageIDs = Array.from($(".picture")).map(img => $(img).attr("data-image-id"))
    window.PICTURES = PICTURES.sort((a, b) => {
        return imageIDs.indexOf(a.image_id) > imageIDs.indexOf(b.image_id) ? 1 : -1;
    })
    // document.querySelectorAll(".picture").forEach(e => newOrder.push(e.attributes['data-image-id'].value))
    // window.PICTURES = PICTURES.sort((a, b) => {
    //     let aProp = a.isYoutube ? a.video_id : "" + a.image_id;
    //     let bProp = b.isYoutube ? b.video_id : "" + b.image_id;
    //     let aIndex = newOrder.indexOf(aProp);
    //     let bIndex = newOrder.indexOf(bProp);
    //     return aIndex > bIndex ? 1 : -1;
    // })

}

function delete_image(e) {
    // see if picture needs to be deleted or restored.
    let image_id = e.target.parentElement.getAttribute('data-image-id');
    console.log({image_id});

    let indexOfImage = PICTURES.map(e => !+e.isYoutube ? "" + e.image_id : e.video_id).indexOf(image_id);
    PICTURES[indexOfImage].is_hidden = !+PICTURES[indexOfImage].is_hidden;
    showImages()
}

function submit_post() {
    let edit_reason = window.EDIT_POST ? $("#edit-reason").val() : "";
    // run it once more to weed out any race conditions.
    // This does have the effect of making every other call of this function extraneous oops
    // todo clean this up ^^
    on_sort_finish();

    // make the first image a non-deleted one:
    let firstImageId = PICTURES.filter(img => !+img.is_hidden)[0].image_id;
    let indexOfFirstImage = PICTURES.map(img => img.image_id).indexOf(firstImageId);
    let firstImage = PICTURES.splice(indexOfFirstImage, 1);
    window.PICTURES = [firstImage, PICTURES].flat()
    let data = {
        title: $("#post-title").val(),
        desc: $("#post-input").val(),
        images: JSON.stringify(PICTURES),
        category: $("#category_list").val(),
        post_id: window.EDIT_POST,
        edit_reason,
        date: Date.parse($("#post_date").val()) / 1000
    };
    if (window.COMBINING) data.combining = JSON.stringify(+COMBINING ? [COMBINING] : COMBINING.split(','));

    console.log({data});
    // return;
    $.ajax({
        url: "api.php?object=post&action=POST",
        type: "POST",
        data,
        success: (res) => {
            res = JSON.parse(res);
            console.log(res);
            if (!res.success) {
                Swal.hideLoading();
                Swal.update({
                    type: "error",
                    title: "Oh no!",
                    text: res.error,
                    showConfirmButton: true
                })
            } else {
                Swal.hideLoading();
                Swal.fire({
                    type: "success",
                    title: "Submitted post!",
                    showConfirmButton: true,
                    showLoaderOnConfirm: true,
                    confirmButtonColor: '#2e66b6',
                    allowOutsideClick: false
                }).then(() => {
                    location.href = "picture.php?p=" + res.post_id
                })
            }
        }
    })
}