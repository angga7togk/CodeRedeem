<?php

namespace angga7togk\coderedeem;

use angga7togk\coderedeem\libs\CustomForm;
use angga7togk\coderedeem\libs\SimpleForm;
use angga7togk\coderedeem\updater\ConfigUpdate;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class CodeRedeem extends PluginBase implements Listener
{

    public $cfg;
    public $dt;
    public $cv;
    public $cd;

    const cfgversion = "1.0";
	const prefix = TF::GOLD."[CodeRedeem ]".TF::RESET;

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveResource("config.yml");
        $this->saveResource("code.yml");
        $this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
        $this->dt = new Config($this->getDataFolder() . "data.yml", Config::YAML);
        $this->cd = new Config($this->getDataFolder() . "code.yml", Config::YAML);

        $this->cv = new ConfigUpdate($this);
        $this->cv->ConfigUpdate();
    }

    public function onCommand(CommandSender $sender, Command $cmd, String $label, array $args): bool
    {
        if ($cmd->getName() == "coderedeem") {
            $this->MenuUI($sender);
            $this->cfg->reload();
            $this->dt->reload();
        }
        return true;
    }

    public function MenuUI(Player $player)
    {
        $form = new CustomForm(function (Player $player, $data) {
            if ($data === null) {
                return true;
            }
			if(!isset($this->cd->get("CodeRedeem")[$data[1]])){
				$player->sendMessage($this->cfg->get("Prize")["Message-Failed"]);
				return;
			}
			if(isset($this->dt->get($player->getName())[$data[1]])){
				$player->sendMessage($this->cfg->get("Prize")["Message-Claimed"]);
				return;
			}
			foreach($this->cd->get("CodeRedeem")[$data[1]]["Reward"] as $cmd){
				$this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), str_replace("{player}", $player->getName(), $cmd));
			}
			$this->dt->setNested($player->getName().".".$data[1], true);
			$this->dt->save();
			$player->sendMessage(str_replace("{player}", $player->getName(), $this->cfg->get("Prize")["Message-Succes"]));
        });
        $form->setTitle($this->cfg->get("Title"));
        $form->addLabel($this->cfg->get("Content"));
        $form->addInput("Input Code:", "Example : ABOGOBOGA");
        $player->sendForm($form);
    }
}
