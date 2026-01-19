<?php

namespace FurnaceCommandPMMP5\src;

use CortexPE\Commando\PacketHooker;
use NewPlayerMC\command\FurnaceCommand;
use pocketmine\utils\SingletonTrait;

class Loader extends \pocketmine\plugin\PluginBase
{
    use SingletonTrait;

    protected function onEnable(): void
    {
        self::$instance = $this;
        $this->saveResource('config.yml');

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

        $command = new FurnaceCommand();

        $this->getServer()->getCommandMap()->register("furnace", $command);
    }
}