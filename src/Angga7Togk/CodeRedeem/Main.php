<?php

namespace Angga7Togk\CodeRedeem;

use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\command\{Command, CommandSender};
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\utils\Config;

use jojoe77777\FormAPI\CustomForm;

class Main extends PluginBase implements Listener {
	
	public $cfg;
	public $dt;
	
	 public function onEnable() : void {
	 	$this->getServer()->getPluginManager()->registerEvents($this, $this);
	 	$this->saveResource("config.yml");
		$this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
		$this->saveResource("data.yml");
		$this->dt = new Config($this->getDataFolder() . "data.yml", Config::YAML, array());
	 }

	 public function onCommand(CommandSender $sender, Command $cmd, String $label, Array $args) : bool {

	 	switch($cmd->getName()){
	 		case "coderedeem":
	 			$this->MenuUI($sender);
	 		break;

	 		case "codereset":
	 			$sender->sendMessage("Berhasil Reset Data Player.");
	 			$this->dt->removeNested("Data");
	 			$this->dt->save();
	 			$this->dt->reload();
	 		break;
	 	}
	 	return true;
	 }

	 public function MenuUI($player){
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
	 	$form->sendToPlayer($player);
	 	return $form;
	 }
}
