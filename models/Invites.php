<?php
namespace Signa\Models;

use Phalcon\Mvc\Model;
use Signa\Helpers\Date;

class Invites extends Model
{
    protected $id;
    protected $user_id;
    protected $unique_id;
    protected $email;
    protected $organisation_data;
    protected $client_number;
    protected $valid_till;
    protected $registered;
    protected $created_at;
    protected $created_by;
    protected $updated_at;
    protected $updated_by;
    protected $deleted;
    protected $sended;
    protected $inviter_organisation;

    public function initialize()
    {
        $this->belongsTo('user_id', 'Signa\Models\Users', 'id', array('alias' => 'User'));
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'CreatedBy'));
        $this->belongsTo('updated_by', 'Signa\Models\Users', 'id', array('alias' => 'UpdatedBy'));
    }

    public function saveData($data)
    {
        if(isset($data['user_id'])){
            $this->setUserId($data['user_id']);
        }

        return $this->save();
    }

    public function beforeCreate()
    {
        $user = $this->getDI()->getSession()->get('auth');

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
    public function getUniqueId()
    {
        return $this->unique_id;
    }

    /**
     * @param mixed $unique_id
     */
    public function setUniqueId($unique_id)
    {
        $this->unique_id = $unique_id;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getOrganisationData()
    {
        return $this->organisation_data;
    }

    /**
     * @param mixed $organisation_data
     */
    public function setOrganisationData($organisation_data)
    {
        $this->organisation_data = $organisation_data;
    }

    /**
     * @return mixed
     */
    public function getClientNumber()
    {
        return $this->client_number;
    }

    /**
     * @param mixed $client_number
     */
    public function setClientNumber($client_number)
    {
        $this->client_number = $client_number;
    }

    /**
     * @return mixed
     */
    public function getValidTill()
    {
        return $this->valid_till;
    }

    /**
     * @param mixed $valid_till
     */
    public function setValidTill($valid_till)
    {
        $this->valid_till = $valid_till;
    }

    /**
     * @return mixed
     */
    public function getRegistered()
    {
        return $this->registered;
    }

    /**
     * @param mixed $registered
     */
    public function setRegistered($registered)
    {
        $this->registered = $registered;
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
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param mixed $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return mixed
     */
    public function getSended()
    {
        return $this->sended;
    }

    /**
     * @param mixed $sended
     */
    public function setSended($sended)
    {
        $this->sended = $sended;
    }

    /**
     * @return mixed
     */
    public function getInviterOrganisation()
    {
        return $this->inviter_organisation;
    }

    /**
     * @param mixed $inviter_organisation
     */
    public function setInviterOrganisation($inviter_organisation)
    {
        $this->inviter_organisation = $inviter_organisation;
    }

}