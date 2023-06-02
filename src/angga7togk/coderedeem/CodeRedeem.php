<?php

namespace angga7togk\coderedeem;

use angga7togk\coderedeem\libs\FormAPI\CustomForm;
use angga7togk\coderedeem\updater\ConfigUpdate;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class CodeRedeem extends PluginBase implements Listener
{
    public $cfg;
    private $dt;
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
        $this->cd = new Config($this->getDataFolder() . "code.yml", Config::YAML);
        @mkdir($this->getDataFolder()."data");

		// ConfigUpdate
        $this->cv = new ConfigUpdate($this);
        $this->cv->ConfigUpdate();
    }

	/** @param CommandSender $sender
	 * @param Command $cmd
	 * @param string $label
	 * @param array $args
	 */
    public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool
    {
        if ($cmd->getName() == "coderedeem") {
            $config = new Config($this->getDataFolder() . "data/".strtolower($sender->getName()).".yml", Config::YAML);
            if(!is_file($this->getDataFolder() . "data/".strtolower($sender->getName()).".yml")){
                $config->setAll([]);
                $config->save();
            }
			$this->cfg->reload();
            $config->reload();
            $this->cd->reload();
            $this->MenuUI($sender);
        }
        return true;
    }

    public function onJoin(PlayerJoinEvent $event){
        $player = $event->getPlayer();
        $config = new Config($this->getDataFolder() . "data/".strtolower($player->getName()).".yml", Config::YAML);
        if(!is_file($this->getDataFolder() . "data/".strtolower($player->getName()).".yml")){
            $config->setAll([]);
            $config->save();
        }
    }

	/** @param Player $player */
    public function MenuUI(Player $player)
    {   
        $this->dt = new Config($this->getDataFolder()."data/".strtolower($player->getName()).".yml", Config::YAML, []);
        $form = new CustomForm(function (Player $player, $data) {
            if ($data === null) {
                return true;
            }
			// Delete Chache
			if(isset($this->dt->get("Code-Claimed")[$data[1]])){
				if(!isset($this->cd->get("CodeRedeem")[$data[1]])){
					$this->dt->removeNested("Code-Claimed.".$data[1]);
					$this->dt->save();
					$player->sendMessage(self::prefix.$this->cfg->get("Prize")["Message-Failed"]);
					return;
				}
			}
			if(!isset($this->cd->get("CodeRedeem")[$data[1]])){
				$player->sendMessage(self::prefix.$this->cfg->get("Prize")["Message-Failed"]);
				return;
			}
			if(isset($this->dt->get("Code-Claimed")[$data[1]])){
				$player->sendMessage(self::prefix.$this->cfg->get("Prize")["Message-Claimed"]);
				return;
			}
			foreach($this->cd->get("CodeRedeem")[$data[1]]["Reward"] as $cmd){
				$this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), str_replace("{player}", $player->getName(), $cmd));
			}
			$this->dt->setNested("Code-Claimed.".$data[1], true);
			$this->dt->save();
			$player->sendMessage(self::prefix.str_replace("{player}", $player->getName(), $this->cfg->get("Prize")["Message-Succes"]));
        });
        $form->setTitle($this->cfg->get("Title"));
        $form->addLabel($this->cfg->get("Content"));
        $form->addInput("Input Code:", "Example : ABOGOBOGA");
        $player->sendForm($form);
    }
}
