<?php



function cercaprodottiassociati ($leadriferimento,$commercialeriferimento,$datainizio,$datafine){
    require ("config.php");
    $result = mysqli_query($mysqli, "SELECT * FROM prodotti_lead WHERE id_operatore = '$commercialeriferimento' AND lead_riferimento = '$leadriferimento' AND data BETWEEN '$datainizio' AND '$datafine' ORDER BY data DESC");
    
    $almenouno = false;
    $somma = 0;
    
     while($res = mysqli_fetch_array($result)) {
         
         if (!empty($res['prezzo'])){
             $almenouno = true;
             
             $operazione = $res['prezzo'] - $res['detrazione'];
             
             $somma = $somma + $operazione;
             
         }
    
     }
    

    
    return $somma;
    
}


function controllaseiscritto ($leadriferimento,$commercialeriferimento,$datainizio,$datafine){
    require ("config.php");
    $result = mysqli_query($mysqli, "SELECT * FROM prodotti_lead WHERE id_operatore = '$commercialeriferimento' AND lead_riferimento = '$leadriferimento' AND data BETWEEN '$datainizio' AND '$datafine' ORDER BY data DESC");
    
    $almenouno = false;
   
    
     while($res = mysqli_fetch_array($result)) {
         
         if (!empty($res['prezzo'])){
             $almenouno = true;
             
         }
    
     }
    
    
   return $almenouno;
    
}




function xmlpertutti ($data,$fine){
    require("config.php");
    
     $nomicognomi = [];
    $prodotti = [];
    $prezzi = [];
    
    $result = mysqli_query($mysqli, "SELECT * FROM prodotti_lead WHERE data BETWEEN '$data' AND '$fine' ORDER BY data DESC");
    
    while($res = mysqli_fetch_array($result)) {
    $prodotto = $res['prodotto'];
    $prezzo = $res['prezzo'] - $res['detrazione'];
    
    // INTERROGAZIONE SECONDO DATABASE
$result2 = mysqli_query($mysqli, "SELECT * FROM leads WHERE id = $leadriferimento");

while ($res2 = mysqli_fetch_array($result2)){
    $nomecognome = $res2['firstname']. ' '.$res2['lastname'];
}

// INTERROGAZIONE SECONDO DATABASE


array_push($nomicognomi, $nomecognome);
           array_push ($prodotti, $prodotto);
           array_push ($prezzi, '€'.$prezzo);


    }
    
    //REALIZZAZIONE XML
    
       
    $xml = new DOMDocument("1.0");
    $xml->formatOutput=true;
    
    $dataset=$xml->createElement("dataset");
    $xml->appendChild($dataset);
    
    $nelementi = count($nomicognomi);
    
    
    for ($x = 0; $x <=$nelementi; $x++){
        
        
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
    
    return $xml;
    
    
}


?>