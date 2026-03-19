<?php

namespace voidworks\ppitems\items\preset;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Entity;
use pocketmine\entity\projectile\Egg;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use pocketmine\world\particle\HugeExplodeParticle;
use pocketmine\world\sound\ExplodeSound;
use voidworks\ppitems\items\BasePartnerItem;
use voidworks\ppitems\items\impl\OnChildAttackPartnerItem;
use voidworks\ppitems\items\impl\OnUsePartnerItem;

final class BallOfRage extends BasePartnerItem implements OnChildAttackPartnerItem, OnUsePartnerItem {

    public function __construct() {
        parent::__construct(
            'ballofrage',
            TextFormat::colorize('&r&6Ball Of Rage'),
            VanillaItems::EGG()
        );
    }

    public function onUse(Player $player): void {
        $effectManager = $player->getEffects();

        $player->getWorld()->addSound(
            $player->getPosition(),
            new ExplodeSound()
        );

        $player->getWorld()->addParticle(
            $player->getPosition(),
            new HugeExplodeParticle()
        );

        $effectManager->add(new EffectInstance(VanillaEffects::RESISTANCE(), 6 * 20, 2));
        $effectManager->add(new EffectInstance(VanillaEffects::STRENGTH(), 6 * 20, 1));

        $player->sendMessage(TextFormat::colorize('&r&eYou have successfully used &cBall Of Rage'));
    }

    public function onChildAttack(Player $damager, Player $player, Entity $child): bool {
        if (!$child instanceof Egg) {
            return false;
        }

        $effectManager = $player->getEffects();
        $effectManager->add(new EffectInstance(VanillaEffects::WITHER(), 20 * 8, 1));
        return true;
    }
}
