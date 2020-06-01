<?php
namespace Signa\Controllers\Signadens;

use Phalcon\Exception;
use Signa\Helpers\SalesReport as Report;
use Signa\Helpers\Translations as Trans;
use Signa\Models\CodeLedger;
use Signa\Models\CodeTariff;
use Signa\Models\Organisations;

class SalesReportController extends InitController
{
    public function indexAction (){

        $report = null;

        try {
            $post = ($this->request->isPost()) ? $this->request->getPost() : null;
            $report = (is_null($post)) ? null : new Report($this->db, $post);
        }
        catch (Exception $e) {
            var_dump($e->getMessage());
        }
        finally {
            $ledgerCodes = CodeLedger::find();
            $tariffCodes = CodeTariff::find();
            $labs = Organisations::find();
            $this->view->labs = $labs;
            $this->view->ledgerCodes = $ledgerCodes;
            $this->view->tariffCodes = $tariffCodes;
            $this->view->reports = (is_null($report)) ? null : $report->getReport();
        }
    }
}