<?php
namespace Signa\Models;

class PriceChangeAlert extends \Phalcon\Mvc\MongoCollection
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

class InPriceChangeAlert extends PriceChangeAlert {
    public function getSource()
    {
        return "pricechangealert";
    }
}
