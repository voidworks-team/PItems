<?php

namespace voidworks\ppitems\items\preset;

use pocketmine\entity\Location;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use voidworks\ppitems\items\BasePartnerItem;
use voidworks\ppitems\items\etc\MinionEntity;
use voidworks\ppitems\items\impl\OnUsePartnerItem;
use voidworks\ppitems\Loader;

final class Minions extends BasePartnerItem implements OnUsePartnerItem {

    public function __construct() {
        MinionEntity::register();
        parent::__construct(
            "minions",
            "&r&eMinions",
            VanillaItems::ZOMBIE_SPAWN_EGG(),
        );
    }

    public function onUse(Player $player): void {
        $location = $player->getLocation();

        /**
         * @var MinionEntity[] $minions
         */
        $minions = [];

        for($i = 0; $i <= 3; $i++){
            $offsetX = mt_rand(-3, 3);
            $offsetZ = mt_rand(-3, 3);

            $spawnPos = $location->add($offsetX, 0, $offsetZ);

            $minion = new MinionEntity(Location::fromObject($spawnPos, $location->getWorld(), $location->getYaw(), $location->getPitch()), null, $player);
            $minion->spawnToAll();
            $minion->startTicking();
            $minions[] = $minion;
        }

        Loader::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(
            function() use ($minions): void {
                foreach ($minions as $minion){
                    if($minion->isClosed()){
                        continue;
                    }
                    $minion->flagForDespawn();
                }
            }
        ), 20*30);
    }
}