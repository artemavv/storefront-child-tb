jQuery($ => {
    $('.scrollToDescription').on('click', () => {
        let vw = document.documentElement.clientWidth;
        window.scrollTo({
            top: $('#productDescription').offset().top - (vw * 0.037),
            behavior: "smooth"
        })
    })
})