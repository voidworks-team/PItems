<?php

namespace voidworks\ppitems\items\preset;

use pocketmine\event\entity\EntityDamageByChildEntityEvent;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use ReflectionException;
use voidworks\ppitems\items\BasePartnerItem;
use voidworks\ppitems\items\impl\OnAttackPartnerItem;
use voidworks\ppitems\Loader;
use voidworks\ppitems\utils\TimedListener;
use WeakMap;

final class ThiefGloves extends BasePartnerItem implements OnAttackPartnerItem {

    protected WeakMap $players;
    protected $probability = 0;

    public function __construct() {
        parent::__construct(
            "thiefgloves",
            "&r&aThief Gloves",
            VanillaItems::FIRE_CHARGE()
        );
        $this->players = new WeakMap();
        $this->probability = max(1, $this->config->get("probability", 3));
    }

    /**
     * @throws ReflectionException
     */
    public function onAttack(Player $damager, Player $player): void {
        if(isset($this->players[$damager])){
            $player->sendMessage(TextFormat::RED . "You already have one thief glove active!");
            return;
        }

        $this->players[$damager] = true;

        TimedListener::listen(Loader::getInstance(), function(EntityDamageByChildEntityEvent $event) use ($player, $damager){
            if($event->getDamager() !== $damager || $event->getEntity() !== $player){
                return;
            }

            if(mt_rand(1, 100) % $this->probability == 3){
                $item = $player->getInventory()->getHotbarSlotItem(mt_rand(0, 8));
                $player->getInventory()->remove($item);
                $player->dropItem($item);

                $player->sendMessage(TextFormat::RED . "You have been affected by a thief glove and dropped {$item->getName()} from your hotbar!");
            }
        }, onFinish: function() use ($damager): void {
            unset($this->players[$damager]);
        }, delay: 20*10);
    }
}