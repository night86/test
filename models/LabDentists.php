<?php
namespace Signa\Models;

use Phalcon\Mvc\Model;
use Signa\Helpers\Date;

class LabDentists extends Model
{
    protected $id;
    protected $lab_id;
    protected $dentist_id;
    protected $payment_arrangement_id;
    protected $active_recipes;
    protected $client_preferences;
    protected $client_preferences_tariff;
    protected $client_preferences_recipe;
    protected $client_number;
    protected $contract;
    protected $status;
    protected $created_by;
    protected $created_at;

    public function initialize()
    {
        $this->belongsTo('lab_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Lab'));
        $this->belongsTo('dentist_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Dentist'));
        $this->belongsTo('payment_arrangement_id', 'Signa\Models\LabPaymentArrangements', 'id', array('alias' => 'PaymentArrangement'));
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'CreatedBy'));
    }

    public function saveData($data)
    {
        if(isset($data['lab_id']))
            $this->setLabId($data['lab_id']);
        if(isset($data['dentist_id']))
            $this->setDentistId($data['dentist_id']);

        return $this->save();
    }

    public function beforeCreate()
    {
        $user = $this->getDI()->getSession()->get('auth');

        $this->setCreatedAt(Date::currentDatetime());
        $this->setCreatedBy(($user) ? $user->getId() : NULL);
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
    public function getLabId()
    {
        return $this->lab_id;
    }

    /**
     * @param mixed $lab_id
     */
    public function setLabId($lab_id)
    {
        $this->lab_id = $lab_id;
    }

    /**
     * @return mixed
     */
    public function getDentistId()
    {
        return $this->dentist_id;
    }

    /**
     * @param mixed $dentist_id
     */
    public function setDentistId($dentist_id)
    {
        $this->dentist_id = $dentist_id;
    }

    /**
     * @return mixed
     */
    public function getPaymentArrangementId()
    {
        return $this->payment_arrangement_id;
    }

    /**
     * @param mixed $payment_arrangement_id
     */
    public function setPaymentArrangementId($payment_arrangement_id)
    {
        $this->payment_arrangement_id = $payment_arrangement_id;
    }

    /**
     * @return mixed
     */
    public function getActiveRecipes()
    {
        return json_decode($this->active_recipes, true);
    }

    /**
     * @param mixed $active_recipes
     */
    public function setActiveRecipes($active_recipes)
    {
        $this->active_recipes = json_encode($active_recipes);
    }

    /**
     * @return mixed
     */
    public function getClientPreferences()
    {
        return $this->client_preferences;
    }

    /**
     * @param mixed $client_preferences
     */
    public function setClientPreferences($client_preferences)
    {
        $this->client_preferences = $client_preferences;
    }

    /**
     * @return mixed
     */
    public function getClientPreferencesTariff()
    {
        return $this->client_preferences_tariff;
    }

    /**
     * @param mixed $client_preferences_tariff
     */
    public function setClientPreferencesTariff($client_preferences_tariff)
    {
        $this->client_preferences_tariff = $client_preferences_tariff;
    }

    /**
     * @return mixed
     */
    public function getClientPreferencesRecipe()
    {
        return $this->client_preferences_recipe;
    }

    /**
     * @param mixed $client_preferences_recipe
     */
    public function setClientPreferencesRecipe($client_preferences_recipe)
    {
        $this->client_preferences_recipe = $client_preferences_recipe;
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
    public function getContract()
    {
        return $this->contract;
    }

    /**
     * @param mixed $contract
     */
    public function setContract($contract)
    {
        $this->contract = $contract;
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