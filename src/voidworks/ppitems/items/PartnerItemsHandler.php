<?php

namespace voidworks\ppitems\items;

use pocketmine\item\Item;
use pocketmine\nbt\tag\StringTag;
use pocketmine\utils\AssumptionFailedError;
use ReflectionClass;
use ReflectionException;
use voidworks\ppitems\items\impl\PartnerItem;
use voidworks\ppitems\Loader;

final class PartnerItemsHandler {

    protected const PARTNER_ITEM_NAME_TAG = "pp_item_namedtag";

    protected array $partnerItems = [];

    public function __construct(
        protected Loader $plugin
    ) {
        $this->prepare();
    }

    private function prepare(): void {
        $dir = scandir(__DIR__ . "/preset/");
        if ($dir !== false) {
            foreach ($dir as $file) {
                if (!str_ends_with($file, '.php')) continue;

                $className = basename($file, '.php');
                $fqcn = "voidworks\\ppitems\\items\\preset\\" . $className;

                try {
                    $reflection = new ReflectionClass($fqcn);

                    if ($reflection->isAbstract() || $reflection->isInterface()) {
                        continue;
                    }

                    if ($reflection->getConstructor()?->getNumberOfRequiredParameters() !== 0) {
                        continue;
                    }

                    $instance = new $fqcn();
                    if ($instance instanceof PartnerItem) {
                        $this->register($instance);
                    }
                } catch (ReflectionException $exception) {
                    //pass
                }
            }
        }
    }

    private function register(PartnerItem $partnerItem): void {
        $k = $partnerItem->getIdentifier();

        if ($this->plugin->getBlacklists()->exists($k, true)) {
            return;
        }

        $vanillaItem = $partnerItem->getItem();
        $vanillaItem->getNamedTag()->setString(self::PARTNER_ITEM_NAME_TAG, $k);
        $this->partnerItems[$k] = $partnerItem;
    }

    /**
     * @param Item $item
     * @return PartnerItem|null
     */
    public function getPartnerItem(Item $item): ?PartnerItem {
        if (!($tag = $item->getNamedTag()->getTag(self::PARTNER_ITEM_NAME_TAG)) instanceof StringTag) {
            return null;
        }

        return $this->partnerItems[$tag->getValue()] ?? throw new AssumptionFailedError();
    }

    /**
     * @param string $identifier
     * @return PartnerItem|null
     */
    public function getPartnerItemByIdentifier(string $identifier): ?PartnerItem {
        return $this->partnerItems[$identifier] ?? null;
    }

    /**
     * @return array
     */
    public function getPartnerItems(): array {
        return $this->partnerItems;
    }

}