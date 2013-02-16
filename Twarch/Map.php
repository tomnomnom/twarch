<?php
namespace Twarch;

abstract class Map {
  protected $map = [];

  public function __construct(Array $map = []){
    $this->map = $map;
  }

  public function getAll(){
    return $this->map;
  }
}
