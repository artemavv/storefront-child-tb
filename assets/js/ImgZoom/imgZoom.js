import gsap from 'gsap';

let finder = function () {
    return document.querySelector('.product-slider .swiper-slide-active img');
};

document.addEventListener('DOMContentLoaded', () => {
    let workingArea = document.querySelector('.product-slider__top');
    if (!workingArea) return;

    class ImgZoomEvtHandler {
        handleEvent(evt) {
            let cursorArea = evt.target;
            let classCheck = nameOfClass => cursorArea.classList.contains(nameOfClass);

            switch (evt.type) {
                case 'mousemove':
                    if (!classCheck('imgZoomCap')) return;
                    let target = cursorArea.getBoundingClientRect();
                    let w = cursorArea.scrollWidth;
                    let h = cursorArea.scrollHeight;
                    // Абсолютные координаты курсора в 'тачпаде'
                    let x = evt.clientX - target.left;
                    let y = evt.clientY - target.top;
                    // Относительные координаты курсора в 'тачпаде', в % от центра
                    let centerX = (x - w / 2) / w * -120;
                    let centerY = (y - h / 2) / h * -120;
                    gsap.to(finder(), { x: `${centerX}%`, y: `${centerY}%`, scale: 2, duration: 0 });
                    break;

                case 'mouseout':
                    if (!classCheck('imgZoomCap')) return;
                    finder().removeAttribute('style');
                    break;
            }
        }
    }

    let imgZoomEvt = new ImgZoomEvtHandler();
    workingArea.addEventListener('mousemove', imgZoomEvt);
    workingArea.addEventListener('mouseout', imgZoomEvt);
});