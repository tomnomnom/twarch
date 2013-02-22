<?php
namespace Twarch\Module;

class WordCount extends \Twarch\Module {
  public function exec($args){

    $findTweets = $this->db->prepare('
      SELECT id, created, text FROM tweets
    ');

    $findTweets->execute();

    $charCount = 0;
    $wordCount = 0;
    while ($tweet = $findTweets->fetch(\PDO::FETCH_OBJ)){
      $words = explode(' ', $tweet->text);
      $wordCount += sizeOf($words);
      $charCount += strlen($tweet->text);
    }

    $this->setResult(new \Twarch\Result\Text(
        "Words: {$wordCount}".PHP_EOL.
        "Characters: {$charCount}"
    ));

    return true;
  }
}
