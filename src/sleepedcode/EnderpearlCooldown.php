<?php

namespace sleepedcode;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\ItemTypeIds;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class EnderpearlCooldown extends PluginBase implements Listener {

    private array $cooldowns = [];

    private Config $config;

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveResource("config.yml");
        $this->config = new Config($this->getDataFolder()."config.yml", Config::YAML);
    }

    public function onPlayerInteractEvent(PlayerInteractEvent $event): void {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if($item->getTypeId() === ItemTypeIds::ENDER_PEARL) {
            if($this->hasCooldown($player)){
                $event->cancel();
                $this->sendMessage($player);
            }else{
                $this->cooldowns[$player->getName()] = time();
            }
        }
    }

    public function sendMessage(Player $player): void {
        $time = time() - $this->cooldowns[$player->getName()];
        $message = $this->config->get("message-cooldown");
        $player->sendMessage(str_replace("{time}", ($this->config->get("time-cooldown") - $time), $message));
    }

    public function hasCooldown(Player $player): bool {
        $cooldown = $this->config->get("time-cooldown");
        if(isset($this->cooldowns[$player->getName()]) and time() - $this->cooldowns[$player->getName()] < $cooldown){
            return true;
        }
        return false;
    }

}