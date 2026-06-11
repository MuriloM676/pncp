<?php

class PNCPClient {
    private $baseUrl;
    private $publicKey;
    private $logger;

    public function __construct($baseUrl, $publicKey = null) {
        $this->baseUrl = rtrim($baseUrl, '/') . '/';
        $this->publicKey = $publicKey;
    }

    public function request($endpoint, $params = [], $retries = 3) {
        $url = $this->baseUrl . $endpoint;
        
        // Adicionar publicKey se disponível
        if ($this->publicKey) {
            $params['publicKey'] = $this->publicKey;
        }

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $attempt = 0;
        while ($attempt < $retries) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_USERAGENT, 'PNCP-Integration-App/1.0');
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Temporário para evitar erros de certificado gov

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($httpCode === 200) {
                return json_decode($response, true);
            }

            Logger::error("API PNCP Falhou: $url | Code: $httpCode | Error: $error | Response: " . substr($response, 0, 100));

            if ($httpCode === 429) { // Rate limit da API externa
                $wait = pow(2, $attempt);
                Logger::log("API PNCP Rate Limit (429). Aguardando {$wait}s... (Tentativa " . ($attempt + 1) . ")");
                sleep($wait);
            } elseif ($error || $httpCode >= 500) {
                Logger::error("Erro na API PNCP: $url | Code: $httpCode | Error: $error");
                sleep(1);
            } else {
                // Erros 4xx (exceto 429) geralmente não adianta retry
                Logger::error("Erro cliente API PNCP: $url | Code: $httpCode");
                break;
            }

            $attempt++;
        }

        return null;
    }
}
