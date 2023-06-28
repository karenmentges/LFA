<?php

// Função para imprimir o AFND
function printAFND($lista, $lists, $array, $finalstate) {
?>
    <H3>AFND</H3>
    <div class="table-wrapper">
        <table>
            <tr>
                <th>δ</th>
                <?php
                foreach($lista as $alphabet){
                ?>
                    <th><?=$alphabet->getContent()?></th>
                <?php
                }
                ?>
            </tr>
            <?php
            foreach($lists as $state){
            ?>
                <tr>
                    <?php
                    $flag = False;
                    for ($i=0; $i < count($finalstate); $i++) {
                        if($finalstate[$i] == $state->getContent()){
                            $flag = True;
                        }
                    }
                    if($flag == True) {
                        $stateTable = implode("", explode("_", $state->getContent()));
                    ?>
                        <td>*<?= $stateTable?></td>
                    <?php
                    }
                    else {
                        $stateTable = implode("", explode("_", $state->getContent()));
                    ?>
                        <td><?= $stateTable?></td>
                    <?php
                    }
                    ?>
                    <?php
                    foreach($lista as $alphabet){
                        if(isset($array[$state->getContent()][$alphabet->getContent()])){
                            $contentTable = implode("", explode("_", $array[$state->getContent()][$alphabet->getContent()]));
                    ?>
                        <td><?=$contentTable?></td>
                    <?php
                        }
                        else {
                        ?>
                            <td></td>
                        <?php    
                        }
                    }
                    ?>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
    <br>
    <br>
<?php
}

// Função para imprimir o AFD
function printAFD($lista, $array3, $array2, $finalstate) {
?>
    <div class="table-wrapper">
        <H3>AFD</H3>
        <table>
            <tr>
                <th>δ</th>
                <?php
                foreach($lista as $alphabet){
                ?>
                    <th><?=$alphabet->getContent()?></th>
                <?php
                }
                ?>
            </tr>
            <?php
            foreach($array3 as $state){
            ?>
                <tr>
                    <?php
                    $flag = False;
                    for ($i=0; $i < count($finalstate); $i++) {
                        if($finalstate[$i] == $state){
                            $flag = True;
                        }
                    }
                    if($flag == True) {
                        if(count(explode(".", $state)) > 1) {
                            $stateTable = implode("", explode("_", implode("", explode(".", $state))));
                    ?>
                            <td>*<?='['.$stateTable.']'?></td>
                    <?php
                        }
                        else {
                            $stateTable = implode("", explode("_", implode("", explode(".", $state))));
                    ?>
                            <td>*<?=$stateTable?></td>
                    <?php
                        }
                    }
                    else {
                        if(count(explode(".", $state)) > 1 && $state != 'xx') {
                            $stateTable = implode("", explode("_", implode("", explode(".", $state))));
                    ?>
                            <td><?='['.$stateTable.']'?></td>
                    <?php
                        }
                        else {
                            $stateTable = implode("", explode("_", implode("", explode(".", $state))));
                    ?>
                            <td><?=$stateTable?></td>
                    <?php
                        }
                    }
                    ?>
                    <?php
                    foreach($lista as $alphabet){
                        if(isset($array2[$state][$alphabet->getContent()])){
                            if(count(explode(".", $array2[$state][$alphabet->getContent()])) > 1 && $array2[$state][$alphabet->getContent()] != 'xx') {
                                $contentTable = implode("", explode("_", implode("", explode(".", $array2[$state][$alphabet->getContent()]))));
                    ?>
                                <td><?='['.$contentTable.']'?></td>
                    <?php
                            }
                            else {
                                $contentTable = implode("", explode("_", implode("", explode(".", $array2[$state][$alphabet->getContent()]))));
                    ?>
                                <td><?=$contentTable?></td>
                    <?php
                            } 
                        }
                        else {
                        ?>
                            <td></td>
                        <?php    
                        }
                    }
                    ?>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
<?php
}

// Função para imprimir a tabela de simbolos
function printTabelaSimbolos($tabelaSimbolos) {
?>
    <div class="table-wrapper">
        <H3>Tabela de Simbolos</H3>
        <table>
            <tr>
                <th class="small">Nome</th>
                <th class="small">Linha</th>
            </tr>
            <?php
            foreach($tabelaSimbolos as $simbolos){
            ?>
                <tr>
                    <td><?=$simbolos['nome']?></td>
                    <td><?=$simbolos['linha']?></td>
                </tr>
            <?php
            }
            ?>
        </table>
    </div>
<?php
}

?>