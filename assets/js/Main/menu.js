jQuery($ => {

    $('.menu-btn__icon').on('click', function () {
        $('.mobile-menu').toggleClass('active')
        $('.menu-icon').toggleClass('active')
        $('.menu-btn__text').toggleClass('active')

        if($('.menu-btn__text').hasClass('active')) {
            $('.menu-btn__text').text('закрыть')
        } else {
            $('.menu-btn__text').text('меню')
        }
    })
})
