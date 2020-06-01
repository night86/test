<?php
/**
 * Created by PhpStorm.
 * User: Bartek
 * Date: 15.07.2016
 * Time: 12:13
 */

namespace Signa\Models;

use Phalcon\Exception;
use Phalcon\Mvc\Model;
use Signa\Helpers\Date;
use Signa\Helpers\Import;
use Signa\Helpers\Translations;
use Signa\Models\LogLabPriceChange;
use Signa\Models\Organisations;

/**
 * Class Products
 * @package Signa\Models
 *
 * @property \Signa\Models\Organisations Organisation
 */
class Products extends Model
{
    protected $id;
    protected $category_id;
    protected $main_category_id;
    protected $sub_category_id;
    protected $sub_sub_category_id;
    protected $ledger_purchase_id;
    protected $ledger_sales_id;
    protected $name;
    protected $description;
    protected $manufacturer;
    protected $manufacturer_id;
    protected $code;
    protected $material;
    protected $price;
    protected $currency;
    protected $barcode_supplier;
    protected $delivery_time;
    protected $key_words;
    protected $optional_order_quantity;
    protected $retail_price;
    protected $external_link;
    protected $external_link_productsheet;
    protected $internal_link_productsheet;
    protected $internal_productsheet;
    protected $images;
    protected $amount;
    protected $amount_min;
    protected $amount_include;
    protected $old_product_id;
    protected $start_date;
    protected $tariff_id;
    protected $import_id;
    protected $changed_price;
    protected $approved;
    protected $declined;
    protected $decline_message;
    protected $created_at;
    protected $created_by;
    protected $updated_at;
    protected $updated_by;
    protected $deleted_at;
    protected $deleted_by;
    protected $deleted;
    protected $active;
    protected $skipped;
    protected $signa_id;
    protected $supplier_id;
    protected $tax_percentage;
    protected $signa_external_link;
    protected $signa_productsheet;
    protected $signa_description;
    protected $product_group;
    protected $category_import_name;
    protected $special_order;
    protected $removal_request;
    protected $is_removed;
    protected $need_update;
    protected $current_product;
    protected $waiting_images;
    protected $waiting_for_approve;
    protected $approve_in_progress;
    protected $approval_status;

    public function initialize()
    {
        $this->belongsTo('supplier_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Organisation'));
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'Created'));
        $this->belongsTo('updated_by', 'Signa\Models\Users', 'id', array('alias' => 'Updated'));
        $this->belongsTo('deleted_by', 'Signa\Models\Users', 'id', array('alias' => 'Deleted'));
        $this->belongsTo('category_id', 'Signa\Models\ProductCategories', 'id', array('alias' => 'Category'));
        $this->belongsTo('main_category_id', 'Signa\Models\ProductCategories', 'id', array('alias' => 'MainCategory'));
        $this->belongsTo('sub_category_id', 'Signa\Models\ProductCategories', 'id', array('alias' => 'SubCategory'));
        // DONT ENABLE RELATION
//        $this->belongsTo('sub_sub_category_id', 'Signa\Models\ProductCategories', 'id', array('alias' => 'SubSubCategory'));
        $this->belongsTo('ledger_purchase_id', 'Signa\Models\CodeLedger', 'id', array('alias' => 'LedgerPurchase'));
        $this->belongsTo('ledger_sales_id', 'Signa\Models\CodeLedger', 'id', array('alias' => 'LedgerSales'));
        $this->belongsTo('manufacturer_id', 'Signa\Models\Manufacturers', 'id', array('alias' => 'Manufacturer'));
        $this->belongsTo('import_id', 'Signa\Models\ImportProducts', 'id', array('alias' => 'Import'));
        $this->belongsTo('tariff_id', 'Signa\Models\CodeTariff', 'id', array('alias' => 'Tariff'));
        $this->hasMany('id', 'Signa\Models\RecipeProduct', 'product_id', array('alias' => 'RecipeProduct'));
        $this->hasMany('id', 'Signa\Models\OrderShortlist', 'product_id', array('alias' => 'OrderShortlist'));
    }

    public function beforeSave()
    {
        if(is_null($this->special_order)){
            $this->special_order = 0;
        }
        if(is_null($this->is_removed)){
            $this->is_removed = 0;
        }
        if(is_null($this->current_product)){
            $this->current_product = 0;
        }

        if ($this->deleted == 0 && $this->approved == 1 && $this->active == 1 && $this->declined == 0 && !$this->deleted_at && !$this->skipped) {
            $this->setNeedUpdate(1); // update in Solr
        } else {
            $this->setNeedUpdate(2); // delete from Solr
        }
    }

    /**
     * @param string $code
     * @param int $supplierId
     * @return mixed
     */
    public static function findSignaId($code, $supplierId)
    {
        $signaId = null;

        $products = self::query()
            ->columns([
                'id',
                'signa_id'
            ])
            ->where('Signa\Models\Products.deleted_at IS NULL')
            ->andWhere('Signa\Models\Products.deleted = 0')
            ->andWhere('Signa\Models\Products.declined = 0')
            ->andWhere('Signa\Models\Products.code = :code:')
            ->andWhere('Signa\Models\Products.supplier_id = :supplier_id:')
            ->orderBy('Signa\Models\Products.created_at ASC')
            ->bind([
                'code' => $code,
                'supplier_id' => $supplierId,
            ])
            ->limit(1)
            ->execute();

        if (count($products) > 0) {
            if ($products[0]->signa_id) {
                $signaId = $products[0]->signa_id;
            } else {
                $signaId = $products[0]->id;
            }
        }

        return $signaId;
    }

    /**
     * Calculate discount for product
     * @param \Signa\Models\Products $product
     * @param Organisations|null $supplier
     * @return float
     * @throws \Phalcon\Exception
     */
    private static function discount($product, $supplier = null) {
        if(null === $supplier) $supplier = Organisations::findFirst($product->supplier_id);
        if( ! $supplier instanceof Organisations) throw new Exception('Wrong organisation instance.');

        $supplierInfo = $supplier->getRelated('SupplierInfo');
        $price = $product->price;

        if(
            (is_object($supplierInfo) && $supplierInfo->getType() == SupplierInfo::TYPE_DISCOUNT)
            && $fa = FrameworkAgreements::getActiveAgreementBySupplier($supplier->getId())
        ) {
            $discounts = $fa->getDiscounts();
            if ($fa->getDiscountType() == Discounts::TYPE_ALL) {
                $price = Discounts::calculate($product->price, $discounts->getDiscount());
            } elseif ($fa->getDiscountType() == Discounts::TYPE_CATEGORY) {
                $productCategory = ProductCategories::findFirst($product->category_id);
                $discount = 0;
                foreach ($discounts as $discountItem) {
                    if ($discountItem->getRelativeId() == $productCategory->getId()) {
                        $discount = $discountItem->getDiscount();
                    }
                }
                $price = Discounts::calculate($product->price, $discount);
            } elseif ($fa->getDiscountType() == Discounts::TYPE_GROUP) {
                $productGroup = $product->product_group;
                $discount = 0;
                foreach ($discounts as $discountItem) {
                    if ($discountItem->getRelativeId() == $productGroup) {
                        $discount = $discountItem->getDiscount();
                    }
                }
                $price = Discounts::calculate($product->price, $discount);
            }
        }

        return round($price, 2);
    }

    /**
     * @param bool $discounted
     * @return mixed
     */
    public function getPrice($discounted = true)
    {
        $price = $this->price;

        /** @var \Signa\Models\Organisations $supplier */
        $supplier = $this->getRelated('Organisation');
        $supplierInfo = $supplier->getRelated('SupplierInfo');

        if(
            $discounted
            && (is_object($supplierInfo) && $supplierInfo->getType() == SupplierInfo::TYPE_DISCOUNT)
            && $fa = FrameworkAgreements::getActiveAgreementBySupplier($supplier->getId())
        ) {
            $discounts = $fa->getDiscounts();
            if ($fa->getDiscountType() == Discounts::TYPE_ALL) {
                $price = Discounts::calculate($this->price, $discounts->getDiscount());
            } elseif ($fa->getDiscountType() == Discounts::TYPE_CATEGORY) {
                $productCategory = ProductCategories::findFirst($this->category_id);
                $discount = 0;
                foreach ($discounts as $discountItem) {
                    if ($discountItem->getRelativeId() == $productCategory->getId()) {
                        $discount = $discountItem->getDiscount();
                    }
                }
                $price = Discounts::calculate($this->price, $discount);
            } elseif ($fa->getDiscountType() == Discounts::TYPE_GROUP) {
                $productGroup = $this->product_group;
                $discount = 0;
                foreach ($discounts as $discountItem) {
                    if ($discountItem->getRelativeId() == $productGroup) {
                        $discount = $discountItem->getDiscount();
                    }
                }
                $price = Discounts::calculate($this->price, $discount);
            }
        }
        return $price;
    }

    /**
     * Calculate discount for specific price
     * @param $price
     * @return mixed
     */
    public function calculateDiscount($price) {
        $tmpPrice = $this->price;
        $this->price = $price;
        $result = $this->getPrice();
        $this->price = $tmpPrice;
        return $result;
    }

    // Mostly used for import product
    public function saveData($productArr)
    {
//        print_r($productArr); die;
//        var_dump($productArr);
        if (isset($productArr['name']) && !is_null($productArr['name']))
            $this->setName($productArr['name']);
        if (isset($productArr['category_id']) && !is_null($productArr['category_id']))
            $this->setCategoryId($productArr['category_id']);
        if (isset($productArr['code']) && !is_null($productArr['code']))
            $this->setCode($productArr['code']);
        if (isset($productArr['description']) && !is_null($productArr['description']))
            $this->setDescription($productArr['description']);
        if (isset($productArr['manufacturer']) && !is_null($productArr['manufacturer']))
            $this->setManufacturer($productArr['manufacturer']);
        if (isset($productArr['material']) && !is_null($productArr['material']))
            $this->setMaterial($productArr['material']);
        if (isset($productArr['price']) && !is_null($productArr['price']))
            $this->setPrice($productArr['price']);
//        if(isset($productArr['price_currency']) && !is_null($productArr['price_currency']))
//            $this->setCurrency($productArr['price_currency']);
        if (isset($productArr['supplier_id']) && !is_null($productArr['supplier_id']))
            $this->setSupplierId($productArr['supplier_id']);
        if (isset($productArr['old_product_id']) && !is_null($productArr['old_product_id']))
            $this->setOldProductId($productArr['old_product_id']);
        if (isset($productArr['amount']) && !is_null($productArr['amount']) && $productArr['amount'] != '')
            $this->setAmount($productArr['amount']);
        if (isset($productArr['amount_min']) && !is_null($productArr['amount_min']) && $productArr['amount_min'] != '')
            $this->setAmountMin($productArr['amount_min']);
        if (isset($productArr['amount_include']) && !is_null($productArr['amount_include']) && $productArr['amount_include'] != '')
            $this->setAmountInclude($productArr['amount_include']);
        if (isset($productArr['start_date']) && !is_null($productArr['start_date']))
            $this->setStartDate($productArr['start_date']);
        if (isset($productArr['import_id']) && !is_null($productArr['import_id']))
            $this->setImportId($productArr['import_id']);
        if (isset($productArr['category_id']) && !is_null($productArr['category_id']))
            $this->setCategoryId($productArr['category_id']);
        if (isset($productArr['changed_price']) && !is_null($productArr['changed_price']))
            $this->setChangedPrice($productArr['changed_price']);
        if (isset($productArr['barcode_supplier']) && !is_null($productArr['barcode_supplier']))
            $this->setBarcodeSupplier($productArr['barcode_supplier']);
        if (isset($productArr['delivery_time']) && !is_null($productArr['delivery_time']) && $productArr['delivery_time'] != '')
            $this->setDeliveryTime($productArr['delivery_time']);
        if (isset($productArr['key_words']) && !is_null($productArr['key_words']))
            $this->setKeyWords($productArr['key_words']);
        // if(isset($productArr['package_unit']) && !is_null($productArr['package_unit']))
        //    $this->setPackageUnit($productArr['package_unit']);
        if (isset($productArr['optional_order_quantity']) && !is_null($productArr['optional_order_quantity']) && $productArr['optional_order_quantity'] != '')
            $this->setOptionalOrderQuantity($productArr['optional_order_quantity']);
        if (isset($productArr['retail_price']) && !is_null($productArr['retail_price']))
            $this->setRetailPrice($productArr['retail_price']);
        if (isset($productArr['external_link']) && !is_null($productArr['external_link']))
            $this->setExternalLink($productArr['external_link']);
        if (isset($productArr['external_link_productsheet']) && !is_null($productArr['external_link_productsheet']))
            $this->setExternalLinkProductsheet($productArr['external_link_productsheet']);
        if (isset($productArr['signa_external_link']) && !is_null($productArr['signa_external_link']))
            $this->setSignaExternalLink($productArr['signa_external_link']);
        if (isset($productArr['signa_productsheet']) && !is_null($productArr['signa_productsheet']))
            $this->setSignaProductsheet($productArr['signa_productsheet']);
        if (isset($productArr['signa_description']) && !is_null($productArr['signa_description']))
            $this->setSignaDescription($productArr['signa_description']);
        if (isset($productArr['product_group']) && !is_null($productArr['product_group']))
            $this->setProductGroup($productArr['product_group']);
        if (isset($productArr['tax_percentage']) && !is_null($productArr['tax_percentage']))
            $this->setTaxPercentage($productArr['tax_percentage']);
        if (isset($productArr['main_category_id']) && !is_null($productArr['main_category_id']) && $productArr['main_category_id'] != '')
            $this->setMainCategoryId($productArr['main_category_id']);
        if (isset($productArr['sub_category_id']) && !is_null($productArr['sub_category_id']) && $productArr['sub_category_id'] != '')
            $this->setSubCategoryId($productArr['sub_category_id']);
        if (isset($productArr['sub_sub_category_id']) && !is_null($productArr['sub_sub_category_id']) && $productArr['sub_sub_category_id'] != '')
            $this->setSubSubCategoryId($productArr['sub_sub_category_id']);
        if (isset($productArr['special_order']) && !is_null($productArr['special_order']))
            $this->setSpecialOrder($productArr['special_order']);

        $this->setCurrency('euro');

        if (isset($productArr['external_product_image'])) {
            $images = $this->getImages();
            if (count($images) > 0 && $images[0]['url'] != $productArr['external_product_image']) {
                $di = \Phalcon\DI::getDefault();
                $configDir = $di->getConfig()->application->productImagesDir;

                $isValid = getimagesize($productArr['external_product_image']);
                if(!(bool)$isValid) {

                } else {

                    // Get image name from url
                    preg_match('/(([^\/])+)$/', $productArr['external_product_image'], $matches);
                    $imageName = $matches[0];

                    // Generate unique image name
                    $uniqueImageName = Import::generateRandomString() . '_' . $imageName;

                    // Checking if product folder exist and creating it
                    $imgDir = $configDir . $this->getId() . '/';
                    if (!is_dir($imgDir)) {
//                    mkdir($imgDir, 0777);
                        mkdirR($imgDir);
                    }
                    // Getting image from url and save into folder
                    copy($productArr['external_product_image'], $imgDir . $uniqueImageName);

                    $images[0]['original_name'] = $imageName;
                    $images[0]['unique_name'] = $uniqueImageName;
                    $images[0]['url'] = $productArr['external_product_image'];

                    $this->setImages($images);
                }
            }
        }

        if(isset($productArr['product_category']) && !is_null($productArr['product_category'])){
            $name = $productArr['product_category'];
//            $category = ProductCategories::findFirst(    [
//                'conditions' => 'name = ?1',
//                'bind'       => [
//                    1 => $name,
//                ]
//            ]);
//            if($category){
//                $this->setCategoryId($category->getId());
//            }

            $this->setCategoryImportName($name);
        }

        if (isset($productArr['catIds']['main_product_category']) && $productArr['catIds']['main_product_category'] != 0) {
            $this->setMainCategoryId($productArr['catIds']['main_product_category']);
        }

        if (isset($productArr['catIds']['sub_product_category']) && $productArr['catIds']['sub_product_category'] != 0) {
            $this->setSubCategoryId($productArr['catIds']['sub_product_category']);
        }

        if (isset($productArr['catIds']['sub_sub_product_category']) && $productArr['catIds']['sub_sub_product_category'] != 0) {
            $this->setSubSubCategoryId($productArr['catIds']['sub_sub_product_category']);
        }

        if (isset($productArr['manufacturersId']) && $productArr['manufacturersId'] != 0) {
            $this->setManufacturerId($productArr['manufacturersId']);
        }

        return $this->save();
    }

    public function approveWithCategory($categoryId = null)
    {
        if ($categoryId) {
            $this->setCategoryId($categoryId);
        }
        $this->setApproved(1);

        /*
         * Added temporary to test products
         */
        $this->setActive(1);

        return $this->save();
    }

    public function approveWithoutCategory()
    {
        $this->setApproved(1);

        return $this->save();
    }

    public function activateNewProduct()
    {
        $this->setActive(1);
        $this->setWaitingForApprove(null);
        $this->setApproveInProgress(null);

        if ($this->getOldProductId()) {
            $oldProduct = self::findFirst('id = '.$this->getOldProductId().' AND deleted = 0');
        } else {
            $oldProduct = false;
        }
        if ($oldProduct !== false) {

            $organisations = Organisations::find();
            //create mongo products for every LAB user
            foreach ($organisations as $organisation) {
                if ($organisation) {
                    if ($organisation->OrganisationType->getSlug() == 'lab') {
                        $logLabPriceChange = new LogLabPriceChange();
                        $logLabPriceChange->product_id = $this->id;
                        $logLabPriceChange->product_code = $this->code;
                        $logLabPriceChange->product_name = $this->name;
                        $logLabPriceChange->new_price = $this->getPrice();
                        $logLabPriceChange->old_price = $oldProduct->getPrice();
                        $logLabPriceChange->start_date = $this->start_date;
                        $logLabPriceChange->organisation_id = $organisation->getId();
                        $logLabPriceChange->created_at = (new \DateTime())->format('Y-m-d H:i:s');
                        if ($this->getDI()->has('session')) {
                            $user = $this->getDI()->getSession()->get('auth');
                            if ($user) {
                                $logLabPriceChange->created_by = $user->getId();
                            }
                        }
                        $logLabPriceChange->isopened = false;
                        $logLabPriceChange->save();
                    }
                }
            }

            $oldProduct->softDelete();
        }

        return $this->save();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    public function compareOldNew()
    {
        $compareFields = array(
            'name',
            'description',
            'material',
            'price',
            'amount_min',
            'amount_include',
            'manufacturer'
        );
        $old = self::findFirstById($this->getOldProductId());
        $compared = array();

        if ($old) {
            foreach ($compareFields as $field) {
                $compared['new'][$field] = $this->campareValues($old, $field, 'new');
                $compared['old'][$field] = $this->campareValues($old, $field, 'old');
            }
        }
        return $compared;
    }

    /**
     * @return mixed
     */
    public function getOldProductId()
    {
        return $this->old_product_id;
    }

    /**
     * @param mixed $old_product_id
     */
    public function setOldProductId($old_product_id)
    {
        $this->old_product_id = $old_product_id;
    }

    private function campareValues($old, $field, $version)
    {
        $new = $this->toArray();
        $old = $old->toArray();
        $result = array('value' => '', 'change' => false);

        switch ($version) {
            case 'new':
                $result['value'] = $new[$field];
                if ($new[$field] != $old[$field])
                    $result['change'] = true;
                return $result;
            case 'old':
                $result['value'] = $old[$field];
                if ($new[$field] != $old[$field])
                    $result['change'] = true;
                return $result;
        }
    }

    public function idsFromShortlist()
    {
        if ($this->getDI()->has('session')) {
            $user = $this->getDI()->getSession()->get('auth');
            $db = $this->getDi()->getShared('db');
            $result = $db->query(
                "SELECT product_id FROM `order_shortlist` WHERE organisation_id = ".$user->Organisation->getId(
                )." ORDER BY product_id ASC"
            );
            $idsArr = array();
            foreach ($result->fetchAll() as $productId) {
                $idsArr[] = $productId['product_id'];
            }

            return $idsArr;
        } else {
            return null;
        }
    }

    public function getFilterValues($products = null)
    {

        if ($products !== null) {
            if (!empty($products)) {
                $i=0;
                $productListIds = [];
                $supplierIds = [];
                $categoryIds = [];
                foreach ($products as $product) {
                    $productListIds[$i] = $product['id'];
                    if(!is_null($product['category_id'])){
                        $categoryIds[$i] = $product['category_id'];
                    }

                    $supplierIds[$i] = $product['supplier_id'];
                    $i++;
                }
            } else {
                $productListIds = [0];
                $supplierIds = [0];
            }
            $filters = array(
                'complex' => array(
                    'categories' => $this->getFilterQueryIds('product_categories', ' WHERE id IN (' . implode(',', $categoryIds) . ')'),
                    'suppliers' => $this->getFilterQueryIds('organisations', ' WHERE organisation_type_id = 1 AND active = 1 AND id IN (' . implode(',', $supplierIds) . ')'),
                ),
                'simple' => array(
                    'material' => $this->getFilterQueryName('material', ' WHERE id IN (' . implode(',', $productListIds) . ')'),
                    'manufacturer' => $this->getFilterQueryName('manufacturer', ' WHERE id IN (' . implode(',', $productListIds) . ')'),
                    'product_group' => $this->getFilterQueryName('product_group', ' WHERE id IN (' . implode(',', $productListIds) . ')'),
                )
            );
        } else {
            $filters = array(
                'complex' => array(
                    'categories' => $this->getFilterQueryIds('product_categories'),
                    'suppliers' => $this->getFilterQueryIds('organisations', ' WHERE organisation_type_id = 1 AND active = 1'),
                ),
                'simple' => array(
                    'material' => $this->getFilterQueryName('material'),
                    'manufacturer' => $this->getFilterQueryName('manufacturer'),
                    'product_group' => $this->getFilterQueryName('product_group'),
                )
            );
        }

        return $filters;
    }

    private function getFilterQueryIds($table, $whereStatement = '')
    {
        $db = $this->getDi()->getShared('db');
        $result = $db->query("SELECT id, name FROM " . $table . $whereStatement);
        $valuesArr = array();
        foreach ($result->fetchAll() as $key => $value) {
            $valuesArr[$key]['value'] = $value[0];
            $valuesArr[$key]['name'] = $value[1];
        }
        return $valuesArr;
    }

    private function getFilterQueryName($field, $whereStatment = '')
    {
        $db = $this->getDi()->getShared('db');
        $result = $db->query("SELECT " . $field . " FROM products " . $whereStatment . " GROUP BY " . $field);
        $valuesArr = array();
        foreach ($result->fetchAll() as $value) {
            if (!is_null($value[0]) && $value[0] !== '')
                $valuesArr[] = $value[0];
        }
        return $valuesArr;
    }

    public function softDelete()
    {
        if ($this->getDI()->has('session')) {
            $user = $this->getDI()->getSession()->get('auth');
            $this->setDeletedBy($user->getId());
        }
        $this->setDeletedAt(Date::currentDatetime());
        $this->setDeleted(1);
        $this->save();
    }

    public function beforeCreate()
    {
        if ($this->getDI()->has('session')) {
            $user = $this->getDI()->getSession()->get('auth');
            $this->setCreatedBy($user->getId());
        }

        $this->setCreatedAt(Date::currentDatetime());
    }

    public function beforeUpdate()
    {
        if ($this->getDI()->has('session')) {
            $user = $this->getDI()->getSession()->get('auth');
            $this->setUpdatedBy($user->getId());
        }

        $this->setUpdatedAt(Date::currentDatetime());
    }

    public function beforeDelete()
    {
        $date = new \DateTime();
        $this->setDeletedAt($date->format('Y-m-d H:i:s'));
        $this->setDeleted(1);
        $r = $this->save();
        if ($this->getDI()->has('session') && $this->getDI()->getSession()) {
            if ($r) {
                $this->getDI()->getSession()->set('message', array('type' => 'success', 'content' => Translations::make('Product has been deleted.')));
            } else {
                $this->getDI()->getSession()->set('message', array('type' => 'warning', 'content' => Translations::make('Product doesn\'t exist.')));
            }
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * @param mixed $category_id
     */
    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
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
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    /**
     * @param mixed $manufacturer
     */
    public function setManufacturer($manufacturer)
    {
        $this->manufacturer = strtolower($manufacturer);
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
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * @param mixed $material
     */
    public function setMaterial($material)
    {
        $this->material = $material;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = str_replace(',', '.', $price);
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getBarcodeSupplier()
    {
        return $this->barcode_supplier;
    }

    /**
     * @param mixed $barcode_supplier
     */
    public function setBarcodeSupplier($barcode_supplier)
    {
        $this->barcode_supplier = $barcode_supplier;
    }

    /**
     * @return mixed
     */
    public function getDeliveryTime()
    {
        return $this->delivery_time;
    }

    /**
     * @param mixed $delivery_time
     */
    public function setDeliveryTime($delivery_time)
    {
        $this->delivery_time = $delivery_time;
    }

    /**
     * @return mixed
     */
    /*public function getPackageUnit()
    {
        return $this->package_unit;
    }*/

    /**
     * @param mixed $package_unit
     */
    /*public function setPackageUnit($package_unit)
    {
        $this->package_unit = $package_unit;
    }*/

    /**
     * @return mixed
     */
    public function getKeyWords()
    {
        return $this->key_words;
    }

    /**
     * @param mixed $key_words
     */
    public function setKeyWords($key_words)
    {
        $this->key_words = $key_words;
    }

    /**
     * @return mixed
     */
    public function getOptionalOrderQuantity()
    {
        return $this->optional_order_quantity;
    }

    /**
     * @param mixed $optional_order_quantity
     */
    public function setOptionalOrderQuantity($optional_order_quantity)
    {
        $this->optional_order_quantity = $optional_order_quantity;
    }

    /**
     * @return mixed
     */
    public function getRetailPrice()
    {
        return $this->retail_price;
    }

    /**
     * @param mixed $retail_price
     */
    public function setRetailPrice($retail_price)
    {
        $this->retail_price = $retail_price;
    }

    /**
     * @return mixed
     */
    public function getExternalLink()
    {
        return $this->external_link;
    }

    /**
     * @param mixed $external_link
     */
    public function setExternalLink($external_link)
    {
        $this->external_link = $external_link;
    }

    /**
     * @return mixed
     */
    public function getExternalLinkProductsheet()
    {
        return $this->external_link_productsheet;
    }

    /**
     * @param mixed $external_link_productsheet
     */
    public function setExternalLinkProductsheet($external_link_productsheet)
    {
        $this->external_link_productsheet = $external_link_productsheet;
    }

    /**
 * @return mixed
 */
    public function getInternalProductsheet()
    {
        return $this->internal_productsheet;
    }

    /**
     * @param mixed $internal_productsheet
     */
    public function setInternalProductsheet($internal_productsheet)
    {
        $this->internal_productsheet = $internal_productsheet;
    }

    /**
     * @return mixed
     */
    public function getInternalLinkProductsheet()
    {
        return $this->internal_link_productsheet;
    }

    /**
     * @param mixed $internal_link_productsheet
     */
    public function setInternalLinkProductsheet($internal_link_productsheet)
    {
        $this->internal_link_productsheet = $internal_link_productsheet;
    }

    /**
     * @return mixed
     */
    public function getImages()
    {
        return unserialize($this->images);
    }

    /**
     * @param mixed $images
     */
    public function setImages($images)
    {
        $this->images = serialize($images);
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getAmountMin()
    {
        return $this->amount_min;
    }

    /**
     * @param mixed $amount_min
     */
    public function setAmountMin($amount_min)
    {
        $this->amount_min = $amount_min;
    }

    /**
     * @return mixed
     */
    public function getAmountInclude()
    {
        return $this->amount_include;
    }

    /**
     * @param mixed $amount_include
     */
    public function setAmountInclude($amount_include)
    {
        $this->amount_include = $amount_include;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * @param mixed $start_date
     */
    public function setStartDate($start_date)
    {
        $this->start_date = $start_date;
    }

    /**
     * @return mixed
     */
    public function getImportId()
    {
        return $this->import_id;
    }

    /**
     * @param mixed $import_id
     */
    public function setImportId($import_id)
    {
        $this->import_id = $import_id;
    }

    /**
     * @return mixed
     */
    public function getTariffId()
    {
        return $this->tariff_id;
    }

    /**
     * @param mixed $tariff_id
     */
    public function setTariffId($tariff_id)
    {
        $this->tariff_id = $tariff_id;
    }

    /**
     * @return mixed
     */
    public function getChangedPrice()
    {
        return $this->changed_price;
    }

    /**
     * @param mixed $changed_price
     */
    public function setChangedPrice($changed_price)
    {
        $this->changed_price = $changed_price;
    }

    /**
     * @return mixed
     */
    public function getApproved()
    {
        return $this->approved;
    }

    /**
     * @param mixed $approved
     */
    public function setApproved($approved)
    {
        $this->approved = $approved;
    }

    /**
     * @return mixed
     */
    public function getDeclined()
    {
        return $this->declined;
    }

    /**
     * @param mixed $declined
     */
    public function setDeclined($declined)
    {
        $this->declined = $declined;
    }

    /**
     * @return mixed
     */
    public function getDeclineMessage()
    {
        return $this->decline_message;
    }

    /**
     * @param mixed $decline_message
     */
    public function setDeclineMessage($decline_message)
    {
        $this->decline_message = $decline_message;
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
    public function getSupplierId()
    {
        return $this->supplier_id;
    }

    /**
     * @param mixed $supplier_id
     */
    public function setSupplierId($supplier_id)
    {
        $this->supplier_id = $supplier_id;
    }

    /**
     * @return int
     */
    public function getSkipped()
    {
        return $this->skipped;
    }

    /**
     * @param int $skipped
     */
    public function setSkipped($skipped)
    {
        $this->skipped = $skipped;
    }

    public function getMarginPrice()
    {
        if ($this->getDI()->has('session')) {
            $orgId = $this->getDI()->getSession()->get('auth')->getOrganisationId();
            $sl = $this->OrderShortlist;
            $shortlistSet = '';
            foreach ($sl as $item) {
                if ($item->getOrganisationId() == $orgId) {
                    $shortlistSet = $item->getProductPrice();
                    break;
                }
            }

            return $shortlistSet;
        }
    }

    /**
     * @return mixed
     */
    public function getTaxPercentage()
    {
        return $this->tax_percentage;
    }

    /**
     * @param mixed $tax_percentage
     * @return Products
     */
    public function setTaxPercentage($tax_percentage)
    {
        $this->tax_percentage = $tax_percentage;
        return $this;
    }

    public function getSignaExternalLink()
    {
        return $this->signa_external_link;
    }

    /**
     * @param mixed $signa_external_link
     */
    public function setSignaExternalLink($signa_external_link)
    {
        $this->signa_external_link = $signa_external_link;
    }

    /**
     * @return mixed
     */
    public function getSignaProductsheet()
    {
        return $this->signa_productsheet;
    }

    /**
     * @param $signa_productsheet
     */
    public function setSignaProductsheet($signa_productsheet)
    {
        $this->signa_productsheet = $signa_productsheet;
    }

    /**
     * @return mixed
     */
    public function getSignaDescription()
    {
        return $this->signa_description;
    }

    /**
     * @param mixed $signa_description
     */
    public function setSignaDescription($signa_description)
    {
        $this->signa_description = $signa_description;
    }

    public function getProductGroup()
    {
        return $this->product_group;
    }

    /**
     * @param mixed $product_group
     * @return Products
     */
    public function setProductGroup($product_group)
    {
        $this->product_group = $product_group;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCategoryImportName()
    {
        return $this->category_import_name;
    }

    /**
     * @param mixed $category_import_name
     */
    public function setCategoryImportName($category_import_name)
    {
        $this->category_import_name = $category_import_name;
    }

    /**
     * @return mixed
     */
    public function getSpecialOrder()
    {
        return $this->special_order;
    }

    /**
     * @param mixed $special_order
     */
    public function setSpecialOrder($special_order)
    {
        $this->special_order = $special_order;
    }

    /**
     * @return mixed
     */
    public function getRemovalRequest()
    {
        return $this->removal_request;
    }

    /**
     * @param mixed $removal_request
     */
    public function setRemovalRequest($removal_request)
    {
        $this->removal_request = $removal_request;
    }

    /**
     * @return mixed
     */
    public function getIsRemoved()
    {
        return $this->is_removed;
    }

    /**
     * @param mixed $is_removed
     */
    public function setIsRemoved($is_removed)
    {
        $this->is_removed = $is_removed;
    }

    /**
     * @return mixed
     */
    public function getMainCategoryId()
    {
        return $this->main_category_id;
    }

    /**
     * @param mixed $main_category_id
     */
    public function setMainCategoryId($main_category_id)
    {
        $this->main_category_id = $main_category_id;
    }

    /**
     * @return mixed
     */
    public function getSubCategoryId()
    {
        return $this->sub_category_id;
    }

    /**
     * @param mixed $sub_category_id
     */
    public function setSubCategoryId($sub_category_id)
    {
        $this->sub_category_id = $sub_category_id;
    }

    /**
     * @return mixed
     */
    public function getSubSubCategoryId()
    {
        return $this->sub_sub_category_id;
    }

    /**
     * @param mixed $sub_sub_category_id
     */
    public function setSubSubCategoryId($sub_sub_category_id)
    {
        $this->sub_sub_category_id = $sub_sub_category_id;
    }

    /**
     * @return mixed
     */
    public function getNeedUpdate()
    {
        return $this->need_update;
    }

    /**
     * @param mixed $need_update
     */
    public function setNeedUpdate($need_update)
    {
        $this->need_update = $need_update;
    }

    /**
     * @param mixed $current_product
     */
    public function setCurrentProduct($current_product)
    {
        $this->current_product = $current_product;
    }

    /**
     * @return mixed
     */
    public function getWaitingImages()
    {
        return $this->waiting_images;
    }

    /**
     * @param mixed $waiting_images
     */
    public function setWaitingImages($waiting_images)
    {
        $this->waiting_images = $waiting_images;
    }

    /**
     * @return mixed
     */
    public function getWaitingForApprove()
    {
        return $this->waiting_for_approve;
    }

    /**
     * @param mixed $waiting_for_approve
     */
    public function setWaitingForApprove($waiting_for_approve)
    {
        $this->waiting_for_approve = $waiting_for_approve;
    }

    /**
     * @return mixed
     */
    public function getApproveInProgress()
    {
        return $this->approve_in_progress;
    }

    /**
     * @param mixed $approve_in_progress
     */
    public function setApproveInProgress($approve_in_progress)
    {
        $this->approve_in_progress = $approve_in_progress;
    }

    /**
     * @return mixed
     */
    public function getSignaId()
    {
        return $this->signa_id;
    }

    /**
     * @param mixed $signa_id
     */
    public function setSignaId($signa_id)
    {
        $this->signa_id = $signa_id;
    }

    /**
     * @return mixed
     */
    public function getLedgerPurchaseId()
    {
        return $this->ledger_purchase_id;
    }

    /**
     * @return mixed
     */
    public function getLedgerSalesId()
    {
        return $this->ledger_sales_id;
    }

    /**
     * @param mixed $ledger_purchase_id
     */
    public function setLedgerPurchaseId($ledger_purchase_id)
    {
        $this->ledger_purchase_id = $ledger_purchase_id;
    }

    /**
     * @param mixed $ledger_sales_id
     */
    public function setLedgerSalesId($ledger_sales_id)
    {
        $this->ledger_sales_id = $ledger_sales_id;
    }

    /**
     * @return mixed
     */
    public function getManufacturerId()
    {
        return $this->manufacturer_id;
    }

    /**
     * @param mixed $manufacturer_id
     */
    public function setManufacturerId($manufacturer_id)
    {
        $this->manufacturer_id = $manufacturer_id;
    }

    /**
     * @return mixed
     */
    public function getApprovalStatus()
    {
        return $this->approval_status;
    }

    /**
     * @param mixed $approval_status
     */
    public function setApprovalStatus($approval_status)
    {
        $this->approval_status = $approval_status;
    }

    /**
     * @param int $productId
     * @return null|Products
     */
    public static function getCurrentProduct($productId)
    {
        /** @var Products $baseProduct */
        $baseProduct = self::findFirstById($productId);
        if (!$baseProduct) {
            return null;
        }

        $currentDate = Date::currentDate();
        $currentProducts = Products::query()
            ->join('Signa\Models\Organisations', 'o.id = Signa\Models\Products.supplier_id', 'o')
            ->where("Signa\Models\Products.start_date <= '". $currentDate. "' AND deleted = 0 AND approved = 1 AND Signa\Models\Products.active = 1 AND declined = 0 AND o.active = 1")
            ->andWhere('Signa\Models\Products.skipped IS NULL')
            ->andWhere('Signa\Models\Products.supplier_id = :supplier_id:')
            ->andWhere('Signa\Models\Products.code = :code:')
            ->bind([
                'code' => $baseProduct->getCode(),
                'supplier_id' => $baseProduct->getSupplierId()
            ])
            ->groupBy('Signa\\Models\\Products.id')
            ->execute();

        if (count($currentProducts) === 0) {
            return null;
        } else {
            return $currentProducts[0];
        }

    }
}