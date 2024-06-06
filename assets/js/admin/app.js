//Core
require('./bootstrap')
require('./core/source/App.js')
require('summernote')
require('summernote/lang/summernote-ru-RU')

//Third-party
import * as FilePond from 'filepond'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'

require('./libs/DataTables/jquery.dataTables.min')

//App
require('./removeItem')
require('./parserOptions')
require('./publishComment')
require('./replyComment')
require('./CreateProduct/addAttribute')
require('./RequestForm/requestForm')

document.addEventListener('DOMContentLoaded', function () {

    const createProductImage = document.getElementById('createProductImage')
    const productImage = document.getElementById('productImage')

    if (typeof (createProductImage) != 'undefined' && createProductImage != null) {
        FilePond.registerPlugin(FilePondPluginImagePreview)

        const pond = FilePond.create(createProductImage)
    }
    if (typeof (productImage) != 'undefined' && productImage != null) {
        FilePond.registerPlugin(FilePondPluginImagePreview)

        const path = document.getElementById('productImage').dataset.path
        const pond = FilePond.create(productImage, {
            files: [
                {
                    source: path
                }
            ]
        })
    }
})

jQuery(document).ready(function ($) {

    $('.clickable-row > td:not(.remove)').on('click', function () {
        window.location = $(this).parent('.clickable-row').data('href')
    })

    $('#productForm').validate()
    $('#editOrderForm').validate()
    $('#createAttr').validate()
    $('#phone').inputmask({
        mask: '[+](7) (999) 999-99-99',
        showMaskOnHover: false
    })

    $('.date-picker').datepicker({
        autoclose: true,
        todayHighlight: true,
        format: 'dd/mm/yyyy',
        language: 'ru'
    })

    $('.datatable1').DataTable({
        'dom': 'lCfrtip',
        'order': [],
        'colVis': {
            'buttonText': 'Columns',
            'overlayFade': 0,
            'align': 'right'
        },
        'language': {
            'lengthMenu': '_MENU_ кол-во на страницу',
            'search': '<i class="fa fa-search"></i>',
            'zeroRecords': 'Результаты не найдены',
            'infoEmpty': 'Сейчас тут пусто',
            'info': 'Показана _PAGE_ из _PAGES_',
            'paginate': {
                'previous': '<i class="fa fa-angle-left"></i>',
                'next': '<i class="fa fa-angle-right"></i>'
            }
        }
    })

    $('#orders').DataTable({
        'dom': 'lCfrtip',
        'order': [[0, 'desc']],
        'colVis': {
            'buttonText': 'Columns',
            'overlayFade': 0,
            'align': 'right'
        },
        'language': {
            'lengthMenu': '_MENU_ кол-во на страницу',
            'search': '<i class="fa fa-search"></i>',
            'zeroRecords': 'Результаты не найдены',
            'infoEmpty': 'Сейчас тут пусто',
            'info': 'Показана _PAGE_ из _PAGES_',
            'paginate': {
                'previous': '<i class="fa fa-angle-left"></i>',
                'next': '<i class="fa fa-angle-right"></i>'
            }
        }
    })

    $('.order-table').DataTable({
        'dom': 'lCfrtip',
        'order': [],
        'bLengthChange': false,
        'info': false,
        'paginate': false,
        'colVis': {
            'buttonText': 'Columns',
            'overlayFade': 0,
            'align': 'right'
        },
        'language': {
            'search': '<i class="fa fa-search"></i>',
            'zeroRecords': 'Результаты не найдены',
            'infoEmpty': 'Сейчас тут пусто'
        }
    })

    $('#datatable1').DataTable({
        'dom': 'lCfrtip',
        'order': [],
        'colVis': {
            'buttonText': 'Columns',
            'overlayFade': 0,
            'align': 'right'
        },
        'language': {
            'lengthMenu': '_MENU_ кол-во на страницу',
            'search': '<i class="fa fa-search"></i>',
            'zeroRecords': 'Результаты не найдены',
            'infoEmpty': 'Сейчас тут пусто',
            'info': 'Показана _PAGE_ из _PAGES_',
            'paginate': {
                'previous': '<i class="fa fa-angle-left"></i>',
                'next': '<i class="fa fa-angle-right"></i>'
            }
        }
    })
    // $('#datatable1 tbody').on('click', 'tr', function () {
    //     $(this).toggleClass('selected');
    // });

    $('.select2-list').select2({
        allowClear: true
    })

    $('.nestable-list').nestable()

    var table = $('#datatable2').DataTable({
        'dom': 'T<"clear">lfrtip',
        // "ajax": $('#datatable2').data('source'),
        'columns': [
            {
                'class': 'details-control',
                'orderable': false,
                'data': null,
                'defaultContent': ''
            },
            {'data': '№'},
            {'data': 'Статус'},
            {'data': 'Товар'},
            {'data': 'Стоимость'},
            {'data': 'Кол-во'},
            {'data': 'Итог'},
            {'data': 'Поставщик'},
            {'data': 'Ссылка на сайте'}
        ],
        'order': [[1, 'asc']],
        'language': {
            'lengthMenu': '_MENU_ кол-во на страницу',
            'search': '<i class="fa fa-search"></i>',
            'zeroRecords': 'Результаты не найдены',
            'infoEmpty': 'Сейчас тут пусто',
            'info': 'Показана _PAGE_ из _PAGES_',
            'paginate': {
                'previous': '<i class="fa fa-angle-left"></i>',
                'next': '<i class="fa fa-angle-right"></i>'
            }
        }
    })

    $('.texteditor').summernote({

        lang: 'ru-RU',
        height: 200,
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear', 'style']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['link', ['linkDialogShow', 'unlink']]


        ],
        styleTags: [
            'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
        ],
        link: []
    })

})
