/* global $ */
(() => {
    window.Voting = {
        changeVote,
        callback,
        isIconAVoteButton
    }

    function changeVote(voteArea, value) {
        if(!!voteArea.jquery) voteArea = voteArea[0]; // make it vanilla

        let upvoteElement = voteArea.querySelector(".upvote");
        let downvoteElement = voteArea.querySelector(".downvote");

        // make both outlined first
        upvoteElement.classList.replace("fa-thumbs-up", "fa-thumbs-o-up");
        upvoteElement.classList.remove("voted");
        downvoteElement.classList.replace("fa-thumbs-down", "fa-thumbs-o-down");
        downvoteElement.classList.remove("voted");

        // add fill to correct element
        if (value === 1) {
            upvoteElement.classList.replace("fa-thumbs-o-up", "fa-thumbs-up");
            upvoteElement.classList.add('voted')
        }
        else if (value === -1) {
            downvoteElement.classList.replace("fa-thumbs-o-down", "fa-thumbs-down");
            downvoteElement.classList.add('voted')
        }
    }

    function callback(elem, postId) {

        let voteArea = $(elem).closest(".vote-area"); // parent of both icons
        let voteScore = voteArea.find(".vote-score");

        voteArea.addClass("loading");

        // if cancelling out past vote
        if (elem.classList.contains("voted")) {
            $.post("vote.php", {type: "post", vote: "cancel", id: postId}, data => {
                voteArea.removeClass("loading");
                if (!data.success) return toastr.error(data.error);

                // update html
                changeVote(voteArea, 0)
                voteScore.text(data.vote_count);
            })
        } else {
            $.post("vote.php", {
                type: "post",
                vote: elem.classList.contains("upvote") ? "up" : "down",
                id: postId
            }, data => {
                voteArea.removeClass("loading");
                if (!data.success) return toastr.error(data.error);

                // update html
                changeVote(voteArea, elem.classList.contains("upvote") ? 1 : -1)
                voteScore.text(data.vote_count);
            })
        }

    }

    function isIconAVoteButton(elem) {
        return elem.classList.contains("upvote") || elem.classList.contains("downvote");

    }


})();
