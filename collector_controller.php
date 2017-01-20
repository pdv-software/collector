<?php

  defined('EMONCMS_EXEC') or die('Restricted access');
  
  function collector_controller()
  {
	  global $session,$route,$mysqli,$user,$redis;

      $result = false;
	  
	  require_once "Modules/controller/collector_model.php";
	  
	  
	  if ($route->format == 'html')
    {
        if ($route->action == "view" && $session['write']) {
            $collector_templates = $collector->get_templates();
            $result = view("Modules/collector/Views/collector_view.php",array('collector_templates'=>$collector_templates));
        }
        //if ($route->action == 'api') $result = view("Modules/device/Views/device_api.php", array());
    }
	  
	return array("content" => $result);
  }

?>