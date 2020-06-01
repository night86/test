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

class FileSharedUser extends Model
{
    protected $id;
    protected $user_id;
    protected $file_shared_organisation_id;
    protected $status;
    protected $created_by;
    protected $created_at;

    private $statusLabels = array('Pending', 'Accepted', 'Denied');

    public function initialize()
    {
        $this->belongsTo('user_id', 'Signa\Models\Users', 'id', array('alias' => 'User'));
        $this->belongsTo('file_shared_organisation_id', 'Signa\Models\FileSharedOrganisation', 'id', array('alias' => 'FileSharedOrganisation'));
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'CreatedBy'));
    }

    public function beforeCreate()
    {
        $user = $this->getDI()->getSession()->get('auth');

        $this->setStatus(0);
        $this->setCreatedAt(Date::currentDatetime());
        $this->setCreatedBy($user->getId());
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
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return mixed
     */
    public function getFileSharedOrganisationId()
    {
        return $this->file_shared_organisation_id;
    }

    /**
     * @param mixed $file_shared_organisation_id
     */
    public function setFileSharedOrganisationId($file_shared_organisation_id)
    {
        $this->file_shared_organisation_id = $file_shared_organisation_id;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->statusLabels[$this->status];
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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