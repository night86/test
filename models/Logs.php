<?php
namespace Signa\Models;

class Logs extends \Phalcon\Mvc\MongoCollection
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

class InLogs extends Logs {
    public function getSource()
    {
        return "logs";
    }
}
