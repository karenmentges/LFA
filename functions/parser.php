<?php
// Função para realizar a análise sintática e armazenar a tabela de símbolos
function parser($xml, $tape, $tabelaSimbolos) {

    $symbolTable = [];  // Carrega os simbolos da tabela
    $aux = []; // Array para realizar as trocas de estados da fita
    foreach($xml->m_Symbol->Symbol as $symbol) {
        $symbolTable[] = [
            'Index' => (int) $symbol['Index'],
            'Name' => (string) $symbol['Name'],
            'Type' => (int) $symbol['Type']
        ];

        $aux[] = (string) $symbol['Name'];
    }

    $productionsTable = [];  // Carrega as produções da tabela
    foreach($xml->m_Production->Production as $production) {
        $productionsTable[] = [
            'NonTerminalIndex' => (int) $production['NonTerminalIndex'],
            'SymbolCount' => (string) $production['SymbolCount']
        ];
    }

    $statesLALRTable = [];  // Carrega as ações da tabela
    foreach($xml->LALRTable->LALRState as $state) {
        $statesLALRTable[] = [];
        foreach($state->LALRAction as $action){
            // $state['Index'] = indice do estado da AFD, $action['SymbolIndex'] = indice do simbolo da gramática
            $statesLALRTable[(int) $state['Index']][(int) $action['SymbolIndex']] = [
                'Action' => (int) $action['Action'],
                'Value' => (int) $action['Value']
            ];
        }        
    }

    // Corrigindo os estados da fita
    for ($i=0; $i < count($tape); $i++) { 
        if($tape[$i] == 'xx') {
            $tape[$i] =  array_search('Error', $aux);
        }
        else if($tape[$i] == '$') {
            $tape[$i] =  array_search('EOF', $aux);
        }
        else {
            $tape[$i] = array_search($tape[$i], $aux);
        }
    }

    $pilha[] = 0; // Insere o símbolo inicial da gramática
    $pilha[] = 0; // Insere o símbolo final da pilha
    $idx = 0;     // Contador do index do token

    while(true) {
        // Se na tabela LALR não existe uma ação para a posição 0 da pilha com a posição 0 da fita 
        // retorna um erro sintático com a linha e o token
        if(!isset($statesLALRTable[$pilha[0]][$tape[0]])) {
            echo "Erro sintático na linha " . $tabelaSimbolos[$idx]['linha'] . ", token: " . $tabelaSimbolos[$idx]['nome'] . ".<br>";
            break;
        }
        
        // Armazena a ação a ser realizada
        $action = $statesLALRTable[$pilha[0]][$tape[0]];
        
        // Empilhamento ou shift
        if ($action['Action'] == '1') { 
            $pilha = array_reverse($pilha);  // Inverte a pilha
            $pilha[] = array_shift($tape);   // Insere o token na posição 0 da fita
            $pilha[] = $action['Value'];     // Insere o valor da ação
            $pilha = array_reverse($pilha);  // Inverte a pilha
            $idx += 1;
        }
        // Redução
        else if($action['Action'] == '2') { 
            $size = $productionsTable[$action['Value']]['SymbolCount'] * 2; // Recupera o número de símbolos da produção
            while($size) { // Desempilha os símbolos da produção
                array_shift($pilha); // Desempilha o topo da pilha
                $size -= 1;
            }
            $pilha = array_reverse($pilha);                                                          // Inverte a pilha
            $pilha[] = $productionsTable[$action['Value']]['NonTerminalIndex'];                      // Insere o estado que da nome a produção
            $pilha[] = $statesLALRTable[$pilha[count($pilha)-2]][$pilha[count($pilha)-1]]['Value'];  // Realiza o salto
            $pilha = array_reverse($pilha);                                                          // Inverte a pilha
        } 
        // Salto
        else if($action['Action'] == '3') {
            // Realizado na redução
        }
        // Aceita
        else if($action['Action'] == '4') {
            echo('Texto inserido confere com a gramática');
            break;
        }        
    }            
}
?>