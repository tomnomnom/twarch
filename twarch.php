<?php
//////////////////////////////
// Twarch main 'executable' //
//////////////////////////////

// Bootstrap 
date_default_timezone_set('UTC');

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/Twarch/Init.php';

$phargs = new \Phargs\Factory();
$screen = $phargs->screen();
$args = $phargs->args();

$twarch = new \Twarch\Factory();

// Exit codes
const EXIT_SUCCESS        = 0;
const EXIT_INVALID_MODE   = 1;
const EXIT_MODULE_FAILURE = 2;
const EXIT_UNKNOWN_RESULT = 3;

// Module defs
$modules = array(
  'help'        => '\\Twarch\\Module\\Help',
  'createdb'    => '\\Twarch\\Module\\CreateDB',
  'import'      => '\\Twarch\\Module\\Import',
  'find'        => '\\Twarch\\Module\\Find',
  'uniquewords' => '\\Twarch\\Module\\UniqueWords',
  'wordcount'   => '\\Twarch\\Module\\WordCount',
);

// Meat
$moduleName = strToLower($args->getResidualArg(0));
if (!$moduleName){
  $moduleName = 'help';
}

if (!isset($modules[$moduleName])){
  $screen->errln("Invalid mode: {$moduleName}");
  exit(EXIT_INVALID_MODE);
}

$moduleClass = $modules[$moduleName];
$module = new $moduleClass($twarch->db(), $screen);

$worked = $module->exec($phargs->args());
if (!$worked){
  $screen->errln($module->getFailureReason());
  exit(EXIT_MODULE_FAILURE);
}

$result = $module->getResult();

switch (get_class($result)){
  case 'Twarch\\Result\\Text':
    $screen->outln($result->get());
    break;

  case 'Twarch\\Result\\Table':
    $table = $phargs->table();
    $table->setFields($result->getFields());
    $table->addRows($result->getRows());
    $screen->out($table);
    break;

  default:
    $screen->errln("Unknown result type"); 
    exit(EXIT_UNKNOWN_RESULT);
    break;
}

exit(EXIT_SUCCESS);
