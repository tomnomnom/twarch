<?php
namespace Twarch;

date_default_timezone_set('UTC');
ini_set('display_errors', 'on');
error_reporting(-1);

spl_autoload_register(function($class){
  $class = ltrim($class, '\\');
  $class = str_replace('\\', '/', $class);
  require __DIR__."/../{$class}.php";
});
