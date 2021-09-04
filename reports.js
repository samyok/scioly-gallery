/**
 * reports.js created by samy-oak-tree (2020)
 *
 * Manages the JS for reports.php -- admin page for managing reports. Some -- if not most -- of this code is
 * copied from picture.js
 */
function delete_post(n) {
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
                    post_id: n
                }
            }).then(data => {
                return data;
            })
        },
        allowOutsideClick: () => !Swal.isLoading() // while deleting don't allow outside click
    }).then((result) => {
        if(result.dismiss) return;

        const {error, success} = JSON.parse(result.value);
        if (success) {
            Swal.fire('Deleted!', 'Your post has been deleted.', 'success').then(() => {
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
        text: "Do you really want to permanently delete this comment",
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

        if(result.dismiss) return;

        const {error, success} = JSON.parse(result.value);
        if (success) {
            Swal.fire('Deleted!', 'Your comment has been deleted.', 'success').then(() => {
                location.reload();
            })
        } else {
            Swal.fire("Something went wrong...", error, "error");
        }
    })
}
function clear_report(n){
    $.post("api.php", {
        object: 'report',
        action: 'DELETE',
        id: n
    }, data => {
        $("#report-" + n).hide(200);
    })
}