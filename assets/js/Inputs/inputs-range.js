import noUiSlider from 'nouislider';

let range = document.querySelector('.sidebar-range__input');
let rangeValues = [
    document.querySelector('.sidebar-range__output:first-child span:last-child'),
    document.querySelector('.sidebar-range__output:last-child span:last-child')
];

if (range && rangeValues[0] && rangeValues[1]) {
    // console.log(range, rangeValues[0], rangeValues[1])
    noUiSlider.create(range, {
        start: [rangeValues[0].textContent, rangeValues[1].textContent],
        connect: true,
        range: {
            min: Number(rangeValues[0].textContent),
            max: Number(rangeValues[1].textContent)
        }
    });

    range.noUiSlider.on('update', function (values, handle) {
        rangeValues[handle].innerHTML = values[handle].slice(0, -3);
    });
}
