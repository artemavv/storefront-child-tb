document.addEventListener('DOMContentLoaded', () => {
    let wrapper = als.qs(document, '.bg-alModal');
    let modal = als.qs(document, '#alModal');
    if (!modal || !wrapper) return;

    let alModalContent = als.qs(modal, '#alModalContent');

    let blurBlocks = () => als.qsAll(document, 'body > *:not(.bg-alModal)');

    // Вертикально центрует модалку
    let vertCentering = () => {
        // Только если высота вьюпорта больше высоты модалки
        if (modal.offsetHeight < window.innerHeight)
            als.addClass(modal, 'verticalCentering');
        else
            als.delClass(modal, 'verticalCentering');
    };

    // Добавляет/убирает размытие
    let blur = () => {
        if (!als.hasClass(wrapper, 'alModal--hidden'))
            for (let block of blurBlocks())
                als.addClass(block, 'alModal--blur');

        else
            for (let block of blurBlocks())
                als.delClass(block, 'alModal--blur');
    };

    let openModal = () => {
        als.delClass(wrapper, 'alModal--hidden');
        document.body.style.overflow = 'hidden';
        setTimeout(() => { wrapper.style.opacity = 1; }, 50);
        vertCentering();
        blur();
    };

    let closing = () => {
        wrapper.style.opacity = 0;
        document.body.removeAttribute('style');
        setTimeout(() => {
            wrapper.classList.add('alModal--hidden');
            blur();
        }, 150);
    };

    class AlModalEvtHandler {
        handleEvent(evt) {
            let target = evt.target;
            switch (evt.type) {
                case 'click':
                    if (als.hasClass(target, 'alModalTrigger')) {
                        alModalContent.innerHTML = target.outerHTML;
                        openModal();
                    }

                    if (target.closest('.alModalAddToCart')) {
                        let itemName = target.closest('.product-card__inner')
                            .querySelector('.product-card__title').textContent;
                        als.qs(modal, '.tb-modal__title').textContent = itemName;
                        openModal();
                    }

                    // Закрытие по клику на крестик или фон модалки
                    if (target.closest('.alModal__close') ||
                        target.closest('.bg-alModal') && !target.closest('.alModal__inner')) {
                        closing();
                    }
                    break;

                case 'resize':
                    // Вертикальная центровка только при открытой модалке
                    if (!als.hasClass(wrapper, 'alModal--hidden'))
                        vertCentering();
                    break;

                case 'keyup':
                    if (evt.code === 'Escape')
                        closing();
                    break;
            }
        }
    }

    let AlModalEvt = new AlModalEvtHandler();
    document.body.addEventListener('click', AlModalEvt);
    window.addEventListener('resize', AlModalEvt);
    document.addEventListener('keyup', AlModalEvt);
});