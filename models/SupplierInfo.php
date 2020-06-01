<?php

namespace Signa\Models;

use Phalcon\Mvc\Model;
use Signa\Helpers\Translations;

class SupplierInfo extends Model
{
    const TYPE_DISCOUNT = 'DISCOUNT_RATE';
    const TYPE_NONE = 'LOWEST_PRICE';


    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $organisation_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $type;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $shipping_costs;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $shipping_less_than;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $delivery_workdays;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $delivery_time;

	public function initialize()
	{
		$this->belongsTo('organisation_id', Organisations::class, 'id', array('alias' => 'organisation'));
	}

	/**
	 * @param $organisationId
	 * @return string
	 */
	public static function getTextByOrganisationId($organisationId) {
		$org = static::findByOrganisation($organisationId)->toArray()[0];
		
		switch($org['shipping_costs']) {
			case 'free_shipping' : $text = 'Gratis verzending'; break;
			case 'shipping_less' : $text = 'Verzendkosten worden berekend wanneer orderkosten lager zijn dan < ' . $org['shipping_less_than'] . '€'; break;
			case 'shipping_costs' : $text = 'Aanvullende verzendkosten worden berekend'; break;
			default: $text = '';
		}

		return Translations::make($text);
	}

    public function getText() {
        switch($this->getShippingCosts()) {
            case 'free_shipping' : $text = 'Gratis verzending'; break;
            case 'shipping_less' : $text = 'Verzendkosten worden berekend wanneer orderkosten lager zijn dan < ' . $this->getShippingLessThan() . '€'; break;
            case 'shipping_costs' : $text = 'Aanvullende verzendkosten worden berekend'; break;
            default: $text = '';
        }

        return Translations::make($text);
	}

	/**
	 * Fins Organisation supplier info
	 * @param $organisationId
	 * @return \Phalcon\Mvc\Model\ResultsetInterface
	 */
	public static function findByOrganisation($organisationId) {
	    return self::findFirst([
	        'organisation_id = :id:',
            'bind' => ['id' => $organisationId]
        ]);
	}

	/**
	 * save data from form
	 *
	 * @param mixed $formData
	 */
	public function saveForm($formData)
	{
		foreach ($formData as $key => $value) {
			if($key == 'shipping_less_than' && $value == '')
				continue;
			$this->$key = $value;
		}
	}

	/**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Method to set the value of field organisation_id
     *
     * @param integer $organisation_id
     * @return $this
     */
    public function setOrganisationId($organisation_id)
    {
        $this->organisation_id = $organisation_id;

        return $this;
    }

    /**
     * Method to set the value of field type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Method to set the value of field shipping_costs
     *
     * @param string $shipping_costs
     * @return $this
     */
    public function setShippingCosts($shipping_costs)
    {
        $this->shipping_costs = $shipping_costs;

        return $this;
    }

    /**
     * Method to set the value of field shipping_less_than
     *
     * @param integer $shipping_less_than
     * @return $this
     */
    public function setShippingLessThan($shipping_less_than)
    {
        $this->shipping_less_than = $shipping_less_than;

        return $this;
    }

    /**
     * Method to set the value of field delivery_workdays
     *
     * @param integer $delivery_workdays
     * @return $this
     */
    public function setDeliveryWorkdays($delivery_workdays)
    {
        $this->delivery_workdays = $delivery_workdays;

        return $this;
    }

    /**
     * Method to set the value of field delivery_time
     *
     * @param string $delivery_time
     * @return $this
     */
    public function setDeliveryTime($delivery_time)
    {
        $this->delivery_time = $delivery_time;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field organisation_id
     *
     * @return integer
     */
    public function getOrganisationId()
    {
        return $this->organisation_id;
    }

    /**
     * Returns the value of field type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the value of field shipping_costs
     *
     * @return string
     */
    public function getShippingCosts()
    {
        return $this->shipping_costs;
    }

    /**
     * Returns the value of field shipping_less_than
     *
     * @return integer
     */
    public function getShippingLessThan()
    {
        return $this->shipping_less_than;
    }

    /**
     * Returns the value of field delivery_workdays
     *
     * @return integer
     */
    public function getDeliveryWorkdays()
    {
        return $this->delivery_workdays;
    }

    /**
     * Returns the value of field delivery_time
     *
     * @return string
     */
    public function getDeliveryTime()
    {
        return $this->delivery_time;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'supplier_info';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return SupplierInfo[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return SupplierInfo
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
