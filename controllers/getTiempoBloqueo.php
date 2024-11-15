<?php
require_once '../data/Misc.php';

$misc = new Misc();
$tiempo = $misc->getMiscTiempo();

echo json_encode(['precio' => $tiempo]);
