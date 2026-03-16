<?php

namespace voidworks\ppitems;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use voidworks\ppitems\command\PartnerItemCommand;
use voidworks\ppitems\items\PartnerItemsHandler;
use voidworks\ppitems\listener\BaseListener;
use voidworks\ppitems\session\SessionHandler;

class Loader extends PluginBase {
    use SingletonTrait{
        make as private self;
        reset as private reset;
    }

    public const GLOBAL_COOLDOWN_TAG = "global_cd";

    protected Config $blacklists;

    protected PartnerItemsHandler $partnerItemsHandler;
    protected SessionHandler $sessionHandler;

    protected function onLoad(): void {
        self::setInstance($this);
        $this->saveResource("blacklist.yml");
        $this->blacklists = new Config($this->getDataFolder() . "blacklist.yml");
        $this->partnerItemsHandler = new PartnerItemsHandler($this);
        $this->sessionHandler = new SessionHandler();
    }

    protected function onEnable(): void {
        $this->getServer()->getCommandMap()->registerAll('ppitems', [
            new PartnerItemCommand
        ]);
    }

    protected function onDisable(): void {

    }

    /**
     * @return SessionHandler
     */
    public function getSessionHandler(): SessionHandler {
        return $this->sessionHandler;
    }

    /**
     * @return PartnerItemsHandler
     */
    public function getPartnerItemsHandler(): PartnerItemsHandler {
        return $this->partnerItemsHandler;
    }

    /**
     * @return Config
     */
    public function getBlacklists(): Config {
        return $this->blacklists;
    }
}