<?php

namespace NewPlayerMC\command;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\constraint\InGameRequiredConstraint;
use NewPlayerMC\Loader;
use NewPlayerMC\command\sub\FurnaceAllSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\crafting\FurnaceType;
use pocketmine\player\Player;
use pocketmine\Server;

class FurnaceCommand extends BaseCommand
{
    private $cooldowns = [];

    public function __construct()
    {
        parent::__construct(Loader::getInstance(), "furnace", Loader::getInstance()->getConfig()->get("command_description"));
    }

    protected function prepare(): void
    {
        $this->setPermission($this->getPermission());
        $this->setPermissionMessage(Loader::getInstance()->getConfig()->get("permission_message"));
        $this->addConstraint(new InGameRequiredConstraint($this));
        $this->registerSubCommand(new FurnaceAllSubCommand());
    }

    public function getPermission(): string
    {
        return "furnace.use";
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

        $config = Loader::getInstance()->getConfig();
        $cooldown = $config->get("cooldown");
        var_dump($cooldown);
        $furnacemanager = Server::getInstance()->getCraftingManager()->getFurnaceRecipeManager(FurnaceType::FURNACE());

        if (isset($this->cooldowns[$sender->getName()]) and time() - $this->cooldowns[$sender->getName()] < $cooldown) {
            $time = time() - $this->cooldowns[$sender->getName()];
            $sender->sendMessage(str_replace("{cooldown}", ($cooldown - $time),($config->get("cooldown_message"))));
        } else {
            $this->cooldowns[$sender->getName()] = time();
            if ($furnacemanager->match($sender->getInventory()->getItemInHand()) === null) {
                $sender->sendMessage($config->get("item_not_furnacable"));
            } else {
                $sender->getInventory()->setItemInHand($furnacemanager->match($sender->getInventory()->getItemInHand())->getResult()->setCount($sender->getInventory()->getItemInHand()->getCount()));
                $sender->sendMessage($config->get("furnace_message"));
            }
        }
    }

}
