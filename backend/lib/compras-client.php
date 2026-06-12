<?php

class ComprasGovClient {
    private $baseUrl;
    private $token;

    public function __construct($baseUrl, $token = null) {
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
        $this->token = $token;
    }

    /**
     * Faz uma requisição genérica para a API com suporte a módulos
     */
    public function request($module, $endpoint, $params = [], $method = 'GET', $retries = 3) {
        // Constrói a URL completa: BaseURL + Módulo + Endpoint
        $fullPath = ltrim($module, '/') . '/' . ltrim($endpoint, '/');
        $url = $this->baseUrl . $fullPath;
        
        if ($method === 'GET' && !empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $attempt = 0;
        while ($attempt < $retries) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'ComprasGov-Integration/1.0');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

            $headers = ['Accept: application/json'];
            if ($this->token) {
                $headers[] = 'Authorization: Bearer ' . $this->token;
            }

            if ($method !== 'GET') {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                if (!empty($params)) {
                    $jsonParams = json_encode($params);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonParams);
                    $headers[] = 'Content-Type: application/json';
                    $headers[] = 'Content-Length: ' . strlen($jsonParams);
                }
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                return json_decode($response, true);
            }

            Logger::error("API Compras.gov.br Falhou: $method $url | Code: $httpCode | Error: $error | Response: " . substr($response, 0, 200));

            if ($httpCode === 429) {
                $wait = pow(2, $attempt);
                Logger::log("API Compras.gov.br Rate Limit (429). Aguardando {$wait}s... (Tentativa " . ($attempt + 1) . ")");
                sleep($wait);
            } elseif ($error || $httpCode >= 500) {
                sleep(1);
            } else {
                break;
            }
            $attempt++;
        }
        return null;
    }

    // --- Módulo Contratações ---
    public function getContratacoes($params = []) {
        $defaultParams = [
            'dataPublicacaoPncpInicial' => date('Y-m-d', strtotime('-30 days')),
            'dataPublicacaoPncpFinal' => date('Y-m-d'),
        ];
        
        // Se a modalidade não for fornecida e não estiver definida como vazia/todas, usa a padrão (5 = Pregão)
        if (!isset($params['codigoModalidade']) && !array_key_exists('codigoModalidade', $params)) {
            $defaultParams['codigoModalidade'] = 5;
        }

        $params = array_merge($defaultParams, $params);

        // Limpa parâmetros nulos, vazios ou 'todos'
        $cleanedParams = [];
        foreach ($params as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            if ($key === 'codigoModalidade' && ($value === 'todos' || $value === 'all' || $value === 0 || $value === '0')) {
                continue;
            }
            $cleanedParams[$key] = $value;
        }

        return $this->request('modulo-contratacoes', '1_consultarContratacoes_PNCP_14133', $cleanedParams);
    }

    public function getContratacaoPorId($id) {
        return $this->request('modulo-contratacoes', '1.1_consultarContratacoes_PNCP_14133_Id', [
            'tipo' => 'idCompra',
            'codigo' => $id
        ]);
    }

    // --- Módulo Material ---
    public function getGruposMaterial($params = []) {
        return $this->request('modulo-material', '1_consultarGrupoMaterial', $params);
    }

    public function getClassesMaterial($params = []) {
        return $this->request('modulo-material', '2_consultarClasseMaterial', $params);
    }

    // --- Módulo Fornecedor ---
    public function getFornecedor($cnpj) {
        return $this->request('modulo-fornecedor', '1_consultarFornecedor', [
            'cnpj' => $cnpj
        ]);
    }

    // --- Módulo Material (Busca PDM) ---
    public function getPdmMaterial($descricao, $pagina = 1) {
        return $this->request('modulo-material', '3_consultarPdmMaterial', [
            'pdm_descricao' => $descricao,
            'pagina' => $pagina
        ]);
    }
}
