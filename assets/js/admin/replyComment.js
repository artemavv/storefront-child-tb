jQuery($ => {
    $('a[data-target="#replyCommentModal"]').on('click', function () {
        const dataAction = $(this).data('action');

        const publishCommentModalForm = $('#publishCommentModalForm');

        publishCommentModalForm.attr('action', dataAction)
    })
})
