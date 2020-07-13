<?php

namespace felony13twelve\deadpool;

use pocketmine\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;

class EventHandler implements Listener {

    public function onActive (PlayerInteractEvent $event) {
        $player = $event->getPlayer();
        $hand = $player->getInventory()->getItemInHand();
        
        if ($event->getAction() == PlayerInteractEvent::RIGHT_CLICK_AIR) {
            if ($hand->getId() == $this->getDeadPool()->items['mascot']['item']) {
                if ($hand->getCustomName() == $this->getDeadPool()->items['mascot']['customName']) {
                    $player->getInventory()->removeItem(Item::get($this->getDeadPool()->items['mascot']['item'], 0, 1)); 
                    $player->setHealth(20);
                    $player->sendTitle('', $this->getDeadPool()->messages['mascot_active'], 20, 100, 20);
                }
            }
        }
    }

    public function onDamageEvent (EntityDamageEvent $event) {
        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            $entity = $event->getEntity();
            if ($damager instanceof Player && $entity instanceof Player) {
                $hand = $damager->getInventory()->getItemInHand();
                if ($hand->getId() == $this->getDeadPool()->items['knife']['item']) {
                    if ($hand->getCustomName() == $this->getDeadPool()->items['knife']['customName']) {
                        $rand = rand(1, 50);
                        switch ($rand) {
                            case 1:
                                $damager->setHealth(20);
                                $damager->sendTitle('', $this->getDeadPool()->messages['knife_active_hp'], 20, 100, 20);
                                break;
                            case 2:
                                $damager->setFood(20);
                                $damager->sendTitle('', $this->getDeadPool()->messages['knife_active_food'], 20, 100, 20);
                                break;
                        }
                    }
                }
            }
        }
    } 

    /**
     * @return DeadPool
     */
    public function getDeadPool () : DeadPool {
        return DeadPool::getInstance();
    }
}