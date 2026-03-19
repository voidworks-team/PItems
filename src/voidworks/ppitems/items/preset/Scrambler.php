<?php

namespace voidworks\ppitems\items\preset;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\world\sound\AnvilBreakSound;
use voidworks\ppitems\items\BasePartnerItem;
use voidworks\ppitems\items\impl\OnAttackPartnerItem;
use voidworks\ppitems\Loader;
use voidworks\ppitems\utils\ClosureCancellableTask;

final class Scrambler extends BasePartnerItem implements OnAttackPartnerItem {

    public function __construct() {
        parent::__construct(
            "scrambler",
            "&r&4&lScrambler",
            VanillaItems::STICK()
        );
    }

    public function onAttack(Player $damager, Player $player): void {
        $counter = 0;
        Loader::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureCancellableTask(
            function() use ($player, &$counter): void {
                ++$counter;
                $inventory = $player->getInventory();
                $hotbarSize = $inventory->getHotbarSize();
                $hotbarItems = [];

                ++$counter;

                for ($i = 0; $i < $hotbarSize; $i++) {
                    $hotbarItems[] = $inventory->getItem($i);
                }

                shuffle($hotbarItems);
                for ($i = 0; $i < $hotbarSize; $i++) {
                    $inventory->setItem($i, $hotbarItems[$i]);
                }

                $player->broadcastSound(new AnvilBreakSound());
            }, fn() => !$player->isOnline() || !$player->isAlive()
        ), 5);
    }
}