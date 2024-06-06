jQuery(($) => {
    $(".catalog-collapse__btn, .delivery__title").on(
        "click",
        function () {
            $(this).toggleClass("active");
            $(this).next().slideToggle();
        }
    );

    $(".checkout__header").on("click", function () {
        $(this).parent().toggleClass("active");
        $(this).toggleClass("active");
        $(this).next().slideToggle();
    });
});
