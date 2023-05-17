<?php

// Função para verificar se um token é aceito pelo AFD
function acceptToken($token, $matrizAFD, $finalstate) {
    $currentState = 'S';
    
    for ($i = 0; $i < strlen($token); $i++) {
        if ($currentState == 'xx') {
            return false;
        }

        $char = $token[$i];
        
        if (!isset($matrizAFD[$currentState][$char])) {
            return false;
        }
        
        $currentState = $matrizAFD[$currentState][$char];
    }
    
    // Verifica se o estado final é um estado de aceitação
    return isset($matrizAFD[$currentState]) && in_array($currentState, $finalstate);
}

// Função para realizar a análise léxica e armazenar a tabela de símbolos
function lexicalAnalyzer($entry, $matrizAFD, $finalstate) {
    // Remoção de quebras de linha e espaços em branco extras
    $entry = str_replace(["\r", "\n"], '', $entry);
    $entry = trim($entry);
    
    // Quebra a entrada em tokens com base no separador (espaço em branco)
    $tokens = explode(' ', $entry);
    
    // Array para armazenar os tokens válidos
    $tokensValidos = [];
    
    // Tabela de símbolos
    $tabelaSimbolos = [];

    // Verifica cada token e adiciona apenas os válidos ao array e à tabela de símbolos
    foreach ($tokens as $token) {
        if (acceptToken($token, $matrizAFD, $finalstate)) {
            $tokensValidos[] = $token;
            
            // Verifica se o token já está na tabela de símbolos
            if (!isset($tabelaSimbolos[$token])) {
                // Se não estiver, adiciona o token à tabela de símbolos com as informações desejadas
                $tabelaSimbolos[$token] = [
                    'informacao1' => 'valor1',
                    'informacao2' => 'valor2',
                    // adicione mais informações se necessário
                ];
            }
        }
    }
    
    // Retorna os tokens válidos e a tabela de símbolos
    return [
        'tokensValidos' => $tokensValidos,
        'tabelaSimbolos' => $tabelaSimbolos
    ];
}


/* 
// Definição do AFD para números inteiros
$afd = [
    'q0' => ['0' => 'q1', '1-9' => 'q2'],
    'q1' => ['0-9' => 'q1', 'accept' => true],
    'q2' => ['0-9' => 'q2', 'accept' => true]
];

// Função para verificar se um token é aceito pelo AFD
function acceptToken($token, $afd) {
    $currentState = 'q0';
    
    for ($i = 0; $i < strlen($token); $i++) {
        $char = $token[$i];
        
        if (!isset($afd[$currentState][$char])) {
            return false;
        }
        
        $currentState = $afd[$currentState][$char];
    }
    
    // Verifica se o estado final é um estado de aceitação
    return isset($afd[$currentState]) && $afd[$currentState]['accept'];
}

// Função para realizar a análise léxica e armazenar a tabela de símbolos
function realizarAnaliseLexica($entrada, $afd) {
    // Remoção de quebras de linha e espaços em branco extras
    $entrada = str_replace(["\r", "\n"], '', $entrada);
    $entrada = trim($entrada);
    
    // Quebra a entrada em tokens com base no separador (espaço em branco)
    $tokens = explode(' ', $entrada);
    
    // Array para armazenar os tokens válidos
    $tokensValidos = [];
    
    // Tabela de símbolos
    $tabelaSimbolos = [];
    
    // Verifica cada token e adiciona apenas os válidos ao array e à tabela de símbolos
    foreach ($tokens as $token) {
        if (acceptToken($token, $afd)) {
            $tokensValidos[] = $token;
            
            // Verifica se o token já está na tabela de símbolos
            if (!isset($tabelaSimbolos[$token])) {
                // Se não estiver, adiciona o token à tabela de símbolos com as informações desejadas
                $tabelaSimbolos[$token] = [
                    'informacao1' => 'valor1',
                    'informacao2' => 'valor2',
                    // adicione mais informações se necessário
                ];
            }
        }
    }
    
    // Retorna os tokens válidos e a tabela de símbolos
    return [
        'tokensValidos' => $tokensValidos,
        'tabelaSimbolos' => $tabelaSimbolos
    ];
}

// Leitura do arquivo de texto
$entrada = file_get_contents('caminho_do_arquivo.txt');

// Realiza a análise léxica e obtém os tokens válidos e a tabela de símbolos
$resultado = realizarAnaliseLexica($entrada, $afd);

// Exibe os tokens válidos encontrados
echo "Tokens válidos: ";
print_r($resultado['tokensValidos']);

// Exibe a tabela de símbolos
echo "Tabela de símbolos

 */