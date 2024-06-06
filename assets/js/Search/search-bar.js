jQuery(($) => {
    const $searchResult = $('.search-result')
    const $search = $('[name="q"]')
    let $searchArea = $('#headerSearchArea');

    $search.on('input', _.debounce(function () {
        const value = $(this).val()

        if (value.length >= 2) {
            axios
                .get(`/api/search?search=${value}`)
                .then(({ data }) => {
                    $searchResult.html(data).show()
                })
                .catch((error) => {
                    console.log(error)
                })
        } else {
            $searchResult.fadeOut(300)
        }
    }, 500))

    $search.on('focusin', function () {
        $searchArea.css({ 'overflow': 'visible' });
        $searchResult.fadeIn(150)
    })

    if ($(window).width() >= 768) {
        $search.on('focusout', function () {
            setTimeout(() => {
                $searchResult.fadeOut(150)
                $searchArea.css({ 'overflow': 'hidden' });
                $searchArea.removeClass('header__search-area--show');
            }, 180)
        })
    }

    $('#headerSearchBtn').on('click', _.debounce(() => {
        $searchArea.toggleClass('header__search-area--show');
        setTimeout(() => {
            if ($searchArea.hasClass('header__search-area--show')) {
                $search.trigger('focus');
            };
        }, 300);
    }, 350, { leading: true, maxWait: 0, trailing: false }));

    $('#headerSearchCloseBtn').on('click', () => {
        $search.val('');
    });
})
