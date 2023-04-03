<?php

namespace angga7togk\coderedeem;

use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\command\{Command, CommandSender};
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\utils\Config;

use angga7togk\coderedeem\libs\jojoe77777\FormAPI\CustomForm;

class CodeRedeem extends PluginBase implements Listener {
	
	public Config $cfg;
	public Config $dt;
	
	 public function onEnable() : void {
	 	$this->getServer()->getPluginManager()->registerEvents($this, $this);
	 	$this->saveResource("config.yml");
		$this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
		$this->dt = new Config($this->getDataFolder() . "data.json", Config::JSON);
	 }

	 public function onCommand(CommandSender $sender, Command $cmd, String $label, Array $args) : bool {

	 	switch($cmd->getName()){
	 		case "coderedeem":
	 			$this->MenuUI($sender);
	 			break;

	 		case "setcode":
				if(isset($args[0])){
					if(isset($args[1])){
						$this->cfg->setNested("Prize".".Code", $args[0]);
						$this->cfg->setNested("Prize".".Command-Give", $args[1]);
						$this->cfg->save();
						$this->cfg->reload();
						unlink($this->getDataFolder(). "data.json");
						$sender->sendMessage("Succes Set Code ".$args[0]);
					}else{
						$sender->sendMessage("usage: /setcode <code> <command-give>");
					}
				}else{
					$sender->sendMessage("usage: /setcode <code> <command-give>");
				}
	 			break;
	 	}
	 	return true;
	 }

	 public function MenuUI(Player $player){
	 	$form = new CustomForm(function(Player $player, $data){
	 	if($data === null){
	 		return true;
	 	}
	 	$command = str_replace("{player}", $player->getName(), $this->cfg->get("Prize")["Command-Give"]);
			if($data[1] === $this->cfg->get("Prize")["Code"]){
				if($this->dt->getNested("Data."."\"".$player->getName()."\"") === $this->dt->getNested("Data."."\"".$player->getName()."\"", true)){
					$player->sendMessage($this->cfg->get("Prize")["Message-Claimed"]);
				} else {
					$this->getServer()->getCommandMap()->dispatch(new ConsoleCommandsender($this->getServer(), $this->getServer()->getLanguage()), $command);
					$player->getServer()->broadcastMessage(str_replace("{player}", $player->getName(), $this->cfg->get("Prize")["Message-Succes"]));
					$this->dt->setNested("Data."."\"".$player->getName()."\"", true);
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
