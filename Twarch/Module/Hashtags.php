<?php
namespace Twarch\Module;

class Hashtags extends \Twarch\Module {
  public function exec($args){
    $findTweets = $this->db->prepare('
      SELECT id, created, text FROM tweets
    ');

    $findTweets->execute();

    $mentionCounts = array();

    while ($tweet = $findTweets->fetch(\PDO::FETCH_OBJ)){
      $words = explode(' ', $tweet->text);
      foreach ($words as $word){
        // Remove most punctuation
        $word = trim($word, ' "\'`*:;.()[]{},!?');
        if (!$word) continue;

        if (strlen($word) < 2) continue;

        // Hashtags only
        if ($word[0] != '#') continue;

        if (!isset($mentionCounts[$word])){
          $mentionCounts[$word] = 0;
        }
        $mentionCounts[$word]++;
      }
    }
    arsort($mentionCounts);

    $fields = array('Hashtag', 'Count');

    $rows = array();
    foreach ($mentionCounts as $word => $count){
      $rows[] = array($word, $count);
    }

    $this->setResult(
      new \Twarch\Result\Table($fields, $rows)
    );

    return true;
  }
}
