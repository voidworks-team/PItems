<?php

namespace voidworks\ppitems\items\preset;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use voidworks\ppitems\items\BasePartnerItem;
use voidworks\ppitems\items\impl\OnUsePartnerItem;

class StrengthTwo extends BasePartnerItem implements OnUsePartnerItem {

    public function __construct() {
        parent::__construct(
            'strength',
            TextFormat::colorize('&r&cStrength II'),
            VanillaItems::BLAZE_POWDER()
        );
    }

    public function onUse(Player $player): void {
        $effectManager = $player->getEffects();
        $effect = new EffectInstance(VanillaEffects::STRENGTH(), 20 * 7, 1);

        if ($effectManager->add($effect)) {
            $player->sendMessage(TextFormat::colorize('&r&aYou have received &eStrength II &afor ' . ($effect->getDuration() / 20) . ' seconds!'));

        }
    }
}