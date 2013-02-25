<?php
namespace Twarch\Module;

class UniqueWords extends \Twarch\Module {
  public function exec($args){
    $args->expectParam('--min-count');
    $args->expectParam('--min-word-length');
    $args->expectFlag('--exclude-mentions');
    
    $minCount = 1;
    if ($args->paramIsSet('--min-count')){
      $minCount = (int) $args->getParamValue('--min-count'); 
    }

    $minWordLength = null;
    if ($args->paramIsSet('--min-word-length')){
      $minWordLength = (int) $args->getParamValue('--min-word-length');
    }

    $excludeMentions = false;
    if ($args->flagIsSet('--exclude-mentions')){
      $excludeMentions = true; 
    }
  
    $findTweets = $this->db->prepare('
      SELECT id, created, text FROM tweets
    ');

    $findTweets->execute();

    $wordCounts = array();

    while ($tweet = $findTweets->fetch(\PDO::FETCH_OBJ)){
      $words = explode(' ', $tweet->text);
      foreach ($words as $word){
        // Remove most punctuation
        $word = trim($word, ' "\'`*:;.()[]{},!?');
        if (!$word) continue;

        // Check for minimum word length
        if (!is_null($minWordLength)){
          if (strLen($word) < $minWordLength) continue;
        }

        // Optionally exclude mentions
        if ($excludeMentions){
          if ($word[0] == '@') continue;
        }

        if (!isset($wordCounts[$word])){
          $wordCounts[$word] = 0;
        }
        $wordCounts[$word]++;
      }
    }
    arsort($wordCounts);

    $fields = array('Word', 'Count');

    $rows = array();
    foreach ($wordCounts as $word => $count){
      if ($count < $minCount) break;
      $rows[] = array($word, $count);
    }

    $this->setResult(
      new \Twarch\Result\Table($fields, $rows)
    );

    return true;
  }
}
