<?php

namespace Pechenka\model;

use pocketmine\entity\Skin;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;

class ModelConvert extends PluginBase implements Listener {

    private $skinData;
    private $geometryData;

    public function onEnable() {
        $f = $this->getDataFolder();
        if(!is_dir($f))
            @mkdir($f);
        $this->saveResource('default.json');
        $this->saveResource('defaut.png');

        $this->geometryData = file_get_contents($f . 'default.json');
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        if(!file_exists($f . 'default.yml')){
            $this->skinData = self::fromImage(imagecreatefrompng($f.'default.png'));
            file_put_contents($f . 'default.yml', $this->skinData);
            $this->getLogger()->info("Skin converted success!");
            return true;
        }

        $this->skinData = file_get_contents($f . 'default.yml');
    }

    public function onPlayerJoin(PlayerJoinEvent $event) {
        $player = $event->getPlayer();
        $skin = new Skin("backpack.default", $this->skinData, "", "geometry.backpack.default", $this->geometryData);
        if (!$skin->isValid()) {
            $this->getLogger()->error("Resulting skin of is not valid");
            return true;
        }
        $player->changeSkin($skin, "backpack.default", $player->getSkin()->getGeometryName());
    }

    /**
     * from skinapi
     * @param resource $img
     * @return string
     */
    public static function fromImage($img)
    {
        $bytes = '';
        for ($y = 0; $y < imagesy($img); $y++) {
            for ($x = 0; $x < imagesx($img); $x++) {
                $rgba = @imagecolorat($img, $x, $y);
                $a = ((~((int)($rgba >> 24))) << 1) & 0xff;
                $r = ($rgba >> 16) & 0xff;
                $g = ($rgba >> 8) & 0xff;
                $b = $rgba & 0xff;
                $bytes .= chr($r) . chr($g) . chr($b) . chr($a);
            }
        }
        @imagedestroy($img);
        return $bytes;
    }
}