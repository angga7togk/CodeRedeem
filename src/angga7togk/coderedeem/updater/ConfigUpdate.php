<?php

namespace angga7togk\coderedeem\updater;

use angga7togk\coderedeem\CodeRedeem;

class ConfigUpdate {

	public CodeRedeem $plugin;

    public function __construct(CodeRedeem $plugin) {
        $this->plugin = $plugin;
    }

    public function ConfigUpdate(){
        if($this->plugin->cfg->exists("Config-Version")){
            if($this->plugin->cfg->get("Config-Version") == $this->plugin->cfgversion){
                $this->plugin->saveResource("config.yml");
            }else{
                rename($this->plugin->getDataFolder(). "config.yml", $this->plugin->getDataFolder()."config_old.yml");
                $this->plugin->saveResource("config.yml");
            }
        }
    }
}