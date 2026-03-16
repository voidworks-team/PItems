<?php

namespace voidworks\ppitems\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\plugin\PluginBase;
use voidworks\ppitems\items\event\PartnerItemUseEvent;
use voidworks\ppitems\items\impl\OnUsePartnerItem;
use voidworks\ppitems\items\PartnerItemsHandler;
use voidworks\ppitems\Loader;
use voidworks\ppitems\session\SessionHandler;

final class BaseListener implements Listener {

    protected PartnerItemsHandler $handler;
    protected SessionHandler $sessionHandler;

    public function construct(Loader $plugin): void {
        $this->handler = $plugin->getPartnerItemsHandler();
        $this->sessionHandler = $plugin->getSessionHandler();
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function onItemUseEvent(PlayerItemUseEvent $event): void {
        $partnerItem = $this->handler->getPartnerItem($event->getItem());

        if($partnerItem === null){
            return;
        }

        $session = $this->sessionHandler->getSession($event->getPlayer());

        if($session->hasGlobalCooldown()) {
            //gc cd
            return;
        }

        if($session->hasCooldown($partnerItem)){
            // cd message
            return;
        }

        if($partnerItem instanceof OnUsePartnerItem){
			$session->applyCooldowns($partnerItem);
            $partnerItem->onUse($event->getPlayer());
        }

    }
}