document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('pay_option').addEventListener('change', function () {
        var installmentOptions = document.getElementById('installment_option');

        if (this.value === 'installment') {
            installmentOptions.style.display = 'block';
        }
        else {
            installmentOptions.style.display = 'none';
        }
    })
})