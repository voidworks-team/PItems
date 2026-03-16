<?php

namespace voidworks\ppitems\session;

use pocketmine\player\Player;
use pocketmine\Server;
use voidworks\ppitems\items\impl\PartnerItem;
use voidworks\ppitems\Loader;

final class Session {

    protected Loader $loader;

    protected array $itemsCoodowns = [];
    protected int $globalCooldown = 0;

    public function __construct (
        protected Player $player
    ) {
        $this->loader = $player->getServer()->getPluginManager()->getPlugin("PartnerItems");
    }

    /**
     * @return bool
     */
    public function hasGlobalCooldown(): bool {
        return $this->globalCooldown > time();
    }

    public function hasCooldown(PartnerItem $item): bool {
        return ($this->itemsCoodowns[$item->getIdentifier()] ??= 0) >= time();
    }

    /**
     * @return array
     */
    public function getItemsCoodowns(): array {
        return $this->itemsCoodowns;
    }

    /**
     * @param PartnerItem $item
     * @return void
     */
    public function applyCooldowns(PartnerItem $item): void {
        $this->globalCooldown = ($now = time()) + $this->loader->getConfig()->get(Loader::GLOBAL_COOLDOWN_TAG, 15);
        $this->itemsCoodowns[$item->getIdentifier()] = $now + $item->getCooldown();
    }
}