<?php

namespace NewPlayerMC\command;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use NewPlayerMC\command\sub\FurnaceAllSubCommand;
use NewPlayerMC\Main;
use pocketmine\command\CommandSender;
use pocketmine\crafting\FurnaceType;
use pocketmine\player\Player;
use pocketmine\Server;

class FurnaceCommand extends BaseCommand
{
    private $cooldowns = [];

    public function __construct()
    {
        parent::__construct(Main::getInstance(), "furnace", Main::getInstance()->getConfig()->get("command_description"));
    }

    protected function prepare(): void
    {
        $this->setPermission("furnace.all");
        $this->setPermissionMessage(Main::getInstance()->getConfig()->get("permission_message"));
        $this->addConstraint(new InGameRequiredConstraint($this));
        $this->registerSubCommand(new FurnaceAllSubCommand());
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void
    {
        if (!$sender instanceof Player) {
            $sender->sendMessage("§cNo console allowed");
            return;
        }
        if (!$this->testPermission($sender)) {
            $sender->sendMessage($this->getPermissionMessage());
            return;
        }
        if (count($args) > 1) {
            $sender->sendMessage("§c/" . $this->getUsage());
            return;
        }

        $cooldown = Main::getInstance()->getConfig()->get("cooldown");
        $furnacemanager = Server::getInstance()->getCraftingManager()->getFurnaceRecipeManager(FurnaceType::FURNACE());

        if (isset($this->cooldowns[$sender->getName()]) and time() - $this->cooldowns[$sender->getName()] < $cooldown) {
            $time = time() - $this->cooldowns[$sender->getName()];
            $sender->sendMessage(str_replace("{cooldown}", ($cooldown - $time), Main::getInstance()->getConfig()->get("cooldown_message")));
        } else {
            $this->cooldowns[$sender->getName()] = time();
            if ($furnacemanager->match($sender->getInventory()->getItemInHand()) === null) {
                $sender->sendMessage(Main::getInstance()->getConfig()->get("item_not_furnacable"));
            } else {
                $sender->getInventory()->setItemInHand($furnacemanager->match($sender->getInventory()->getItemInHand())->getResult()->setCount($sender->getInventory()->getItemInHand()->getCount()));
                $sender->sendMessage(Main::getInstance()->getConfig()->get("furnace_message"));
            }
        }
    }

}
