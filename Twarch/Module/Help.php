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
      "  find <searchTerm> - Find Tweets containing <searchTerm>",
      "  uniquewords       - Find unique words used",
      "    [--min-count=N]       - Minimum word count to be returned",
      "    [--min-word-length=N] - Minimum word length to be returned",
      "  wordcount         - Total word count",
    );
    $helpText = implode(PHP_EOL, $helpText);

    $this->setResult(
      new \Twarch\Result\Text($helpText)
    );
    return true;
  }
}
