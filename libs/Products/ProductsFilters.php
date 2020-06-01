<?php
namespace Signa\Libs\Products;

use Phalcon\Http\Client\Request;
use Signa\Helpers\Date;
use Signa\Models\Organisations;
use Signa\Models\ProductCategories;
use Signa\Models\Products;
use Phalcon\Mvc\User\Component;

/**
 * Class with log functions to read and create logs in MongoDB
 *
 */
class ProductsFilters extends Component
{
    /** @var ProductCategories[] */
    private $main_categories;

    /** @var ProductCategories[] */
    private $sub_categories;

    /** @var ProductCategories[] */
    private $subsub_categories;

    /** @var Organisations[] */
    private $suppliers;

    public function initFilters()
    {
        $this->getProductCategories();
        $this->getSuppliersFromDB();
    }

    private function getSuppliersFromDB()
    {
        $this->suppliers = Organisations::query()
            ->where('organisation_type_id = 1') // supplier type
            ->andWhere('deleted_at IS NULL')
            ->andWhere('active = 1')
            ->columns([
                'id', 'name', 'logo'
            ])
            ->orderBy('name ASC')
            ->execute();
    }

    private function getProductCategories()
    {
        /** @var ProductCategories[] $categories */
        $categories = ProductCategories::find(['deleted_by IS NULL']);

        $this->main_categories = [];
        $this->sub_categories = [];
        $this->subsub_categories = [];

        /** @var $category $category */
        foreach ($categories as $category) {
            if (!$category->getParentId()) {
                $this->main_categories[] = $category;
            } else if ($category->Parent && !$category->Parent->Parent) {
                $this->sub_categories[] = $category;
            } else if ($category->Parent && $category->Parent->Parent) {
                $this->subsub_categories[] = $category;
            }
        }
    }

    /**
     * @return ProductCategories[]
     */
    public function getMainCategories()
    {
        return $this->main_categories;
    }

    /**
     * @param ProductCategories[] $main_categories
     */
    public function setMainCategories($main_categories)
    {
        $this->main_categories = $main_categories;
    }

    /**
     * @return ProductCategories[]
     */
    public function getSubCategories()
    {
        return $this->sub_categories;
    }

    /**
     * @param ProductCategories[] $sub_categories
     */
    public function setSubCategories($sub_categories)
    {
        $this->sub_categories = $sub_categories;
    }

    /**
     * @return ProductCategories[]
     */
    public function getSubsubCategories()
    {
        return $this->subsub_categories;
    }

    /**
     * @param ProductCategories[] $subsub_categories
     */
    public function setSubsubCategories($subsub_categories)
    {
        $this->subsub_categories = $subsub_categories;
    }

    /**
     * @return Organisations[]
     */
    public function getSuppliers()
    {
        return $this->suppliers;
    }

    /**
     * @param Organisations[] $suppliers
     */
    public function setSuppliers($suppliers)
    {
        $this->suppliers = $suppliers;
    }


}

