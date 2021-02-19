<?php

declare(strict_types=1);


namespace BedrockBalloons\iteplenky;


	use BedrockBalloons\iteplenky\entity\Balloon;
	use BedrockBalloons\iteplenky\entity\BunchBalls;
 
	use pocketmine\plugin\PluginBase;
	use pocketmine\math\Vector3;

	use pocketmine\block\Block;
	use pocketmine\entity\Entity;

	use pocketmine\Player;
	use pocketmine\Server;
 
	class Main extends PluginBase 
	{
		private static $instance;

        public function onLoad(): void 
        {
            self::setInstance($this);
		}
	 
	    public function onEnable(): void
	    {
			Balloon::registerMe();
			BunchBalls::registerMe();

			BunchBalls::spawnMe(256, 68, 239);
	    }

	    private static function setInstance(Main $instance): void 
	    {
            self::$instance = $instance;
        }

        public static function getInstance(): Main
        {
            return self::$instance;
        }

        public static function getResourcePath(): string
        {
            return self::getInstance()->getFile() . "/resources/";
        }

        public function onDisable(): void
        {
        	$level = Server::getInstance()->getLevelByName('world');
            foreach ($level->getEntities() as $entity) {
                if ($entity instanceof BunchBalls) {
                	$entity->close();
                }
            }
        }
    }

?>
