jQuery($ => {
    $('a[data-target="#publishCommentModal"]').on('click', function () {
        const dataAction = $(this).data('action');

        const replyCommentModalForm = $('#replyCommentModalForm');

        replyCommentModalForm.attr('action', dataAction)
    })
})
