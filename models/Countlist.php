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

class Countlist extends Model
{
    protected $id;
    protected $status;
    protected $complete_date;
    protected $value;
    protected $created_by;
    protected $created_at;

    private $statusLabels = array(
        1 => 'Open',
        2 => 'Progress',
        3 => 'Complete'
    );

    public function initialize()
    {
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'CreatedBy'));
    }

    public function createNew()
    {
        $user = $this->getDI()->getSession()->get('auth');

        $this->setStatus(1);
        $this->setCreatedBy($user->getId());
        $this->setCreatedAt(Date::currentDatetime());
    }

    public function complete($value)
    {
        $this->setStatus(3);
        $this->setCompleteDate(Date::currentDatetime());
        $this->setValue($value);
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
    public function getStatus()
    {
        return $this->status;
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
    public function getCompleteDate()
    {
        return $this->complete_date;
    }

    /**
     * @param mixed $complete_date
     */
    public function setCompleteDate($complete_date)
    {
        $this->complete_date = $complete_date;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
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
     * @return string
     */
    public function getStatusLabel()
    {
        return $this->statusLabels[$this->status];
    }
}