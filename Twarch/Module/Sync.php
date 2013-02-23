<?php
namespace Twarch\Module;

class Sync extends \Twarch\Module {
  public function exec($args){

    $username = $args->getResidualArg(1);

    if (!$username){
      $this->setFailureReason("Please specify a username");
      return false;
    }

    $params = array(
      "screen_name" => $username,
      "include_rts" => "true"
    );
    
    // Get the ID of the last tweet
    $lastTweet = $this->db->prepare('
      SELECT id, created FROM tweets ORDER BY created DESC LIMIT 1
    ');
    $lastTweet->execute();
    $tweet = $lastTweet->fetch(\PDO::FETCH_OBJ);

    $this->progress("Last Tweet had ID [{$tweet->id}]");
    $params['since_id'] = $tweet->id;
    
    // Request tweets since the last one
    $q = http_build_query($params);
    $url = "http://api.twitter.com/1/statuses/user_timeline.json?{$q}";
    $response = file_get_contents($url);

    if (!$response){
      $this->setFailureReason("Failed to fetch Tweets for [{$username}]");
      return false;
    }
    
    $tweets = json_decode($response);
    
    if (sizeOf($tweets) == 0){
      $this->setResult(new \Twarch\Result\Text(
        "No Tweets for [{$username} since [$tweet->id]]"
      ));
      return true;
    }
    
    $addTweet = $this->db->prepare('
      INSERT INTO tweets(id, created, text) values(:id, :created, :text)
    ');

    $count = 0;
    foreach($tweets as $tweet){

      $this->progress("Importing Tweet with ID [{$tweet->id}]");
      $addTweet->execute(array(
        'id'      => $tweet->id,
        'created' => strToTime($tweet->created_at),
        'text'    => str_replace("\n", "", html_entity_decode($tweet->text))
      ));
      $count++;
      
    }

    $this->setResult(new \Twarch\Result\Text(
      "Imported {$count} Tweets"
    ));

    return true; 
  }
}
