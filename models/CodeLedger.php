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
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness as UniquenessValidator;

class CodeLedger extends Model
{
    protected $id;
    protected $code;
    protected $description;
    protected $group_type;
    protected $balance_type;
    protected $balance_side;
    protected $product_id;
    protected $added_type;
    protected $active;
    protected $organisation_id;
    protected $created_by;
    protected $created_at;
    protected $updated_by;
    protected $updated_at;

    private $addedTypeLabels = array(
        1 => 'Manual',
        2 => 'Import'
    );

    public function initialize()
    {
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'CreatedBy'));
        $this->belongsTo('updated_by', 'Signa\Models\Users', 'id', array('alias' => 'UpdatedBy'));
        $this->belongsTo('product_id', 'Signa\Models\Products', 'id', array('alias' => 'Product'));
        $this->belongsTo('organisation_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Organisation'));
        $this->setSource("code_ledgers");
    }

    public function validation()
    {
        $validator = new Validation();

        /*$validator->add(
            'code',
            new UniquenessValidator([
                'model' => $this,
                'message' => 'Sorry, that code is already taken.',
            ])
        );*/

        return $this->validate($validator);
    }

    public function setDatas($data)
    {
        if(isset($data['code']))
            $this->setCode($data['code']);
        if(isset($data['description']))
            $this->setDescription($data['description']);
        if(isset($data['group_type']))
            $this->setGroupType($data['group_type']);
        if(isset($data['balance_type']))
            $this->setBalanceType($data['balance_type']);
        if(isset($data['balance_side']))
            $this->setBalanceSide($data['balance_side']);
        if(isset($data['product_id']))
            $this->setProductId($data['product_id']);
        if(isset($data['added_type']))
            $this->setAddedType($data['added_type']);

        return $this->save();
    }

    public function activateDeactivate($status)
    {
        $this->setActive((int)$status);
        return $this->save();
    }

    public function beforeCreate()
    {
        $user = $this->getDI()->getSession()->get('auth');

//        $this->setActive(0);
        $this->setOrganisationId($user->Organisation->getId());
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
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getGroupType()
    {
        return $this->group_type;
    }

    /**
     * @param mixed $group_type
     */
    public function setGroupType($group_type)
    {
        $this->group_type = $group_type;
    }

    /**
     * @return mixed
     */
    public function getBalanceType()
    {
        return $this->balance_type;
    }

    /**
     * @param mixed $balance_type
     */
    public function setBalanceType($balance_type)
    {
        $this->balance_type = $balance_type;
    }

    /**
     * @return mixed
     */
    public function getBalanceSide()
    {
        return $this->balance_side;
    }

    /**
     * @param mixed $balance_side
     */
    public function setBalanceSide($balance_side)
    {
        $this->balance_side = $balance_side;
    }

    /**
     * @return mixed
     */
    public function getProductId()
    {
        return $this->product_id;
    }

    /**
     * @param mixed $product_id
     */
    public function setProductId($product_id)
    {
        $this->product_id = $product_id;
    }

    /**
     * @return mixed
     */
    public function getAddedType()
    {
        return $this->addedTypeLabels[$this->added_type];
    }

    /**
     * @param mixed $added_type
     */
    public function setAddedType($added_type)
    {
        $this->added_type = $added_type;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        $this->active = $active;
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