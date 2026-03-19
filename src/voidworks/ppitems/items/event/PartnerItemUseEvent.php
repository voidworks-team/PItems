<?php

namespace voidworks\ppitems\items\event;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\Event;
use pocketmine\player\Player;
use voidworks\ppitems\items\impl\PartnerItem;

class PartnerItemUseEvent extends Event implements Cancellable {
    use CancellableTrait;

    protected ?string $cancelContext = null;

    public function __construct(
        protected Player      $player,
        protected PartnerItem $item
    ) {

    }

    /**
     * @return Player
     */
    public function getPlayer(): Player {
        return $this->player;
    }

    /**
     * @return PartnerItem
     */
    public function getPartnerItem(): PartnerItem {
        return $this->item;
    }

    /**
     * @return string|null
     */
    public function getCancelContext(): ?string {
        return $this->isCancelled() ? $this->cancelContext : null;
    }

    /**
     * @param string $cancelContext
     */
    public function setCancelContext(string $cancelContext): void {
        $this->cancelContext = $cancelContext;
    }


}