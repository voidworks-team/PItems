<?php

namespace voidworks\ppitems\items\preset;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\item\VanillaItems;
use pocketmine\world\particle\HugeExplodeParticle;
use pocketmine\world\sound\ExplodeSound;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use voidworks\ppitems\items\BasePartnerItem;
use voidworks\ppitems\items\impl\OnUsePartnerItem;

class BallOfRage extends BasePartnerItem implements OnUsePartnerItem {

    public function __construct() {
        parent::__construct(
            'ballofrage',
            TextFormat::colorize('&r&6Ball Of Rage'),
            VanillaItems::EGG()
        );
    }

    public function onUse(Player $player): void {
        $item = $player->getInventory()->getItemInHand();
        $effectManager = $player->getEffects();
        
        $effects = [
        new EffectInstance(VanillaEffects::RESISTANCE(), 6 * 20, 2),
        new EffectInstance(VanillaEffects::STRENGTH(), 6 * 20, 1),
        new EffectInstance(VanillaEffects::WITHER(), 20 * 8, 1)
    ];

        $player->getWorld()->addSound(
            $player->getPosition(),
            new ExplodeSound()
        );

        $player->getWorld()->addParticle(
            $player->getPosition(),
            new HugeExplodeParticle()
        );
        
        foreach ($effects as $effect) {
         $effectManager->add($effect);
        }
            $player->sendMessage(TextFormat::colorize('&r&eYou have successfully used &cBall Of Rage'));

            $item->pop();
            $player->getInventory()->setItemInHand($item);

    }
}
