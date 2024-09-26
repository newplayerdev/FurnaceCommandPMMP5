<?php

namespace NewPlayerMC;

use CortexPE\Commando\PacketHooker;
use NewPlayerMC\command\FurnaceCommand;

class Main extends \pocketmine\plugin\PluginBase
{
    /** @var Main  */
    private static Main $instance;

    protected function onEnable(): void
    {
        self::$instance = $this;
        $this->saveDefaultConfig();

        $this->getServer()->getCommandMap()->register("furnace", new FurnaceCommand());

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }
    }

    /**
     * @return Main
     */
    public static function getInstance(): Main
    {
        return self::$instance;
    }

}