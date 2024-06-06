import { clean } from "../app";

jQuery($ => {
    const $body = $('body');

    $body.on('click', '.change-cart', function (e) {
        e.preventDefault();
        const productId = $(this).data('id');
        const quantity = $(this).data('quantity');
        const action = $(this).data('action');
        const fromPage = $(this).data('page');
        const variation = $(this).data('variation');
        const material = $(this).data('material');
        const doneSVG = $(this).closest('.product-card').find('.product-card__done-img');

        if (fromPage !== 'catalog') {
            changeCart(productId, action, fromPage, quantity, variation, material, doneSVG);
        }

        $(this).addClass('loading');
        setTimeout(() => {
            $(this).removeClass('loading');
        }, 500);
    });

    $body.on('click', '.cart-plus, .cart-minus',
        _.debounce(
            function () {
                let quantity = $(this).data('quantity');
                const type = $(this).data('type');
                if (type === 'minus') {
                    if (quantity > 0) {
                        quantity--;
                    }
                }
                if (type === 'plus') {
                    if (quantity >= 0) {
                        quantity++;
                    }
                }
                if (Number(quantity)) {
                    const productId = $(this).data('id');
                    const action = $(this).data('action');
                    const fromPage = $(this).data('page');
                    const variation = $(this).data('variation');
                    const material = $(this).data('material');
                    $(this).parent().find('.cart-qty').val(quantity);

                    changeCart(productId, action, fromPage, quantity, variation, material);
                } else {
                    return null;
                }
            }, 250));

    $body.on('blur', '.cart-qty',
        _.debounce(
            function () {
                let quantity = $(this).data('quantity');
                if (Number($(this).val()) !== Number(quantity)) {
                    const productId = $(this).data('id');
                    const action = $(this).data('action');
                    const fromPage = $(this).data('page');
                    const variation = $(this).data('variation');
                    const material = $(this).data('material');
                    quantity = $(this).val();

                    changeCart(productId, action, fromPage, quantity, variation, material);
                } else {
                    return null;
                }
            }, 250));

    function changeCart(productId, action, fromPage, quantity, variation, material, doneSVG) {
        let data = {
            productId, action, fromPage, quantity, variation, material
        };
        data = clean(data);
        const cart = $('#cart');
        const counter = $('#cartCounter');
        const cartTotal = $('#cartTotal');
        $.ajax({
            type: 'POST',
            url: "/api/cart",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: JSON.stringify(data),
            contentType: 'application/json',
            beforeSend: () => {
                cart.addClass('loading');
            },
            success: data => {
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

                if (fromPage === 'cart') {
                    cart.replaceWith(data['html']);
                } else if (fromPage === 'lk') {
                    const favoriteInCart = $(`#favorite-in-cart-${productId}`);
                    favoriteInCart.replaceWith(data['html']);
                } else if (fromPage === 'product') {
                    const productAdd = document.querySelector('#product-add');
                    productAdd.innerHTML = data['html'];
                } else if (fromPage === 'catalog') {
                    const productAddToCart = $(`#product-add-to-cart__box-${productId}`);
                    productAddToCart.replaceWith(data['html']);
                }
                cart.removeClass('loading');
            },
            error: e => {
                console.log(e);
            }
        });
    }
});
