document.addEventListener('DOMContentLoaded', () => {
    let blockForResponse = document.querySelector('#product-add');
    if (!blockForResponse) return;

    function productSelectsRequest(url, data) {
        $.ajax({
            type: 'POST',
            url: `${url}`,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: data,
            beforeSend: () => {
                $('.toCartText').addClass('loading');
            },
            success: responseData => {
                blockForResponse.innerHTML = responseData;
            },
            error: e => {
                console.log(e);
                alert('Ошибка');
            }
        });
    }

    blockForResponse.addEventListener('change', evt => {
        if (evt.target.id === 'productSelect1') {
            productSelectsRequest('/api/get-product-price',
                {
                    product_variation_id: $('#productSelect1').val(),
                    material_product_id: $('#productSelect2').val()
                }
            );
        }
    });
});