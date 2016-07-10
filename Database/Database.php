<?php

namespace Database;

use \Service\Session;
use \Database\Driver\Oci;
use \Database\Driver\Mysql;

class Database
{
    protected static $config = [];
    protected $connections = [];

    public function __construct()
    {
        self::$config = autoload_config();
        return $this;
    }

    public function getInstance($name = null)
    {
        if (!$this->connections) {
            $this->setConnections();
        }

        !$name ? $name = self::$config['database']['DB_DEFAULT_CONN'] : false;

        return $this->connections[$name];
    }

    public function getConnections($name = null)
    {
        return $name ? $this->connections[$name] : $this->connections;
    }

    public function setConnections()
    {
        foreach (self::$config['database']['connections'] as $environment => $connections) {
            $DB_DEFAULT_ENV = Session::get('s_environment') ? Session::get('s_environment') : self::$config['database']['DB_DEFAULT_ENV'];

            if ($DB_DEFAULT_ENV === $environment) {
                foreach ($connections as $instance) {
                    if ($instance['autoload']) {
                        if (self::$config['database']['DB_AUTH'] && self::$config['database']['DB_DEFAULT_CONN'] === $instance['connection']) {
                            $username = Session::get('s_username') ? Session::get('s_username') : false;
                            $password = Session::get('s_password') ? base64_decode(Session::get('s_password')) : false;
                        } else {
                            $username = $instance['username'];
                            $password = $instance['password'];
                        }

                        switch ($instance['driver']) {
                            case 'oci':
                                $this->connections[$instance['connection']] = new \Database\Driver\Oci($instance['connection'], $instance['database'], $instance['host'], $instance['port'], $username, $password);
                            break;

                            case 'mysql':
                                $this->connections[$instance['connection']] = new \Database\Driver\Mysql($instance['connection'], $instance['database'], $instance['host'], $instance['port'], $username, $password);
                            break;
                        }
                    }
                }
            }
        }

        return $this;
    }
}
