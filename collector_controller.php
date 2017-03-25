<?php

  defined('EMONCMS_EXEC') or die('Restricted access');
  
  function collector_controller()
  {
	  global $session,$route,$mysqli,$user,$redis;

      $result = false;
	  
	  require_once "Modules/collector/collector_model.php";
	  $collector = new Collector($mysqli,$redis);
	  
	if ($route->format == 'html')
    {
        if ($route->action == "view" && $session['write']) {
            $collector_templates = $collector->get_templates();
            $result = view("Modules/collector/Views/collector_view.php",array('collector_templates'=>$collector_templates));
        }
        if ($route->action == 'api') $result = view("Modules/collector/Views/collector_api.php", array());
    }
	  
	if ($route->format == 'json')
    {
        if ($route->action == 'list') {
            if ($session['userid']>0 && $session['write']) $result = $collector->get_list($session['userid']);
        }
        elseif ($route->action == "create") {
            if ($session['userid']>0 && $session['write']) $result = $collector->create($session['userid']);
        }
        elseif ($route->action == "listtemplates") {
            if ($session['userid']>0 && $session['write']) $result = $collector->get_templates();
        }
        else {
            $collectorid = (int) get('id');
            if ($collector->exist($collectorid)) // if the feed exists
            {
                $collectorget = $collector->get($collectorid);
                if (isset($session['write']) && $session['write'] && $session['userid'] > 0 && $collectorget['userid'] == $session['userid']) {
                    if ($route->action == "get") $result = $collectorget;
                    if ($route->action == "delete") $result = $collector->delete($collectorid);
                    if ($route->action == 'set') $result = $collector->set_fields($collectorid, get('fields'));
                    if ($route->action == 'properties') $result = $collector->getProperties($collectorid);
                }
            }
            else
            {
                $result = array('success'=>false, 'message'=>'Collector does not exist');
            }
        }     
    }

    return array('content'=>$result);
  }

?>