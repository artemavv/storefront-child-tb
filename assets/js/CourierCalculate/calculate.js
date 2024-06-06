jQuery($ => {
    let orderPerDay
    let daysInWeek
    let brand = false
    let pricePerOrder = 225
    let brandMultiplier = 1.2
    let weeksPerMonth = 4.33

    $('#orderPerDay').on('input', function () {
        const value = $(this).val()
        if (value > 0) {
            orderPerDay = value
        } else {
            orderPerDay = 0
        }
        calculate(orderPerDay, daysInWeek, brand, pricePerOrder, brandMultiplier, weeksPerMonth)
    })

    $('#choice-checkbox').on('input', function () {
        brand = this.checked
        calculate(orderPerDay, daysInWeek, brand, pricePerOrder, brandMultiplier, weeksPerMonth)
    })

    const $daysInWeek = $('#daysInWeek')

    $daysInWeek.on('input', function () {
        const value = $(this).val()
        if (value > 0 && value <= 7) {
            daysInWeek = value
        } else {
            daysInWeek = 0
        }
        calculate(orderPerDay, daysInWeek, brand, pricePerOrder, brandMultiplier, weeksPerMonth)
    })

    $daysInWeek.on('blur', function () {
        const value = $(this).val()
        if (value <= 0) {
            $(this).val(1)
        }
        if (value > 7) {
            $(this).val(7)
        }
    })

    const calculate = (orderPerDay, daysInWeek, brand, pricePerOrder, brandMultiplier, weeksPerMonth) => {
        let averagePrice;
        if (orderPerDay && daysInWeek) {
            averagePrice = Math.round(orderPerDay * daysInWeek * weeksPerMonth * pricePerOrder)

            if (brand) {
                averagePrice = Math.round(averagePrice * brandMultiplier)
            }

            $('#incomePrice').text(averagePrice.toLocaleString('ru-RU'))
            $('#incomeData').text(averagePrice.toLocaleString('ru-RU') + ' ₽')
            $('#incomeText').addClass('d-none')
        } else {
            const $incomePrice = $('#incomePrice')
            const defaultIncome = $incomePrice.data('income')
            $incomePrice.text(defaultIncome)
            $('#incomeData').text(defaultIncome + ' ₽')
            $('#incomeText').removeClass('d-none')
        }
    }

    $('#passengerCar').on('input', function () {
        if ($(this).val()) {
            $(this).parents('.choice__item').addClass('active')
            $('#truckCar').prop('disabled', true)
        } else {
            $(this).parents('.choice__item').removeClass('active')
            $('#truckCar').prop('disabled', false)
        }
    })

    $('#truckCar').on('input', function () {
        if ($(this).val()) {
            $(this).parents('.choice__item').addClass('active')
            $('#passengerCar').prop('disabled', true)
        } else {
            $(this).parents('.choice__item').removeClass('active')
            $('#passengerCar').prop('disabled', false)
        }
    })
})
