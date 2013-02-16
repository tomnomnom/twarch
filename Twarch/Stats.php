<?php
namespace Twarch;

class Stats extends Map {
  protected $map = [
    'wordCount' => 0
  ];

  public function __construct(Array $map = []){
    // Only allow expected keys
    foreach ($this->map as $key => $value){
      if (!isSet($map[$key])) continue;
      $this->map[$key] = $map[$key];
    }
  }

  public function addToWordCount($count = 1){
    $this->map['wordCount'] += (int) $count;
  }

  public function getWordCount(){
    return (int) $this->map['wordCount'];
  }
}
