<?php

namespace angga7togk\coderedeem;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\{Command, CommandSender};
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\utils\Config;

use angga7togk\coderedeem\libs\CustomForm;
use angga7togk\coderedeem\updater\ConfigUpdate;

class CodeRedeem extends PluginBase implements Listener {
	
	public Config $cfg;
	public Config $dt;
	public $cfgversion;
	public $cv;

	const cfgversion = "1.0";
	
	 public function onEnable() : void {
	 	$this->getServer()->getPluginManager()->registerEvents($this, $this);
	 	$this->saveResource("config.yml");
		$this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
		$this->dt = new Config($this->getDataFolder() . "data.json", Config::JSON);

		$this->cfgversion = self::cfgversion;

		$this->cv = new ConfigUpdate($this);
		$this->cv->ConfigUpdate();
	 }

	 public function onCommand(CommandSender $sender, Command $cmd, String $label, Array $args) : bool {

		if($cmd->getName() == "coderedeem"){
			$this->MenuUI($sender);
			$this->cfg->reload();
			$this->dt->reload();
		}
	 	return true;
	 }

	 public function MenuUI(Player $player){
	 	$form = new CustomForm(function(Player $player, $data){
	 	if($data === null){
	 		return true;
	 	}
			if($data[1] === $this->cfg->get("Prize")["Code"]){
				if($this->dt->exists($player->getName())){
					$player->sendMessage($this->cfg->get("Prize")["Message-Claimed"]);
				} else {
					foreach($this->cfg->get("Reward") as $reward){
						$this->getServer()->getCommandMap()->dispatch(new ConsoleCommandsender($this->getServer(), $this->getServer()->getLanguage()), str_replace("{player}", $player->getName(), $reward));
					}
					$player->getServer()->broadcastMessage(str_replace("{player}", $player->getName(), $this->cfg->get("Prize")["Message-Succes"]));
					$this->dt->setNested($player->getName(), true);
					$this->dt->save();
					$this->dt->reload();
				}
			} else {
				$player->sendMessage($this->cfg->get("Prize")["Message-Failed"]);
			}
	 	});
	 	$form->setTitle($this->cfg->get("Title"));
	 	$form->addLabel($this->cfg->get("Content"));
	 	$form->addInput("Input Code:", "Example : ABOGOBOGA");
	 	$player->sendForm($form);
	 }
}
