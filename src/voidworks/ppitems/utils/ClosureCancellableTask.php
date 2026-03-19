<?php

namespace voidworks\ppitems\utils;

use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\Utils as PMUtils;

final class ClosureCancellableTask extends ClosureTask {

    /**
     * @param \Closure $closure
     * @param \Closure $condition
     * If $condition returns true the task will be cancelled
     */
    public function __construct(\Closure $closure, protected \Closure $condition) {
        parent::__construct($closure);
        PMUtils::validateCallableSignature($condition, fn(): bool => false);
    }

    public function onRun(): void {
        if(($this->condition)()){
            throw new CancelTaskException("This task was cancelled by closure");
        }
        parent::onRun();
    }

}