<?php
namespace Twarch\Module;

class Help extends \Twarch\Module {
  public function exec($args){
    $helpText = array(
      "Twarch usage:",
      "  twarch <mode> [args]",
      "",
      "Modes:",
      "  help              - Display this help text",
      "  createdb          - Create an empty Tweets DB",
      "  import <tweetDir> - Import Tweets from .js files in <tweetDir>",
      "  find <searchTerm> - Find Tweets containing <searchTerm>"
    );
    $helpText = implode(PHP_EOL, $helpText);

    $this->setResult(
      new \Twarch\Result\Text($helpText)
    );
    return true;
  }
}
