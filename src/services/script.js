$(document).ready(function() {
    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();

        var file = $('#fileInput')[0].files[0];
        if (!file) {
            showMessage('Por favor, selecione um arquivo antes de enviar.', 'error');
            return;
        }

        var formData = new FormData(this);

        $.ajax({
            url: 'http://localhost:8101', // Caminho atualizado
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log("Resposta do servidor: ", response);
                try {
                    var products = JSON.parse(response);
                    var tableBody = $('#productTable tbody');
                    tableBody.empty();

                    products.forEach(function(product) {
                        var row = $('<tr>');

                        if (product.isNegativePrice) {
                            row.css('background-color', 'red');
                        }

                        row.append('<td>' + product.name + '</td>');
                        row.append('<td>' + product.code + '</td>');
                        row.append('<td>' + (product.price ? product.price.toFixed(2) : '0.00') + '</td>');

                        if (product.hasEvenNumber) {
                            var actionButton = '<button onclick="copyToClipboard(\'' + btoa(JSON.stringify(product)) + '\')">Copiar</button>';
                            row.append('<td>' + actionButton + '</td>');
                        } else {
                            row.append('<td></td>');
                        }

                        tableBody.append(row);
                    });

                    showMessage('Arquivo enviado com sucesso!', 'success');
                } catch (error) {
                    console.error('Erro ao processar JSON:', error.message, response);
                    showMessage('Erro ao processar os dados recebidos do servidor.', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error("Erro na requisição AJAX:", status, error);
                showMessage('Erro ao enviar o arquivo.', 'error');
            }
        });
    });
});

function copyToClipboard(data) {
    navigator.clipboard.writeText(data).then(() => {
        showMessage('Texto copiado!', 'success');
    }).catch(err => {
        console.error('Falha ao copiar: ', err);
        fallbackCopy(data);
    });
}

function fallbackCopy(data) {
    var tempInput = document.createElement('textarea'); 
    tempInput.value = data;
    document.body.appendChild(tempInput);
    tempInput.select();

    try {
        document.execCommand('copy');
        showMessage('Texto copiado!', 'success');
    } catch (err) {
        console.error('Falha ao copiar usando execCommand:', err);
        showMessage('Não foi possível copiar.', 'error');
    }

    document.body.removeChild(tempInput);
}

/* Exibir mensagem personalizada */
function showMessage(message, type) {
    var messageBox = document.createElement('div');
    messageBox.className = 'message-box ' + type;
    messageBox.innerText = message;
    document.body.appendChild(messageBox);

    setTimeout(() => {
        messageBox.style.display = 'block';
        messageBox.style.opacity = '1';
    }, 100);

    setTimeout(() => {
        messageBox.style.opacity = '0';
        setTimeout(() => {
            messageBox.remove();
        }, 300);
    }, 2500);
}
