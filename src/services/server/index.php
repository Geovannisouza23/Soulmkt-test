<?php
// Definir cabeçalhos CORS
header("Access-Control-Allow-Origin: *"); // Permitir todas as origens (pode restringir para um domínio específico)
header("Access-Control-Allow-Methods: GET, POST, OPTIONS"); // Métodos permitidos
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With"); // Cabeçalhos permitidos

// Se for uma requisição OPTIONS, responder rapidamente sem processamento adicional
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

class ProductProcessor
{
    private $separator;
    private $file;

    public function __construct($file, $separator)
    {
        $this->file = $file;
        $this->separator = $separator;
    }

    public function process()
    {
        $data = $this->parseCsv();
        $products = $this->filterAndSortProducts($data);
        echo json_encode($products);
    }

    private function parseCsv()
    {
        if (!file_exists($this->file) || !is_readable($this->file)) {
            error_log("Arquivo não encontrado ou não é legível: " . $this->file);
            return [];
        }

        $handle = fopen($this->file, "r");
        if (!$handle) {
            error_log("Erro ao abrir o arquivo: " . $this->file);
            return [];
        }

        $rows = [];
        while (($row = fgetcsv($handle, 1000, $this->separator)) !== false) {
            $rows[] = $row;
        }

        fclose($handle);
        return $rows;
    }

    private function filterAndSortProducts($data)
    {
        if (empty($data) || !isset($data[0])) {
            return [];
        }

        $products = [];
        $header = array_map('strtolower', $data[0]);

        // Identificar colunas
        $nameCol = array_search('nome', $header);
        $codeCol = array_search('codigo', $header);
        $priceCol = array_search('preco', $header);

        if ($nameCol === false || $codeCol === false || $priceCol === false) {
            error_log("Colunas essenciais não encontradas.");
            return []; // Retorna vazio caso as colunas essenciais não sejam encontradas
        }

        // Processar produtos
        foreach ($data as $index => $row) {
            if ($index === 0) continue; // Ignorar cabeçalho

            $name = isset($row[$nameCol]) ? trim($row[$nameCol]) : '';
            $code = isset($row[$codeCol]) ? trim($row[$codeCol]) : '';
            $price = isset($row[$priceCol]) ? floatval($row[$priceCol]) : 0;

            // Ignorar produtos sem nome ou código válido
            if (!$name || !$code) continue;

            $isNegativePrice = $price < 0;
            $hasEvenNumber = $this->hasEvenNumberInCode($code);

            $products[] = [
                'name' => $name,
                'code' => $code,
                'price' => $price,
                'isNegativePrice' => $isNegativePrice,
                'hasEvenNumber' => $hasEvenNumber
            ];
        }

        // Ordenar produtos pelo nome
        usort($products, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return $products;
    }

    private function hasEvenNumberInCode($code)
    {
        preg_match_all('/\d/', $code, $matches);
        foreach ($matches[0] as $digit) {
            if (intval($digit) % 2 === 0) {
                return true;
            }
        }
        return false;
    }
}

// Verificar upload e processar arquivo
if (isset($_FILES['fileInput']) && $_FILES['fileInput']['error'] === UPLOAD_ERR_OK) {
    // Verificar se o arquivo está vazio
    if ($_FILES['fileInput']['size'] === 0) {
        error_log("Arquivo vazio enviado.");
        echo json_encode(['error' => 'O arquivo está vazio.']);
        exit;
    }

    // Logando o nome do arquivo recebido
    error_log("Arquivo recebido: " . $_FILES['fileInput']['name']);
    error_log(print_r($_FILES, true));
    
    // Remover caracteres especiais indesejados do nome do arquivo
    $filename = $_FILES['fileInput']['name'];
    $filename = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $filename);  // Limpar caracteres especiais
    
    // Validando o tipo MIME do arquivo
    $fileInfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $fileInfo->file($_FILES['fileInput']['tmp_name']);
    if ($mimeType !== 'text/csv' && $mimeType !== 'text/plain') {
        error_log("Tipo de arquivo inválido: " . $mimeType);
        echo json_encode(['error' => 'O arquivo deve ser um CSV válido.']);
        exit;
    }
    
    $separator = $_POST['separator'] ?? ',';
    $destination = __DIR__ . "/uploads/" . basename($filename);

    // Verificar se o diretório de uploads existe, caso contrário, criar
    if (!is_dir(__DIR__ . "/uploads")) {
        mkdir(__DIR__ . "/uploads", 0775, true);
    }

    if (move_uploaded_file($_FILES["fileInput"]["tmp_name"], $destination)) {
        $processor = new ProductProcessor($destination, $separator);
        $processor->process();
    } else {
        error_log("Erro ao mover o arquivo para o diretório de uploads.");
        echo json_encode(['error' => 'Erro ao salvar o arquivo.']);
    }
} else {
    // Exibindo erro se o upload falhar
    error_log("Erro no upload: " . $_FILES['fileInput']['error']);
    echo json_encode(['error' => 'Nenhum arquivo foi enviado ou ocorreu um erro no upload.']);
}
?>
