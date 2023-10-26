<?php

require("config.php");

$id = $_GET['q'];

$result = mysqli_query($mysqli, "DELETE FROM catalogo_prodotti WHERE id=$id");

?>