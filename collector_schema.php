<?php

$schema['collector'] = array(
  'id' => array('type' => 'int(11)', 'Null'=>'NO', 'Key'=>'PRI', 'Extra'=>'auto_increment'),
  'name' => array('type' => 'varchar(32)'),
  'type' => array('type' => 'varchar(32)'),
  'properties' => array('type' => 'text', 'default' => '')
);
