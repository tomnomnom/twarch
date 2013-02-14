<?php
ini_set('display_errors', 'off');
date_default_timezone_set('UTC');

$db = new \PDO('sqlite:'.__DIR__.'/tweets.sq3');

$searchTerm = $_GET['term'];

$findTweets = $db->prepare('
  SELECT id, created, text FROM tweets WHERE text match :searchTerm
');

$findTweets->execute(array(
  'searchTerm' => $searchTerm
));

$tweets = array();
while ($tweet = $findTweets->fetch(PDO::FETCH_OBJ)){
  $tweets[] = array(
    'created' => date(DATE_ISO8601, $tweet->created),
    'text' => $tweet->text
  );
}

header('Content-Type: application/json');
echo json_encode($tweets);
