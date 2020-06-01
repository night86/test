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
use Signa\Helpers\Translations as Trans;

class Notifications extends Model
{
    protected $id;
    protected $type;
    protected $order_id;
    protected $user_id;
    protected $reply_id;
    protected $organisation_from;
    protected $organisation_to;
    protected $subject;
    protected $description;
    protected $send_at;
    protected $email_sended;
    protected $framework_agreement_id;
    protected $read_at;
    protected $archived_at;
    protected $created_by;
    protected $created_at;
    protected $updated_by;
    protected $updated_at;
    protected $deleted_by;
    protected $deleted_at;

    private $typeLabels = array(
        1 => 'Order',
        2 => 'Import',
        3 => 'File share',
        4 => 'Project invitation',
        5 => 'Cooperate invitation',
        6 => 'Task',
        7 => 'Status change',
        8 => 'Recipes',
        9 => 'Reminder',
        10 => 'Reminder', // yeah its not duplicate
        11 => 'Framework agreement disabled',
        12 => 'Product'
    );

    public function initialize()
    {
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'Created'));
        $this->belongsTo('updated_by', 'Signa\Models\Users', 'id', array('alias' => 'Updated'));
        $this->belongsTo('deleted_by', 'Signa\Models\Users', 'id', array('alias' => 'Deleted'));
        $this->belongsTo('user_id', 'Signa\Models\Users', 'id', array('alias' => 'Owner'));
        $this->belongsTo('order_id', 'Signa\Models\Order', 'id', array('alias' => 'Order'));
        $this->belongsTo('organisation_from', 'Signa\Models\Organisations', 'id', array('alias' => 'OrganisationFrom'));
        $this->belongsTo('organisation_to', 'Signa\Models\Organisations', 'id', array('alias' => 'OrganisationTo'));
    }

    public function beforeCreate()
    {
        $user = $this->getDI()->getSession()->get('auth');

        $this->setCreatedAt(Date::currentDatetime());
        $this->setCreatedBy($user->getId());
    }

    /**
     * @param mixed $created_by
     */
    public function setCreatedBy($created_by)
    {
        $this->created_by = $created_by;
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getArchivedAt()
    {
        return $this->archived_at;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * @return mixed
     */
    public function getDeletedAt()
    {
        return $this->deleted_at;
    }

    /**
     * @return mixed
     */
    public function getDeletedBy()
    {
        return $this->deleted_by;
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->order_id;
    }

    /**
     * @return mixed
     */
    public function getReadAt()
    {
        return $this->read_at;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @return mixed
     */
    public function getUpdatedBy()
    {
        return $this->updated_by;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return mixed
     */
    public function getReplyId()
    {
        return $this->reply_id;
    }

    /**
     * @param mixed $reply_id
     */
    public function setReplyId($reply_id)
    {
        $this->reply_id = $reply_id;
    }

    /**
     * @param mixed $archived_at
     */
    public function setArchivedAt($archived_at)
    {
        $this->archived_at = $archived_at;
    }

    /**
     * @param mixed $deleted_at
     */
    public function setDeletedAt($deleted_at)
    {
        $this->deleted_at = $deleted_at;
    }

    /**
     * @param mixed $deleted_by
     */
    public function setDeletedBy($deleted_by)
    {
        $this->deleted_by = $deleted_by;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $order_id
     */
    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
    }

    /**
     * @param mixed $read_at
     */
    public function setReadAt($read_at)
    {
        $this->read_at = $read_at;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @param mixed $updated_by
     */
    public function setUpdatedBy($updated_by)
    {
        $this->updated_by = $updated_by;
    }

    /**
     * @param mixed $user_id
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * @return string
     */
    public function getTypeLabel()
    {
        return Trans::make($this->typeLabels[$this->type]);
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @return array
     */
    public function getTypeLabels()
    {
        return $this->typeLabels;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @param array $typeLabels
     */
    public function setTypeLabels($typeLabels)
    {
        $this->typeLabels = $typeLabels;
    }

    /**
     * @return mixed
     */
    public function getSendAt() {
        return $this->send_at;
    }

    /**
     * @param mixed $send_at
     */
    public function setSendAt($send_at) {
        $this->send_at = $send_at;
    }

    public function saveNotification($array)
    {
        if(isset($array['type']) && !is_null($array['type']))
            $this->setType($array['type']);
        if(isset($array['order_id']) && !is_null($array['order_id']))
            $this->setOrderId($array['order_id']);
        if(isset($array['user_id']) && !is_null($array['user_id']))
            $this->setUserId($array['user_id']);
        if(isset($array['subject']) && !is_null($array['subject']))
            $this->setSubject($array['subject']);
        if(isset($array['description']) && !is_null($array['description']))
            $this->setDescription($array['description']);
        if(isset($array['created_by']) && !is_null($array['created_by']))
            $this->setCreatedBy($array['created_by']);
        if(isset($array['created_at']) && !is_null($array['created_at']))
            $this->setCreatedAt($array['created_at']);
        if(isset($array['send_at']) && !is_null($array['send_at']))
            $this->setSendAt($array['created_at']);

        return $this->save();
    }

    /**
     * @return mixed
     */
    public function getOrganisationFrom()
    {
        return $this->organisation_from;
    }

    /**
     * @param mixed $organisation_from
     */
    public function setOrganisationFrom($organisation_from)
    {
        $this->organisation_from = $organisation_from;
    }

    /**
     * @return mixed
     */
    public function getOrganisationTo()
    {
        return $this->organisation_to;
    }

    /**
     * @param mixed $organisation_to
     */
    public function setOrganisationTo($organisation_to)
    {
        $this->organisation_to = $organisation_to;
    }

    /**
     * @return mixed
     */
    public function getEmailSended() {
        return $this->email_sended;
    }

    /**
     * @param mixed $email_sended
     */
    public function setEmailSended($email_sended) {
        $this->email_sended = $email_sended;
    }

    /**
     * @return int
     */
    public function getFrameworkAgreementId() {
        return $this->framework_agreement_id;
    }

    /**
     * @param int $framework_agreement_id
     */
    public function setFrameworkAgreementId($framework_agreement_id) {
        $this->framework_agreement_id = $framework_agreement_id;
    }


}