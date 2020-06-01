<?php
namespace Signa\Models;

class LogLabPriceChange extends \Phalcon\Mvc\MongoCollection
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

class InLogLabPriceChange extends LogLabPriceChange
{
    public function getSource()
    {
        return "log_lab_price_change";
    }
}
