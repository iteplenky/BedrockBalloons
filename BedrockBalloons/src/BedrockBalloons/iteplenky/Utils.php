<?php

declare(strict_types=1);


namespace BedrockBalloons\iteplenky;


    use BedrockBalloons\iteplenky\Main;

    use pocketmine\Player;
	use pocketmine\entity\Skin;

    class Utils {

        public static function getColor($colors): string
        {
            return $colors[array_rand($colors)];
        }

        public static function makeGeometrySkin(Skin $skin, string $path, string $geometryName, string $color): Skin
        {
            if (!file_exists($path . $geometryName . ".json") or !file_exists($path . $color . ".png"))
                return $skin;
        
            $img = imagecreatefrompng($path . $color . ".png");
            $bytes = "";
            $size = getimagesize($path . $color . ".png")[1];
        
            for ($y = 0; $y < $size; $y ++) {
                for ($x = 0; $x < 64; $x ++) {
                    $colorat = imagecolorat($img, $x, $y);
                    $a = ((~((int) ($colorat >> 24))) << 1) & 0xff;
                    $r = ($colorat >> 16) & 0xff;
                    $g = ($colorat >> 8) & 0xff;
                    $b = $colorat & 0xff;
                    $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
                }
            }
            imagedestroy($img);
            return new Skin($skin->getSkinId(), $bytes, "", "geometry." . $geometryName, file_get_contents($path . $geometryName . ".json"));
        }
    }

?>
