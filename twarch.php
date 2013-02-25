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

// Global args
$args->expectFlag('--output-tsv');

$twarch = new \Twarch\Factory();

// Exit codes
const EXIT_SUCCESS        = 0;
const EXIT_INVALID_MODE   = 1;
const EXIT_MODULE_FAILURE = 2;
const EXIT_UNKNOWN_RESULT = 3;
const EXIT_DB_ERROR       = 4;

// Module defs
$modules = array(
  'help'        => '\\Twarch\\Module\\Help',
  'createdb'    => '\\Twarch\\Module\\CreateDB',
  'import'      => '\\Twarch\\Module\\Import',
  'find'        => '\\Twarch\\Module\\Find',
  'all'         => '\\Twarch\\Module\\All',
  'uniquewords' => '\\Twarch\\Module\\UniqueWords',
  'stats'       => '\\Twarch\\Module\\Stats',
  'sync'        => '\\Twarch\\Module\\Sync',
  'mentions'    => '\\Twarch\\Module\\Mentions',
  'timeofday'   => '\\Twarch\\Module\\TimeOfDay',
  'dayofweek'   => '\\Twarch\\Module\\DayOfWeek',
  'dayofmonth'  => '\\Twarch\\Module\\DayOfMonth',
  'monthofyear' => '\\Twarch\\Module\\MonthOfYear',
  'trend'       => '\\Twarch\\Module\\Trend',
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

try {
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
      if ($args->flagIsSet('--output-tsv')){
        $table = $phargs->tsv();
      } else {
        $table = $phargs->table();
      }
      $table->setFields($result->getFields());
      $table->addRows($result->getRows());
      $screen->out($table);

      break;

    default:
      $screen->errln("Unknown result type"); 
      exit(EXIT_UNKNOWN_RESULT);
      break;
  }
} catch(\PDOException $e){
  $screen->errln(
    "A database error occured: Code ".$e->getCode()." - ".$e->getMessage()
  ); 
  exit(EXIT_DB_ERROR);
}

exit(EXIT_SUCCESS);
