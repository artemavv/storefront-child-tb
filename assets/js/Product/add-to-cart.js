import { clean } from "../app";

jQuery($ => {
    $('body').on('click', '.add-to-cart:not(.loading)', function (e) {
        e.preventDefault();
        const productId = $(this).data('id');
        const quantity = $(this).data('quantity');
        const fromPage = $(this).data('page');
        const variation = $(this).data('variation');
        const material = $(this).data('material');
        const action = 'add';
        const counter = $('#cartCounter');
        const cartTotal = $('#cartTotal');
        const doneSVG = $(this).closest('.product-card').find('.product-card__done-img');

        let data = {
            productId,
            fromPage,
            quantity,
            variation,
            material,
            action
        };
        data = clean(data);
        if (fromPage !== 'catalog') {
            $.ajax({
                type: 'POST',
                url: "/api/cart",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: JSON.stringify(data),
                contentType: 'application/json',
                beforeSend: () => {
                    $(this).addClass('loading');
                },
                success: data => {
                    if (!data) alert('Ошибка. Попробуйте обновить страницу');
                    if (data['count'] === 0) {
                        counter.addClass('d-none');
                        counter.removeClass('d-flex');
                    } else {
                        counter.addClass('d-flex');
                        counter.removeClass('d-none');
                    }
                    counter.html(data['count']);
                    cartTotal.html(data['cartTotal']);

                    if (doneSVG) {
                        doneSVG.addClass('show');
                        setTimeout(() => {
                            doneSVG.removeClass('show');
                        }, 1000);
                    }

                    if (fromPage === 'product') {
                        setTimeout(() => {
                            const productAdd = document.querySelector('#product-add');
                            productAdd.innerHTML = data['html'];
                        }, 500);
                    } else if (fromPage === 'lk') {
                        const favoriteInCart = $(`#favorite-in-cart-${productId}`);
                        favoriteInCart.replaceWith(data['html']);
                    } else if (fromPage === 'catalog') {
                        const productAddToCart = $(`#product-add-to-cart__box-${productId}`);
                        productAddToCart.replaceWith(data['html']);
                    } else {
                        if (fromPage === 'cart') {
                            const cartContent = $('#cart');
                            cartContent.replaceWith(data['html']);
                        }
                        $(this).removeClass('add-to-cart');
                        $(this).addClass('btn-inactive');
                        $(this).text('В корзине');
                    }

                    setTimeout(() => {
                        $(this).removeClass('loading');
                    }, 1000);
                },
                error: e => {
                    console.log(e);
                }
            });
        }
    });
});
