<?php

  // no direct access
defined('EMONCMS_EXEC') or die('Restricted access');

class Device
{
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
}

?>