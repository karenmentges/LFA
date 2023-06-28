<?php

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

    /* funcionando, testar com erro */

    $pilha[] = 0;
    $pilha[] = 0;
    $idx = 0;

    while(true) {
        if(!isset($statesLALRTable[$pilha[0]][$tape[0]])) {
            echo "Erro sintático na linha " . $tabelaSimbolos[$idx]['linha'] . ", token: " . $tabelaSimbolos[$idx]['nome'] . ".<br>";
            break;
        }
        
        $action = $statesLALRTable[$pilha[0]][$tape[0]];
        
        // Empilhamento ou shift
        if ($action['Action'] == '1') { 
            $pilha = array_reverse($pilha);
            $pilha[] = array_shift($tape);
            $pilha[] = $action['Value'];
            $pilha = array_reverse($pilha);
            $idx += 1;           
        }
        // Redução
        else if($action['Action'] == '2') { 
            $size = $productionsTable[$action['Value']]['SymbolCount'] * 2; // Recupera o número de símbolos da produção
            while($size) { // Desempilha os símbolos da produção
                array_shift($pilha);
                $size -= 1;
            }
            $pilha = array_reverse($pilha);
            $pilha[] = $productionsTable[$action['Value']]['NonTerminalIndex'];
            $pilha[] = $statesLALRTable[$pilha[count($pilha)-2]][$pilha[count($pilha)-1]]['Value'];
            $pilha = array_reverse($pilha);
        } 
        // Salto
        else if($action['Action'] == '3') {
            echo('salto');
                # Mude para o próximo estado indicado na célula correspondente do símbolo não-terminal
                # pilha.insert(0, lalr_table[int(pilha[0])][action['Value']]['Value'])
        }
        // Aceita
        else if($action['Action'] == '4') {
            echo('Texto inserido confere com a gramática');
            break;
        }
    }            
}

?>