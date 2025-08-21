// Funciones de notificación reutilizables
const showSuccessNotification = (message, title = '¡Éxito!') => {
    Swal.fire({
        icon: 'success',
        title: title,
        text: message,
        timer: 1500,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
};

const showErrorNotification = (message, title = '¡Error!') => {
    Swal.fire({
        icon: 'error',
        title: title,
        text: message,
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
};

const showAjaxErrorNotification = (message) => {
    Swal.fire({
        icon: 'error',
        title: 'Error de comunicación',
        text: message,
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
};

// ... el resto de tu código de notifications.js...
// ... (ejemplo: la lógica del login y el botón WOL)