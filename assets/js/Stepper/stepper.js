import is from 'is_js'
import IMask from 'imask'

const element = document.getElementById('phone')
const maskOptions = { mask: '+0 (000) 000-00-00' }

if (element) { const mask = IMask(element, maskOptions) }

jQuery(($) => {
    const $body = $('body')

    // Initial
    $('.stepper > [data-step="1"]').addClass('active')
    $('.stepper > [data-step="1"] > .stepper-collapse').slideDown()
    const maxStep = $('.stepper [data-step]').length
    const $items = $('.stepper > [data-step]')
    const $itemsBody = $('.stepper > [data-step] > .stepper-collapse')

    const showMoscowDeliveries = (formId) => {
        const data = $(formId).serializeArray()
        let isShow = false
        const $items = $('[name="delivery"]')

        data.forEach((el) => {
            if (el.name === 'city' && el.value.trim().toLowerCase() === 'москва') {
                isShow = true
            }
        })
        $items.each(function (index, el) {
            const city = $(el).data('city')?.trim().toLowerCase()
            const radio = $(el).parent('.radio')
            if ((isShow && city === 'москва') || !city) {
                radio.show()
            } else {
                radio.hide()
            }
        })
    }

    const hideAll = () => {
        $items.each(function () {
            $(this).removeClass('active')
        })
        $itemsBody.each(function () {
            $(this).slideUp()
        })
    }

    $body.on('input', '[name="city-radio"]', function () {
        const val = $(this).val()

        if ($(this).is(':checked')) {
            $('[name="city"]').val(val)
        }
    })

    $body.on('keyup', '[name="city"]', _.debounce(function () {
        const cityRadios = $('[name="city-radio"]')
        const value = $(this).val()
        cityRadios.each(function (index, el) {
            if ($(el).val() === value) {
                $(el).prop('checked', true)
            } else {
                $(el).prop('checked', false)
            }
        })
    }, 200))

    const toggleStep = (currentStep, inc) => {
        $(`.stepper > [data-step="${inc ? currentStep + 1 : currentStep - 1}"]`).addClass('active')
        $(`.stepper > [data-step="${inc ? currentStep + 1 : currentStep - 1}"] input`).each(function () {
            $(this).attr('readonly', false)
        })
        $(`.stepper > [data-step="${inc ? currentStep + 1 : currentStep - 1}"] > .stepper-collapse`).slideDown()
    }

    const disableCurrentStep = (currentStep) => {
        $(`.stepper > [data-step="${currentStep}"] input`).each(function () {
            $(this).attr('readonly', true)
        })
    }

    const validateInput = ($input) => {
        const type = $($input).attr('type')
        let isValid = false
        const val = $($input).val()
        switch (type) {
            case 'text':
                isValid = val !== ''
                break
            case 'email':
                isValid = is.email(val)
                break
            case 'tel':
                isValid = mask.unmaskedValue.length === 11
                break
            default:
                break
        }
        if (isValid) {
            $($input).removeClass('is-invalid')
            $($input).addClass('is-valid')
        } else {
            $($input).removeClass('is-valid')
            $($input).addClass('is-invalid')
        }
        return isValid
    }

    const validateStep = (currentStep) => {
        const $inputs = $(`.stepper > [data-step="${currentStep}"] input:required`)
        let valArr = []
        $inputs.each(function () {
            valArr = [...valArr, validateInput(this)]
        })
        const isValid = valArr.every(el => el)
        if (isValid) {
            $(`.stepper > [data-step="${currentStep}"]`).addClass('step-success')
        } else {
            $(`.stepper > [data-step="${currentStep}"]`).removeClass('step-success')
        }
        return isValid
    }

    $body.on('keyup', '.stepper [data-step] input.is-invalid, .stepper [data-step] input.is-valid',
        _.debounce(function () {
            const currentStep = $(this).parents('.stepper-item').data('step')
            validateStep(currentStep)
        }, 200))

    $body.on('click', '.stepper .next-step', function () {
        const currentStep = $(this).parents('.stepper-item').data('step')
        const isValid = validateStep(currentStep)
        if (maxStep > currentStep && isValid) {
            $(`.stepper > [data-step="${currentStep}"]`).addClass('step-success')
            hideAll()
            disableCurrentStep(currentStep)
            toggleStep(currentStep, true)
            showMoscowDeliveries('#checkout')
        }
    })

    $body.on('click', '.stepper .prev-step', function () {
        const currentStep = $(this).parents('.stepper-item').data('step')
        validateStep(currentStep)
        if (currentStep > 1) {
            hideAll()
            disableCurrentStep(currentStep)
            toggleStep(currentStep, false)
            showMoscowDeliveries('#checkout')
        }
    })
})
