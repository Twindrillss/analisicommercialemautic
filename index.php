<?php
//controllo cookie per accesso
//versione 3.1 rel - Daniel Intrieri - Tutti i diritti riservati - concesso in licenza
if (isset($_COOKIE["AccessoConsentitoMautic"])) {


?>



<?php
include("config.php");
include("funzioni.php");
include("funzioni_db.php");

$data = date("Y-m-d");


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
		<meta name="viewport" content="width=device-width, initial-scale=1" />
        
        <title>Strumento Analisi Commerciale</title>
    </head>
    
    
    
    <body>
        
                     <!-- INIZIO BARRA NAVIGAZIONE -->
             	    <div style="max-height:90%;" class="sidenav">
	        

	        <p class="titolinav">Strumenti</p>

	        <a class="azioni" href="http://gestionale.360forma.com/s/dashboard">Torna su Mautic</a>
	        <a class="azioni" href="cal.php">Passa a visualizzazione calendario</a>
	        <a class="azioni" href="modificalistino.php">Consulta/Modifica Listino</a>
	        
	        
	        <br>
	        

<br>

</div>
<!--QUI FINISCE LA BARRA DI NAVIGAZIONE -->
        
        
        <div class="mainnocal">
        <form class="azioni" action="index.php" method="post"> 
        Seleziona date<br>
        INIZIO: <input type="datetime-local" id="datainizio"
       name="datainizio" value="<?php echo $data; ?> 00:00"
       ><br>
       FINE: <input type="datetime-local" id="datafine"
       name="datafine" value="<?php echo $data; ?> 23:59"
       ><br>
       <input type="submit" name="submit" value="filtra">
        </form>
        
        
         <?php
        
        if (isset($_POST['submit'])){
            
            //AREA DATA
             $iniziofix = str_replace('T', " ", $_POST['datainizio']);
            $finefix = str_replace('T', " ", $_POST['datafine']);
             $iniziofix .= ':00';
            $finefix .= ':59';
            
            
            $ricercadatabaseuno = new DateTime($iniziofix);

            $ricercadb_inizio = $ricercadatabaseuno->format('Y-m-d');
            
            $ricercadatabasedue = new DateTime($finefix);
            
            $ricercadb_fine = $ricercadatabasedue -> format ('Y-m-d');
            
            //AREA DATA
            
            
            
            //QUI CI SONO GLI ARRAY DA PORTARE ALLA FINE
            $arraycommercialinomecognome = [];
            $arrayleadassegnati = [];
            $arrayleadnegativi = [];
            $arrayleadaperti = [];
            $arrayiscritti = [];
            //$arrayfatturato = [];
            $arrayfatturatovero = [];
            //$arrayiscrittiveri = [];
            //QUI CI SONO GLI ARRAY DA PORTARE ALLA FINE
            
            
            
            
            
             $result = mysqli_query($mysqli, "SELECT * FROM users WHERE role_id = 4 OR role_id =1 OR role_id=2");
            
            while($res = mysqli_fetch_array($result)) {
                
                
            $commercialeriferimento = $res['id'];
                
            //QUI CI SONO LE VARIABILI PER L'ELABORAZIONE
            $conteggioleadassegnati = 0; // OK
            $conteggioleadnegativi = 0; // OK
            $conteggioleadaperti = 0; // OK
            $conteggioleadiscritti = 0;  // OK
            //$conteggiofatturato = 0; // OK
            //QUI CI SONO LE VARIABILI PER L'ELABORAZIONE
            
            
            
            
            
            $result2 = mysqli_query($mysqli, "SELECT * FROM leads WHERE owner_id = '$commercialeriferimento' AND date_modified BETWEEN '$iniziofix' AND '$finefix' ORDER BY date_modified DESC");
            
            
            
            while($res2 = mysqli_fetch_array($result2)){
                
                $conteggioleadassegnati = $conteggioleadassegnati + 1;
                // ------------
                
                if ($res2['leadnegativo'] == 'lead negativo'){
                    //AGGIUNGI UN LEAD NEGATIVO
                    $conteggioleadnegativi = $conteggioleadnegativi + 1;
                } else if (controllaseiscritto($res2['id'],$commercialeriferimento,$ricercadb_inizio,$ricercadb_fine)){
                    //AGGIUNGI UN ISCRITTO
                    $conteggioleadiscritti = $conteggioleadiscritti + 1;
                    //esegui funzione per contare i totali dei lead in questione
                    //$conteggiofatturato = $conteggiofatturato + cercaprodottiassociati($res2['id'],$commercialeriferimento,$ricercadb_inizio,$ricercadb_fine);
                    //esegui funzione per contare i totali dei lead in questione
                    
                } else {
                    $conteggioleadaperti = $conteggioleadaperti + 1;
                }
                
            }
            
            
           //CONTROLLA DIRETTAMENTE IL DATABASE CON I PRODOTTI (arrayfatturatovero)
           
           $result3 = mysqli_query($mysqli, "SELECT * FROM prodotti_lead WHERE id_operatore = $commercialeriferimento AND data BETWEEN '$ricercadb_inizio' AND '$ricercadb_fine' ORDER BY data DESC");
           
           $giapresente = [];
           $trovati = 0;
           $sommavera = 0;
           
           while($res3 = mysqli_fetch_array($result3)) {
        $prezzovero = $res3['prezzo'] - $res3['detrazione'];
        $sommavera = $sommavera + $prezzovero;
        
        $conteggioarrayvero = count($giapresente);
        $checkvero = true;
        for ($x = 0; $x < $conteggioarrayvero; $x++) {
            if ($giapresente[$x] ==$res3['lead_riferimento']){
                $checkvero = false;
                break;
            }
        }
        
        if ($checkvero){
            array_push($giapresente, $res3['lead_riferimento']);
            $trovati = $trovati + 1;
        } 
        
           }
           
           
           //CONTROLLA DIRETTAMENTE IL DATABASE CON I PRODOTTI
           
           
           
           
           
           //FAI QUADRARE I DATI
           
           
           //conteggioleadiscritti è la variabile in cui gli iscritti vengono conteggiati nella vecchia maniera
           
           //trovati è la variabile in cui gli iscritti vengono conteggiati con precisione secondo il database di riferimento
           
           if ($trovati > $conteggioleadiscritti){
                $comodo = $trovati - $conteggioleadiscritti;
                $conteggioleadiscritti = $conteggioleadiscritti + $comodo;
                $conteggioleadassegnati = $conteggioleadassegnati + $comodo;
                //poi aggiungere a totali
           }
           
           
           //FAI QUADRARE I DATI
             
             
             
            
            
            //INSERISCI LE ELABORAZIONI NEI RISPETTIVI ARRAY
            
            array_push($arraycommercialinomecognome, $res['first_name'].' '.$res['last_name']);
            
            array_push($arrayleadassegnati,$conteggioleadassegnati);
            
            array_push($arrayleadaperti, $conteggioleadaperti);
            
            array_push($arrayleadnegativi, $conteggioleadnegativi);
            
            array_push($arrayiscritti, $conteggioleadiscritti);
            
            //array_push($arrayfatturato,$conteggiofatturato);
            
            // DATI NUOVI
            
            array_push($arrayfatturatovero, $sommavera);
            
            //array_push($arrayiscrittiveri, $trovati);
                
            }
            
        }
        
        ?>
        
        
<!-- CREAZIONE DELLA TABELLA IN HTML -->



<?php

//count($arraycommercialinomecognome)

$verificacondizioni = 0;
$verificacondizioni = count((array)$arraycommercialinomecognome);




if ($verificacondizioni > 0){
    
    ?>
    
    <div style="text-align:center !important; background-color:blue !important ;color:white !important;margin-bottom:15px;"><strong>Date selezionate:</strong> DA: <?php echo $ricercadb_inizio ?> A: <?php echo $ricercadb_fine ?></div>
    <div style='overflow-x:auto;margin-bottom:100px;'>
    <table class="zui-table">
        <thead>
        <tr>
            <th>Commerciale</th>
            <th>Lead Assegnati</th>
            <th>Lead Negativi</th>
            <th>Lead Aperti</th>
            <th>Lead Iscritti</th>
            <th>Fatturato</th>
        </tr>
        </thead>
        <tbody>
        
        <?php 
        
            $totaleleadassegnati = 0;
            $totaleleadnegativi = 0;
            $totaleleadaperti = 0;
            $totaleiscritti = 0;
            $totalefatturato = 0;
        
        for ($x = 0; $x <= $verificacondizioni; $x++) {
            
            if (!empty($arraycommercialinomecognome[$x])){
            echo '<tr>';
            echo '<th style="text-align:left !important;">'.$arraycommercialinomecognome[$x].'</th>';
            echo '<th>'.$arrayleadassegnati[$x].'</th>';
            echo '<th>'.$arrayleadnegativi[$x].'</th>';
            echo '<th>'.$arrayleadaperti[$x].'</th>';
            echo '<th>'.$arrayiscritti[$x].' ('.calcolopercentuale ($arrayleadassegnati[$x], $arrayiscritti[$x]).'%)</th>';
            echo '<th>'.$arrayfatturatovero[$x].'</th>';
            echo '</tr>';
            
            $totaleleadassegnati = $totaleleadassegnati + $arrayleadassegnati[$x];
            $totaleleadnegativi = $totaleleadnegativi + $arrayleadnegativi[$x];
            $totaleleadaperti = $totaleleadaperti + $arrayleadaperti[$x];
            $totaleiscritti = $totaleiscritti + $arrayiscritti[$x];
            $totalefatturato = $totalefatturato + $arrayfatturatovero[$x];
            }
        }
        
        echo '<tr>';
        echo '<th style="color:red;">TOTALE</th>';
         echo '<th style="color:red;">'.$totaleleadassegnati.'</th>';
         echo '<th style="color:red;">'.$totaleleadnegativi.'</th>';
         echo '<th style="color:red;">'.$totaleleadaperti.'</th>';
         echo '<th style="color:red;">'.$totaleiscritti.'</th>';
         echo '<th style="color:red;">'.$totalefatturato.'</th>';
         echo '</tr>';
        ?>
       </tbody> 
    </table>
    </div>
    </div>
    
    <?php
}


?>


<!-- CREAZIONE DELLA TABELLA IN HTML -->
   <p style="position: fixed;bottom: 0px;"><b>Versione 3.1 REL</b> - Nota sui dati: questo sistema permette di verificare i lead associati ad ogni commerciale. Per una verifica dei lead in arrivo, <a href="http://gestionale.360forma.com/s/dashboard">visitare la homepage di Mautic.</a></p>
    </body>
    
</html>

        <?php
} else {
//condizione se cookie non trovato
echo 'Autorizzazione negata, accedi a Mautic.';
}
?>