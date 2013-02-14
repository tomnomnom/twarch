<?php
// The following is a hacked POC

date_default_timezone_set('UTC');
ini_set('display_errors', 'on');
error_reporting(-1);

$tweetFilePattern = __DIR__."/tweets/data/js/tweets/*.js";

// Clean up before insert
$db = new \PDO('sqlite:'.__DIR__.'/tweets.sq3');
$db->query('DELETE FROM tweets');

$i = new \GlobIterator($tweetFilePattern);
foreach ($i as $tweetFile){
  insertTweets($db, $tweetFile->getPathname());
}


/* Lib */
function insertTweets($db, $tweetFile){
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
    echo "Inserting tweet [{$t->id}] [{$t->text}]\n";
    $addTweet->execute(array(
      'id'      => $t->id,
      'created' => strToTime($t->created_at),
      'text'    => $t->text
    ));
  }
}
