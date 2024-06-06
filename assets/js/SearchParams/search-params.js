jQuery(($) => {
    $('[data-key-to-search]')
        .on('click', function () {
            const key = $(this)
                .data('key-to-search')
            const data = $(this)
                .data('data-to-search')
            const url = new URL(window.location)
            if (data) {
                url.searchParams.set(key, data)
            } else {
                url.searchParams.delete(key)
            }
            url.searchParams.sort()
            window.history.pushState({}, '', url.toString())
        })
})
