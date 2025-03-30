<?php

require_once __DIR__ . '/../server/index.php'; 
use PHPUnit\Framework\TestCase;

class IndexTest extends TestCase
{
    private $testFile;

    protected function setUp(): void
    {
        // Criar um arquivo CSV de teste temporário
        $this->testFile = tempnam(sys_get_temp_dir(), 'test_') . '.csv';
    }

    protected function tearDown(): void
    {
        // Remover o arquivo de teste após os testes
        unlink($this->testFile);
    }

    public function testMissingColumn()
    {
        file_put_contents($this->testFile, "Nome,Preco\nProduto1,10.5\nProduto2,-5.5\n");
        $processor = new ProductProcessor($this->testFile, ',');

        // Usando Reflection para acessar o método privado
        $reflection = new ReflectionClass('ProductProcessor');

        // Acessando o método privado parseCsv
        $parseCsvMethod = $reflection->getMethod('parseCsv');
        $parseCsvMethod->setAccessible(true);
        $data = $parseCsvMethod->invoke($processor);

        // Acessando o método privado filterAndSortProducts
        $filterAndSortMethod = $reflection->getMethod('filterAndSortProducts');
        $filterAndSortMethod->setAccessible(true);
        $result = $filterAndSortMethod->invoke($processor, $data);
        
        $this->assertCount(0, $result);
    }

    public function testMissingNameColumn()
    {
        file_put_contents($this->testFile, "Codigo,Preco\n123,10.5\n456,-5.5\n");
        $processor = new ProductProcessor($this->testFile, ',');

        // Usando Reflection para acessar o método privado
        $reflection = new ReflectionClass('ProductProcessor');

        // Acessando o método privado parseCsv
        $parseCsvMethod = $reflection->getMethod('parseCsv');
        $parseCsvMethod->setAccessible(true);
        $data = $parseCsvMethod->invoke($processor);

        // Acessando o método privado filterAndSortProducts
        $filterAndSortMethod = $reflection->getMethod('filterAndSortProducts');
        $filterAndSortMethod->setAccessible(true);
        $result = $filterAndSortMethod->invoke($processor, $data);
        
        $this->assertCount(0, $result);
    }

    public function testProductWithMissingName()
    {
        file_put_contents($this->testFile, "Nome,Codigo,Preco\n,123,10.5\nProduto2,456,-5.5\n");
        $processor = new ProductProcessor($this->testFile, ',');

        // Usando Reflection para acessar o método privado
        $reflection = new ReflectionClass('ProductProcessor');

        // Acessando o método privado parseCsv
        $parseCsvMethod = $reflection->getMethod('parseCsv');
        $parseCsvMethod->setAccessible(true);
        $data = $parseCsvMethod->invoke($processor);

        // Acessando o método privado filterAndSortProducts
        $filterAndSortMethod = $reflection->getMethod('filterAndSortProducts');
        $filterAndSortMethod->setAccessible(true);
        $result = $filterAndSortMethod->invoke($processor, $data);

        $this->assertCount(1, $result);
        $this->assertEquals('Produto2', $result[0]['name']);
    }

    public function testProductWithMissingCode()
    {
        file_put_contents($this->testFile, "Nome,Codigo,Preco\nProduto1,,10.5\nProduto2,456,-5.5\n");
        $processor = new ProductProcessor($this->testFile, ',');

        // Usando Reflection para acessar o método privado
        $reflection = new ReflectionClass('ProductProcessor');

        // Acessando o método privado parseCsv
        $parseCsvMethod = $reflection->getMethod('parseCsv');
        $parseCsvMethod->setAccessible(true);
        $data = $parseCsvMethod->invoke($processor);

        // Acessando o método privado filterAndSortProducts
        $filterAndSortMethod = $reflection->getMethod('filterAndSortProducts');
        $filterAndSortMethod->setAccessible(true);
        $result = $filterAndSortMethod->invoke($processor, $data);

        $this->assertCount(1, $result);
        $this->assertEquals('Produto2', $result[0]['name']);
    }

    public function testDifferentSeparator()
    {
        file_put_contents($this->testFile, "Nome;Codigo;Preco\nProduto1;123;10.5\nProduto2;456;5.5\n");
        $processor = new ProductProcessor($this->testFile, ';');

        // Usando Reflection para acessar o método privado
        $reflection = new ReflectionClass('ProductProcessor');

        // Acessando o método privado parseCsv
        $parseCsvMethod = $reflection->getMethod('parseCsv');
        $parseCsvMethod->setAccessible(true);
        $data = $parseCsvMethod->invoke($processor);

        // Acessando o método privado filterAndSortProducts
        $filterAndSortMethod = $reflection->getMethod('filterAndSortProducts');
        $filterAndSortMethod->setAccessible(true);
        $result = $filterAndSortMethod->invoke($processor, $data);

        $this->assertCount(2, $result);
        $this->assertEquals('Produto1', $result[0]['name']);
        $this->assertEquals('Produto2', $result[1]['name']);
    }

    public function testEmptyFile()
    {
        file_put_contents($this->testFile, "");
        $processor = new ProductProcessor($this->testFile, ',');

        // Usando Reflection para acessar o método privado
        $reflection = new ReflectionClass('ProductProcessor');

        // Acessando o método privado parseCsv
        $parseCsvMethod = $reflection->getMethod('parseCsv');
        $parseCsvMethod->setAccessible(true);
        $data = $parseCsvMethod->invoke($processor);

        // Acessando o método privado filterAndSortProducts
        $filterAndSortMethod = $reflection->getMethod('filterAndSortProducts');
        $filterAndSortMethod->setAccessible(true);
        $result = $filterAndSortMethod->invoke($processor, $data);

        $this->assertCount(0, $result);
    }
}
