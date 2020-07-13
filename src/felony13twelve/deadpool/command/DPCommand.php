<?php

namespace felony13twelve\deadpool\command;

use pocketmine\Player;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\Item;

use felony13twelve\deadpool\DeadPool;

class DPCommand extends Command {

    public function __construct ($cmd = 'dp') {
        parent::__construct($cmd);

        $this->setUsage("/$cmd <mascot|knife|info>");
        $this->setDescription("Крутые вещи от Дэдпула");
        $this->setPermission("deadpool.command.dp");
    }

    public function execute (CommandSender $player, $label, array $args) {
        if (!($player instanceof Player)) {
            return true;
        }

        if (count($args) == 0) {
            $player->sendMessage($this->getDeadPool()->getMessage('help', [
                'prefix' => $this->getDeadPool()->messages['prefix']
            ]));
            return true;
        }

        $active = strtolower($args[0]);
        if ($active === 'info') {
            $player->sendMessage($this->getDeadPool()->getMessage('info', [
                'prefix' => $this->getDeadPool()->messages['prefix']
            ]));
            return true;
        }
        if (array_key_exists($active, $this->getDeadPool()->messages)) {
            $moneyPlayer = $this->getDeadPool()->economy->getMoney($player);
            if ($moneyPlayer >= $this->getDeadPool()->settings[$active]['cost']) {
                $this->getDeadPool()->economy->takeMoney($player, $this->getDeadPool()->settings[$active]['cost']);

                $item = Item::get($this->getDeadPool()->items[$active]['item'], 0, 1);
                $item->setCustomName($this->getDeadPool()->items[$active]['customName']);

                $player->getInventory()->addItem($item);
                $player->sendMessage($this->getDeadPool()->messages[$active . '_buy']);
                return true;
            }
            $player->sendMessage($this->getDeadPool()->getMessage($active, [
                'cost' => $this->getDeadPool()->settings[$active]['cost']
            ]));
            return true;
        }
        $player->sendMessage($this->getDeadPool()->getMessage('help', [
            'prefix' => $this->getDeadPool()->messages['prefix']
        ]));
    }

    /**
     * @return DeadPool
     */
    public function getDeadPool () : DeadPool {
        return DeadPool::getInstance();
    }
}