<?php
//controllo cookie per accesso

if (isset($_COOKIE["AccessoConsentitoMautic"])) {


?>

<?php
//ANALISI COMMERCIALE MAUTIC CON CALENDARIO E DOWNLOAD XML - 2023 DANIEL INTRIERI - TUTTI I DIRITTI RISERVATI
//CONCESSO PER L'UTILIZZO A 360FORMA
//VERSIONE 0.90 rel del 13-09-2023


//carica le risorse esterne per il funzionamento
include 'calendar.php';
include 'funzioni.php';
require("config.php");



//controlla se il tasto submit sul downloader per xml è stato premuto

if (isset($_POST['submit'])){
    if ($_GET['stop'] != 1){
    $generaxml = true;} else if ($_GET['stop'] == 1)  {
        $generaxml = false;
    }
}else {
    $generaxml = false;
}


//verifica presenza di un id utente

if (isset($_GET['idutente'])){
    $idutente = $_GET['idutente'];
}else {
    $idutente = 1;
}

//verifica presenza di mese
$mesepagina = "";
if (isset($_GET['mese'])){
$mesepagina = $_GET['mese'];
}
if (empty($mesepagina)){
$mesepagina = date("m");
$data = date("Y-m").'-1';
$data_anno = date("Y");
$data_mese = date("m");
} else {
$data = date("Y").'-'.$mesepagina.'-'.'1';
$data_mese = $mesepagina;
$data_anno = date("Y");
}

//calcolo per tasto mese successivo
if ($data_mese < 12){
    $datamesesucc = $data_mese + 1;
} else {
    $datamesesucc = 1;
}

//calcolo per tasto mese precedente
if ($data_mese == 1){
    $datameseprec = 12;
} else {
    $datameseprec = $data_mese - 1;
}




$calcolo = cal_days_in_month(CAL_GREGORIAN,$data_mese,$data_anno);
$fine = $data_anno.'-'.$data_mese.'-'.$calcolo;
if (date("m")==$mesepagina){
$calendar = new Calendar(date("Y-m-d"));
}else if (empty($mesepagina)) {
$calendar = new Calendar(date("Y-m-d"));
}else {
$calendar = new Calendar($data);
}



$conteggiolead = 0;
$conteggioeuro = 0;

if ($generaxml){
    $nomicognomi = [];
    $prodotti = [];
    $prezzi = [];
}

$result = mysqli_query($mysqli, "SELECT * FROM prodotti_lead WHERE id_operatore = $idutente AND data BETWEEN '$data' AND '$fine' ORDER BY data DESC");
while($res = mysqli_fetch_array($result)) {
    $prodotto = $res['prodotto'];
    $prezzo = $res['prezzo'] - $res['detrazione'];    
    $leadriferimento = $res['lead_riferimento'];
    $colorepick = aggiustacolore($res['id_operatore']);

// INTERROGAZIONE SECONDO DATABASE
$result2 = mysqli_query($mysqli, "SELECT * FROM leads WHERE id = $leadriferimento");

while ($res2 = mysqli_fetch_array($result2)){
    $nomecognome = $res2['firstname']. ' '.$res2['lastname'];
}
 
// INTERROGAZIONE SECONDO DATABASE
       
       if ($generaxml){
           array_push($nomicognomi, $nomecognome);
           array_push ($prodotti, $prodotto);
           array_push ($prezzi, '€'.$prezzo);
       }
       
    $calendar->add_event($nomecognome,$res['data'],1,$colorepick,$res['lead_riferimento'],$prodotto.' - € '.$prezzo,$res['lead_riferimento']);
    $conteggiolead = $conteggiolead + 1;
    $numero1 = $conteggioeuro;
    $numero2 = $prezzo;
    $sommax = $numero1+$numero2;
    $conteggioeuro = $sommax;

    
}

?>

<?php

if ($generaxml){
    
    $xml = new DOMDocument("1.0");
    $xml->formatOutput=true;
    
    $dataset=$xml->createElement("dataset");
    $xml->appendChild($dataset);
    
    $nelementi = count($nomicognomi);
    
    
    for ($x = 0; $x <$nelementi; $x++){
        
        
        $record=$xml->createElement("record");
        $nomicognomixml=$xml->createElement("nome_cognome",$nomicognomi[$x]);
        $record->appendchild($nomicognomixml);
    
    
        $prodottixml=$xml->createElement("prodotto",$prodotti[$x]);
        $record->appendchild($prodottixml);
        
        $prezzixml=$xml->createElement("prezzo",$prezzi[$x]);
        $record->appendchild($prezzixml);
        $dataset->appendChild($record);
    }
    
    
    
    
$result = mysqli_query($mysqli, "SELECT * FROM users WHERE id = $idutente");



while($res = mysqli_fetch_array($result)) {

$nomecognomefile = $res['first_name'].' '.$res['last_name'];
$meseannofile =  $mesepagina . '-'.date("Y");

}
    
    $xml->save("data/".$nomecognomefile.$meseannofile.".xml");
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>360Forma - Analisi Commerciale</title>
		<link href="stilemodal.css" rel="stylesheet" type="text/css">
		<link href="calendar.css" rel="stylesheet" type="text/css">
		<link href="nuovotooltip.css" rel="stylesheet" type="text/css">
		<link href="sidebar.css" rel="stylesheet" type="text/css">
		<link href="aggiunte.css" rel="stylesheet" type="text/css">
		<link href="favicon-1.ico" rel="icon">
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		
		<script>
		    
		    function downloadFile(url, fileName){
  fetch(url, { method: 'get', mode: 'no-cors', referrerPolicy: 'no-referrer' })
    .then(res => res.blob())
    .then(res => {
      const aElement = document.createElement('a');
      aElement.setAttribute('download', fileName);
      const href = URL.createObjectURL(res);
      aElement.href = href;
      // aElement.setAttribute('href', href);
      aElement.setAttribute('target', '_blank');
      aElement.click();
      URL.revokeObjectURL(href);
    });
};
		    
		</script>
		
		<?php
		
		if ($generaxml){
		    $linkperxml = "data/".$nomecognomefile.$meseannofile.".xml";
		    $nomeperxml = $nomecognomefile.$meseannofile.".xml";
		?>
		
		<script>
		    
		    downloadFile("<?php echo $linkperxml ?>","<?php echo $nomeperxml ?>");
		    
		</script>
		
		<?php
		$generaxml = false;
		}
		?>
		
		
		
	</head>
	<body>
	     
	    <div style="max-height:90%;" class="sidenav">
	        <p class="titolinav">Elenco commerciali</p>
	        
	        <?php 
	        // creazione lista commerciali su lato sinistro pagina
	        
	        $result = mysqli_query($mysqli, "SELECT * FROM users WHERE role_id = 4 OR role_id =1 OR role_id=2");
	        
	        while($res = mysqli_fetch_array($result)) {
	           
	            $colorepick = aggiustacolore($res['id']);
	            echo '<div class="event '.$colorepick.'"><a style="color:white;" href="cal.php?idutente='.$res['id'].'">'.$res['first_name'].' '.$res['last_name'].'</a></div>';
	        
	            
	            }
	        
	        // fine creazione lista commerciali su lato sinistro pagina
	        ?>
	        <p style="outline-style: dotted;outline-color:red;text-align:center;">Aggiornamento automatico tra<br><span style="font-weight:bold;color:red;" id="timer"></span><br><button onclick="aggiorna()">Aggiorna</button></p>
	        <p class="titolinav">Strumenti</p>
	        <?php 
	        if ($mesepagina == date("m")){
	        ?>
	        <a class="azioni" href="#giornocorrente">Vai a giorno corrente</a>
	        <?php
	        } else {
	        ?>
	        <a class="azioni" href="cal.php?idutente=<?php echo $idutente ?>&mese=<?php echo date("m") ?>">Vai al mese corrente</a>
	        <?php } ?>
	        <a class="azioni" href="http://gestionale.360forma.com/s/dashboard">Torna su Mautic</a>
	        <a class="azioni" href="index.php">Torna ad analisi commerciale</a>
	        
	        <form method="post" action="cal.php?idutente=<?php echo $idutente ?>&mese=<?php echo $mesepagina ?>&stop=0">
	            
	            <button class="azioni" style="width:100%" type="submit" name="submit" id="submit">Scarica XML</button>
	            
	        </form>
	        
	        <br>
	        

<br>

<p style="outline-style: dotted;outline-color:red;text-align:center;">
    <b>Statistiche:</b><br><br>
    Guadagno per il mese di <?php echo nomemese($data_mese) ?>:<br><b style="color:red;">€<?php echo $conteggioeuro ?></b>
</p>
</div>
<!--QUI FINISCE LA BARRA DI NAVIGAZIONE -->
<div class="main">
	   
		<div class="content home">
			<?=$calendar?>
		</div>
		<div style="text-align:center;margin-top:20px;margin-bottom:20px;">
		<a href="cal.php?mese=<?php echo $datameseprec ?>&idutente=<?php echo $idutente ?>" class="previous">&laquo; Mese Precedente</a>
	
<?php 
	if ($datamesesucc == 1){
		?>
<a href="cal.php?mese=<?php echo $datamesesucc ?>&idutente=<?php echo $idutente ?>" class="next">Torna a Gennaio &raquo;</a>
			<?php
	} else {
	
	?>
			
<a href="cal.php?mese=<?php echo $datamesesucc ?>&idutente=<?php echo $idutente ?>" class="next">Mese Successivo &raquo;</a>
<?php
			} 
	
	?>
</div>
</div>


<!-- INSERIMENTO MODAL -->

 <div id="id01" class="w3-modal">
    <div class="w3-modal-content">
      <div class="w3-container">
        <span onclick="document.getElementById('id01').style.display='none'" class="w3-button w3-display-topright">&times;</span>
        
        <!-- tab -->
        
        <div class="tab">
  <button class="tablinks active" onclick="openCity(event, 'info')">Informazioni</button>
</div>

<div id="info" class="tabcontent" style="display: block;">
  <h3 id="titololead">Nome Cognome</h3>
  <p id="contenuto">Contenuto.</p>
  <a class="azioni" id="tastolead" href="" target="_blank">Apri Lead</a>
</div>

        <!-- tab -->
        
      </div>
    </div>
  </div>

<!-- FINE INSERIMENTO MODAL -->


<!-- SCRIPT FUNZIONAMENTO MODAL -->

<script>

function riprogramma(){
    var id = document.getElementById('databasenote').innerHTML;
    var data = document.getElementById('datariprogrammazione').value;
    
    var xmlhttp = new XMLHttpRequest();
    
    xmlhttp.open("GET", "riprogramma.php?id=" + id + "&data=" + data, true);
    xmlhttp.send();
    alert("lead riprogrammato per data " + data);
    location.reload();
    
}


function boxazioni(lead,nota,link,dbnota,data) {

document.getElementById('id01').style.display='block';
document.getElementById('titololead').innerHTML = lead;
document.getElementById('contenuto').innerHTML = nota;
document.getElementById('tastolead').href= link;
document.getElementById('databasenote').innerHTML = dbnota;
document.getElementById('datariprogrammazione').value = data;
document.getElementById('datacorrente').innerHTML = 'Data corrente: ' + data;
}

</script>

<script>
function openCity(evt, cityName) {
  var i, tabcontent, tablinks;
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }
  document.getElementById(cityName).style.display = "block";
  evt.currentTarget.className += " active";
}
</script>
<!-- FINE SCRIPT FUNZIONAMENTO MODAL -->
<!-- SCRIPT FUNZIONAMENTO TIMER -->
<script>

document.getElementById('timer').innerHTML =
  01 + ":" + 01;
startTimer();

function startTimer() {
 var controlla = document.getElementById('id01');
                
  var presentTime = document.getElementById('timer').innerHTML;
  var timeArray = presentTime.split(/[:]+/);
  var m = timeArray[0];
  var s = timeArray[1];
  if (controlla.style.display != "block"){
      s = checkSecond((timeArray[1] - 1));
  }
  if(s==59){m=m-1}
  if(m<0){aggiorna();}
  
  document.getElementById('timer').innerHTML =
    m + ":" + s;
  setTimeout(startTimer, 1000);

}

function checkSecond(sec) {
  if (sec < 10 && sec >= 0) {sec = "0" + sec}; // add zero in front of numbers < 10
  if (sec < 0) {sec = "59"};
  return sec;
}

function aggiorna() {
    window.location.replace('cal.php?idutente=<?php echo $idutente ?>&mese=<?php echo $mesepagina ?>&stop=1');
}

</script>
<!-- FINE SCRIPT FUNZIONAMENTO TIMER -->

	</body>
</html>
        <?php
} else {
//condizione se cookie non trovato
echo 'Autorizzazione negata, accedi a Mautic.';
}
?>
