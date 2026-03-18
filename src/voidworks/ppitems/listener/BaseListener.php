<?php

namespace voidworks\ppitems\listener;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use voidworks\ppitems\items\impl\OnAttackPartnerItem;
use voidworks\ppitems\items\impl\OnUsePartnerItem;
use voidworks\ppitems\items\impl\PartnerItem;
use voidworks\ppitems\items\PartnerItemsHandler;
use voidworks\ppitems\Loader;
use voidworks\ppitems\session\Session;
use voidworks\ppitems\session\SessionHandler;

final class BaseListener implements Listener {

    protected PartnerItemsHandler $handler;
    protected SessionHandler $sessionHandler;

    public function __construct(Loader $plugin) {
        $this->handler = $plugin->getPartnerItemsHandler();
        $this->sessionHandler = $plugin->getSessionHandler();
        $plugin->getServer()->getPluginManager()->registerEvents($this, $plugin);
    }

    public function onItemUseEvent(PlayerItemUseEvent $event): void {
        $partnerItem = $this->handler->getPartnerItem($event->getItem());
        $player = $event->getPlayer();

        if ($partnerItem === null) {
            return;
        }

        $session = $this->sessionHandler->getSession($player);

        if ($this->sendCooldownMessageIfOnCooldown($player, $session, $partnerItem)) {
            return;
        }

        if ($partnerItem instanceof OnUsePartnerItem) {
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

        return false;
    }

    public function onEntityDamageEvent(EntityDamageByEntityEvent $event): void {
        $player = $event->getEntity();
        $damager = $event->getDamager();

        if (!$player instanceof Player || !$damager instanceof Player) {
            return;
        }

        $partnerItem = $this->handler->getPartnerItem($damager->getInventory()->getItemInHand());
        $session = $this->sessionHandler->getSession($player);

        if ($this->sendCooldownMessageIfOnCooldown($player, $session, $partnerItem)) {
            return;
        }

        if ($partnerItem instanceof OnAttackPartnerItem) {
            $session->applyCooldowns($partnerItem);
            $partnerItem->onAttack($damager, $player);
        }
    }
}