<?php
namespace Twarch;

class StatsStorage {
  protected $db;

  public function __construct(\PDO $db){
    $this->db = $db; 
  }

  public function save(Stats $stats){
    $insert = $this->db->prepare('INSERT OR IGNORE INTO stats(key, value) values(:key, :value)');
    $update = $this->db->prepare('UPDATE stats set value = :value WHERE key = :key');

    foreach ($stats->getAll() as $key => $value){
      // Insert silently fails if the stat already exists
      $insert->execute([
        'key'   => $key,
        'value' => $value
      ]);
      $update->execute([
        'key'   => $key,
        'value' => $value
      ]);
      echo $value.PHP_EOL;
    }
  }

  public function get(){
    $getStats = $this->db->prepare('SELECT key, value FROM stats');
    $getStats->execute();

    $stats = [];
    while ($stat = $getStats->fetch(\PDO::FETCH_OBJ)){
      $stats[$stat->key] = $stat->value; 
    }
    return new Stats($stats); 
  }
}
