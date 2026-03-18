<?php

namespace voidworks\ppitems;

use muqsit\invmenu\InvMenuHandler;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use voidworks\ppitems\command\PartnerItemCommand;
use voidworks\ppitems\items\PartnerItemsHandler;
use voidworks\ppitems\listener\BaseListener;
use voidworks\ppitems\session\SessionHandler;

class Loader extends PluginBase {
    use SingletonTrait {
        make as private;
        reset as private;
    }

    public const GLOBAL_COOLDOWN_TAG = "global_cd";

    protected Config $blacklists;

    protected PartnerItemsHandler $partnerItemsHandler;
    protected SessionHandler $sessionHandler;

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

    protected function onLoad(): void {
        self::setInstance($this);
        $this->saveResource("blacklist.yml");
        $this->blacklists = new Config($this->getDataFolder() . "blacklist.yml");
        $this->partnerItemsHandler = new PartnerItemsHandler($this);
        $this->sessionHandler = new SessionHandler();
    }

    protected function onEnable(): void {
        $bootstrap = 'phar://' . Server::getInstance()->getPluginPath() . $this->getName() . '.phar/vendor/autoload.php';

        if (!is_file($bootstrap)) {
            $this->getLogger()->error('Composer autoloader not found at ' . $bootstrap);
            $this->getLogger()->warning('Please install/update Composer dependencies or use provided build.');
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }

        require_once($bootstrap);

        if (!InvMenuHandler::isRegistered()) InvMenuHandler::register($this);

        $this->getServer()->getCommandMap()->registerAll('ppitems', [
            new PartnerItemCommand
        ]);

        (new BaseListener($this));
    }

    protected function onDisable(): void {

    }
}