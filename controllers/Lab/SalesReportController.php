<?php
namespace Signa\Controllers\Lab;

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
            $rules = array(sprintf('deleted = 0 AND organisation_id = %s', $this->currentUser->Organisation->getId()));
            $organisation = Organisations::findFirst($this->currentUser->Organisation->getId());
            $ledgerCodes = CodeLedger::find();
            $tariffCodes = CodeTariff::find();
            $labs = Organisations::find();
            $this->view->organisation = $organisation;
            $this->view->ledgerCodes = $ledgerCodes;
            $this->view->tariffCodes = $tariffCodes;
            $this->view->labs = $labs;
            $this->view->reports = (is_null($report)) ? null : $report->getReport();
        }
    }
}