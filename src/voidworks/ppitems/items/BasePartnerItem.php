<?php


namespace voidworks\ppitems\items;

use pocketmine\item\Item;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use voidworks\ppitems\items\impl\PartnerItem;
use voidworks\ppitems\Loader;

abstract class BasePartnerItem implements PartnerItem {

    protected int $cooldown = 0;
    protected array $lore = [];

    public function __construct(
        protected string $identifier,
        protected string $displayName,
        protected Item   $item
    ) {
        $this->displayName = TextFormat::colorize($this->displayName);
        $dir = Loader::getInstance()->getDataFolder() . "/items";
        if (!is_dir($dir)) {
            mkdir($dir);
        }
        $config = new Config($dir . "/" . $this->identifier . ".yml", Config::YAML);
        $this->cooldown = $config->get("cooldown", 60);
        $this->lore = array_map([TextFormat::class, "colorize"], $config->get("lore", []));
        $this->item->setCustomName($this->displayName);
        $this->item->setLore($this->lore);
    }

    public function getIdentifier(): string {
        return $this->identifier;
    }

    public function getDisplayName(): string {
        return $this->displayName;
    }

    public function getItem(): Item {
        return $this->item;
    }

    public function getCooldown(): int {
        return $this->cooldown;
    }

    public function canApplyLore(): bool {
        return count($this->lore) > 0;
    }
}
