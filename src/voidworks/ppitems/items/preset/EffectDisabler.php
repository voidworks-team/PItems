<?php

namespace voidworks\ppitems\items\preset;

use pocketmine\event\entity\EntityEffectAddEvent;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use ReflectionException;
use voidworks\ppitems\items\BasePartnerItem;
use voidworks\ppitems\items\impl\OnAttackPartnerItem;
use voidworks\ppitems\Loader;
use voidworks\ppitems\utils\TimedListener;

final class EffectDisabler extends BasePartnerItem implements OnAttackPartnerItem {

    public function __construct() {
        parent::__construct(
            "effectdisabler",
            "&r&aEffect Disabler",
            VanillaItems::SLIMEBALL()
        );
    }

    /**
     * @throws ReflectionException
     */
    public function onAttack(Player $damager, Player $player): void {
        $effects = $player->getEffects();

        if(count($effects->all()) === 0){
            $player->sendMessage(TextFormat::RED . 'Player has no effects to remove');
            return;
        }

        $player->sendMessage(TextFormat::RED . "The player {$damager->getName()} just removed your effects!");
        $damager->sendMessage(TextFormat::GREEN . "You just removed all the effects from {$player->getName()}!");

        TimedListener::listen(Loader::getInstance(), function(EntityEffectAddEvent $event) use ($player) : void {
            if($event->getEntity() === $player){
                $event->cancel();
            }
        }, function (): void {}, delay: 20 * 8);

        $effects->clear();
    }
}