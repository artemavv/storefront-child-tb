window.addEventListener('DOMContentLoaded', () => {
    if (typeof photosInputs === 'undefined' || photosInputs !== 'standby')
        return;

    const form = document.querySelector('#addPhotosForm');
    if (!form)
        return console.error('Форма добавления фото не найдена', { form });

    // Кнопки переключения вкладок
    const openTrigger = document.querySelector('.createReviewTrigger');
    const reviewsTabBtn = document.querySelector('#reviewsTargetBtn');
    // Элементы для вставки инфо о товаре
    let productIdInput = form.querySelector('#productIdInput');
    const reviewItemInfo = form.querySelector('#reviewItemInfo');

    if (!(reviewsTabBtn && productIdInput && reviewItemInfo)) {
        return console.error('Не найдены элементы для переключения вида или вставки инфо о товаре',
            { reviewsTabBtn, productIdInput, reviewItemInfo });
    }

    if (!openTrigger)
        return console.info('Не найдено кнопок добавления отзыва о товаре');

    let reviewItemImg = reviewItemInfo.querySelector('img');
    let reviewItemName = reviewItemInfo.querySelector('p');
    let reviewItemPrice = reviewItemInfo.querySelector('#reviewItemPrice');

    const reviewsList = () => { return document.querySelector('#lkreviews'); };

    // Обработка нажатия на 'Написать отзыв'
    document.body.addEventListener('click', function (evt) {
        if (!evt.target.classList.contains('createReviewTrigger'))
            return;

        // Находим инфо о товаре
        const itemID = evt.target.value;
        const itemWrap = evt.target.closest('.orders-content__item');
        const itemSrc = itemWrap.querySelector('.orders-content__item-img').getAttribute('src');
        const itemName = itemWrap.querySelector('.orders-content__item-img').getAttribute('alt');
        const itemPrice = itemWrap.querySelector('.priceForReview').textContent;

        // Добавляем в форму полученную инфу
        productIdInput.value = itemID;
        reviewItemImg.setAttribute('src', itemSrc);
        reviewItemImg.setAttribute('alt', itemName);
        reviewItemName.textContent = itemName;
        reviewItemPrice.textContent = itemPrice;

        // Удаляем флаг неактивного элемента у списка отзывов, если есть
        // Переключение этого флага нужно для корректной логики работы при нажатии на табы
        if (reviewsList().classList.contains('notActiveElement')) {
            reviewsList().classList.remove('notActiveElement');
        }

        // Показываем форму, скрываем список отзывов, переключаемся на целевую вкладку
        // и добавляем флаг неактивного элемента списку отзывов
        form.closest('#lkreviews-form-wrap').classList.remove('hiddenElement');
        reviewsList().classList.add('hiddenElement');
        reviewsTabBtn.click(); // Если бы был флаг, то форма бы скрылась сразу после показа,
        // поэтому в обработке нажатия на таб есть проверка на наличие флага

        // Добавили флаг ПОСЛЕ клика по табу
        reviewsList().classList.add('notActiveElement');
    });

    // Обработка нажатия на таб с отзывами
    reviewsTabBtn.addEventListener('click', () => {
        if (reviewsList().classList.contains('notActiveElement')) {
            form.closest('#lkreviews-form-wrap').classList.add('hiddenElement');
            reviewsList().classList.remove('hiddenElement');
            reviewsList().classList.remove('notActiveElement');
        }
    });

    // ======================== Конец секции с переключением вкладок/видов =============================
    // =========================== Далее работа с самой формой и фотками ===============================

    // Элементы для проведения операций с добавляемыми фото
    const phList = form.querySelector('#addPhotosList');
    let phCounter = form.querySelector('#addedPhotosCounter');
    let mainInput = form.querySelector('#phInputMain');
    const phInput = form.querySelector('.phInputAdding');

    if (!(phList && phCounter && mainInput && phInput))
        return console.error('Не найден контейнер для фото/каунтер/input',
            { phList, phCounter, mainInput, phInput });

    // Функция обновления индексов превью
    let itemsForUpdate = () => { return phList.querySelectorAll('.itemShow'); };
    itemsIndexesUpdate = () => itemsForUpdate().forEach((item, i) => item.querySelector('.deletePhotoBtn').value = i);

    // Находим все блоки для фото, в каждом из них элементы для img-src и filename
    const listItems = phList.querySelectorAll('.lkreviews-form__added-photo-item');
    const listImages = phList.querySelectorAll('.reviewAddedPhoto');
    const listFileNames = phList.querySelectorAll('.filename');

    const limit = 5; // Максимально допустимое количество добавленных фото

    const addBtnViewSwitch = qty => {
        phCounter.textContent = `${qty}/${limit}`;
        if (qty === limit) {
            als.addClass(phInput.closest('label'), 'hiddenElement');
        } else {
            als.delClass(phInput.closest('label'), 'hiddenElement');
        };
    };

    form.addEventListener('change', (evt) => {
        const input = evt.target;
        if (input !== phInput)
            return;

        // console.log(mainInput.files);
        // console.log(input.files);

        const selectingDT = new DataTransfer();

        // Добавляем ранее выбранные файлы в список
        for (let i = 0; i < mainInput.files.length; i++) {
            let alreadyAddedFile = mainInput.files[i];
            let alreadyAddedFileName = alreadyAddedFile.name;

            selectingDT.items.add(new File([alreadyAddedFile], alreadyAddedFileName));
        }

        // Добавляем новые файлы в список
        for (let i = 0; i < input.files.length; i++) {
            if (selectingDT.files.length >= limit)
                break;

            let appendFile = input.files[i];

            // Пропускаем если не картинка
            if (!appendFile.type.match('image.*'))
                continue;

            let appendFileName = appendFile.name;
            selectingDT.items.add(new File([appendFile], appendFileName));
        }

        for (let i = 0; i < limit; i++) {
            let finalFile = selectingDT.files[i];
            if (!finalFile) {
                als.delClass(listItems[i], 'itemShow');
                continue;
            }
            let finalFileName = finalFile.name;

            // Выводим картинку в src через файловое API
            const reader = new FileReader();
            reader.onload = evt => {
                if (listImages[i].src !== evt.target.result)
                    listImages[i].src = evt.target.result;
            };
            reader.readAsDataURL(finalFile);

            // Выводим имя файла, сократив если оно длинное
            // и показываем блок с превью
            if (finalFileName.length > 14)
                finalFileName = `${finalFileName.slice(0, 12)}...`;
            listFileNames[i].textContent = finalFileName;
            als.addClass(listItems[i], 'itemShow');
        }

        // Заменяем список файлов инпута на новый
        mainInput.files = selectingDT.files;

        // console.log(mainInput.files);

        addBtnViewSwitch(mainInput.files.length);
        itemsIndexesUpdate();
    });

    form.addEventListener('click', function (evt) {
        if (!evt.target.closest('.deletePhotoBtn'))
            return;

        // Скрываем превью и узнаём его индекс(порядковый номер начиная с 0)
        als.delClass(evt.target.closest('.lkreviews-form__added-photo-item'), 'itemShow');
        let fileIndex = evt.target.closest('.deletePhotoBtn').value;

        const replacingDT = new DataTransfer();

        for (let i = 0; i < limit; i++) {
            let file = mainInput.files[i];
            // Добавляем в очередь все файлы кроме удаляемого
            if (file && i != fileIndex) replacingDT.items.add(new File([file], file.name));
        };

        // Заменяем списки файлов в input на новый
        mainInput.files = replacingDT.files;

        addBtnViewSwitch(mainInput.files.length);
        itemsIndexesUpdate();

        // console.log(mainInput.files);
    });
});