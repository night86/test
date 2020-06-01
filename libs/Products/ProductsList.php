<?php
namespace Signa\Libs\Products;

use Phalcon\Http\Client\Request;
use Signa\Helpers\Date;
use Signa\Models\OrderShortlist;
use Signa\Models\Products;
use Phalcon\Mvc\User\Component;

/**
 * Class with log functions to read and create logs in MongoDB
 *
 */
class ProductsList extends Component
{
    /** @var ProductsFilters */
    private $filters;

    public function __construct()
    {
        $this->filters = new ProductsFilters();
        $this->filters->initFilters();
    }

    /**
     * @return ProductsFilters
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param ProductsFilters $filters
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    public function skipReplacedUpdates()
    {
        echo "\n" . sprintf('Start skipping...');
        $currentDate = Date::currentDate();

        $products = Products::query()
            ->where('Signa\Models\Products.deleted_at IS NULL')
            ->andWhere('Signa\Models\Products.deleted = 0')
            ->andWhere('Signa\Models\Products.declined = 0')
//            ->andWhere('Signa\Models\Products.signa_id = 228129')
            ->andWhere('(Signa\Models\Products.skipped = 0 OR Signa\Models\Products.skipped IS NULL)')
            ->andWhere('Signa\Models\Products.start_date <= :currDate:')
            ->orderBy('Signa\Models\Products.start_date DESC, Signa\Models\Products.created_at DESC')
            ->bind([
                'currDate' => $currentDate
            ])
            ->execute();

        echo "\n" . sprintf('- found %s products to check', count($products));

        $productsGrouped = [];

        echo "\n" . sprintf('- making groups...');

        /** @var Products $product */
        foreach ($products as $product) {
            if (!array_key_exists($product->getSupplierId(), $productsGrouped)) {
                $productsGrouped[$product->getSupplierId()] = [];
            }
            if (!array_key_exists($product->getCode(), $productsGrouped[$product->getSupplierId()])) {
                $productsGrouped[$product->getSupplierId()][$product->getCode()] = [];
            }
            $productsGrouped[$product->getSupplierId()][$product->getCode()][] = $product;
//            if (!array_key_exists($product->getStartDate(), $productsGrouped[$product->getSupplierId()][$product->getCode()])) {
//                $productsGrouped[$product->getSupplierId()][$product->getCode()][$product->getStartDate()] = [];
//            }
//            $productsGrouped[$product->getSupplierId()][$product->getCode()][$product->getStartDate()][] = $product;
        }
//print_r($productsGrouped); die;
        echo "\n" . sprintf('- groups are done! checking...');

        foreach ($productsGrouped as $supplierId => $codes) {
            /** @var Products[] $products */
            foreach ($codes as $code => $products) {
//                foreach ($startDates as $startDate => $products) {
                    $cnt = count($products);
                    if ($cnt > 1) {
                        for ($i = 1; $i < $cnt; $i++) {
                            $products[$i]->setSkipped(1);
                            $products[$i]->save();
                            echo "\n" . sprintf('-- skipping product id: %s', $products[$i]->getId());
                            // we need get new product fot shortlist is old is skipped
                            $shortlists = OrderShortlist::query()
                                ->where('product_id = :product_id:')
                                ->andWhere('deleted_at IS NULL')
                                ->bind([
                                    'product_id' => $products[$i]->getId()
                                ])
                                ->execute();

                            /** @var OrderShortlist $shortlist */
                            foreach ($shortlists as $shortlist) {
                                $shortlist->setProductId($products[0]->getId());
                                $shortlist->save();

                                echo "\n" . sprintf('--- updating shortlist product id: %s to %s, organisation: %s', $products[$i]->getId(), $products[0]->getId(), $shortlist->getOrganisationId());
                            }
                        }
                    }
//                }
            }
        }

        echo "\n" . sprintf('- done :-)');
        echo "\n" . sprintf('Products skipped!!.');
    }

    public function setSignaIDs()
    {
        echo "\n".sprintf('Getting signa ID...');

        $products = Products::query()
            ->where('Signa\Models\Products.deleted_at IS NULL')
            ->andWhere('Signa\Models\Products.deleted = 0')
            ->andWhere('Signa\Models\Products.declined = 0')
            ->andWhere('Signa\Models\Products.signa_id IS NULL')
            ->andWhere('Signa\Models\Products.skipped IS NULL')
            ->orderBy('Signa\Models\Products.created_at DESC')
            ->execute();

        $countProducts = count($products);
        echo "\n" . sprintf('- found %s products to set signa ID', $countProducts);

        /** @var Products $product */
        foreach ($products as $k => $product) {
            if (!$product->getCode() || !$product->getSupplierId()) {
                continue;
            }

            $signaId = Products::findSignaId($product->getCode(), $product->getSupplierId());
            if (!$signaId) {
                continue;
            }

            $this->db->execute("UPDATE products SET signa_id = {$signaId} WHERE id = {$product->getId()}");

            echo "\n".sprintf('- %s / %s done', $k, $countProducts);
        }

        echo "\n" . sprintf('- done :-)');
    }
}

