let yetToUpload = [], Toast;
$(document).ready(() => {
    Toast = Swal.mixin({
        toast: true,
        position: 'middle',
        showConfirmButton: false,
        timer: 3000
    })

    $(".add-yt-btn").on("click", () => {
        if(yetToUpload.length < 1){
            $(".add-yt-btn").attr("disabled", true);
            Toast.fire({
                type: "warning",
                title: "You cannot upload a YouTube video first!"
            });
            return;
        }
        $("#ex1").modal({
            escapeClose: false,
            clickClose: false,
            showClose: false
        });
    });
    // https://stackoverflow.com/a/27728417
    const ytrx = /^.*(?:(?:youtu\.be\/|v\/|vi\/|u\/\w\/|embed\/)|(?:(?:watch)?\?v(?:i)?=|\&v(?:i)?=))([^#\&\?]*).*/;
    $("#load_youtube_video").on("click", () => {
        try {
            let yt_input = $("#youtube_modal_input");
            let vid_id = yt_input.val().match(ytrx);
            console.log(vid_id);
            $("<img alt='Picture to be uploaded.'/>").attr("src", `https://img.youtube.com/vi/${vid_id[1]}/default.jpg`).appendTo($(".pics"));
            yetToUpload.push({
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

    $("#load_upload").change((event) => {
        readURL(event.target);
        $("#load_upload").val("")
    });

    $(".post-reply").on("click", () => {
        Swal.fire({
            title: "Posting...",
            allowOutsideClick: ()=> !Swal.isLoading()
        });
        Swal.showLoading();
        submit_post()
    })
})

function readURL(input) {
    if (input.files && input.files[0]) {
        let file = input.files[0];
        var reader = new FileReader();

        reader.onload = function (e) {

            console.log(e);
            let fd = new FormData();
            fd.append('fileToUpload', file);

            $.ajax({
                url: "upload.php",
                type: "post",
                data: fd,
                contentType: false,
                processData: false,
                success: res => {
                    res = JSON.parse(res);
                    if (!res.success) {
                        Toast.fire({
                            type: 'error',
                            title: res.message
                        });
                        return;
                    }
                    $(".add-yt-btn").attr("disabled", false);
                    $("<img alt='Picture to be uploaded.'/>").attr("src", e.target.result).appendTo($(".pics"));

                    Toast.fire({
                        type: 'success',
                        title: 'Image uploaded successfully!'
                    })
                    yetToUpload.push({
                        isYoutube: false,
                        image_id: res.image_id
                    });
                }
            })
        }

        reader.readAsDataURL(input.files[0]);


    } else {
        console.log("no")
        console.log(input);
    }
}

function auto_grow(element) {
    element.style.height = "5px";
    element.style.height = (element.scrollHeight) + "px";
}

function submit_post() {
    $.ajax({
        url: "/gallery/api.php?object=post&action=POST",
        type: "POST",
        data: {
            title: $("#post-title").val(),
            desc: $("#post-input").val(),
            images: JSON.stringify(yetToUpload),
            category: $(".category_id").text()
        },
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
                }).then(()=>{
                    location.href = "picture.php?p=" + res.post_id
                })
            }
        }
    })
}