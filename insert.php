<?php
require __DIR__.'/Twarch/Init.php';
$f = new \Twarch\Factory();

$tweetFilePattern = __DIR__."/tweets/data/js/tweets/*.js";

// Clean up before insert
$db = $f->db();
$db->query('DELETE FROM tweets');

$statsStorage = $f->StatsStorage();
$statsStorage->truncate();
$stats = $statsStorage->get();

$wordsStorage = $f->wordsStorage();
$wordsStorage->truncate();
$words = $wordsStorage->get();


$i = new \GlobIterator($tweetFilePattern);
foreach ($i as $tweetFile){

  $tweetFile = $tweetFile->getPathname();
  $tweetFileContents = file_get_contents($tweetFile);

  // Remove assignment from the top of the file to make it valid JSON
  $tweetFileContents = preg_replace('/^[^\.]+.data.tweets_\d{4}_\d{2} =/', '', $tweetFileContents, 1);

  $tweets = json_decode($tweetFileContents);

  $error = json_last_error();
  if (json_last_error() != JSON_ERROR_NONE || !is_array($tweets)){
    echo "Failed to parse JSON from [{$tweetFile}]\n";
    return false;
  }

  $addTweet = $db->prepare('
    INSERT INTO tweets(id, created, text) values(:id, :created, :text)
  ');

  foreach ($tweets as $t){
    $wordsInTweet = explode(' ', $t->text);
    $wordsInTweet = array_map('strToLower', $wordsInTweet);
    $words->seenWords($wordsInTweet);
    $stats->addToWordCount(sizeOf($wordsInTweet));
    echo "Inserting tweet [{$t->id}] [{$t->text}]\n";
    $addTweet->execute(array(
      'id'      => $t->id,
      'created' => strToTime($t->created_at),
      'text'    => $t->text
    ));
  }
}

$statsStorage->save($stats);
$wordsStorage->save($words);
