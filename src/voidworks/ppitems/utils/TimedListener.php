<?php

namespace voidworks\ppitems\utils;

use Closure;
use pocketmine\event\Event;
use pocketmine\event\EventPriority;
use pocketmine\event\HandlerListManager;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Utils as PMUtils;
use ReflectionException;
use ReflectionFunction;

final class TimedListener {

    private function __construct(){
        // NOOP
    }

    /**
     * @param PluginBase $plugin
     * @param Closure $callback
     * @param Closure|null $onFinish
     * @param int $priority
     * @param int $delay
     * @return void
     *  Creates a temporal listener which will be unregister at the given time
     * @throws ReflectionException
     */
    static public function listen(PluginBase $plugin, Closure $callback, ?Closure $onFinish = null, int $priority = EventPriority::NORMAL, int $delay = 10): void {
        PMUtils::validateCallableSignature($callback, function (Event $event): void {});

        if ($onFinish !== null) {
            PMUtils::validateCallableSignature($onFinish, function (): void {});
        }

        $eventClass = (new ReflectionFunction($callback))->getParameters()[0]->getType()->getName();

        /**
         * having a registered listener has more impact on performance
         * {@see HandlerList::unregister()}
         */
        $registeredListener = $plugin->getServer()->getPluginManager()->registerEvent($eventClass, $callback, $priority, $plugin);

        $plugin->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($eventClass, $registeredListener, $onFinish): void {
            HandlerListManager::global()->getListFor($eventClass)->unregister($registeredListener);

            if ($onFinish !== null) {
                ($onFinish)();
            }
        }), $delay);
    }
}