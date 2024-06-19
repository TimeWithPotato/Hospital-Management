document.addEventListener('DOMContentLoaded', function () {
    const closeButtons = document.querySelectorAll('.close-btn');
    closeButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const errorBox = button.closest('.error');
            errorBox.style.display = 'none'; // Hide the error message box
        });
    });

    document.getElementById('toggle-adm-id').addEventListener('change', function () {
        //admission id
        var admIdContainer = document.getElementById('adm-id-container');
        var admIdInput = document.getElementById('adm_id');

        //ward id
        var wardIdContainer = document.getElementById('ward-id-container');
        var wardIdInput = document.getElementById('ward_id');

        //cabin id
        var cabinIdContainer = document.getElementById('cabin-id-container');
        var cabinIdInput = document.getElementById('cabin_id');

        //cabin-rent
        var cabinRentContainer = document.getElementById('cabin-rent-container');
        var cabinRentInput = document.getElementById('cabin_rent');

        //date of admission
        var dateOfAdmContainer = document.getElementById('date-of-adm-container');
        var dateOfAdm = document.getElementById('date_of_adm');

        if (this.checked) {
            //admission
            admIdContainer.style.display = 'block';
            admIdInput.disabled = false;

            //ward 
            wardIdContainer.style.display = 'block';
            wardIdInput.disabled = false;

            //cabin id
            cabinIdContainer.style.display = 'block';
            cabinIdInput.disabled = false;

            //cabin rent
            cabinRentContainer.style.display = 'block';
            cabinRentInput.disabled = false;

            //date of admission
            dateOfAdmContainer.style.display = 'block';
            dateOfAdm.disabled = false;

        } else {
            //admission id
            admIdContainer.style.display = 'none';
            admIdInput.disabled = true;
            admIdInput.value = '';

            //ward id
            wardIdContainer.style.display = 'none';
            wardIdInput.disabled = true;
            wardIdInput.value = '';

            //cabin id
            cabinIdContainer.style.display = 'none';
            cabinIdInput.disabled = true;
            cabinIdInput.value = '';

            //cabin rent
            cabinRentContainer.style.display = 'none';
            cabinRentInput.disabled = true;
            cabinRentInput.value = '';

            //date of admission
            dateOfAdmContainer.style.display = 'none';
            dateOfAdm.disabled = true;
            dateOfAdm.value = '';
        }
    });
});