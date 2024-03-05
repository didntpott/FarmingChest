<?php

namespace Yookou\FarmingChest;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use Yookou\FarmingChest\bloc\FarmingChest;

class Main extends PluginBase {

    use SingletonTrait;
   public function onEnable(): void {
       self::setInstance($this);
       $this->saveDefaultConfig();

       $this->getServer()->getPluginManager()->registerEvents(new FarmingChest(), $this);
   }

}
