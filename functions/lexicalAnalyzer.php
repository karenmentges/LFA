<?php

// Função para verificar se um token é aceito pelo AFD
function acceptToken($token, $matrizAFD, $finalstate) {
    $currentState = 'S';
    
    for ($i = 0; $i < strlen($token); $i++) {
        if ($currentState == 'xx') {
            return [
                'accept' => 'false',
                'tapeState' => 'xx'
            ];
        }

        $char = $token[$i];
        
        if (!isset($matrizAFD[$currentState][$char])) {
            return [
                'accept' => 'false',
                'tapeState' => 'xx'
            ];
        }
        
        $currentState = $matrizAFD[$currentState][$char];
    }

    // Verifica se o estado final é um estado de aceitação
    if (isset($matrizAFD[$currentState]) && in_array($currentState, $finalstate)) {
        return [
            'accept' => 'true',
            'tapeState' => $currentState
        ];
    }
    else {
        return [
            'accept' => 'false',
            'tapeState' => 'xx'
        ];
    }
}

// Função para realizar a análise léxica e armazenar a tabela de símbolos
function lexicalAnalyzer($entry, $matrizAFD, $finalstate) {
    $i = 0;                 // Contador de tokens
    $line = 1;              // Contador da linha do documento
    $tape = [];             // Fita
    $tokensValidos = [];    // Array para armazenar os tokens válidos
    $tokensInvalidos = [];  // Array para armazenar os tokens inválidos 
    $tabelaSimbolos = [];   // Tabela de símbolos

    // Separa a entrada pelas linhas do texto
    $arrayEntry = explode("\n", $entry);

    foreach ($arrayEntry as $lineEntry) {

        // Quebra a entrada em tokens com base no separador (espaço em branco)
        $tokens = explode(' ', $lineEntry);

        // Verifica cada token e adiciona apenas os válidos ao array e à tabela de símbolos
        foreach ($tokens as $token) {
            $token = trim($token);
            $result = acceptToken($token, $matrizAFD, $finalstate);
            if ($result['accept'] == 'true') {
                $tape[] = $token;

                $tokensValidos[] = $token;

                $tabelaSimbolos[$i] = [
                    'nome' => $token,
                    'linha' => $line
                ];
            }
            else {
                $tape[] = $result['tapeState'];

                $tokensInvalidos[] = $token;

                $tabelaSimbolos[$i] = [
                    'nome' => $token,
                    'linha' => $line
                ];

                echo "Erro léxico na linha " . $line . ", token: " . $token . ".<br>";
            }

            $i += 1;
        }
        
        $line += 1;
    }

    $tape[] = '$';

    // Retorna os tokens válidos, tokens inválidos e a tabela de símbolos
    return [
        'tape' => $tape,
        'tokensValidos' => $tokensValidos,
        'tokensInvalidos' => $tokensInvalidos,
        'tabelaSimbolos' => $tabelaSimbolos
    ];
}

?>