<?php
/**
 * Created by PhpStorm.
 * User: Bartek
 * Date: 15.07.2016
 * Time: 12:13
 */

namespace Signa\Models;

use Phalcon\Mvc\Model;
use Signa\Helpers\Date;

class ImportMaps extends Model
{
    protected $id;
    protected $file;
    protected $organisation_id;
    protected $map;

    public function initialize()
    {
        $this->belongsTo('organisation_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Organisation'));
    }

    public function saveData($dataArr)
    {
        if(isset($dataArr['file']) && !is_null($dataArr['file']))
            $this->setFile($dataArr['file']);
        if(isset($dataArr['organisation_id']) && !is_null($dataArr['organisation_id']))
            $this->setOrganisationId($dataArr['organisation_id']);
        if(isset($dataArr['map']) && !is_null($dataArr['map']))
            $this->setMap($dataArr['map']);

        return $this->save();
    }

    public function getMapByFile($file)
    {
        $user = $this->getDI()->getSession()->get('auth');
        $map = self::findFirst('organisation_id = '.$user->Organisation->getId().' AND file LIKE \''.$file.'\'');
        if($map)
            return $map->getMap();
        return false;
    }

    public function getMapByOrganisation()
    {
        $user = $this->getDI()->getSession()->get('auth');
        $maps = self::find('organisation_id = '.$user->Organisation->getId());
        return $maps;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param mixed $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }

    /**
     * @return mixed
     */
    public function getOrganisationId()
    {
        return $this->organisation_id;
    }

    /**
     * @param mixed $organisation_id
     */
    public function setOrganisationId($organisation_id)
    {
        $this->organisation_id = $organisation_id;
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

}