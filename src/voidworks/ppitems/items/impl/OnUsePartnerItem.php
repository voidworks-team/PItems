<?php

namespace voidworks\ppitems\items\impl;

use pocketmine\player\Player;

interface OnUsePartnerItem extends PartnerItem {

    /**
     * @param Player $player
     * @return void
     */
    public function onUse(Player $player): void;
}