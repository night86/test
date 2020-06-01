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
use Signa\Models\LabDentists;

/**
 * Class Organisations
 * @property \Signa\Models\SupplierInfo SupplierInfo
 * @property \Signa\Models\FrameworkAgreements FrameworkAgreement
 * @package Signa\Models
 */
class Organisations extends Model
{
    protected $id;
    protected $country_id;
    protected $organisation_type_id;
    protected $name;
    protected $logo;
    protected $email;
    protected $telephone;
    protected $address;
    protected $zipcode;
    protected $city;
    protected $isgroup;
//    protected $client_number;
//    protected $client_preferences;
    protected $delivery_notes;
    protected $financial_data;
    protected $salutation;
    protected $invoice_footer;
    protected $invoice_sequence;
    protected $kvk_number;
    protected $footer;
    protected $is_group;
    protected $active;
    protected $created_at;
    protected $created_by;
    protected $updated_at;
    protected $updated_by;
    protected $deleted_at;
    protected $deleted_by;
    protected $iso2h_url;
    protected $iso2h_username;
    protected $iso2h_password;

    /** @var bool  */
    protected $products_need_update = false; // this is not from DB


    public function initialize()
    {
        $this->belongsTo('country_id', 'Signa\Models\Countries', 'id', array('alias' => 'Country'));
        $this->belongsTo('organisation_type_id', 'Signa\Models\OrganisationTypes', 'id', array('alias' => 'OrganisationType'));
        $this->belongsTo('role_template_id', 'Signa\Models\RoleTemplates', 'id', array('alias' => 'roleTemplate'));

        $this->hasMany('id', 'Signa\Models\Users', 'organisation_id', array('alias' => 'users'));
        $this->hasMany('id', 'Signa\Models\ImportProducts', 'supplier_id', array('alias' => 'Imports'));
        $this->hasMany('id', 'Signa\Models\ContactPersons', 'organisation_id', array('alias' => 'ContactPersons'));
        $this->hasMany('id', 'Signa\Models\DentistLocation', 'dentist_id', array('alias' => 'DentistLocations'));

        $this->hasOne('id', 'Signa\Models\SupplierInfo', 'organisation_id', array('alias' => 'SupplierInfo'));
        $this->hasOne('id', 'Signa\Models\InvoiceInfo', 'dentist_id', array('alias' => 'InvoiceInfo'));
//        $this->hasOne('id', 'Signa\Models\InvoiceInfo', 'organisation_id', array('alias' => 'InvoiceInfoGeneral'));
        $this->hasOne('id', FrameworkAgreements::class, 'supplier_id', array('alias' => 'FrameworkAgreement'));
    }

    public function getAdmins()
    {
        $users = $this->users;
        $adminsArr = array();
        foreach ($users as $user)
        {
            $userRoles = $user->UserRoles;
            foreach ($userRoles as $userRole)
            {
                if($userRole->Role->getName() == 'ROLE_ADMIN')
                {
                    $adminsArr[] = $user;
                    break;
                }
            }
        }

        return $adminsArr;
    }

    public function getSupplierAdmins()
    {
        $users = $this->users;
        $adminsArr = array();
        foreach ($users as $user)
        {
            if($user->getRoleTemplateId() == 14){
                $adminsArr[] = $user;
                //break;
            }
        }

        return $adminsArr;
    }

    public function deactivate()
    {
        $this->setActive(0);
        return $this->save();
    }

    public function activate()
    {
        $this->setActive(1);
        return $this->save();
    }

    public function connectedWithDentist()
    {
        $user = $this->getDI()->getSession()->get('auth');
        $labDentist = LabDentists::findFirst('lab_id = '. $this->getId() . ' AND dentist_id = '. $user->getId());

        if($labDentist)
        {
            return true;
        }
        return false;
    }

    public function softDelete()
    {
        $user = $this->getDI()->getSession()->get('auth');
        $this->setDeletedAt(Date::currentDatetime());
        $this->setDeletedBy($user->getId());
        $this->save();
    }

    public function afterSave()
    {
        // if supplier then we need update products in solr
        if (
            $this->isProductsNeedUpdate() &&
            $this->OrganisationType &&
            $this->OrganisationType->getSlug() == 'supplier'
        ) {
            if ($this->getActive() === 1) {
                $this->getDi()->getShared('db')->execute(
                    'UPDATE products SET need_update = 1 WHERE supplier_id = '.$this->getId()
                );
            } else {
                $this->getDi()->getShared('db')->execute(
                    'UPDATE products SET need_update = 2 WHERE supplier_id = '.$this->getId()
                );
            }
        }
    }

    public function beforeCreate()
    {
        if ($this->getDI()->getSession()) {
            $user = $this->getDI()->getSession()->get('auth');
            if ($user) {
                $this->setCreatedAt(Date::currentDatetime());
                $this->setCreatedBy(($user) ? $user->getId() : null);
            }
        }
    }

    public function beforeUpdate()
    {
        if ($this->getDI()->getSession()) {
            $user = $this->getDI()->getSession()->get('auth');
            if ($user) {
                $this->setUpdatedAt(Date::currentDatetime());
                $this->setUpdatedBy($user->getId());
            }
        }
    }

    /**
     * save data from form
     *
     * @param mixed $formData
     */
    public function saveForm($formData)
    {
        foreach ($formData as $key => $value) {

            if($key == 'invoice_sequence' && empty($value)){
                $this->$key = null;
            }
            else {
                $this->$key = $value;
            }
        }
    }

    /**
     * how many users is in this organisation
     *
     * @return int
     */
    public function countUsers()
    {
        $counter = 0;
        if($this->OrganisationType)
        {
            foreach ($this->OrganisationType->organisations as $organisation)
            {
                $counter += count($organisation->users);
            }
        }
        return $counter;
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
    public function getCountryId()
    {
        return $this->country_id;
    }

    /**
     * @param mixed $country_id
     */
    public function setCountryId($country_id)
    {
        $this->country_id = $country_id;
    }

    /**
     * @return mixed
     */
    public function getOrganisationTypeId()
    {
        return $this->organisation_type_id;
    }

    /**
     * @param mixed $organisation_type_id
     */
    public function setOrganisationTypeId($organisation_type_id)
    {
        $this->organisation_type_id = $organisation_type_id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param mixed $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
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
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getZipcode()
    {
        return $this->zipcode;
    }

    /**
     * @param mixed $zipcode
     */
    public function setZipcode($zipcode)
    {
        $this->zipcode = $zipcode;
    }

    /**
     * @return mixed
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param mixed $city
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * @return mixed
     */
    public function getKvkNumber()
    {
        return $this->kvk_number;
    }

    /**
     * @param mixed $kvk_number
     */
    public function setKvkNumber($kvk_number)
    {
        $this->kvk_number = $kvk_number;
    }

//    /**
//     * @return mixed
//     */
//    public function getClientNumber()
//    {
//        return $this->client_number;
//    }
//
//    /**
//     * @param mixed $client_number
//     */
//    public function setClientNumber($client_number)
//    {
//        $this->client_number = $client_number;
//    }
//
//    /**
//     * @return mixed
//     */
//    public function getClientPreferences()
//    {
//        return $this->client_preferences;
//    }
//
//    /**
//     * @param mixed $client_preferences
//     */
//    public function setClientPreferences($client_preferences)
//    {
//        $this->client_preferences = $client_preferences;
//    }

    /**
     * @return mixed
     */
    public function getDeliveryNotes()
    {
        return $this->delivery_notes;
    }

    /**
     * @param mixed $delivery_notes
     */
    public function setDeliveryNotes($delivery_notes)
    {
        $this->delivery_notes = $delivery_notes;
    }

    /**
     * @return mixed
     */
    public function getFinancialData()
    {
        return $this->financial_data;
    }

    /**
     * @param mixed $financial_data
     */
    public function setFinancialData($financial_data)
    {
        $this->financial_data = $financial_data;
    }

    /**
     * @return mixed
     */
    public function getInvoiceSequence()
    {
        return $this->invoice_sequence;
    }

    /**
     * @param mixed $invoice_sequence
     */
    public function setInvoiceSequence($invoice_sequence)
    {
        $this->invoice_sequence = $invoice_sequence;
    }

    /**
     * @return mixed
     */
    public function getIsGroup()
    {
        return $this->is_group;
    }

    /**
     * @param mixed $is_group
     */
    public function setIsGroup($is_group)
    {
        $this->is_group = $is_group;
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
        $this->setProductsNeedUpdate(true);
    }

    /**
     * @return mixed
     */
    public function getSalutation()
    {
        return $this->salutation;
    }

    /**
     * @param mixed $salutation
     */
    public function setSalutation($salutation)
    {
        $this->salutation = $salutation;
    }

    /**
     * @return mixed
     */
    public function getInvoiceFooter()
    {
        return $this->invoice_footer;
    }

    /**
     * @param mixed $invoice_footer
     */
    public function setInvoiceFooter($invoice_footer)
    {
        $this->invoice_footer = $invoice_footer;
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
    public function getDeletedAt()
    {
        return $this->deleted_at;
    }

    /**
     * @param mixed $deleted_at
     */
    public function setDeletedAt($deleted_at)
    {
        $this->deleted_at = $deleted_at;
    }

    /**
     * @return mixed
     */
    public function getDeletedBy()
    {
        return $this->deleted_by;
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
    public function getIso2hUrl()
    {
        return $this->iso2h_url;
    }

    /**
     * @param mixed $iso2h_url
     */
    public function setIso2hUrl($iso2h_url)
    {
        $this->iso2h_url = $iso2h_url;
    }

    /**
     * @return mixed
     */
    public function getIso2hUsername()
    {
        return $this->iso2h_username;
    }

    /**
     * @param mixed $iso2h_username
     */
    public function setIso2hUsername($iso2h_username)
    {
        $this->iso2h_username = $iso2h_username;
    }

    /**
     * @return mixed
     */
    public function getIso2hPassword()
    {
        return $this->iso2h_password;
    }

    /**
     * @param mixed $iso2h_password
     */
    public function setIso2hPassword($iso2h_password)
    {
        $this->iso2h_password = $iso2h_password;
    }

    /**
     * @return bool
     */
    public function isProductsNeedUpdate()
    {
        return $this->products_need_update;
    }

    /**
     * @param bool $products_need_update
     */
    public function setProductsNeedUpdate($products_need_update)
    {
        $this->products_need_update = $products_need_update;
    }
}