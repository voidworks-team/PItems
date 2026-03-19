<?php

namespace voidworks\ppitems\items\preset;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use voidworks\ppitems\items\BasePartnerItem;
use voidworks\ppitems\items\impl\OnUsePartnerItem;

final class ResistanceThree extends BasePartnerItem implements OnUsePartnerItem {

    public function __construct() {
        parent::__construct(
            'resistance',
            '&r&eResistance III',
            VanillaItems::IRON_INGOT()
        );
    }

    public function onUse(Player $player): void {
        $effectManager = $player->getEffects();
        $effect = new EffectInstance(VanillaEffects::RESISTANCE(), 20 * 7, 2);

        if ($effectManager->add($effect)) {
            $player->sendMessage(TextFormat::colorize('&r&aYou have received &eResistance III &afor ' . ($effect->getDuration() / 20) . ' seconds!'));
        }
    }
}