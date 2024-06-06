jQuery($ => {
    const parserOptions = $('.parserOption')

    $('.parserToggle').on('change', function () {
        toggleOption($(this).attr('name'), $(this).is(':checked'))
    })

    const toggleOption = (name, value) => {

        if (name === 'parserOn' && value) {
            parserOptions.map((index, item) => {

                $(item).prop('disabled', false)
                $(item).prop('checked', true)
            })
        }
        if (name === 'parserOff' && value) {
            parserOptions.map((index, item) => {

                $(item).prop('disabled', true)
                $(item).prop('checked', false)
            })
        }
    }

    $('.parserToggle').map((index, item) => {
        toggleOption($(item).attr('name'), $(item).is(':checked'))
    })
})
