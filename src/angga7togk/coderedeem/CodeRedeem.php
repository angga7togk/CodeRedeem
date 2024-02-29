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
    public Config $cfg, $cd, $data;
    public $cv;
    const cfgversion = "1.0";
    const prefix = TF::GOLD . "[CodeRedeem ]" . TF::RESET;

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveResource("config.yml");
        $this->saveResource("code.yml");
        $this->cfg = new Config($this->getDataFolder() . "config.yml", Config::YAML, array());
        $this->cd = new Config($this->getDataFolder() . "code.yml", Config::YAML);
        $this->data = new Config($this->getDataFolder() . "data.yml", Config::YAML, array());

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
            $this->MenuUI($sender);
        }
        return true;
    }

    public function onJoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        if (!$this->data->exists(strtolower($player->getName()))) {
            $this->data->set(strtolower($player->getName()), []);
            $this->data->save();
        }
    }

    /** @param Player $player */
    public function MenuUI(Player $player)
    {
        $form = new CustomForm(function (Player $player, $data) {
            if ($data === null) {
                return true;
            }
            $nameLower = strtolower($player->getName());
            // Delete Chache
            if (isset($this->data->get($nameLower)[$data[1]])) {
                if (!isset($this->cd->get("CodeRedeem")[$data[1]])) {
                    $this->data->removeNested($nameLower . "." . $data[1]);
                    $this->data->save();
                    $player->sendMessage(self::prefix . $this->cfg->get("Prize")["Message-Failed"]);
                    return;
                }
            }
            if (!isset($this->cd->get("CodeRedeem")[$data[1]])) {
                $player->sendMessage(self::prefix . $this->cfg->get("Prize")["Message-Failed"]);
                return;
            }
            if (isset($this->data->get($nameLower)[$data[1]])) {
                $player->sendMessage(self::prefix . $this->cfg->get("Prize")["Message-Claimed"]);
                return;
            }
            foreach ($this->cd->get("CodeRedeem")[$data[1]]["Reward"] as $cmd) {
                $this->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender($this->getServer(), $this->getServer()->getLanguage()), str_replace("{player}", $player->getName(), $cmd));
            }
            $this->data->setNested($nameLower . "." . $data[1], true);
            $this->data->save();
            $player->sendMessage(self::prefix . str_replace("{player}", $player->getName(), $this->cfg->get("Prize")["Message-Succes"]));
        });
        $form->setTitle($this->cfg->get("Title"));
        $form->addLabel($this->cfg->get("Content"));
        $form->addInput("Input Code:", "Example : ABOGOBOGA");
        $player->sendForm($form);
    }
}
