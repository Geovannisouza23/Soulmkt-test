# Teste-PHP-Soulmkt-
# Instruções de uso

1. Clone o repositório para sua máquina local.
2. Construa e inicie os containers no Docker:
3. Acesse a aplicação através de um navegador "localhost:8080" ou abra o docker e clique na porta de saida. 
4. Faça o upload de uma planilha CSV contendo as colunas "nome", "codigo" e "preco".
5. A tabela será gerada com os dados ordenados. Linhas com preços negativos serão coloridas de vermelho.
6. Para códigos de produto com números pares, um botão "Copiar" estará disponível, permitindo copiar os dados para a área de transferência.

7. Para realizar o teste pelo PHPUnit, abra a pasta src/teste no terminal e digite o comando 'composer require --dev "phpunit/phpunit:^9.5"" em seguida "./vendor/bin/phpunit indexTest.php"
