jQuery($ => {
    async function requestForm(formSelector, url, type, data) {
        return $.ajax({
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
                return answer
            },
            error: e => {
                return e
            }
        })
    }

    $('#changeBonusAmount').on('submit', function (e) {
        e.preventDefault()
        const url = $(this).attr('action')
        const type = $(this).attr('type')
        const data = $(this).serializeArray()
        const normalizedData = data.filter(el => el.name !== '_token')
        requestForm('#changeBonusAmount', url, type, normalizedData)
            .then(answer => {
                $('#bonusAmountTarget').text(+answer.bonusAmount / 100)
                $('#totalAmountTarget').text(+answer.totalAmount / 100)
                $('.error-target-amount').hide()
            })
            .catch(e => {
                $('.error-target-amount').text(e.responseJSON.errors.bonusAmount).show()
            })
    })
})
