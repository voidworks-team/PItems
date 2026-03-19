<?php

namespace voidworks\ppitems\listener;

use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use voidworks\ppitems\items\etc\DurablePartnerItem;
use voidworks\ppitems\items\event\PartnerItemUseEvent;
use voidworks\ppitems\items\impl\OnAttackPartnerItem;
use voidworks\ppitems\items\impl\OnChildAttackPartnerItem;
use voidworks\ppitems\items\impl\OnUsePartnerItem;
use voidworks\ppitems\items\impl\PartnerItem;
use voidworks\ppitems\items\PartnerItemsHandler;
use voidworks\ppitems\Loader;
use voidworks\ppitems\session\Session;
use voidworks\ppitems\session\SessionHandler;

final class BaseListener implements Listener {

    protected PartnerItemsHandler $partnerItemsHandler;
    protected SessionHandler $sessionHandler;

    public function __construct(Loader $plugin) {
        $this->partnerItemsHandler = $plugin->getPartnerItemsHandler();
        $this->sessionHandler = $plugin->getSessionHandler();
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function onItemUseEvent(PlayerItemUseEvent $event): void {
        $partnerItem = $this->partnerItemsHandler->getPartnerItem($event->getItem());
        $player = $event->getPlayer();

        if ($partnerItem === null) {
            return;
        }

        $session = $this->sessionHandler->getSession($player);

        if ($partnerItem instanceof OnUsePartnerItem) {
            if ($this->sendCooldownMessageIfOnCooldown($player, $session, $partnerItem)) {
                return;
            }

            $session->applyCooldowns($partnerItem);
            $partnerItem->onUse($event->getPlayer());
        }
    }

    private function sendCooldownMessageIfOnCooldown(Player $player, Session $session, PartnerItem $partnerItem): bool {
        if ($session->hasGlobalCooldown()) {
            $player->sendMessage(TextFormat::RED . 'You have global ability cooldown: ' . $session->formatToTime($session->getGlobalCooldown()));
            return true;
        }

        if ($session->hasCooldown($partnerItem)) {
            $player->sendMessage(TextFormat::RED . 'You have ' . $partnerItem->getDisplayName() . ' cooldown: ' . $session->formatToTime($session->getCooldown($partnerItem)));
            return true;
        }

        $event = new PartnerItemUseEvent($player, $partnerItem);
        $event->call();

        if($event->isCancelled()){
            $context = $event->getCancelContext();

            if($context !== null){
                $player->sendMessage($context);
            }

            return true;
        }

        $item = $player->getInventory()->getItemInHand();

        if ($partnerItem instanceof DurablePartnerItem) {
            $item = $partnerItem->pop($item);
        } else {
            $item->pop();
        }

        $player->getInventory()->setItemInHand($item);
        return false;
    }

    public function onEntityDamageEvent(EntityDamageEvent $event): void {
        $player = $event->getEntity();

        if (!$player instanceof Player) {
            return;
        }

        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();

            if (!$damager instanceof Player) {
                return;
            }

            $partnerItem = $this->partnerItemsHandler->getPartnerItem($damager->getInventory()->getItemInHand());
            $session = $this->sessionHandler->getSession($damager);

            if ($partnerItem instanceof OnAttackPartnerItem && !$event instanceof EntityDamageByChildEntityEvent) {
                if ($this->sendCooldownMessageIfOnCooldown($damager, $session, $partnerItem)) {
                    return;
                }

                $session->applyCooldowns($partnerItem);
                $partnerItem->onAttack($damager, $player);
            }

            if ($event instanceof EntityDamageByChildEntityEvent) {
                if ($partnerItem instanceof OnChildAttackPartnerItem) {

                    if($this->sendCooldownMessageIfOnCooldown($damager, $session, $partnerItem)){
                        return;
                    }

                    if (!$partnerItem->onChildAttack($damager, $player, $event->getChild())) {
                        $event->cancel();
                    }
                }
            }
        }
    }
}