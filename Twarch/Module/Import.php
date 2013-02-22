<?php
namespace Twarch\Module;

class Import extends \Twarch\Module {
  public function exec($args){

    $tweetDir = $args->getResidualArg(1);

    if (!$tweetDir){
      $this->setFailureReason("Please specify a directorty containing .js Tweet files"); 
      return false;
    }

    if (!is_dir($tweetDir)){
      $this->setFailureReason("Directory [{$tweetDir}] does not exist");
      return false;
    }

    $tweetFilePattern = "{$tweetDir}/*.js";

    // Clean up before import
    $this->progress("Removing old Tweets...");
    $this->db->query('DELETE FROM tweets');

    $this->progress("Importing new Tweets...");
    $i = new \GlobIterator($tweetFilePattern);
    $count = 0;
    foreach ($i as $tweetFile){

      $tweetFile = $tweetFile->getPathname();
      $tweetFileContents = file_get_contents($tweetFile);

      // Remove assignment from the top of the file to make it valid JSON
      $tweetFileContents = preg_replace('/^[^\.]+.data.tweets_\d{4}_\d{2} =/', '', $tweetFileContents, 1);

      $tweets = json_decode($tweetFileContents);

      $error = json_last_error();
      if (json_last_error() != JSON_ERROR_NONE || !is_array($tweets)){
        $this->setFailureReason("Failed to parse JSON from [{$tweetFile}]");
        return false;
      }

      $addTweet = $this->db->prepare('
        INSERT INTO tweets(id, created, text) values(:id, :created, :text)
      ');

      foreach ($tweets as $t){
        $this->progress("Importing Tweet [{$t->id}: $t->text]");
        $addTweet->execute(array(
          'id'      => $t->id,
          'created' => strToTime($t->created_at),
          'text'    => html_entity_decode($t->text)
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
