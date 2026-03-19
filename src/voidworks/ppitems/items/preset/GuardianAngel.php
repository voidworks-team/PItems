<?php

namespace voidworks\ppitems\items\preset;

use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\EventPriority;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use ReflectionException;
use voidworks\ppitems\items\BasePartnerItem;
use voidworks\ppitems\items\impl\OnUsePartnerItem;
use voidworks\ppitems\Loader;
use voidworks\ppitems\utils\TimedListener;
use WeakMap;

final class GuardianAngel extends BasePartnerItem implements OnUsePartnerItem {

    protected WeakMap $players;

    public function __construct() {
        parent::__construct(
            "guardianangel",
            "&r&6Guardian Angel",
            VanillaItems::CLOCK()
        );
        $this->players = new WeakMap();
    }

    /**
     * @throws ReflectionException
     */
    public function onUse(Player $player): void {
        if(isset($this->players[$player])) {
            $player->sendMessage(TextFormat::RED . 'You have already one guardian angel in use!');
            return;
        }

        $this->players[$player] = true;

        $player->sendMessage(TextFormat::GREEN . 'You have activated your guardian angel!');

        TimedListener::listen(Loader::getInstance(), function(EntityDamageEvent $event) use ($player){
            if($event->getEntity() !== $player || !isset($this->players[$player])){
                return;
            }

            if($event->isCancelled()){
                return;
            }

            if($player->getHealth() - $event->getFinalDamage() <= 4){
                $event->cancel();
                $player->setHealth($player->getMaxHealth());
                $player->getEffects()->add(new EffectInstance(VanillaEffects::RESISTANCE(), 20*12, 2, false, false));
                $player->getEffects()->add(new EffectInstance(VanillaEffects::REGENERATION(), 20*12, 2, false, false));
                $player->sendMessage(TextFormat::GREEN . 'Your guardian angel has activated!');
                unset($this->players[$player]);
            }
        },function() use ($player): void {
            if($player->isOnline() && isset($this->players[$player])){
                $player->sendMessage(TextFormat::RED . 'Your guardian angel has expired!');
            }
            unset($this->players[$player]);
        }, EventPriority::HIGHEST, 20*120);
    }
}