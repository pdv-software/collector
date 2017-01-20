<?php

  $domain = "messages";
  bindtextdomain($domain, "Modules/collector/locale");
  bind_textdomain_codeset($domain, 'UTF-8');
  
  $menu_dropdown_config[] = array('name'=> dgettext($domain, "Collector Setup"), 'icon'=>'icon-briefcase', 'path'=>"collector/view" , 'session'=>"write", 'order' => 45 );