<?php
namespace Twarch;

abstract class MapStorage {
  protected $db = null;
  protected $tableName = '';
  protected $keyFieldName = 'key';
  protected $valueFieldName = 'value';
  protected $mapClass = '';
  protected $orderClause = '';

  public function __construct(\PDO $db){
    $this->db = $db; 
  }

  public function save(Map $map){
    $insert = $this->db->prepare("
      INSERT OR IGNORE INTO
      {$this->tableName}({$this->keyFieldName}, {$this->valueFieldName}) 
      values(:key, :value)
    ");
    $update = $this->db->prepare("
      UPDATE {$this->tableName} set {$this->valueFieldName} = :value 
      WHERE {$this->keyFieldName} = :key
    ");

    foreach ($map->getAll() as $key => $value){
      $insert->execute([
        'key'   => $key,
        'value' => $value
      ]);
      $update->execute([
        'key'   => $key,
        'value' => $value
      ]);
    }
  }

  public function get(){
    $getMap = $this->db->prepare("
      SELECT {$this->keyFieldName}, {$this->valueFieldName}
      FROM {$this->tableName}
      {$this->orderClause}
    ");
    $getMap->execute();

    $map = [];
    while ($item = $getMap->fetch(\PDO::FETCH_ASSOC)){
      $map[$item[$this->keyFieldName]] = $item[$this->valueFieldName]; 
    }
    return new $this->mapClass($map); 
  }

  public function truncate(){
    $this->db->query("DELETE FROM {$this->tableName}");
  }
}
