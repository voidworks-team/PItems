<?php

namespace voidworks\ppitems\items\preset;

use pocketmine\entity\Entity;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\EventPriority;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use voidworks\ppitems\items\BasePartnerItem;
use voidworks\ppitems\items\impl\OnUsePartnerItem;
use voidworks\ppitems\Loader;
use voidworks\ppitems\utils\TimedListener;

final class AntiTrapRod extends BasePartnerItem implements OnUsePartnerItem {

    protected \WeakMap $hasRod;

    public function __construct() {
        parent::__construct(
            "antitraprod",
            "&r&gAntiTrap Rod",
            VanillaItems::BLAZE_ROD()
        );
        $this->hasRod = new \WeakMap();
    }

    public function onUse(Player $player): void {
        $players = array_filter($player->getWorld()->getNearbyEntities($player->getBoundingBox()->expandedCopy(15, 15, 15), $player), function(Entity $player): bool { return $player instanceof Player; });
        foreach($players as $p){
            $this->hasRod[$p] = true;
        }

        TimedListener::listen(Loader::getInstance(), function(BlockPlaceEvent $event): void {
            if(isset($this->hasRod[$event->getPlayer()])){
                $event->cancel();
            }
        }, null, EventPriority::LOWEST, 30*20);
    }
}