<?php
namespace Twarch;

class WordsStorage extends MapStorage {
  protected $tableName      = 'words';
  protected $keyFieldName   = 'word';
  protected $valueFieldName = 'count';
  protected $mapClass       = '\\Twarch\\Words';
  protected $orderClause    = 'ORDER BY count DESC';
}
