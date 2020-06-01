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

class SettingFiles extends Model
{
    protected $id;
    protected $to_user_id;
    protected $from_user_id;
    protected $allow;
    protected $created_by;
    protected $created_at;
    protected $updated_by;
    protected $updated_at;

    private $statusLabels = array('No', 'Yes');

    public function initialize()
    {
        $this->belongsTo('to_user_id', 'Signa\Models\Users', 'id', array('alias' => 'ToUser'));
        $this->belongsTo('from_user_id', 'Signa\Models\Users', 'id', array('alias' => 'FromUser'));
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'CreatedBy'));
        $this->belongsTo('updated_by', 'Signa\Models\Users', 'id', array('alias' => 'UpdatedBy'));
    }

    public function beforeCreate()
    {
        $user = $this->getDI()->getSession()->get('auth');

        $this->setCreatedAt(Date::currentDatetime());
        $this->setCreatedBy($user->getId());
    }

    public function beforeUpdate()
    {
        $user = $this->getDI()->getSession()->get('auth');

        $this->setUpdatedAt(Date::currentDatetime());
        $this->setUpdatedBy($user->getId());
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getToUserId()
    {
        return $this->to_user_id;
    }

    /**
     * @param mixed $to_user_id
     */
    public function setToUserId($to_user_id)
    {
        $this->to_user_id = $to_user_id;
    }

    /**
     * @return mixed
     */
    public function getFromUserId()
    {
        return $this->from_user_id;
    }

    /**
     * @param mixed $from_user_id
     */
    public function setFromUserId($from_user_id)
    {
        $this->from_user_id = $from_user_id;
    }

    /**
     * @return mixed
     */
    public function getAllow()
    {
        return $this->statusLabels[$this->allow];
    }

    /**
     * @param mixed $allow
     */
    public function setAllow($allow)
    {
        $this->allow = $allow;
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

    /**
     * @return mixed
     */
    public function getUpdatedBy()
    {
        return $this->updated_by;
    }

    /**
     * @param mixed $updated_by
     */
    public function setUpdatedBy($updated_by)
    {
        $this->updated_by = $updated_by;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

}