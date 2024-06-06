const {on} = require('codemirror')

jQuery($ => {

    $('.js-range-slider').ionRangeSlider()

    $('.plus').on('click', function () {
        const input = $(this).closest('.counter').find('input')
        input.val(+input.val() + 1)
    })

    $('.minus').on('click', function () {
        const input = $(this).closest('.counter').find('input')
        input.val(+input.val() - 1)
    })

})


