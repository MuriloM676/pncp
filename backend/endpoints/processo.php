<?php

$cnpj = $_GET['cnpj'] ?? '';
$ano = $_GET['ano'] ?? '';
$sequencial = $_GET['sequencial'] ?? '';

// MOCK DETALHES
$mockDetalhe = [
    'numeroCompra' => "{$sequencial}/{$ano}",
    'objeto' => 'Aquisição de materiais de escritório e papelaria para diversas unidades administrativas. Inclui papel A4, canetas, pastas e outros itens essenciais para o funcionamento diário.',
    'dataAberturaPropostas' => date('c', strtotime('+5 days')),
    'nomeOrgao' => 'Órgão de Exemplo via Mock',
    'municipioNome' => 'Cidade Exemplo',
    'siglaUf' => 'SP',
    'valorEstimado' => 15500.50,
    'informacoesComplementares' => 'Este é um dado de exemplo exibido porque a API oficial do governo está temporariamente fora do ar (Timeout 504).'
];

echo json_encode($mockDetalhe);
