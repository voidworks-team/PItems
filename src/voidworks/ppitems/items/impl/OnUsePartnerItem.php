<?php

namespace voidworks\ppitems\items\impl;

use pocketmine\player\Player;

interface OnUsePartnerItem extends PartnerItem {

    public function onUse(Player $player): void;
}