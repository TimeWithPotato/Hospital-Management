document.addEventListener('DOMContentLoaded', function () {
    const departments = document.querySelectorAll('.container .card');

    departments.forEach(function (department) {
        // Add event listener for click
        department.addEventListener('click', function () {
            const deptId = department.id === 'cardiology' ? 'cardi101' :
                department.id === 'dermatology' ? 'derma301' :
                    department.id === 'neurology' ? 'neuro201' : '';

            if (deptId) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'http://localhost/Hospital-Management/DOCTOR/department.php';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'dept_id';
                input.value = deptId;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });

        // Add event listener for mouseover
        department.addEventListener('mouseover', function (event) {
            department.style.transform = 'scale(1.2)';
            department.style.cursor = 'pointer';
        });

        // Add event listener for mouseout
        department.addEventListener('mouseout', function (event) {
            department.style.transform = 'scale(1)';
        });
    });

    // for patient view
    departments.forEach(function (department) {
        // Add event listener for click
        department.addEventListener('click', function () {
            const deptId = department.id === 'pat-view-cardiology' ? 'cardi101' :
                department.id === 'pat-view-dermatology' ? 'derma301' :
                    department.id === 'pat-view-neurology' ? 'neuro201' : '';

            if (deptId) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'http://localhost/Hospital-Management/user/pat_view_doctor.php';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'dept_id';
                input.value = deptId;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });

        // Add event listener for mouseover
        department.addEventListener('mouseover', function (event) {
            department.style.transform = 'scale(1.2)';
            department.style.cursor = 'pointer';
        });

        // Add event listener for mouseout
        department.addEventListener('mouseout', function (event) {
            department.style.transform = 'scale(1)';
        });
    });
    // for gen patient view
    departments.forEach(function (department) {
        // Add event listener for click
        department.addEventListener('click', function () {
            const deptId = department.id === 'gen-pat-view-cardiology' ? 'cardi101' :
                department.id === 'gen-pat-view-dermatology' ? 'derma301' :
                    department.id === 'gen-pat-view-neurology' ? 'neuro201' : '';

            if (deptId) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'http://localhost/Hospital-Management/user/gen_pat_view_doctor.php';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'dept_id';
                input.value = deptId;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });

        // Add event listener for mouseover
        department.addEventListener('mouseover', function (event) {
            department.style.transform = 'scale(1.2)';
            department.style.cursor = 'pointer';
        });

        // Add event listener for mouseout
        department.addEventListener('mouseout', function (event) {
            department.style.transform = 'scale(1)';
        });
    });

    // For doctor view
    departments.forEach(function (department) {
        // Add event listener for click
        department.addEventListener('click', function () {
            const deptId = department.id === 'doc-view-cardiology' ? 'cardi101' :
                department.id === 'doc-view-dermatology' ? 'derma301' :
                    department.id === 'doc-view-neurology' ? 'neuro201' : '';

            if (deptId) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'http://localhost/Hospital-Management/user/doctor_view_doctor.php';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'dept_id';
                input.value = deptId;

                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            }
        });

        // Add event listener for mouseover
        department.addEventListener('mouseover', function (event) {
            department.style.transform = 'scale(1.2)';
            department.style.cursor = 'pointer';
        });

        // Add event listener for mouseout
        department.addEventListener('mouseout', function (event) {
            department.style.transform = 'scale(1)';
        });
    });
});
