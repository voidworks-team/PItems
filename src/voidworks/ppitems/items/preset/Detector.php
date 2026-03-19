<?php

namespace voidworks\ppitems\items\preset;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\item\Item;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use ReflectionException;
use voidworks\ppitems\items\BasePartnerItem;
use voidworks\ppitems\items\impl\OnAttackPartnerItem;
use voidworks\ppitems\Loader;
use voidworks\ppitems\utils\TimedListener;

final class Detector extends BasePartnerItem implements OnAttackPartnerItem {

    private int $maxPartnerItemsCapped;

    public function __construct(string $identifier, string $displayName, Item $item) {
        parent::__construct($identifier, $displayName, $item);
        $this->maxPartnerItemsCapped = $this->config->get("max-partner-items-capped", 15);
    }

    /**
     * @throws ReflectionException
     */
    public function onAttack(Player $damager, Player $player): void {
        $abilities = count(array_filter($player->getInventory()->getContents(), fn(Item $item) => !$item->isNull() && Loader::getInstance()->getPartnerItemsHandler()->getPartnerItem($item) !== null));
        $abilities = min($this->maxPartnerItemsCapped, $abilities); // cap this
        $pct = $abilities * 2 / 100;

        $damager->sendMessage(TextFormat::colorize("&eYou used &6Detector &eon &c{$player->getName()}&e: &e{$abilities} ability items detected, &a+{$abilities}% extra damage!"));

        TimedListener::listen(Loader::getInstance(), function(EntityDamageByEntityEvent $event) use ($damager, $player, $pct){
            if($event->getEntity() !== $player || $event->getDamager() !== $damager){
                return;
            }

            if($event->isCancelled()){
                return;
            }

            $event->setModifier($event->getFinalDamage() * $pct, 211); //magic number
        }, delay: 20*12);
    }
}