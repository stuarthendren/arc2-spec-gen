<?php
include_once("arc/ARC2.php");

$config = array(
  /* db */
  'db_name' => 'db_name',
  'db_user' => 'db_user',
  'db_pwd' => 'password',
  /* store */
  'store_name' => 'store_name',
);

$store = ARC2::getStore($config);

if (!$store->isSetUp()) {
  $store->setUp();
}

$store = ARC2::getComponent('ARC2_SpecGenPlugin', $config);

$specloc = "<http://stuarthendren.net/dublincore.owl>";
$template = "dublincore.html";
$prefix = "dc:";
$instances = "False";  // True includes the instances in the specification
$saveFile = "False";  // give/path/and/filename.html to output to file instead of screen
$omits = array();

// This calls the main function and writes the specification
$store->specgen($specloc, $template, $prefix, $instances, $saveFile, $omits);


?>