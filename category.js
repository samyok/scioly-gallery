/**
 * category.js created by samy-oak-tree (2020)
 *
 * This JS file handles a lot of the masonry that happens in category.php
 */
$(document).ready(() => {
    let pageURL = "category.php";
    if (window.PAGE_USER_ID) pageURL = "user.php";
    if (window.ALL_CATEGORIES) pageURL = "all.php";
    $('.gallery-category').show().masonry(masonry_opts)
    $(".hamburger").on("click", () => {
        $(".menu-reactive").toggleClass("menu-open")
    })
    $(".add-new-post").on("click", () => {
        location.href = "add.php?c=";
    })

    let waitingTimer;
    $(window).resize(() => {
        clearTimeout(waitingTimer)
        // if it hasn't been cleared within 500ms, it'll resize. woo!
        // Will be fired max 10x per second instead of 100x per sec!
        waitingTimer = setTimeout(resizeMasonry, 100);
    });
    resizeMasonry()
    $("#tabular_switch").on('click', (event) => {
        if (!localStorage.getItem('tabularView')) {
            localStorage.setItem('tabularView', '1');
        } else {
            localStorage.setItem('tabularView', '');
        }
        syncTabular()
    })

    function syncTabular() {
        if (!localStorage.getItem('tabularView')) {
            // no tabular view. Normal view.
            $("#tabular_switch i").removeClass('fa-th-list').addClass('fa-th');
            $(".gallery-category").removeClass('tabular');
        } else {
            $("#tabular_switch i").addClass('fa-th-list').removeClass('fa-th');
            $(".gallery-category").hide().addClass('tabular').show();
        }
        resizeMasonry();
        setTimeout(() => {
            resizeMasonry();
        }, 250);
    }

    syncTabular();
    let queryParams = new URLSearchParams(location.search);
    let currentFilters = queryParams.get('filter') ? queryParams.get('filter').split(',') : [];
    let search = queryParams.get('search') || '';
    let sort = queryParams.get('sort') || 'new';

    function replaceState(url) {
        if (window.history.pushState) {
            window.history.pushState({searchString: queryParams.toString()}, 'Search', `${pageURL}?${url}`);
        } else {
            location.href = `${pageURL}?${url}`;
        }
    }

    window.onpopstate = function (e) {
        if (e.state !== null) { // state data available
            queryParams = new URLSearchParams(e.state.searchString);
        } else {
            // no state available, probably a carousel. Use URLSearchParams for this.
            return;
            // queryParams = new URLSearchParams(location.search);
        }
        currentFilters = queryParams.get('filter') ? queryParams.get('filter').split(',') : [];
        search = queryParams.get('search') || '';
        sort = queryParams.get('sort') || 'new';
        currentPage = 0;
        $("#sort_by").val(sort);
        $("#searchbar").val(search);
        $(".tag").each((index, tag) => {
            if (currentFilters.includes($(tag).text())) $(tag).addClass('active');
            else $(tag).removeClass('active');
            console.log({tag, currentFilters})
        })
        retrieve_images();
    }

    // check each tag to see which ones are active
    $(".tag").each((index, tag) => {
        if (currentFilters.includes($(tag).text())) $(tag).addClass('active');
        else $(tag).removeClass('active');
        console.log({tag, currentFilters})
    }).on("click", (event) => {
        let tag = $(event.currentTarget);
        tag.toggleClass("active");
        if (tag.hasClass('active')) {
            currentFilters.push(tag.text());
        } else {
            currentFilters.splice(currentFilters.indexOf(tag.text()), 1);
        }
        queryParams.set('filter', currentFilters.join(','));
        replaceState(queryParams)
        retrieve_images()
    })

    $("#sort_by").val(sort).on('change', event => {
        sort = event.target.value;
        queryParams.set('sort', sort);
        replaceState(queryParams);
        retrieve_images();
    })

    $("#searchbar").val(search).on('keyup', (event) => {
        search = event.target.value;
        queryParams.set('search', search);
        if (event.key === 'Enter') {
            $("#search_btn").click()
        }
    })

    $("#search_btn").on('click', () => {
        replaceState(queryParams);
        retrieve_images()
    });

    let carousel = $("<div style='display: none'></div>").appendTo($('body'));
    // let gallery = carousel.lightGallery();
    // console.log({gallery});

    let selectModeActive = false;

    let selectedPosts = new Set();

    document.addEventListener('keydown', e => {
        if (e.key === "Alt") selectModeActive = true;
    })

    document.addEventListener('keyup', e => {
        if (e.key === "Alt") selectModeActive = false;
    })
    $(".combine-posts").on("click", event => {
        let selectedPostsArray = Array.from(selectedPosts);
        let combinedString = selectedPostsArray.filter((val, index) => index !== 0).join(",");
        // new combinedString
        location.href = "add.php?edit=" + selectedPostsArray[0] + "&combine=" + combinedString;
    })

    function cardClickHandlers() {
        $(".gallery-post").off("click").on("click", event => {
            let postElement = $(event.target).closest(".gallery-post");
            let postId = postElement.attr("data-post-id");
            if (selectModeActive) {
                postElement.toggleClass("selected-post");
                if (selectedPosts.has(postId)) selectedPosts.delete(postId);
                else selectedPosts.add(postId);

                if (selectedPosts.size > 0) {
                    $(".combine-posts").show()
                } else {
                    $(".combine-posts").hide();
                }

                console.log({selectedPosts});

                let combinePostsElem = $(".combine-posts");
                if (selectedPosts.size > 1) combinePostsElem.removeClass("hidden");
                else combinePostsElem.addClass("hidden");
                return;
            }
            switch ($(event.target).prop("tagName")) {
                case "I":
                    if (Voting.isIconAVoteButton(event.target)) {
                        Voting.callback(event.target, postId);
                    } else location.href = "picture.php?c=" + category + "&p=" + postId;

                    break;
                case "IMG":
                    // location.href = "picture.php?p=" + postId + "#lg=1&slide=0";
                    // We want to show just that image, don't redirect.
                    // to view the post they can click on the banner at the bottom

                    // append to bottom. Is invisible. then lightgallery that.
                    // first create images for post. then put in invisible container.
                    $.get('picture.php?images_only=true&p=' + postId, data => {

                        carousel.remove();
                        carousel = $("<div style='display: none'></div>").appendTo($('body'));
                        carousel.html(data);
                        carousel.find(".pics").lightGallery();
                        $("#lightroom_banner a").attr('href', "picture.php?c=" + category + "&p=" + postId);
                        const targetElement = document.querySelector('.lg-outer');
                        carousel.on('onAfterOpen.lg', (event) => {
                            $("#lightroom_banner").slideDown(250);
                            window.bodyScrollLock.disableBodyScroll(targetElement);

                            // pushState in browser so they are taken directly to the page if they copy URL
                            // instead of looking at the post in category

                            if (window.history.pushState) {
                                window.history.pushState({carousel: true}, 'Gallery Image Preview', `picture.php?p=${postId}${window.location.hash}`);
                            }

                        })
                        carousel.on('onBeforeClose.lg', (event) => {
                            $("#lightroom_banner").slideUp(50);
                            window.bodyScrollLock.enableBodyScroll(targetElement);
                            if (window.history.state && window.history.state.carousel) {
                                console.log('closing');
                                window.history.back();
                            }
                        })
                        carousel.find('a:first-child').click();
                    })
                    break;
                default:
                    location.href = "picture.php?p=" + postId;
            }
        })
    }

    cardClickHandlers();
    let currentPage = 1;
    let category = window.CATEGORY_ID;
    let intersectionObserverOptions = {
        threshold: 0.01
    }

    let observer = new IntersectionObserver(loadNextPage, intersectionObserverOptions);
    // let lastChild = document.querySelector(".gallery-post-container:nth-last-child(19)");
    // if (lastChild) observer.observe(lastChild);
    // else {
    // we have less than 20 here, which means we should show the last
    // $(".no-more-images").text("That's the end of this category!").show();

    // }

    let doneLoading = false;
    let isCurrentlyLoadingAPage = false;
    let lastLoadedPage = Date.now();


    $(window).scroll(windowScrollListener);

    function windowScrollListener() {
        if (doneLoading) return;
        let scrollPos = $(document).scrollTop();
        let maxDistance = $(document).height() - $(window).height();
        // how pixels above the bottom of the page should we trigger a refresh
        let threshold = Math.max(750, 1.5 * window.innerHeight);
        let rateLimit = 250; // wait at least 250ms between page loads

        if (scrollPos > maxDistance - threshold && Date.now() - lastLoadedPage > rateLimit && !isCurrentlyLoadingAPage) {
            isCurrentlyLoadingAPage = true;
            $(".no-more-images").text("Loading...").show();
            _rawNextPageLoading().then(isDone => {
                doneLoading = isDone;
                isCurrentlyLoadingAPage = false;
                lastLoadedPage = Date.now();
            });
        }

    }

    windowScrollListener();

    function _rawNextPageLoading() {
        return new Promise((resolve, reject) => {
            $.get(`images.php?page=${++currentPage}&${queryParams}`, data => {
                console.debug('data loaded')
                $(".category_loading_text").hide().remove();
                let categoryContainer = $(".gallery-category");
                if (!data || data === 'done') {
                    $(".no-more-images").show();
                    if ($(".gallery-post").length) $(".no-more-images").text("That's the end of this category!")
                    resolve(true);
                } else {
                    $(".no-more-images").hide();
                }
                if (shouldMasonryExist()) {
                    $(data).each((_, elem) => {
                        categoryContainer.masonry().append(elem).masonry('appended', elem).masonry();
                    })
                } else {
                    $(data).each((_, elem) => {
                        categoryContainer.append(elem);
                    })
                }
                resizeMasonry();
                cardClickHandlers();
                resolve(false);
            })

        })
    }

    function loadNextPage(e) {
        console.debug(`loading next page (is synthetic: ${e.synthetic})`)
        if (e.synthetic || e[0].isIntersecting) {
            if (!e.synthetic) observer.unobserve(e[0].target);
            _rawNextPageLoading().then(isDone => {
                if (isDone) {
                    if (e.synthetic) {
                        categoryContainer.html("");
                        resizeMasonry()
                    } else {
                        observer.unobserve(e[0].target);
                    }
                }
                if (!isDone) { // we're not done loading yet
                    let observingElem = document.querySelector(".gallery-post-container:nth-last-child(5)");
                    if (observingElem) observer.observe(observingElem);
                }
            })
        } else {
            console.log({e: e[0]})
        }
    }

    function retrieve_images() {
        // get images.php with relevant queries
        if (window.CATEGORY_ID) {
            queryParams.set('c', window.CATEGORY_ID);
        } else if (window.PAGE_USER_ID) {
            queryParams.set('u', window.PAGE_USER_ID);
        }
        // start from first one.
        currentPage = 0;
        // remove all images
        $(".gallery-category").html("<div class='category_loading_text'>Loading...</div>");
        $(".no-more-images").hide();
        // we use synthetic: true to signify that it's not an intersection observer firing this callback
        loadNextPage({synthetic: true})
    }
})
const masonry_opts = {
    itemSelector: '.gallery-post-container',
    columnWidth: 410
};

let doesMasonryExist = true;

function shouldMasonryExist() {
    return window.innerWidth > 870 && !localStorage.getItem('tabularView')
}

function resizeMasonry() {
    // let width = window.innerWidth;
    let width = document.querySelector("#gallery-width").getBoundingClientRect().width;
    if (shouldMasonryExist()) {
        console.debug('creating masonry');
        if (!doesMasonryExist) {
            doesMasonryExist = true;
            // for some reason this fixed a bug when creating a masonry again after going from a small screen.
            setTimeout(resizeMasonry, 500);
        }
        console.debug('recalculating masonry', width);
        let curWidth = window.innerWidth - 75;
        let goodWidth =
            curWidth > 410 ?
                (Math.floor((curWidth - 40) / 410) * 410) + "px" : "100%";
        let searchBarWidth = curWidth > 410 ?
            (Math.floor((curWidth - 40) / 410) * 410 - 10) + "px" : "100%";
        // $(".gallery-control").css("width", searchBarWidth);
        $(".gallery-post").css("width", "400px");
        $(".gallery-category").css('width', goodWidth).css('margin-left', null).masonry(masonry_opts);
    } else {
        if ($(".gallery-category").masonry && doesMasonryExist) $(".gallery-category").masonry('destroy')
        doesMasonryExist = false;
        console.log('killing masonry');
        $(".gallery-post").css("width", "100%");
        // $(".gallery-control").css("width", '100%');
        $(".gallery-category").css('width', '100%');//.css('margin-left', '-10px');//.masonry(masonry_opts);

    }
}
