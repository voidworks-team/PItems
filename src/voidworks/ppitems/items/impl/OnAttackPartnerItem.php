<?php

namespace voidworks\ppitems\items\impl;

use pocketmine\player\Player;

interface OnAttackPartnerItem extends PartnerItem {

    /**
     * @param Player $damager
     * @param Player $player
     * @return void
     */
    public function onAttack(Player $damager, Player $player): void;

}