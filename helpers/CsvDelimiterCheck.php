<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 28.03.17
 * Time: 16:24
 */

namespace Signa\Helpers;


class CsvDelimiterCheck
{
    public $delimiter;

    public function __construct($testFile)
    {
        $res = [];
        $testString = fread(fopen($testFile, 'r'), 100);
        $res['semicolon'] = $this->checkSemicolon($testString);
        $res['comma'] = $this->checkComma($testString);
        $res['tab'] = $this->checkTab($testString);
        arsort($res);
        reset($res);
        $this->delimiter = key($res);

    }

    private function checkSemicolon($testString)
    {
        return count(explode(';', $testString));

    }

    private function checkComma($testString)
    {
        return count(explode(',', $testString));

    }

    private function checkTab($testString)
    {
        return count(explode('\t', $testString));

    }
}