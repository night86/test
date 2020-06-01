<?php
namespace Signa\Models;

class LogLabOrderStatus extends \Phalcon\Mvc\MongoCollection
{
}

//if (checkEnv() == "development"){
//    class Logs extends \Phalcon\Mvc\MongoCollection
//    {
//    }
//} else {
//    class Logs extends \Phalcon\Mvc\Collection
//    {
//    }
//}

class InLogLabOrderStatus extends LogLabOrderStatus {
    public function getSource()
    {
        return "log_lab_order_status";
    }
}
