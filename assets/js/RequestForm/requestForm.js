jQuery($ => {

    const successMessage = (text) => {
        text = text || 'Мы свяжемся с вами в ближайшее время.\n' +
            'По всем интересующим вопросам вы можете позвонить нам по телефону'
        return (
            `<div class="success-request-form">
                <img src="/svg/check-big.svg" alt="Заявка создана">
                <p class="success-request-form__title">Спасибо за заявку!</p>
                <p>${text}</p>
                <a class="mb-3" href="tel:88007777474">8 800 777 74 74</a>
                <a href="tel:84951907090">8 495 190 70 90</a>
            <div>`
        )
    }

    const successMessageReturn = (text) => {
        text = text || 'Мы свяжемся с вами в ближайшее время.\n' +
            'По всем интересующим вопросам вы можете позвонить нам по телефону'
        return (
            `<div class="success-request-form">
                <img src="/svg/check-big.svg" alt="Заявка создана">
                <p class="success-request-form__title">Заявка на возврат принята</p>
                <p>${text}</p>
                <a class="mb-3" href="tel:88007777474">8 800 777 74 74</a>
                <a href="tel:84951907090">8 495 190 70 90</a>
            <div>`
        )
    }

    function requestForm(formSelector, url, data) {
        $.ajax({
            type: 'POST',
            url: `${url}`,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: data,
            beforeSend: () => {
                $(formSelector).addClass('loading')
            },
            success: answer => {
                $(formSelector).removeClass('loading')
                $(formSelector).replaceWith(successMessage())
            },
            error: e => {
                console.log(e)
            }
        })
    }

    function returnProductRequest(formSelector, url, data, productId) {
        $.ajax({
            type: 'POST',
            url: `${url}`,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: data,
            beforeSend: () => {
                $(formSelector).addClass('loading')
            },
            success: answer => {
                $(formSelector).removeClass('loading')
                $(formSelector).children().hide()
                $(formSelector).append(successMessageReturn())
                $(`[data-productId="${productId}"]`).replaceWith(answer)
            },
            error: e => {
                console.log(e)
            }
        })
    }

    let productId = null
    $('[data-target="#returnProduct"]').on('click', function () {
        productId = $(this).data('id')
        $('#returnProductModalForm #returnProductId').val(productId)
    })

    $('#returnProduct').on('hidden.bs.modal', function () {
        $(this).find('form').children().show()
        $(this).find('textarea').val('')
        $(this).find('.success-request-form').remove()
    })

    $('#returnProductModalForm').on('submit', function (e) {
        e.preventDefault()
        const url = $(this).attr('action')
        const data = $(this).serializeArray()
        const normalizedData = data.filter(el => el.name !== '_token')
        if (productId) {
            returnProductRequest('#returnProductModalForm', url, normalizedData, productId)
        }
        productId = null
    })

    $('#mainRequestForm').on('submit', function (e) {
        e.preventDefault()
        const url = $(this).attr('action')
        const data = $(this).serializeArray()
        const normalizedData = data.filter(el => el.name !== '_token')
        requestForm('#mainRequestForm', url, normalizedData)
    })

    $('#mainRequestFormModal').on('submit', function (e) {
        e.preventDefault()
        const url = $(this).attr('action')
        const data = $(this).serializeArray()
        const normalizedData = data.filter(el => el.name !== '_token')
        requestForm('#mainRequestFormModal', url, normalizedData)
    })
})
