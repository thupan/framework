<?php

namespace Service\Debugbar;

use \DebugBar\DataCollector\TimeDataCollector;

class PHPDebugBarEloquentCollector extends \DebugBar\DataCollector\PDO\PDOCollector
{
    protected $timeDataCollector;
    protected static $eloquent_instance;

    public function __construct($eloquent_instance, TimeDataCollector $timeDataCollector = null)
    {
        parent::__construct();
        self::$eloquent_instance = $eloquent_instance;
        $this->timeDataCollector = $timeDataCollector;
        $this->addConnection($this->getTraceablePdo(), 'Eloquent PDO');
    }

    public function getTimeDataCollector()
    {
        return $this->timeDataCollector;
    }

    /**
     * @return Illuminate\Database\Capsule\Manager;
     */
    protected function getEloquentCapsule()
    {
        // ... Return your Illuminate\Database\Capsule\Manager instance here...
        return self::$eloquent_instance;
    }

    /**
     * @return PDO
     */
    protected function getEloquentPdo()
    {
        return $this->getEloquentCapsule()->getConnection()->getPdo();
    }

    /**
     * @return \DebugBar\DataCollector\PDO\TraceablePDO
     */
    protected function getTraceablePdo()
    {
        return new \DebugBar\DataCollector\PDO\TraceablePDO($this->getEloquentPdo());
    }

    // Override
    public function getName()
    {
        return 'eloquent_pdo';
    }

    // Override
    public function getWidgets()
    {
        return array(
            'eloquent' => array(
                'icon' => 'inbox',
                'widget' => 'PhpDebugBar.Widgets.SQLQueriesWidget',
                'map' => 'eloquent_pdo',
                'default' => '[]',
            ),
            'eloquent:badge' => array(
                'map' => 'eloquent_pdo.nb_statements',
                'default' => 0,
            ),
        );
    }
}
