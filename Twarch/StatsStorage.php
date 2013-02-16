<?php
namespace Twarch;

class StatsStorage extends MapStorage {
  protected $tableName = 'stats';
  protected $mapClass = '\\Twarch\\Stats';
}
