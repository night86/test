<?php
namespace Signa\Models;

class Purchase extends \Phalcon\Mvc\MongoCollection
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

class InPurchase extends Purchase {
    public function getSource()
    {
        return "purchase";
    }
}
