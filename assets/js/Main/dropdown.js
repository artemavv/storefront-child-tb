jQuery($ => {
    $('.user-panel__login')
        .on('click', function () {
            $('#auth-modal')
                .fadeToggle()
            $('#auth-header')
                .fadeToggle()
        })

    $('.auth-modal__close')
        .on('click', function () {
            $('#auth-modal')
                .fadeToggle()
        })

    $(document)
        .on('click', function (e) {
            if ($(e.target)
                .closest($('.user-panel')).length === 0) {
                $('#auth-header')
                    .fadeOut()
            }
        })
})
