<?php

namespace Signa\Models;

use Signa\Helpers\Date;
use Phalcon\Mvc\Model;

class DentistOrderData extends Model
{

    protected $id;
    protected $order_id;
    protected $patient_initials;
    protected $patient_insertion;
    protected $patient_lastname;
    protected $patient_number;
    protected $patient_gender;
    protected $patient_birth;
    protected $created_at;
    protected $created_by;
    protected $updated_at;
    protected $updated_by;
    protected $deleted_at;
    protected $deleted_by;

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
     * @param mixed $order_id
     */
    public function setOrderId($order_id)
    {
        $this->order_id = $order_id;
    }

    /**
     * @param mixed $patient_initials
     */
    public function setPatientInitials($patient_initials)
    {
        $this->patient_initials = $patient_initials;
    }

    /**
     * @param mixed $patient_insertion
     */
    public function setPatientInsertion($patient_insertion)
    {
        $this->patient_insertion = $patient_insertion;
    }

    /**
     * @param mixed $patient_lastname
     */
    public function setPatientLastname($patient_lastname)
    {
        $this->patient_lastname = $patient_lastname;
    }

    /**
     * @param mixed $patient_gender
     */
    public function setPatientGender($patient_gender)
    {
        $this->patient_gender = $patient_gender;
    }

    /**
     * @param mixed $patient_birth
     */
    public function setPatientBirth($patient_birth)
    {
        $this->patient_birth = $patient_birth;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @param mixed $created_by
     */
    public function setCreatedBy($created_by)
    {
        $this->created_by = $created_by;
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
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
    public function getPatientInitials()
    {
        return $this->patient_initials;
    }

    /**
     * @return mixed
     */
    public function getPatientInsertion()
    {
        return $this->patient_insertion;
    }

    /**
     * @return mixed
     */
    public function getPatientLastname()
    {
        return $this->patient_lastname;
    }

    /**
     * @return mixed
     */
    public function getPatientGender()
    {
        return $this->patient_gender;
    }

    /**
     * @return mixed
     */
    public function getPatientBirth()
    {
        return $this->patient_birth;
//        $formatted = (new \DateTime($this->patient_birth))->format('Y-m-d');
//        return $formatted;
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
    public function getPatientNumber()
    {
        return $this->patient_number;
    }

    /**
     * @param mixed $patient_number
     */
    public function setPatientNumber($patient_number)
    {
        $this->patient_number = $patient_number;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'dentist_order_data';
    }

    public function getPatientBirthFormat()
    {
        if (!$this->patient_birth) {
            return '';
        }
        return date("d-m-Y", strtotime($this->patient_birth));
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return DentistOrderData[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return DentistOrderData
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
