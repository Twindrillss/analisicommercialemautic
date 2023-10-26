<?php
//controllo cookie per accesso

if (isset($_COOKIE["AccessoConsentitoMautic"])) {


?>

<html>
    
    <head>
        
        <meta charset="utf-8">
     
  
		<link href="tab.css" rel="stylesheet" type="text/css">
		<link href="stilemodal.css" rel="stylesheet" type="text/css">
		<link href="nuovotooltip.css" rel="stylesheet" type="text/css">
		<link href="sidebar.css" rel="stylesheet" type="text/css">
		<link href="aggiunte.css" rel="stylesheet" type="text/css">
		<link href="calendar.css" rel="stylesheet" type="text/css">
		
		<link href="favicon-1.ico" rel="icon">
		<meta name="viewport" content="width=device-width, initial-scale=1"/>
        
        <title>Consulta / Modifica Listino</title>
        
        
        <script>
            
            function eliminadato(dato){
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
          location.reload();
      }
    }
    xmlhttp.open("GET", "https://addon.360forma.com/comm/ajaxdel.php?q="+dato, true);
    xmlhttp.send();
            }
            
    function inserisci(){
        
        var nomeprodotto = document.getElementById("nomeprodotto").value;
        var prezzo = document.getElementById("prezzolistino").value;
        var detrazione = document.getElementById("detrazione").value;
        
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
          location.reload();
      }
    }
    xmlhttp.open("GET", "https://addon.360forma.com/comm/ajaxadd.php?prod="+ nomeprodotto + "&prezzo=" + prezzo + "&detrazione=" + detrazione, true);
    xmlhttp.send();
            }
            
        </script>
        
    </head>


<body>
    
    
                         <!-- INIZIO BARRA NAVIGAZIONE -->
             	    <div style="max-height:90%;" class="sidenav">
	        

	        <p class="titolinav">Strumenti</p>

	        <a class="azioni" href="http://gestionale.360forma.com/s/dashboard">Torna su Mautic</a>
	        <a class="azioni" href="cal.php">Passa a visualizzazione calendario</a>
	        <a class="azioni" href="index.php">Strumento Analisi Commerciale</a>
	        
	        
	        <br>
	        

<br>

</div>
<!--QUI FINISCE LA BARRA DI NAVIGAZIONE -->
    
<div class="mainnocal">
    <div style="text-align:center;margin-top:20px;"><p style="border-style: solid;display: inline-block; padding:10px;"><input type="text" placeholder="Nome Prodotto" id="nomeprodotto" /><input type="number" placeholder="Prezzo Listino" id="prezzolistino" /><input type="number" placeholder="Detrazione" id="detrazione" /><br><button style="margin-top:10px;" onclick="inserisci()">Inserisci</button></p></div>
    
    <br><br>
    <div style='overflow-x:auto'>
    <table class="zui-table">
        <thead>
            
            <tr>
                <th>Nome Prodotto</th>
                <th>Prezzo Listino</th>
                <th>Detrazione</th>
                <th>Azioni</th>
            </tr>
            
        </thead>
        
        <tbody>
<?php

include ("config.php");

$result = mysqli_query($mysqli, "SELECT * FROM catalogo_prodotti");

while($res = mysqli_fetch_array($result)) {
    echo '<tr>';
    echo '<th>'.$res['nome_prodotto'].'</th>';
    echo '<th>'.$res['prezzo'].'</th>';
    echo '<th>'.$res['detrazione'].'</th>';
    echo '<th><button onclick="eliminadato('.$res['id'].')">Elimina</button></th>';
    echo '</tr>';
}

?>
</tbody>
</table></div></div>
</body>

</html>

       <?php
} else {
//condizione se cookie non trovato
echo 'Autorizzazione negata, accedi a Mautic.';
}
?>