<?php

namespace voidworks\ppitems\session;

use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use RuntimeException;
use voidworks\ppitems\items\impl\PartnerItem;
use voidworks\ppitems\Loader;
use voidworks\ppitems\utils\StringToTimeTranslator;

final class Session {

    protected Plugin $loader;

    protected array $itemsCooldowns = [];
    protected int $globalCooldown = 0;

    public function __construct(
        protected Player $player
    ) {
        $this->loader = $player->getServer()->getPluginManager()->getPlugin("PartnerItems") ?? throw new RuntimeException();
    }

    /**
     * @return bool
     */
    public function hasGlobalCooldown(): bool {
        return $this->globalCooldown > time();
    }

    public function hasCooldown(PartnerItem $item): bool {
        return ($this->itemsCooldowns[$item->getIdentifier()] ??= 0) >= time();
    }

    /**
     * @return int
     */
    public function getGlobalCooldown(): int {
        return $this->globalCooldown;
    }

    /**
     * @return array
     */
    public function getItemsCooldowns(): array {
        return $this->itemsCooldowns;
    }

    /**
     * @param PartnerItem $item
     * @return void
     */
    public function applyCooldowns(PartnerItem $item): void {
        $this->globalCooldown = ($now = time()) + $this->loader->getConfig()->get(Loader::GLOBAL_COOLDOWN_TAG, 15);
        $this->itemsCooldowns[$item->getIdentifier()] = $now + $item->getCooldown();
    }

    public function getCooldown(PartnerItem $item): int {
        return $this->itemsCooldowns[$item->getIdentifier()] ??= 0;
    }

    public function formatToTime(int $cooldown): string {
        return StringToTimeTranslator::format($cooldown);
    }
}