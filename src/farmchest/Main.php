<?php

namespace farmchest;

use farmchest\bloc\FarmingChest;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase {

    use SingletonTrait;
   public function onEnable(): void {
       self::setInstance($this);
       $this->saveDefaultConfig();

       $this->getServer()->getPluginManager()->registerEvents(new FarmingChest(), $this);
   }

}
