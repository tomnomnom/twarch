<?php
require __DIR__.'/Twarch/Init.php';
$f = new \Twarch\Factory();

$wordsStorage = $f->wordsStorage();
$words = $wordsStorage->get();

//header('Content-Type: application/json');
//echo json_encode($words->getAll());
foreach ($words->getAll() as $word => $count){
  echo "{$word} - {$count}\n";
}
