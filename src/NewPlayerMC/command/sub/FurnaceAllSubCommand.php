<?php

namespace NewPlayerMC\command\sub;

use CortexPE\Commando\BaseSubCommand;
use NewPlayerMC\Main;
use pocketmine\command\CommandSender;
use pocketmine\crafting\FurnaceType;
use pocketmine\Server;

class FurnaceAllSubCommand extends BaseSubCommand
{
    private $cooldowns = [];

    public function __construct()
    {
        parent::__construct(Main::getInstance(), "all");
    }

    /**
     * @inheritDoc
     */
    protected function prepare(): void
    {
        $this->setPermission("furnace.all");
        $this->setPermissionMessage(Main::getInstance()->getConfig()->get("permission_message"));
    }

    /**
     * @inheritDoc
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $cooldown = Main::getInstance()->getConfig()->get("cooldown");
        $furnacemanager = Server::getInstance()->getCraftingManager()->getFurnaceRecipeManager(FurnaceType::FURNACE());

        if (isset($this->cooldowns[$sender->getName()]) and time() - $this->cooldowns[$sender->getName()] < $cooldown) {
            $time = time() - $this->cooldowns[$sender->getName()];
            $sender->sendMessage(str_replace("{cooldown}", ($cooldown - $time), Main::getInstance()->getConfig()->get("cooldown_message")));
        } else {
            $this->cooldowns[$sender->getName()] = time();
            foreach ($sender->getInventory()->getContents() as $slot => $item) {
                if ($furnacemanager->match($item) !== null) {
                    $sender->getInventory()->setItem($slot, $furnacemanager->match($item)->getResult()->setCount($item->getCount()));
                }
            }
            $sender->sendMessage(Main::getInstance()->getConfig()->get("furnace_all_message"));
        }
    }
}