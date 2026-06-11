<?php

$uf = $_GET['uf'] ?? '';
$pagina = $_GET['pagina'] ?? 1;

// MOCK DE DADOS DEVIDO A INSTABILIDADE NA API DO GOVERNO (504 TIMEOUT)
$mockData = [
    'data' => [
        [
            'numeroCompra' => '123/2026',
            'objeto' => 'Aquisição de materiais de escritório e papelaria para diversas unidades administrativas.',
            'dataPublicacaoPncp' => date('c'),
            'orgaoEntidade' => [
                'razaoSocial' => 'Prefeitura Municipal de Exemplo',
                'cnpj' => '12345678000199'
            ],
            'unidadeOrgao' => [
                'ufSigla' => 'SP'
            ],
            'valorTotalEstimado' => 15500.50,
            'anoCompra' => 2026,
            'sequencialCompra' => 1
        ],
        [
            'numeroCompra' => '45/2026',
            'objeto' => 'Contratação de serviços de manutenção preventiva e corretiva em sistemas de ar condicionado.',
            'dataPublicacaoPncp' => date('c', strtotime('-1 day')),
            'orgaoEntidade' => [
                'razaoSocial' => 'Câmara Municipal Legislativa',
                'cnpj' => '98765432000188'
            ],
            'unidadeOrgao' => [
                'ufSigla' => 'RJ'
            ],
            'valorTotalEstimado' => 42000.00,
            'anoCompra' => 2026,
            'sequencialCompra' => 45
        ],
        [
            'numeroCompra' => '89/2026',
            'objeto' => 'Compra de equipamentos de informática, incluindo notebooks e periféricos para a Secretaria de Saúde.',
            'dataPublicacaoPncp' => date('c', strtotime('-2 days')),
            'orgaoEntidade' => [
                'razaoSocial' => 'Fundo Municipal de Saúde',
                'cnpj' => '11223344000177'
            ],
            'unidadeOrgao' => [
                'ufSigla' => 'MG'
            ],
            'valorTotalEstimado' => 8500.00,
            'anoCompra' => 2026,
            'sequencialCompra' => 89
        ]
    ],
    'paginasTotal' => 1
];

// Filtro por UF no Mock
if ($uf) {
    $mockData['data'] = array_values(array_filter($mockData['data'], function($item) use ($uf) {
        return $item['unidadeOrgao']['ufSigla'] === $uf;
    }));
}

echo json_encode($mockData);
