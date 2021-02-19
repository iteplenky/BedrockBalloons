<?php

declare(strict_types=1);


namespace BedrockBalloons\iteplenky\entity;


    use BedrockBalloons\iteplenky\{Main, Utils};
    use BedrockBalloons\iteplenky\entity\Balloon;
    use Convert\Utils\ModelConvert;

    use pocketmine\{Player, Server};
    use pocketmine\entity\{Entity, Human};

    use pocketmine\math\Vector3;

    use pocketmine\nbt\tag\CompoundTag;
    use pocketmine\event\entity\{EntityDamageEvent, EntityDamageByEntityEvent};

    use pocketmine\timings\Timings;

    class BunchBalls extends Human
    {
        public $height = 1.2;
        public $width = 0.3;

        private $colors = ['red', 'green', 'blue'];

        public function attack(EntityDamageEvent $source): void
        {
            $source->setCancelled();
            if ($source instanceof EntityDamageByEntityEvent and $source->getDamager() instanceof Player) {

                $color = Utils::getColor($this->colors);

                $damager = $source->getDamager();
                $entity = $source->getEntity();

                $x = $entity->getX();
                $y = $entity->getY();
                $z = $entity->getZ();

                Balloon::spawnMe($x, $y, $z, $color);
            }
        }

        public static function registerMe(): void
        {
            Entity::registerEntity(BunchBalls::class, true);
        }

        public function onCollideWithPlayer(Player $player): void
        {
            $x = $player->getX();
            $z = $player->getZ();

            $player->knockBack($player, 0, $x, $z, 0.3);

            $random = mt_rand(1, 5);

            if ($random == 1) {
        	    $y = $player->getY() + 1;
	            $color = Utils::getColor($this->colors);
    	        Balloon::spawnMe($x, $y, $z, $color);
            }
        }

        public static function spawnMe($x, $y, $z): void
        {            
            $path = Main::getResourcePath();
            $texture = $path . 'stick.png';

            $skin = ModelConvert::getSkinFromFile($texture);
            
            $geometry = ModelConvert::makeGeometrySkin($skin, $path, 'stick');
            $nbt = ModelConvert::createEntityBaseNBT(new Vector3($x, $y, $z));
            $npc = ModelConvert::pushCompoundTag($nbt, $geometry);

            $level = Server::getInstance()->getLevelByName('world');

            $entity = ModelConvert::createEntity('BunchBalls', $level, $nbt);

            $entity->spawnToAll();
        }
    }

?>
