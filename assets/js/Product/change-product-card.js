const inputs = document.querySelectorAll('.custom-number__field');
const upBtns = document.querySelectorAll('.custom-number__arrow-up');
const downBtns = document.querySelectorAll('.custom-number__arrow-down');

inputs.forEach(input => {
    input.addEventListener('change', (e) => {

        upBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                let input = btn.nextElementSibling;

                if (input.value >= 0) {
                    input.value++;
                }
            })
        })

        downBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                let input = btn.previousElementSibling;

                if (input.value > 0) {
                    input.value--;
                }
            })
        })
    })
})


