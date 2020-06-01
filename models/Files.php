<?php
/**
 * Created by PhpStorm.
 * User: Bartek
 * Date: 09.11.2016
 * Time: 10:30
 */

namespace Signa\Models;

use Phalcon\Mvc\Model;
use Signa\Helpers\Date;

class Files extends Model
{
    protected $id;
    protected $name;
    protected $name_original;
    protected $size;
    protected $type;
    protected $organisation_id;
    protected $created_by;
    protected $created_at;


    public function initialize()
    {
        $this->belongsTo('organisation_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Organisation'));
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'CreatedBy'));
        $this->hasMany('id', 'Signa\Models\FileSharedOrganisation', 'file_id', array('alias' => 'FileSharedOrganisation'));
    }

    public function deleteWithRelations()
    {
        foreach ($this->FileSharedOrganisation as $fileSharedOrganisation)
        {
            foreach ($fileSharedOrganisation->FileSharedUser as $fileSharedUser)
            {
                $fileSharedUser->delete();
            }
            $fileSharedOrganisation->delete();
        }
        return $this->delete();
    }

    public function beforeCreate()
    {
        $user = $this->getDI()->getSession()->get('auth');

        $this->setOrganisationId($user->Organisation->getId());
        $this->setCreatedAt(Date::currentDatetime());
        $this->setCreatedBy($user->getId());
    }

    public function hasShared()
    {
        return (bool)count($this->FileSharedOrganisation);
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getNameOriginal()
    {
        return $this->name_original;
    }

    /**
     * @param mixed $name_original
     */
    public function setNameOriginal($name_original)
    {
        $this->name_original = $name_original;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = $this->size;
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 1) . ' ' . $units[$pow];
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
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
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * @param mixed $created_by
     */
    public function setCreatedBy($created_by)
    {
        $this->created_by = $created_by;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }
}