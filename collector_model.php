<?php

  // no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

class Collector
{
	
	private $mysqli;
    private $redis;
    private $log;
	
	public function __construct($mysqli,$redis)
    {
        $this->mysqli = $mysqli;
        $this->redis = $redis;
        $this->log = new EmonLogger(__FILE__);
    }
	
	public function get_templates()
    {
        $collectors = array();
        $collectors = $this->load_collectors_template();
        return $collectors;
    }

    private function load_collectors_template() {
        $list = array();
        foreach (glob("Modules/collector/data/*.json") as $file) {
            $content = json_decode(file_get_contents($file));
            $list[basename($file, ".json")] = $content;
        }
        return $list;
    }
	
	public function exist($id)
    {
        $id = intval($id);
        static $collector_exists_cache = array(); // Array to hold the cache
        if (isset($collector_exists_cache[$id])) {
            $collecotrExists = $collector_exists_cache[$id]; // Retrieve from static cache
        } else {
            $result = $this->mysqli->query("SELECT id FROM collector WHERE id = $id");
            $collecotrExists = $result->num_rows > 0;
            $collector_exists_cache[$id] = $collecotrExists; // Cache it
            $this->log->info("exist() $id");
        }
        return $collecotrExists;
    }
	
	public function get($id)
    {
        $id = (int) $id;
        if (!$this->exist($id)) return array('success'=>false, 'message'=>'Collector does not exist');

        $result = $this->mysqli->query("SELECT * FROM collector WHERE id = '$id'");
        $row = (array) $result->fetch_object();

        return $row;
    }
	
	public function getProperties($id)
    {
        $id = (int) $id;
        if (!$this->exist($id)) return array('success'=>false, 'message'=>'Collector does not exist');

        $result = $this->mysqli->query("SELECT `properties` FROM collector WHERE id = '$id'");
        $row = (array) $result->fetch_object();

        return $row;
    }
	
	public function get_list($userid)
    {
        if ($this->redis) {
            return $this->redis_getlist($userid);
        } else {
            return $this->mysql_getlist($userid);
        }
    }
	
	private function redis_getlist($userid)
    {
        $userid = (int) $userid;
        if (!$this->redis->exists("user:collector:$userid")) $this->load_to_redis($userid);

        $collectors = array();
        $collectorids = $this->redis->sMembers("user:collector:$userid");
        foreach ($collectorids as $id)
        {
            $row = $this->redis->hGetAll("collector:$id");
            $collectors[] = $row;
        }
        return $collectors;
    }

    private function mysql_getlist($userid)
    {
        $collectors = array();

        $result = $this->mysqli->query("SELECT `id`, `name`, `description`, `type`,`active`, `interval`, `public`, `properties` FROM collector WHERE userid = $userid");
        while ($row = (array)$result->fetch_object())
        {
            $collectors[] = $row;
        }
        return $collectors;
    }
	
	private function load_to_redis($userid)
    {
        $this->redis->delete("user:collector:$userid");
        $result = $this->mysqli->query("SELECT `id`, `name`, `description`, `type`, `active`, `interval`, `properties`, `public` FROM device WHERE userid = $userid");
        while ($row = $result->fetch_object())
        {
            $this->redis->sAdd("user:collector:$userid", $row->id);
            $this->redis->hMSet("collector:".$row->id,array(
                'id'=>$row->id,
                'name'=>$row->name,
                'description'=>$row->description,
                'type'=>$row->type,
                'active'=>$row->active,
                'interval'=>$row->interval,
				'properties' => $row->properties,
				'public' => $row->public
            ));
        }
    }
	
	public function create($userid)
    {
		$files = glob("Modules/collector/data/*.json");
		if(count($files) > 0){
		  $content = json_decode(file_get_contents($files[0]));
		  $type = $content->name;
		  $desc = $content->description;
          $this->mysqli->query("INSERT INTO collector (`name`, `description`, `active`, `type`, `interval`, `userid`, `properties`, `public`) VALUES ('New Collector','$desc',0,'$type', 0, $userid, '', 0)");
          if ($this->redis) $this->load_to_redis($userid);
          return $this->mysqli->insert_id;
		} else{
			return null;
		}
    }
	
	public function set_fields($id,$fields)
    {
        $id = (int) $id;
        if (!$this->exist($id)) return array('success'=>false, 'message'=>'Collector does not exist');

        $fields = json_decode(stripslashes($fields));

        $array = array();

        // Repeat this line changing the field name to add fields that can be updated:
        if (isset($fields->name)) $array[] = "`name` = '".preg_replace('/[^\p{L}_\p{N}\s-:]/u','',$fields->name)."'";
        if (isset($fields->description)) $array[] = "`description` = '".preg_replace('/[^\p{L}_\p{N}\s-:]/u','',$fields->description)."'";
        if (isset($fields->active)) $array[] = "`active` = '".preg_replace('/[^\p{N}]/u','',$fields->active)."'";
		if (isset($fields->interval)) $array[] = "`interval` = '".preg_replace('/[^\p{N}]/u','',$fields->interval)."'";
        if (isset($fields->public)) $array[] = "`public` = '".preg_replace('/[^\p{N}]/u','',$fields->public)."'";
        if (isset($fields->type)){
			$array[] = "`type` = '".preg_replace('/[^\/\|\,\w\s-:]/','',$fields->type)."'";
			$array[] = "`properties` = ''";
		}
		if(isset($fields->properties)){
			$array[] = "`properties` = '".json_encode($fields->properties)."'";
		}

        // Convert to a comma seperated string for the mysql query
        $fieldstr = implode(",",$array);
        $this->mysqli->query("UPDATE collector SET ".$fieldstr." WHERE `id` = '$id'");

        if ($this->mysqli->affected_rows>0){
            if ($this->redis) {
                $result = $this->mysqli->query("SELECT userid FROM device WHERE id='$id'");
                $row = (array) $result->fetch_object();
                if (isset($row['userid']) && $row['userid']) {
                    $this->load_to_redis($row['userid']);
                }
            }
            return array('success'=>true, 'message'=>'Field updated');
        } else {
            return array('success'=>false, 'message'=>'Field could not be updated');
        }
    }
	
}

?>