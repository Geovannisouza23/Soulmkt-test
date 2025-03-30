$(document).ready(function() {
    $('#uploadForm').on('submit', function(e) {
        e.preventDefault();

        var file = $('#fileInput')[0].files[0];
        if (!file) {
            alert('Por favor, selecione um arquivo antes de enviar.');
            return;
        }

        var formData = new FormData(this);

        $.ajax({
            url: '../src/server/index.php', // Caminho atualizado
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                console.log("Resposta do servidor: ", response); // Logando a resposta do servidor
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

                        // Exibir botão "Copiar" apenas se `hasEvenNumber` for true
                        if (product.hasEvenNumber) {
                            var actionButton = '<button onclick="copyToClipboard(\'' + btoa(JSON.stringify(product)) + '\')">Copiar</button>';
                            row.append('<td>' + actionButton + '</td>');
                        } else {
                            row.append('<td></td>'); // Adiciona célula vazia para manter alinhamento
                        }

                        tableBody.append(row);
                    });
                } catch (error) {
                    console.error('Erro ao processar JSON:', error.message, response);
                    alert('Erro ao processar os dados recebidos do servidor.');
                }
            },
            error: function(xhr, status, error) {
                console.error("Erro na requisição AJAX:", status, error);
                alert('Erro ao enviar o arquivo.');
            }
        });
    });
});

function copyToClipboard(data) {
    navigator.clipboard.writeText(data).then(() => {
        alert('Texto copiado!');
    }).catch(err => {
        console.error('Falha ao copiar: ', err);
        fallbackCopy(data);
    });
}

// Método de fallback para navegadores antigos
function fallbackCopy(data) {
    var tempInput = document.createElement('textarea'); 
    tempInput.value = data;
    document.body.appendChild(tempInput);
    tempInput.select();

    try {
        document.execCommand('copy');
        alert('Texto copiado!');
    } catch (err) {
        console.error('Falha ao copiar usando execCommand:', err);
    }

    document.body.removeChild(tempInput);
}
