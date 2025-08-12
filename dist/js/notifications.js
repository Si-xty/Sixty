$(document).ready(function () {
    $('#loginForm').on('submit', function (e) {
        e.preventDefault();
        const loginUrl = $(this).data('login-url');

        $.ajax({
            url: loginUrl,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function (response) {
            if (response.status === 'success') {
                Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: response.message,
                timer: 500,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
                }).then(() => {
                window.location.href = response.redirect;
                });
            } else {
                Swal.fire({
                icon: 'error',
                title: '¡Error!',
                text: response.message,
                timer: 4000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
                });
            }
            },
            error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error inesperado',
                text: 'Intenta nuevamente más tarde.',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
            }
        });
    });


    // WOL notification
    $('#wol-btn').on('click', function (e) {
        e.preventDefault();
        console.log('Click en WOL detectado');
        $.ajax({
            url: base_url + 'wol',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                console.log('Respuesta AJAX:', response);
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '¡Error!',
                        text: response.message,
                        timer: 4000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            },
            error: function (xhr, status, error) {
                console.log('Error AJAX:', status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error inesperado',
                    text: 'No se pudo ejecutar la acción.',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }
        });
    });
});