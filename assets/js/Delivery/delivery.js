/*jQuery($ => {
    const $body = $('body')

    function updateCartAside(deliveryValue, bonusesToAdd = false, promocode = false, removePromocode = false) {
        let data = {
            deliveryId: +deliveryValue
        }
        if (bonusesToAdd !== false) {
            data.bonusAmount = +bonusesToAdd
        }
        if (promocode !== false) {
            data.promocode = promocode
        }
        if (removePromocode !== false) {
            data.removePromocode = true
        }
        const $aside = $('.cart-aside')
        const isDisabled = $('[type="submit"]').prop('disabled')
        $.ajax({
            type: 'POST',
            url: "/api/cart-aside",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: JSON.stringify(data),
            contentType: 'application/json',
            beforeSend: () => {
                $aside.addClass('loading')
                $('[name="delivery"]').prop('disabled', true)
            },
            success: data => {
                $aside.replaceWith(data['html'])
                $('[type="submit"]').prop('disabled', isDisabled)
                $('[name="delivery"]').prop('disabled', false)
            },
            error: e => {
                console.log(e)
                $('[name="delivery"]').prop('disabled', false)
                // $aside.removeClass('loading')
            }
        });
    }

    $body.on('input', '[name="delivery"]',
        _.debounce(function () {
            const deliveryValue = $(this).val()

            if (deliveryValue) {
                updateCartAside(deliveryValue)
            }
        }, 250))

    $body.on('click', '.add-bonus', function (e) {
        e.preventDefault()
        const deliveryValue = $('[name="delivery"]').val()
        const bonusesToAdd = $('[name="bonusesToAdd"]').val()
        if (deliveryValue) {
            updateCartAside(deliveryValue, bonusesToAdd)
        }
    })

    $body.on('click', '.remove-bonus', function (e) {
        e.preventDefault()
        const deliveryValue = $('[name="delivery"]').val()
        if (deliveryValue) {
            updateCartAside(deliveryValue, 0)
        }
    })

    $body.on('click', '.apply-promocode', function (e) {
        e.preventDefault()
        const deliveryValue = $('[name="delivery"]').val()
        const promocode = $('[name="promocode"]').val()
        if (deliveryValue && promocode) {
            updateCartAside(deliveryValue, false, promocode)
        }
    })

    $body.on('click', '.remove-promocode', function (e) {
        e.preventDefault()
        const deliveryValue = $('[name="delivery"]').val()
        const promocode = $('[name="promocode"]').val()
        if (deliveryValue) {
            updateCartAside(deliveryValue, false, false, true)
        }
    })
})*/
