jQuery(($) => {
    $('a[data-target="#deleteModal"]').on('click', function () {
        const dataAction = $(this).data('action');
        let dataText = $(this).data('text');

        if (typeof dataText === "undefined" || dataText === '') {
            dataText = 'позицию'
        }
        const deleteModalText = $('#deleteModalFormText')
        const deleteModalForm = $('#deleteModalForm');

        deleteModalForm.attr('action', dataAction)
        deleteModalText.text(dataText)
    })
})
