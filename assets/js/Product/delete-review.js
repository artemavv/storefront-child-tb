import {clean} from "../app";

jQuery($ => {
    const $body = $('body')

    $body.on('click', '.delete_review', function (e) {
        e.preventDefault()
        const reviewId = $(this).data('id');
        const action = $(this).data('action');
        const fromPage = $(this).data('page');

        changeCart(reviewId, action, fromPage)
    })


    function changeCart(reviewId, action, fromPage) {
        let data = {
            reviewId, action, fromPage
        }
        data = clean(data)
        const lkReviews = $('#lkreviews')
        $.ajax({
            type: 'POST',
            url: "/api/review",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: JSON.stringify(data),
            contentType: 'application/json',
            beforeSend: () => {
                lkReviews.addClass('loading')
            },
            success: data => {
                if (fromPage === 'lk') {
                    lkReviews.replaceWith(data['html'])
                }
                lkReviews.removeClass('loading')
            },
            error: e => {
                console.log(e)
            }
        });
    }
})
