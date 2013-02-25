<?php
namespace Twarch\Module;

class Stats extends \Twarch\Module {
  public function exec($args){

    $findTweets = $this->db->prepare('
      SELECT id, created, text FROM tweets
    ');

    $findTweets->execute();

    $tweetCount = 0;
    $charCount = 0;
    $wordCount = 0;
    while ($tweet = $findTweets->fetch(\PDO::FETCH_OBJ)){
      $words = explode(' ', $tweet->text);
      $wordCount += sizeOf($words);
      $charCount += strlen($tweet->text);
      $tweetCount++;
    }
    $wordsPerTweet = round($wordCount / $tweetCount);
    $charsPerTweet = round($charCount / $tweetCount);
    $tweetEfficiency = round(100 / 140 * $charsPerTweet, 2);

    $this->setResult(new \Twarch\Result\Text(
        "Tweets: {$tweetCount}".PHP_EOL.
        "Total words: {$wordCount}".PHP_EOL.
        "Words per Tweet: {$wordsPerTweet}".PHP_EOL.
        "Total characters: {$charCount}".PHP_EOL.
        "Characters per Tweet: {$charsPerTweet}".PHP_EOL.
        "Tweet Efficiency: {$tweetEfficiency}%".PHP_EOL
    ));

    return true;
  }
}
