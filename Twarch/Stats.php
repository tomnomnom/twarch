<?php
namespace Twarch;

class Stats {
  protected $stats = [
    'wordCount' => 0
  ];

  public function __construct(Array $stats = []){
    foreach ($this->stats as $key => $value){
      if (!isSet($stats[$key])) continue;
      $this->stats[$key] = $stats[$key];
    }
  }

  public function addToWordCount($count = 1){
    $this->stats['wordCount'] += (int) $count;
  }

  public function getWordCount(){
    return (int) $this->stats['wordCount'];
  }

  public function getAll(){
    return $this->stats;
  }
}
