<?php

namespace NewPlayerMC\command\sub;

use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use NewPlayerMC\Loader;
use pocketmine\command\CommandSender;
use pocketmine\crafting\FurnaceType;
use pocketmine\player\Player;
use pocketmine\Server;

class FurnaceAllSubCommand extends BaseSubCommand
{
    private $cooldowns = [];

    public function __construct()
    {
        parent::__construct("all", "all");
        $this->addConstraint(new InGameRequiredConstraint($this));
    }

    /**
     * @inheritDoc
     */
    protected function prepare(): void
    {
        $this->setPermission("furnace.all");
    }

    /**
     * @inheritDoc
     * @var Player $sender
     */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        $config = Loader::getInstance()->getConfig();
        $cooldown = $config->get("cooldown");
        $furnacemanager = Server::getInstance()->getCraftingManager()->getFurnaceRecipeManager(FurnaceType::FURNACE());

        if (isset($this->cooldowns[$sender->getName()]) and time() - $this->cooldowns[$sender->getName()] < $cooldown) {
            $time = time() - $this->cooldowns[$sender->getName()];
            $sender->sendMessage(str_replace("{cooldown}", ($cooldown - $time), $config->get("cooldown_message")));
        } else {
            $this->cooldowns[$sender->getName()] = time();
            foreach ($sender->getInventory()->getContents() as $slot => $item) {
                if ($furnacemanager->match($item) !== null) {
                    $sender->getInventory()->setItem($slot, $furnacemanager->match($item)->getResult()->setCount($item->getCount()));
                }
            }
            $sender->sendMessage($config->get("furnace_all_message"));
        }
    }
}