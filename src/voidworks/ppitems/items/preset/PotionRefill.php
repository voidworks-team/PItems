<?php

namespace voidworks\ppitems\items\preset;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\PotionType;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use voidworks\ppitems\items\BasePartnerItem;
use voidworks\ppitems\items\impl\OnUsePartnerItem;

final class PotionRefill extends BasePartnerItem implements OnUsePartnerItem {

    public function __construct() {
        parent::__construct(
            'potionRefill',
            '&r&bPotion Refill',
            VanillaItems::POTION()
        );
    }

    public function onUse(Player $player): void {
        $inventory = $player->getInventory();

        foreach ($inventory->getContents(true) as $slot => $content) {
            if ($content->isNull()) {
                $inventory->setItem($slot, VanillaItems::SPLASH_POTION()->setType(PotionType::STRONG_HEALING));
            }
        }

        $player->sendMessage(TextFormat::GREEN . 'Your inventory has been refilled!');
    }
}