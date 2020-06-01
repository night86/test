<?php
/**
 * @copyright Copyright (c) 2015, Gate51 B.V - All Rights Reserved
 *  Unauthorized copying of this file, via any medium is strictly prohibited
 *  Proprietary and confidential
 * @author Gerhard Kegel <g.kegel@gate51.nl>
 * @filesource
 */
namespace Signa\Libs;

/**
 * imports
 */
use Phalcon\Db\Profiler;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File as FileAdapter;

/**
 * Class Database listener. If enabled the database logs all queries and the the Phalcon\Db\Profiler is used
 * to detect the SQL statements that are taking longer to execute than expected:
 *
 */
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

