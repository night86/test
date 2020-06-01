<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 16.05.17
 * Time: 12:55
 */

namespace Signa\Helpers;


class SalesReport
{
    private $report = null;
    private $reportAllCodes = false;
    private $fromDate;
    private $toDate;
    private $codeTypes;
    private $lab;
    private $codes = [];
    public function __construct($db, $params, $labId = null)
    {
        $this->fromDate = (key_exists('from', $params)) ? new \DateTime($params['from']) : new \DateTime('now');
        $this->toDate = (key_exists('to', $params)) ? new \DateTime($params['to']) : new \DateTime('now');
        $this->codeTypes = (key_exists('code', $params)) ? $params['code'] : null;
        $allCodes = (key_exists('allcodes', $params)) ? $params['allcodes'] : null;
        $this->lab = (key_exists('lab', $params)) ? $params['lab'] : null;

        if (!is_null($allCodes)) {
            $tmp = explode("×", $allCodes);
            foreach ($tmp as $k) {
                $this->reportAllCodes = ($k == 'All') ? true : false;
                if ('' === $k || $this->reportAllCodes) continue;
                $this->codes[] = $k;
            }
        }

        $this
            -> initReport()
            -> setReport()
            ;
    }
    public function initReport ()
    {
          return $this;
    }
    public function getReport()
    {
        return $this->report;
    }

    public function setReport()
    {
        $report = new \stdClass();
        $report->periode = $this->fromDate->format('Y-m-d') .' - ' . $this->toDate->format('Y-m-d');
        $report->tariff = 'code';
        $report->amount = 1;
        $report->value = 100;
        $this->report[] = $report;

        return $this;
    }

}