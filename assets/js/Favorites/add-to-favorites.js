import {clean} from "../app";

jQuery($ => {
    $('body').on('click', '.add-to-favorites:not(.loading)', function (e) {
        e.preventDefault()
        const productId = $(this).data('id');
        const action = $(this).data('action');
        const fromPage = $(this).data('page');
        const counter = $('#favoritesCounter')
        const favorites = $('#favorites')
        let data = {
            productId,
            action,
            fromPage,
        }
        data = clean(data)
        $.ajax({
            type: 'POST',
            url: "/api/favorites/action",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: JSON.stringify(data),
            contentType: 'application/json',
            beforeSend: () => {
                if (fromPage === 'favorites') {
                    favorites.addClass('loading')
                } else {
                    $(this).addClass('loading')
                }
            },
            success: data => {
                if (data['count'] === 0) {
                    counter.removeClass('d-xl-block')
                } else {
                    counter.addClass('d-xl-block')
                }
                counter.html(data['count'])

                if (fromPage === 'favorites') {
                    $('#favorites').replaceWith(data['html'])
                    favorites.removeClass('loading')
                } else {
                    if (fromPage === 'catalog') {
                        $(this).toggleClass('active')
                        if (action === 'add') {
                            $(this).data('action', 'remove')
                        } else {
                            $(this).data('action', 'add')
                        }
                    }else if (fromPage === 'product') {
                        $(this).toggleClass('active')
                        if (action === 'add') {
                            $(this).data('action', 'remove')
                        } else {
                            $(this).data('action', 'add')
                        }
                    } else {
                        const icon = $(this).find('i')
                        if (action === 'add') {
                            $(this).data('action', 'remove')
                            $(this).find('span').text('В избранном')
                            icon.addClass('heart-on')
                            icon.removeClass('heart-off')
                        } else {
                            $(this).data('action', 'add')
                            $(this).find('span').text('В избранное')
                            icon.addClass('heart-off')
                            icon.removeClass('heart-on')
                        }
                    }
                    $(this).removeClass('loading')
                }
            },
            error: e => {
                console.log(e)
            }
        });
    })
})
