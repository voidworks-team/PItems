<?php

namespace voidworks\ppitems\items\impl;

use pocketmine\entity\Entity;
use pocketmine\player\Player;

interface OnChildAttackPartnerItem extends PartnerItem {

    /**
     * @param Player $damager
     * @param Player $player
     * @param Entity $child
     * @return bool
     */
    public function onChildAttack(Player $damager, Player $player, Entity $child): bool;

}