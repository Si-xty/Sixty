

function sendMailjetEmail(data) {
    $.ajax({
        url: base_url + 'mailjet',
        type: 'POST',
        data: data,
        dataType: 'json',
        success: function(response) {
                if (response.success) {
                    showSuccessNotification(response.message);
                } else {
                    showErrorNotification(response.message);
                }
        },
        error: function() {
                showAjaxErrorNotification('Error de comunicación con el servidor al enviar el correo.');
        }
    });
}

// Ejemplo de uso:
$('#sendMailjetBtn').on('click', function() {
    const data = {
        to_email: $('#to_email').val(),
        to_name: $('#to_name').val(),
        subject: $('#subject').val(),
        html_body: $('#html_body').val(),
        text_body: $('#text_body').val()
    };
    sendMailjetEmail(data);
});