<?php


namespace voidworks\ppitems\items\impl;

use pocketmine\item\Item;

interface PartnerItem {

    public function getCooldown(): int;

    public function getIdentifier(): string;

    public function getDisplayName(): string;

    public function getItem(): Item;

    public function collectGarbage(): void;
}