<?php
/**
 * Created by PhpStorm.
 * User: Bartek
 * Date: 02.08.2016
 * Time: 11:56
 */

namespace Signa\Helpers;

use Phalcon\Http\Client\Exception;
use Signa\Models\CodeLedger;
use Signa\Models\CodeTariff;
use Signa\Models\ImportMaps;

class Importcode
{
    protected $type;
    protected $headers;
    protected $rows;
    protected $commencingDate;
    protected $map;
    protected $mappedRows;
    protected $fileName;

    public function __construct($type, $headers, $rows, $commencingDate, $fileName)
    {
        $this->type = $type;
        $this->headers = serialize($headers);
        $this->rows = serialize($rows);
        $this->commencingDate = $commencingDate;
        $this->fileName = $fileName;
    }

    public function assignRowsToMap(array $importColumnNames)
    {
        $rows = $this->getRows();
        $mapHeaders = $this->getMap();
        $newRowsArr = array();

        foreach ($rows as $keyRow => $row)
        {
            foreach ($importColumnNames as $keyImportColumnName => $importColumnName)
            {
                $newRowsArr[$keyRow][$importColumnName['name']] = null;
                foreach ($mapHeaders as $keyMapHeader => $mapHeader)
                {
                    if($mapHeader == $importColumnName['id']){
                        $newRowsArr[$keyRow][$importColumnName['name']] = $row[$keyMapHeader];
                    }
                }
            }
            // Validate product if is ok

            if($this->validateImport($newRowsArr[$keyRow]) != null){
                $newRowsArr[$keyRow]['status_array'] = $this->validateImport($newRowsArr[$keyRow]);
                $newRowsArr[$keyRow]['status'] = $this->errorsToString($this->validateImport($newRowsArr[$keyRow]));
            } else {
                $newRowsArr[$keyRow]['status'] = array();
            }

        }

        $this->setMappedRows($newRowsArr);
    }

    private function validateImport(array $row)
    {
        $errorArr = array();
        if($this->getType() == 'ledger')
        {
            foreach ($row as $key => $value)
            {
                switch ($key)
                {
                    case 'code':
                        if($value === null){ array_push($errorArr, 'Code can not be empty.'); }
                        break;
                    case 'description':
                        if($value === null){ array_push($errorArr, 'Description can not be empty.'); }
                        break;
                    case 'group_type':
                        if($value === null){ array_push($errorArr, 'Group type can not be empty.'); }
                        break;
                    case 'balance_type':
                        if($value === null){ array_push($errorArr, 'Balance type can not be empty.'); }
                        break;
                    case 'balance_side':
                        if($value === null){ array_push($errorArr, 'Balance side can not be empty.'); }
                        break;
                }
            }

        }elseif($this->getType() == 'tariff')
        {
            foreach ($row as $key => $value)
            {
                switch ($key)
                {
                    case 'code':
                        if($value === null){ array_push($errorArr, 'Code can not be empty.'); }
                        break;
                    case 'description':
                        if($value === null){ array_push($errorArr, 'Description can not be empty.'); }
                        break;
                    case 'price':
                        if($value === null){ array_push($errorArr, 'Price can not be empty.'); }
                        break;
                }
            }
        }
        if(count($errorArr))
//            $errorArr = $this->errorsToString($errorArr);

        return $errorArr;
    }

    private function errorsToString(array $errors)
    {
        $errorString = '';
        foreach ($errors as $error)
        {
            $errorString .= $error.'<br />';
        }
        return $errorString;
    }

    public static function mapUniqueValidation(array $map)
    {
        foreach ($map as $key => $value)
        {
            if($value == 0){
                unset($map[$key]);
            }
        }
        if((count($map) !== count(array_unique($map))) || count($map) === 0)
            return true;

        return false;
    }

    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public static function beginImport(array $codes, array $excludedCodes, $type)
    {
        $codesAdded = array();
        // Remove excluded products from products list and import new
        foreach ($codes as $key => &$code)
        {
            if(!in_array($key, $excludedCodes))
            {
                $code['added_type'] = 2;

                $user = \Phalcon\DI::getDefault()->getSession()->get('auth');

                if($type == 'tariff')
                {
                    $clean = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $code['code']);
                    $tariff = CodeTariff::findFirst([
                        'code LIKE "%'.$clean.'%" AND organisation_id = :organisation_id:',
                        'bind' => [
                            'organisation_id' => $user->organisation_id
                        ]
                    ]);

                    if($tariff === false)
                    {
                        $tariff = new CodeTariff();
                    }
                    $tariff->setActive(1);
                    $tariff->setDatas($code);
                }elseif($type == 'ledger')
                {
                    $clean = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $code['code']);

                    $ledger = CodeLedger::findFirst([
                        'code LIKE "%'.$clean.'%" AND organisation_id = :organisation_id:',
                        'bind' => [
                            'organisation_id' => $user->organisation_id
                        ]
                    ]);

                    if($ledger === false)
                    {
                        $ledger = new CodeLedger();
                    }
                    $ledger->setActive(1);
                    $ledger->setDatas($code);
                }

                $codesAdded[] = $code;
            }
        }

        return $codesAdded;
    }

    public static function saveMap(array $map, $fileName)
    {
        $di = \Phalcon\DI::getDefault();
        $user = $di->getSession()->get('auth');
        $organisationId = $user->Organisation->getId();
        $importMap = ImportMaps::findFirst('organisation_id = '.$organisationId.' AND file LIKE \''.$fileName.'\'');
        $data = array('file' => $fileName, 'organisation_id' => $organisationId, 'map' => $map);

        if($importMap === false)
        {
            $importMap = new ImportMaps();
        }
        $saved = $importMap->saveData($data);
    }


    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getHeaders()
    {
        return unserialize($this->headers);
    }

    /**
     * @return string
     */
    public function getRows()
    {
        return unserialize($this->rows);
    }

    /**
     * @return mixed
     */
    public function getCommencingDate()
    {
        return $this->commencingDate;
    }

    /**
     * @return mixed
     */
    public function getMap()
    {
        return unserialize($this->map);
    }

    /**
     * @param mixed $map
     */
    public function setMap($map)
    {
        $this->map = serialize($map);
    }

    /**
     * @return mixed
     */
    public function getMappedRows()
    {
        return unserialize($this->mappedRows);
    }

    /**
     * @param mixed $mappedRows
     */
    public function setMappedRows($mappedRows)
    {
        $this->mappedRows = serialize($mappedRows);
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param mixed $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

}