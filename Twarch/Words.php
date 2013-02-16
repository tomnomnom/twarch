<?php
namespace Twarch;

class Words extends Map {
  public function seenWord($word){
    if (!isSet($this->map[$word])){
      $this->map[$word] = 0;
    }
    $this->map[$word]++;
  }

  public function seenWords(Array $words){
    foreach ($words as $word){
      $this->seenWord($word);
    }
  }
}
