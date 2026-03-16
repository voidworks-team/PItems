<?php

namespace voidworks\ppitems\session;

use AllowDynamicProperties;
use pocketmine\player\Player;
use WeakMap;

#[AllowDynamicProperties]
final class SessionHandler {

    protected \WeakMap $sessions;

    public function __construct() {
        $this->sessions = new WeakMap();
    }

    public function getSession(Player $player): Session {
        return $this->sessions[$player] ??= new Session($player);
    }
}