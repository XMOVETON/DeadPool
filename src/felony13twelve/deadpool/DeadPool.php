<?php

namespace felony13twelve\deadpool;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

use felony13twelve\deadpool\command\DPCommand;
use felony13twelve\deadpool\utils\EconomyAPI;

class DeadPool extends PluginBase {

    const KEY_ALGO = "haval160,4";

    /** @var DeadPool */
    private static $instance;

    /** @var $items[] */
    public $items;

    /** @var $messages[] */
    public $messages;

    /** @var $settings[] */
    public $settings;

    /** @var EconomyAPI */
    public $economy;

    public function onLoad () {
        self::$instance = & $this;
    }

    public function onEnable () {
        $f = $this->getDataFolder();
        if (!(is_dir($f))) {
            @mkdir($f);
        }

        $this->saveResource('items.yml');
        $this->saveResource('messages.yml');
        $this->saveResource('settings.yml');

        $this->items = (new Config($f . 'items.yml', Config::YAML))->getAll()['Items'];
        $this->messages = (new Config($f . 'messages.yml', Config::YAML))->getAll()['Messages'];
        $this->settings = (new Config($f . 'settings.yml', Config::YAML))->getAll()['Settings'];

        // generate random key
        $ctx = hash_init(self::KEY_ALGO);
        hash_update($ctx, rand(0, getrandmax()));

        $this->getServer()->getCommandMap()->register(hash_final($ctx), new DPCommand('dp'));
        $this->getServer()->getPluginManager()->registerEvents(new EventHandler(), $this);

        $this->economy = new EconomyAPI($this);
    }

    /**
     * @param string $key
     * @param array $args
     * @return string
     */
    public function getMessage ($key, array $args = []) {
        $message = implode("\n", $this->messages[$key]);

        foreach ($args as $arg => $value) {
            $message = str_replace('{' . $arg . '}', $value, $message);
        }

        return $message;
    }

    /**
     * @return DeadPool
     */
    public static function getInstance () : DeadPool {
        return self::$instance;
    }
}