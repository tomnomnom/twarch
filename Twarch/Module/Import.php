<?php
namespace Twarch\Module;

class Import extends \Twarch\Module {
  public function exec($args){

    $tweetFiles = $args->getResidualArgs(1);

    if (sizeOf($tweetFiles) == 0){
      $this->setFailureReason("Please specify at least one .js file containing Tweets"); 
      return false;
    }

    // Clean up before import
    $this->progress("Removing old Tweets...");
    $this->db->query('DELETE FROM tweets');

    $count = 0;
    foreach ($tweetFiles as $tweetFile){

      $tweetFileContents = file_get_contents($tweetFile);

      // Remove assignment from the top of the file to make it valid JSON
      $tweetFileContents = preg_replace('/^[^\.]+.data.tweets_\d{4}_\d{2} =/', '', $tweetFileContents, 1);

      $tweets = json_decode($tweetFileContents);

      $this->progress("Importing Tweets from [{$tweetFile}]");

      $error = json_last_error();
      if (json_last_error() != JSON_ERROR_NONE || !is_array($tweets)){
        $this->progress("Failed to parse JSON from [{$tweetFile}]; skipping");
      }

      $addTweet = $this->db->prepare('
        INSERT INTO tweets(id, created, text) values(:id, :created, :text)
      ');

      foreach ($tweets as $t){
        $addTweet->execute(array(
          'id'      => $t->id,
          'created' => strToTime($t->created_at),
          'text'    => str_replace("\n", "", html_entity_decode($t->text))
        ));
        $count++;
      }
    }

    $this->setResult(new \Twarch\Result\Text(
      "Imported {$count} Tweets"
    ));

    return true; 
  }
}
