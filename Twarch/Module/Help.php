<?php
namespace Twarch\Module;

class Help extends \Twarch\Module {
  public function exec($args){
    $helpText = 
      "Twarch usage:".PHP_EOL.
      "  twarch <mode> [args]";

    $this->setResult(
      new \Twarch\Result\Text($helpText)
    );
    return true;
  }
}
