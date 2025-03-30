# Usar a imagem oficial do PHP (apenas com servidor embutido)
FROM php:8.1-cli

# Copiar os arquivos do projeto para o diretório do contêiner
COPY . /var/www/html

# Expor a porta 80
EXPOSE 80

# Iniciar o servidor embutido PHP na porta 80
CMD ["php", "-S", "0.0.0.0:80", "-t", "/var/www/html"]
