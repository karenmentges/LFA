<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Karen Mentges">
    <title>PP - LFA</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="Shortcut Icon" href="assets/images/logo_karen.ico" type="image/x-icon">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'> <!-- referente aos icones usados na página -->
</head>
<body>
    <img id="logouffs" src="assets/images/logo_uffs.png" alt="Logo da UFFS">
    <p id="first">Linguagens Formais e Autômatos<br>Karen Ruver Mentges e Izabela Fusieger</p>
    <h1 id="second">Projeto Prático</h1>

    <form action="generator.php" method="post" enctype="multipart/form-data">
        <div class="uploads">
            <div id="input" class="inputfile_box">
                <label for="txt"><span id="file_name" class="file_box"></span><span class="file_button"><i class='bx bx-paperclip' id="clip"></i>&nbsp;&nbsp;Realizar upload do txt</span></label><br>
                <input type="file" id="txt" name="field_txt" accept=".txt" required oninvalid="this.setCustomValidity('Insira um txt!')" oninput="setCustomValidity('')" onchange='uploadFile(this)'/><br>
            </div>
        </div>

        <input type="submit" name="insert" value="CONFIRMAR">
        <input type="reset" name="cancel" value="CANCELAR" onclick="window.location='program.php'">
    </form>
</body>
<script src="assets/js/functions.js"></script>
</html>