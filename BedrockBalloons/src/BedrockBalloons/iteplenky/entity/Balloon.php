<?php

declare(strict_types=1);


namespace BedrockBalloons\iteplenky\entity;


    use BedrockBalloons\iteplenky\{Main, Utils};
    use Convert\Utils\ModelConvert;
    
    use pocketmine\{Player, Server};
    use pocketmine\entity\{Entity, Human};
    
    use pocketmine\math\Vector3;

    use pocketmine\nbt\tag\CompoundTag;
    use pocketmine\event\entity\{EntityDamageEvent, EntityDamageByEntityEvent};

    use pocketmine\timings\Timings;

    class Balloon extends Human
    {
        public $height = 0.1;
        public $width = 0.1;

        public $speed = 0;
        private $airTicks = 0;

        public function attack(EntityDamageEvent $source): void
        {
            $source->setCancelled();
            if ($source instanceof EntityDamageByEntityEvent and $source->getDamager() instanceof Player) {

                $damager = $source->getDamager();
                $this->speed = 0.1;
                $this->setMotion($damager->getDirectionVector());
            }
        }
        
        public static function registerMe(): void
        {
            Entity::registerEntity(Balloon::class, true);
        }

        public function onCollideWithPlayer(Player $player): void
        {
            $direction = $player->getDirectionVector();

            if (!$player->onGround) 
                $direction->y = 0.5;

            if (!$player->isSprinting()) 
                $direction->divide(1.5);

            $this->speed = 0.03;
            $this->setMotion($direction);
        }

        public function onUpdate(int $currentTick): bool 
        {
            if ($this->isClosed()) 
                return false;

            $tickDiff = $currentTick - $this->lastUpdate;

            $this->move($this->motion->x, $this->motion->y, $this->motion->z * $tickDiff);
            $this->motion->y += 0.1 * $tickDiff;
            $this->updateMovement();
            
            if(!$this->isOnGround()) {

                $this->airTicks++;

                if ($this->airTicks > 120) 
                    parent::close();
            }
            return parent::onUpdate($currentTick);
        }

        public static function spawnMe($x, $y, $z, $color): void
        {            
            $path = Main::getResourcePath();
            $texture = $path . $color . '.png';

            $skin = ModelConvert::getSkinFromFile($texture);
            
            $geometry = Utils::makeGeometrySkin($skin, $path, 'balloon', $color);
            $nbt = ModelConvert::createEntityBaseNBT(new Vector3($x, $y - 1, $z));
            $npc = ModelConvert::pushCompoundTag($nbt, $geometry);

            $level = Server::getInstance()->getLevelByName('world');

            $entity = ModelConvert::createEntity('Balloon', $level, $nbt);

            $entity->setScale(0.7);
            
            $entity->spawnToAll();
        }
    }

?>
