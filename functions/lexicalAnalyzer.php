<?php
// Função para verificar se um token é aceito pelo AFD
function acceptToken($token, $matrizAFD, $finalstate) {
    // Inicializa o estado atual com o estado inicial
    $currentState = 'S';
    
    for ($i = 0; $i < strlen($token); $i++) {
        // Se o estado atual é o estado de erro, então retorna que o token não foi reconhecido
        if ($currentState == 'xx') {
            return [
                'accept' => 'false',
                'tapeState' => 'xx'
            ];
        }

        // Armazena um parte do token
        $char = $token[$i];
        
        // Se na matriz não existe um resultado para a posição do estado atual com a parte 
        // analizada do token, então retorna que o token não foi reconhecido
        if (!isset($matrizAFD[$currentState][$char])) {
            return [
                'accept' => 'false',
                'tapeState' => 'xx'
            ];
        }
        
        // Armazena o novo estado atual
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
    $i = 0;                 // Contador de tokens usado na tabela de símbolos
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

        // Verifica cada token e adiciona os válidos e inválidos ao array e à tabela de símbolos
        foreach ($tokens as $token) {
            $token = trim($token);
            $result = acceptToken($token, $matrizAFD, $finalstate);
            if ($result['accept'] == 'true') {
                // Insere o token na fita
                $tape[] = $token;

                // Armazena os tokens validos no array
                $tokensValidos[] = $token;

                // Armazena os tokens validos juntamente com a linha onde se encontra na tabela de símbolos
                $tabelaSimbolos[$i] = [
                    'nome' => $token,
                    'linha' => $line
                ];
            }
            else {
                // Insere o estado de erro na fita
                $tape[] = $result['tapeState'];

                // Armazena os tokens invalidos no array
                $tokensInvalidos[] = $token;

                // Armazena os tokens invalidos juntamente com a linha onde se encontra na tabela de símbolos
                $tabelaSimbolos[$i] = [
                    'nome' => $token,
                    'linha' => $line
                ];

                // Se o token não foi reconhecido retorna uma impressão com a linha e o token
                echo "Erro léxico na linha " . $line . ", token: " . $token . ".<br>";
            }

            $i += 1;
        }
        
        $line += 1;
    }

    // Insere na fita o simbolo final
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