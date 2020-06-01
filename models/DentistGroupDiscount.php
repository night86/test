<?php
/**
 * Created by PhpStorm.
 * User: Bartek
 * Date: 15.07.2016
 * Time: 12:13
 */

namespace Signa\Models;

use Phalcon\Mvc\Model;

class DentistGroupDiscount extends Model
{
    protected $id;
    protected $type;
    protected $organisation_id;
    protected $value;
    protected $code;

    private $addedTypeLabels = array(
        1 => 'Percentage',
        2 => 'Price'
    );

    public function initialize()
    {
        $this->belongsTo('organisation_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Organisation'));
        $this->belongsTo('code', 'Signa\Models\Recipes', 'code', array('alias' => 'Recipes'));
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
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return array
     */
    public function getAddedTypeLabels()
    {
        return $this->addedTypeLabels[$this->type];
    }

    /**
     * @param array $addedTypeLabels
     */
    public function setAddedTypeLabels($addedTypeLabels)
    {
        $this->addedTypeLabels = $addedTypeLabels;
    }

    public function getDiscountPrice($oldprice = null){
        if (!$oldprice) {
            $oldprice = $this->Recipes->getPrice();
        }
        $type = $this->type;
        $value = $this->value;
        if ($type == 1) {
            $price = $oldprice-($oldprice*($value/100));
        } else {
            $price = $oldprice - $value;
        }
        return $price;
    }


}