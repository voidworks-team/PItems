<?php

namespace voidworks\ppitems\items\etc;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\utils\TextFormat;
use voidworks\ppitems\items\BasePartnerItem;

class DurablePartnerItem extends BasePartnerItem {

    protected int $uses = 5;
    protected const USES_NAME_TAG = "pitem_uses_namedtag";


    public function __construct(string $identifier, string $displayName, Item $item) {
        parent::__construct($identifier, $displayName, $item);
        $this->uses = $this->config->get("uses", 5);
        $this->item->getNamedTag()->setInt(self::USES_NAME_TAG, $this->uses);
    }

    public function pop(Item $item): Item {
        $currentUses = $item->getNamedTag()->getInt(self::USES_NAME_TAG, $this->uses);
        $newUses = $currentUses - 1;
        $item->getNamedTag()->setInt(self::USES_NAME_TAG, $newUses);

        if ($newUses <= 0) {
            return VanillaItems::AIR();
        }

        $lore = $item->getLore();
        foreach ($lore as $i => $line) {
            if (str_contains($line, 'Uses')) {
                $lore[$i] = TextFormat::colorize('&l&4Uses: &r') . $newUses;
            }
        }
        $item->setLore($lore);

        return $item;
    }

    public function getUses(): int {
        return $this->uses;
    }
}