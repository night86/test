<?php
/**
 * Created by PhpStorm.
 * User: Bartek
 * Date: 14.07.2016
 * Time: 16:01
 */

namespace Signa\Helpers;

/**
 * imports
 */
use Phalcon\Db\Profiler;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File as FileAdapter;


class DatabaseListener
{
    /**
     * Profiler
     * @var Profiler
     */
    protected $_profiler;

    /**
     * File log
     * @var FileAdapter
     */
    protected $_logger;

    /**
     * Creates the profiler and starts the logging
     */
    public function __construct()
    {
        $this->_profiler = new Profiler();
        $this->_logger   = new FileAdapter(APP_PATH."logs/test.log");

        $this->_logger->log('################################################################', Logger::INFO);
        $this->_logger->log('# start request', Logger::INFO);
        $this->_logger->log('################################################################', Logger::INFO);
    }

    /**
     * Handler for the event 'beforeQuery'
     */
    public function beforeQuery($event, $connection)
    {
        $this->_profiler->startProfile($connection->getSQLStatement());
    }

    /**
     * Handler for the event 'afterQuery'
     *
     * @param $event
     * @param \Phalcon\Db\Adapter\Pdo\Mysql $connection
     */
    public function afterQuery($event, \Phalcon\Db\Adapter\Pdo\Mysql $connection)
    {
        $this->_logger->log($connection->getSQLStatement(), Logger::INFO);
        $this->_profiler->stopProfile();
    }

    public function getProfiler()
    {
        return $this->_profiler;
    }


    /**
     * Handler for the event 'afterConnect'
     */
    public function afterConnect()
    {

    }
}