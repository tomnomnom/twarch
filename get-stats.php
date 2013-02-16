<?php
require __DIR__.'/Twarch/Init.php';
$f = new \Twarch\Factory();

$statsStorage = $f->StatsStorage();
$stats = $statsStorage->get();

header('Content-Type: application/json');
echo json_encode($stats->getAll());
