<?php

namespace angga7togk\coderedeem;

use angga7togk\coderedeem\libs\FormAPI\CustomForm;
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
			$this->cfg->reload();
            $this->dt->reload();
            $this->cd->reload();
            $this->MenuUI($sender);
        }
        return true;
    }

	/** @param Player $player */
    public function MenuUI(Player $player)
    {
        $form = new CustomForm(function (Player $player, $data) {
            if ($data === null) {
                return true;
            }
			// Delete Chache
			if($this->dt->exists($data[1])){
				if(!isset($this->cd->get("CodeRedeem")[$data[1]])){
					$this->dt->remove($data[1]);
					$this->dt->save();
					$player->sendMessage(self::prefix.$this->cfg->get("Prize")["Message-Failed"]);
					return;
				}
			}
			if(!isset($this->cd->get("CodeRedeem")[$data[1]])){
				$player->sendMessage(self::prefix.$this->cfg->get("Prize")["Message-Failed"]);
				return;
			}
			if(isset($this->dt->get($data[1])[$player->getName()])){
				$player->sendMessage(self::prefix.$this->cfg->get("Prize")["Message-Claimed"]);
				return;
			}
			foreach($this->cd->get("CodeRedeem")[$data[1]]["Reward"] as $cmd){
				$this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), str_replace("{player}", $player->getName(), $cmd));
			}
			$this->dt->setNested($data[1].".".$player->getName(), true);
			$this->dt->save();
			$player->sendMessage(self::prefix.str_replace("{player}", $player->getName(), $this->cfg->get("Prize")["Message-Succes"]));
        });
        $form->setTitle($this->cfg->get("Title"));
        $form->addLabel($this->cfg->get("Content"));
        $form->addInput("Input Code:", "Example : ABOGOBOGA");
        $player->sendForm($form);
    }
}
