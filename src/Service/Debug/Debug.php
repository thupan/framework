<?php

namespace Service\Debug;

use \PDO;
use \PDOException;
use \Exception;
use \DebugBar;
use \DebugBar\StandardDebugBar;


class Debug extends StandardDebugBar
{
    protected static $instance;
    protected static $url;
    protected static $resources;

    public static function init()
    {
        $config = autoload_config();

        self::setBaseUrl(URL . $config['app']['DEBUGBAR_RESOURCES']);
        self::setBaseDir(DOC_ROOT . $config['app']['DEBUGBAR_RESOURCES']);

        self::$instance = new \DebugBar\StandardDebugBar();

        self::collectorConfig(require DOC_ROOT.'app/Config/App.php', 'config-app');
        self::collectorConfig(require DOC_ROOT.'app/Config/Database.php', 'config-database');
        self::collectorTimeLine('teste', 'Teste de timeline', null);
    }

    public static function setBaseUrl($url)
    {
        self::$url = $url;
    }

    public static function getBaseUrl()
    {
        return self::$url;
    }

    public static function setBaseDir($dir)
    {
        self::$resources = $dir;
    }

    public static function getBaseDir()
    {
        return self::$resources;
    }

    public static function getInstance($key = null)
    {
        return ($key) ? self::$instance[$key] : self::$instance;
    }

    public static function collectorTwig($twig_instance)
    {
        try {
            $env = new DebugBar\Bridge\Twig\TraceableTwigEnvironment($twig_instance, self::getInstance('time'));
            self::getInstance()->addCollector(new DebugBar\Bridge\Twig\TwigCollector($env));
        } catch (Exception $e) {
            self::getInstance('exceptions')->addException($e);
        }
    }

    public static function collectorPDO($pdo_instance)
    {
        if (!self::getInstance()->hasCollector('pdo')) {
            try {
                $pdo = new \DebugBar\DataCollector\PDO\TraceablePDO($pdo_instance);
                self::getInstance()->addCollector(new \DebugBar\DataCollector\PDO\PDOCollector($pdo),  self::getInstance('time'));
            } catch (Exception $e) {
                self::getInstance('exceptions')->addException($e);
            }
        }
    }

    public static function collectorEloquent($eloquent_instance)
    {
        if (!self::getInstance()->hasCollector('eloquent_pdo')) {
            try {
                self::getInstance()->addCollector(new \Helpers\Debugbar\PHPDebugBarEloquentCollector($eloquent_instance, self::getInstance('time')));
            } catch (Exception $e) {
                self::getInstance('exceptions')->addException($e);
            } catch (PDOException $e) {
                self::getInstance('exceptions')->addException($e);
            }
        }
    }

    public static function collectorConfig($data, $name)
    {
        self::getInstance()->addCollector(new DebugBar\DataCollector\ConfigCollector($data, $name));
    }

    public static function collectorTimeLine($name, $desc, $callback)
    {
        if (!self::getInstance()->hasCollector('time')) {
            self::getInstance()->addCollector(new DebugBar\DataCollector\TimeDataCollector());
        }

        // self::getInstance('time')->startMeasure($name, $desc);
        // ($callback) ? $callback() : false;
        // self::getInstance('time')->stopMeasure($name);

        self::getInstance('time')->measure($desc, function () {
            //
        });
    }

    public static function render()
    {
        return self::getInstance()->getJavascriptRenderer(self::$url, self::$resources);
    }
}
