// jQuery(($) => {
//     $('#checkout').on('submit', function (e) {
//         e.preventDefault()
//         const data = $(this).serializeArray()
//     })
// })

// jQuery($ => {
//     $('#checkout').on('submit', function (e) {
//         e.preventDefault()
//
//         const data = $(this).serializeArray()
//         let isValid = false
//         const $delivery_date = $('[name="delivery_date"]')
//
//         data.forEach((el) => {
//             if (el.name === 'delivery_date') {
//                 const minDate = $('.delivery__input:checked').data('min-date')
//                 if (el.value < minDate) {
//                     isValid = false
//                     $delivery_date.addClass('is-invalid')
//                     $('body,html').animate(
//                         {scrollTop: $delivery_date.offset().top},
//                         300
//                     )
//                 } else {
//                     isValid = true
//                     $('[name="delivery_date"]').removeClass('is-invalid')
//                 }
//
//                 if (isValid) {
//                     $(this).unbind().submit()
//                 }
//             }
//         })
//     })
//
//     $('#delivery_date').on('change',
//         _.debounce(function () {
//             const $deliveryInput = $('.delivery__input:checked')
//             const currentDate = $deliveryInput.data('current-date')
//             const $delivery_time = $('[name="delivery_time"]')
//             const options = $('[name="delivery_time"] option')
//
//             const currentDataTimeArr = $deliveryInput.data('current-date-time-slots')
//                 ? $deliveryInput.data('current-date-time-slots').toString().split(',')
//                 : []
//             const othersDataTimeArr = $deliveryInput.data('other-date-time-slots')
//                 ? $deliveryInput.data('other-date-time-slots').toString().split(',')
//                 : []
//
//             if ($(this).val() >= $deliveryInput.data('min-date')) {
//                 $(this).removeClass('is-invalid')
//             } else {
//                 $(this).addClass('is-invalid')
//             }
//
//             if ($(this).val() > currentDate) {
//                 options.each((i, $el) => {
//                     $($el).prop('disabled', true)
//                     if (othersDataTimeArr.includes(i.toString())) {
//                         if (othersDataTimeArr[0] === i.toString()) {
//                             $delivery_time.val(i)
//                         }
//                         $($el).prop('disabled', false)
//                     }
//                 })
//             } else if (currentDate === $(this).val()) {
//                 options.each((i, $el) => {
//                     $($el).prop('disabled', true)
//                     if (currentDataTimeArr.includes(i.toString())) {
//                         if (currentDataTimeArr[0] === i.toString()) {
//                             $delivery_time.val(i)
//                         }
//                         $($el).prop('disabled', false)
//                     }
//                 })
//             }
//         }, 250))
//
//     function incrementDate(date) {
//         const curDate = new Date(date)
//
//         curDate.setDate(curDate.getDate() + 1)
//
//         const dd = curDate.getDate()
//         const mm = curDate.getMonth() + 1
//         const y = curDate.getFullYear()
//
//         return `${y}-${mm}-${dd}`
//     }
//
//     $('[name="delivery"]').on('change', function () {
//         if ($(this).is(':checked')) {
//             const minDate = $(this).data('min-date')
//             const currentDate = $(this).data('current-date')
//             let $delivery_date = $('[name="delivery_date"]')
//             const $delivery_time = $('[name="delivery_time"]')
//             const currentDataTimeArr = $(this).data('current-date-time-slots')
//                 ? $(this).data('current-date-time-slots').toString().split(',')
//                 : []
//             const othersDataTimeArr = $(this).data('other-date-time-slots')
//                 ? $(this).data('other-date-time-slots').toString().split(',')
//                 : []
//
//             const options = $('[name="delivery_time"] option')
//
//             $delivery_date.attr('min', minDate)
//             $delivery_date.val(minDate).trigger('change')
//
//             if ($delivery_date.val() === currentDate && currentDataTimeArr.length === 0) {
//                 const nextDate = incrementDate($delivery_date.val())
//                 $delivery_date.val(nextDate)
//             }
//
//             if ($delivery_date.val() > currentDate) {
//                 options.each((i, $el) => {
//                     $($el).prop('disabled', true)
//                     if (othersDataTimeArr.includes(i.toString())) {
//                         if (othersDataTimeArr[0] === i.toString()) {
//                             $delivery_time.val(i)
//                         }
//                         $($el).prop('disabled', false)
//                     }
//                 })
//             } else if ($delivery_date.val() === currentDate) {
//                 options.each((i, $el) => {
//                     $($el).prop('disabled', true)
//                     if (currentDataTimeArr.includes(i.toString())) {
//                         if (currentDataTimeArr[0] === i.toString()) {
//                             $delivery_time.val(i)
//                         }
//                         $($el).prop('disabled', false)
//                     }
//                 })
//             }
//         }
//     })
// })
//
