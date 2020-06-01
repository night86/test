<?php
namespace Signa\Models;

class LogSupplierImport extends \Phalcon\Mvc\MongoCollection
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

class InLogSupplierImport extends LogSupplierImport {
    public function getSource()
    {
        return "log_supplier_import";
    }
}
