<?php


    
    require("config.php");
    
    $prodotto = $_GET['prod'];
    $prezzo = $_GET['prezzo'];
    $detrazione = $_GET['detrazione'];
    
    
    
    $result = mysqli_query($mysqli, "INSERT INTO catalogo_prodotti(nome_prodotto,prezzo,detrazione) VALUES('$prodotto','$prezzo','$detrazione')");


?>