<?php

namespace voidworks\ppitems\command;


use abstractplugin\command\BaseCommand;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\transaction\DeterministicInvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransaction;
use muqsit\invmenu\transaction\InvMenuTransactionResult;
use muqsit\invmenu\type\InvMenuTypeIds;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;
use voidworks\ppitems\Loader;

final class PartnerItemCommand extends BaseCommand {

    public function __construct() {
        parent::__construct('pitems', 'Partner item command description.');

        $this->setPermission($this->getPermission());

        $this->registerParent(new parent\GiveArgument);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void {
        if (!$sender instanceof Player) {
            $sender->sendMessage(TextFormat::RED . 'You must be player for execute this command!');

            return;
        }

        if (count($args) === 0) {
            $invMenu = InvMenu::create(InvMenuTypeIds::TYPE_DOUBLE_CHEST);
            $handler = Loader::getInstance()->getPartnerItemsHandler();

            foreach ($handler->getPartnerItems() as $pItem) {
                $invMenu->getInventory()->addItem($pItem->getItem());
            }

            $invMenu->setListener(InvMenu::readonly(function (DeterministicInvMenuTransaction $transaction) use ($handler) : void{
                $player = $transaction->getPlayer();
                $itemClicked = $transaction->getItemClicked();

                if ($handler->getPartnerItem($itemClicked) === null) {
                    return;
                }

                if ($player->getServer()->isOp($player->getName())) {
                    if($player->getInventory()->canAddItem($itemClicked)){
                        $player->getInventory()->addItem($itemClicked);
                    } else {
                        $player->dropItem($itemClicked);
                    }
                }
            }));

            $invMenu->send($sender);

            return;
        }

        parent::execute($sender, $commandLabel, $args);
    }

    public function getPermission(): ?string {
        return DefaultPermissions::ROOT_USER;
    }
}