(function ($) {
    var config = {
        "show_more": window.SMFN_object.show_more,
        "show_less": window.SMFN_object.show_less,
        "max_width": window.SMFN_object.media_query
    };

    //cache DOM
    var $ft = $(".footnote"),
        $ftTrigger = $(".js-article-aside-trigger"),
        screenW = window.matchMedia("(max-width: " + config.max_width + "px)");

    mediaQueries(screenW);
    screenW.addListener(mediaQueries);

    if ($ft.length)
        distributeFn();

    function mediaQueries(screenW) {
        //If max-width: 1023px
        if (screenW.matches) {
            //Toggle citation display
            $ftTrigger.on("click", function () {
                $(this).toggleClass("aside-active").siblings(".article-aside").toggleClass("aside-active");
            });
            //Remove show more button unnecessary on this media query
            $(".article-aside--more-link").remove();
        } else {
            $ft.addClass("show"); //Display footnote smoothly
            $ftTrigger.off(); //Remove click listener
            //Add show more button
            $ft.each(function () {
                //Get content of footnote stripping html tags
                var cite = $(this).find(".article-aside-txt").html().replace(/<(?:.|\n)*?>/gm, "");
                //Check if footnote has more than 1 line to insert "show more" button
                if (cite.length > 50) {
                    //Create trigger element
                    var showMore = document.createElement("a");
                    showMore.className = "article-aside--more-link js-article-unabridge-trigger";
                    showMore.innerHTML = config.show_more;
                    showMore.addEventListener("click", function () {
                        var $triggerContainer = $(this).parents(".footnote");
                        $triggerContainer.toggleClass("article-aside--abridged");
                        $triggerContainer.hasClass("article-aside--abridged") ? $(this).html(config.show_more) : $(this).html(config.show_less);
                    });
                    $(this).addClass("article-aside--abridged").append(showMore);
                }
            });
        }
    }

    function distributeFn() {
        //Clean alignment classes
        //$ft.removeClass("right");
        //Assing class right to every other footnote
        $(".footnote:odd").each(function () {
            $(this).addClass("right");
        });
    }

})(window.jQuery);