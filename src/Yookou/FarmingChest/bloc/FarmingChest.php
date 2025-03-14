<?php

namespace Yookou\FarmingChest\bloc;

use pocketmine\block\BlockTypeIds;
use pocketmine\block\tile\Chest;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use Yookou\FarmingChest\Main;

class FarmingChest implements Listener
{
    public function onUse(PlayerInteractEvent $event): void
    {
        $block = $event->getBlock();
        if ($block->getTypeId() !== BlockTypeIds::TRAPPED_CHEST) {
            return;
        }
        $origin = $block->getPosition();
        $world = $origin->getWorld();
        $chestTile = $world->getTile($origin);
        if (!($chestTile instanceof Chest)) {
            return;
        }

        $config = Main::getInstance()->getConfig();
        $rangeX = $config->getNested("range.x");
        $rangeZ = $config->getNested("range.z");
        $chestFullMessage = $config->get("chest-full-message");

        for ($x = $origin->x - $rangeX; $x <= $origin->x + $rangeX; $x++) {
            for ($z = $origin->z - $rangeZ; $z <= $origin->z + $rangeZ; $z++) {
                $pos = new Vector3($x, $origin->y, $z);
                $currentBlock = $world->getBlockAt($x, $origin->y, $z);

                // POTATOES
                if ($currentBlock->getTypeId() === VanillaBlocks::POTATOES()->getTypeId() && $currentBlock->getAge() === $currentBlock::MAX_AGE) {
                    if ($config->getNested("agriculture.enable-potato", true)) {
                        $item = VanillaItems::POTATO()->setCount(mt_rand(1, 4));
                        if ($chestTile->getInventory()->canAddItem($item)) {
                            $chestTile->getInventory()->addItem($item);
                            $world->setBlock($pos, VanillaBlocks::POTATOES());
                        } else {
                            $event->getPlayer()->sendMessage($chestFullMessage);
                        }
                    }
                } // CARROTS
                elseif ($currentBlock->getTypeId() === BlockTypeIds::CARROTS && $currentBlock->getAge() === $currentBlock::MAX_AGE) {
                    if ($config->getNested("agriculture.enable-carrot", true)) {
                        $item = VanillaItems::CARROT()->setCount(mt_rand(1, 4));
                        if ($chestTile->getInventory()->canAddItem($item)) {
                            $chestTile->getInventory()->addItem($item);
                            $world->setBlock($pos, VanillaBlocks::CARROTS());
                        } else {
                            $event->getPlayer()->sendMessage($chestFullMessage);
                        }
                    }
                } // BEETROOTS
                elseif ($currentBlock->getTypeId() === BlockTypeIds::BEETROOTS && $currentBlock->getAge() === $currentBlock::MAX_AGE) {
                    if ($config->getNested("agriculture.enable-beetroot", true)) {
                        $beetrootItem = VanillaItems::BEETROOT()->setCount(1);
                        $seedItem = VanillaItems::BEETROOT_SEEDS()->setCount(mt_rand(1, 2));
                        if (
                            $chestTile->getInventory()->canAddItem($beetrootItem) &&
                            $chestTile->getInventory()->canAddItem($seedItem)
                        ) {
                            $chestTile->getInventory()->addItem($beetrootItem);
                            $chestTile->getInventory()->addItem($seedItem);
                            $world->setBlock($pos, VanillaBlocks::BEETROOTS());
                        } else {
                            $event->getPlayer()->sendMessage($chestFullMessage);
                        }
                    }
                } // WHEAT
                elseif ($currentBlock->getTypeId() === BlockTypeIds::WHEAT && $currentBlock->getAge() === $currentBlock::MAX_AGE) {
                    if ($config->getNested("agriculture.enable-wheat", true)) {
                        $wheatItem = VanillaItems::WHEAT()->setCount(1);
                        $seedItem = VanillaItems::WHEAT_SEEDS()->setCount(mt_rand(1, 3));
                        if (
                            $chestTile->getInventory()->canAddItem($wheatItem) &&
                            $chestTile->getInventory()->canAddItem($seedItem)
                        ) {
                            $chestTile->getInventory()->addItem($wheatItem);
                            $chestTile->getInventory()->addItem($seedItem);
                            $world->setBlock($pos, VanillaBlocks::WHEAT());
                        } else {
                            $event->getPlayer()->sendMessage($chestFullMessage);
                        }
                    }
                } // SUGAR CANE
                elseif ($currentBlock->getTypeId() === BlockTypeIds::SUGARCANE) {
                    if ($config->getNested("agriculture.enable-sugar-cane", true)) {
                        $caneHeight = 0;
                        $maxHeight = 256;
                        $currentY = $origin->y + 1;
                        while ($currentY < $maxHeight) {
                            $blockAbove = $world->getBlockAt($x, $currentY, $z);
                            if ($blockAbove->getTypeId() === BlockTypeIds::SUGARCANE) {
                                $caneHeight++;
                                $currentY++;
                            } else {
                                break;
                            }
                        }
                        if ($caneHeight > 0) {
                            $dropCount = $caneHeight;
                            $sugarCaneItem = VanillaBlocks::SUGARCANE()->asItem()->setCount($dropCount);
                            if ($chestTile->getInventory()->canAddItem($sugarCaneItem)) {
                                $chestTile->getInventory()->addItem($sugarCaneItem);
                                for ($y = $origin->y + 1; $y < $currentY; $y++) {
                                    $world->setBlockAt($x, $y, $z, VanillaBlocks::AIR());
                                }
                            } else {
                                $event->getPlayer()->sendMessage($chestFullMessage);
                            }
                        }
                    }
                } // BAMBOO
                elseif ($currentBlock->getTypeId() === BlockTypeIds::BAMBOO) {
                    if ($config->getNested("agriculture.enable-bamboo", true)) {
                        $bambooHeight = 0;
                        $maxHeight = 256;
                        $currentY = $origin->y + 1;
                        while ($currentY < $maxHeight) {
                            $blockAbove = $world->getBlockAt($x, $currentY, $z);
                            if ($blockAbove->getTypeId() === BlockTypeIds::BAMBOO) {
                                $bambooHeight++;
                                $currentY++;
                            } else {

                                break;
                            }
                        }
                        if ($bambooHeight > 0) {
                            $dropCount = min(16, mt_rand($bambooHeight, $bambooHeight * 2));
                            $bambooItem = VanillaItems::BAMBOO()->setCount($dropCount);
                            if ($chestTile->getInventory()->canAddItem($bambooItem)) {
                                $chestTile->getInventory()->addItem($bambooItem);
                                for ($y = $origin->y + 1; $y < $currentY; $y++) {
                                    $world->setBlockAt($x, $y, $z, VanillaBlocks::AIR());
                                }
                            } else {
                                $event->getPlayer()->sendMessage($chestFullMessage);
                            }
                        }
                    }
                } // NETHER WART
                elseif ($currentBlock->getTypeId() === BlockTypeIds::NETHER_WART && $currentBlock->getAge() === $currentBlock::MAX_AGE) {
                    if ($config->getNested("agriculture.enable-nether-wart", true)) {
                        $netherWartItem = VanillaBlocks::NETHER_WART()->asItem()->setCount(mt_rand(2, 4));
                        if ($chestTile->getInventory()->canAddItem($netherWartItem)) {
                            $chestTile->getInventory()->addItem($netherWartItem);
                            $world->setBlock($pos, VanillaBlocks::NETHER_WART());
                        } else {
                            $event->getPlayer()->sendMessage($chestFullMessage);
                        }
                    }
                }
            }
        }
    }
}