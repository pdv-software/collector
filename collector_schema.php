<?php

$schema['collector'] = array(
  'id' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
  'userid' => array('type' => 'int(11)'),
  'name' => array('type' => 'varchar(32)'),
  'active' => array('type' => 'tinyint(1)', 'default'=>'0'),
  'type' => array('type' => 'varchar(50)'),
  'interval' => array('type' => 'int(11)', 'default'=>'0'),
  'description' => array('type' => 'varchar(200)'),
  'properties' => array('type' => 'text', 'default' => ''),
  'public' => array('type' => 'tinyint(1)', 'default'=>'0')
);
