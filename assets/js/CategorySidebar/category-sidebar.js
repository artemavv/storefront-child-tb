const headers = document.querySelectorAll('.catalog-dropdown__header');
// const lists = document.querySelectorAll('.catalog-dropdown__list');

headers.forEach(item => {
    item.addEventListener('click', () => {
        item.classList.toggle('show')


    })
})

// lists.forEach(item => {
//     item.addEventListener('click', () => {
//         item.classList.add('.show')
//     })
// })
