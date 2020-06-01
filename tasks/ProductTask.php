<?php

namespace Signa\Tasks;

use Signa\Helpers\Date;
use Signa\Helpers\Import;
use Signa\Libs\Products\ProductsList;
use Signa\Libs\Solr;
use Signa\Models\ImportProducts;
use Signa\Models\OrderShortlist;
use Signa\Models\Products;
use Signa\Models\Users;
use Signa\Models\Roles;
use Phalcon\Http\Client\Request;

class ProductTask extends \Phalcon\Cli\Task
{
	/**
	 * default action for now it shouldn't work
	 */
    public function productAction()
    {
        echo "\n \n";
    }

	/**
     * @param array $params
     */
	public function fixEmptyRegisteredFieldAction(/*array $params*/)
    {
		$users = Users::find(array(
			'conditions'	=> 'registered IS NULL OR registered = 0',
			'bind'			=> array(),
		));

		// update empty registered date by last login date
		foreach ($users as $user) {
			$user->registered = $user->last_login;
			$user->save();
			echo $user->email."\n";
		}

		echo "\n"."Done!!"."\n";
	}

	public function updateAllProductsInSolrAction()
    {
        $productsList = new ProductsList();
        $productsList->skipReplacedUpdates();
        $productsList->setSignaIDs();

        $response = (new Solr())->updateProducts(true);
//        $response = (new Solr())->getProducts();
        print_r($response);

        echo "\n";
        echo "\n";
        echo "\n"."Done! :-)"."\n";
        echo "\n";
    }

    public function deleteAllProductsInSolrAction()
    {
        $response = (new Solr())->deleteAllProducts();
        print_r($response);

        echo "\n";
        echo "\n";
        echo "\n"."Done! :-)"."\n";
        echo "\n";
    }

    public function rebuildShortlistAction()
    {
        $shortlists = OrderShortlist::find();

        echo "\n" . sprintf('Found connections: %s', count($shortlists));

        $cnt = 0;
        $cnt2 = 0;
        /** @var OrderShortlist $shortlist */
        foreach ($shortlists as $shortlist) {
            $newProduct = Products::getCurrentProduct($shortlist->getProductId());
            if ($newProduct) {
                $cnt2++;
            }
            if ($newProduct && $newProduct->getId() != $shortlist->getProductId()) {
                $shortlist->setProductId($newProduct->getId());
                $shortlist->save();
                $cnt++;
            }
        }

        echo "\n" . sprintf('Possible update connections: %s', $cnt2);
        echo "\n" . sprintf('Updated connections: %s', $cnt);

        echo "\n";
        echo "\n";
        echo "\n"."Done! :-)"."\n";
        echo "\n";
    }

    public function updateProductsInSolrWorkerAction()
    {
        $productsToUpdate = Products::query()
            ->where('need_update = 1')
            ->columns(['id'])
            ->execute()
            ->toArray();

        echo "\n" . sprintf('Products to update: %s', count($productsToUpdate));

        $productsToDelete = Products::query()
            ->where('need_update = 2')
            ->columns(['id'])
            ->execute()
            ->toArray();

        echo "\n" . sprintf('Products to delete: %s', count($productsToDelete));

        if (count($productsToDelete) === 0 && count($productsToUpdate) === 0) {
            echo "\n";
            echo "\n";
            echo "\n"."Nothing to do :-)"."\n";
            echo "\n";

            return true;
        }

        $productsList = new ProductsList();
        $productsList->skipReplacedUpdates();
        $productsList->setSignaIDs();

        $solr = new Solr();

        if (count($productsToUpdate) > 0) {
            $ids = $this->getIdsFromResult($productsToUpdate);
            $solr->setIds($ids);
            $solr->updateProducts();
            $this->clearNeedUpdate($ids);
        }

        if (count($productsToDelete) > 0) {
            $ids = $this->getIdsFromResult($productsToDelete);
            $solr->setIds($ids);
            $solr->deleteProducts();
            $this->clearNeedUpdate($ids);
        }

        echo "\n";
        echo "\n";
        echo "\n"."Done! :-)"."\n";
        echo "\n";
    }

    private function clearNeedUpdate($ids)
    {
        $this->db->query('UPDATE products SET need_update = 0 WHERE id IN ('.implode(',', $ids).')');
    }

    private function getIdsFromResult($results)
    {
        $ids = [];
        foreach ($results as $result) {
            $ids[] = $result['id'];
        }
        return $ids;
    }

    public function downloadProductsImagesAction()
    {
        $st = microtime(true);

        /** @var Products[] $products */
        $products = Products::query()
            ->where('waiting_images IS NOT NULL')
            ->andWhere('waiting_images != "-"')
            ->andWhere('active = 1')
            ->andWhere('approved = 1')
            ->andWhere('signa_id IS NOT NULL')
            ->orderBy('created_at ASC')
            ->execute();

        $total = count($products);
        echo "\n" . sprintf('Products to download images: %s', $total);
        $c = 0;
        echo "\n";

        /** @var Products $product */
        foreach ($products as $product) {

            if ($product->getWaitingImages() == 'previous') {
                /** @var Products[] $oldProducts */
                $oldProducts = Products::query()
                    ->where('id != :id:')
                    ->andWhere('active = 1')
                    ->andWhere('approved = 1')
//                    ->andWhere('skipped IS NULL')
                    ->andWhere('signa_id = :signa_id:')
                    ->bind([
                        'id' => $product->getId(),
                        'signa_id' => $product->getSignaId()
                    ])
                    ->orderBy('start_date DESC')
                    ->limit(1)
                    ->execute();

                if (count($oldProducts) > 0 && $oldProducts[0]->getImages()) {
//                    \dump($oldProducts[0]->getId());
//                    \dump($oldProducts[0]->getImages());
                    $product->setImages($oldProducts[0]->getImages());
                }
            } else {
                $savedImagesArr = Import::saveProductImages($product->getWaitingImages(), $product->getId());
                $product->setImages($savedImagesArr);
            }

            $product->setWaitingImages(null);
            $product->save();

//            if ($product->getWaitingImages() == 'previous') {
//
//                \dump($product->getImages());
//                die;
//            }

            $c++;
            echo "\n" . sprintf('--- [%s / %s]: product updated: %s - %s', $c, $total, $product->getCode(), $product->getName());
        }

        echo "\n";
        echo "\n" . sprintf('Done in: %s', microtime(true) - $st);

        echo "\n";
        echo "\n";
        echo "\n"."Done! :-)"."\n";
        echo "\n";
    }

    public function approveImportAction()
    {
        $st = microtime(true);
        $times = [];

        /** @var Products[] $products */
        $products = Products::query()
            ->where('waiting_for_approve = 1')
            ->andWhere('approve_in_progress IS NULL')
            ->execute();

        if (count($products) === 0) {
            echo "\n";
            echo "\n";
            echo "\n".'Nothing to do! :-)'."\n";
            echo "\n";
            die;
        }

        echo "\n" . sprintf('Found: %s products', count($products));

        // prevent to approve the same product 2x
        $productsByImport = [];
        $ids = [];
        /** @var Products $product */
        foreach ($products as $product) {
            $ids[] = $product->getId();
            if (!array_key_exists($product->getImportId(), $productsByImport)) {
                $productsByImport[$product->getImportId()] = [];
            }
            $productsByImport[$product->getImportId()][] = $product->getId();
        }
        $this->db->execute(
            sprintf(
                'UPDATE products SET approve_in_progress = 1 WHERE id IN (%s)',
                implode(',', $ids)
            )
        );
        echo "\n" . sprintf('Products marked as in progress');
        $times['Products marked as in progress'] = microtime(true) - $st;
        $st = microtime(true);

        $this->mongoLogger->createImportedProducts($ids, true);

        $times['mongoLogger createImportedProducts'] = microtime(true) - $st;
        $st = microtime(true);

        foreach ($productsByImport as $importId => $productsIds) {
            echo "\n";
            echo "\n" . sprintf('== APPROVE IMPORT: %s', $importId);

            $import = ImportProducts::findFirstById($importId);
            $approved = Import::updateImport($import, $productsIds, true);

            $this->mongoLogger->createLog(
                array(
                    'datetime' => date('d-m-Y H:i:s'),
                    'page' => null,
                    'user' => null,
                    'import_id' => $import->getId(),
                    'approval' => array(
                        'type' => 'approved',
                        'rows' => $approved['products']
                    )
                ),
                null
            );
        }
        $times['products updated'] = microtime(true) - $st;

        echo "\n";
        foreach ($times as $title => $time) {
            echo "\n" . sprintf('TIME: %s in %s', $title, $time);
        }

        echo "\n";
        echo "\n";
        echo "\n".'Done! :-)'."\n";
        echo "\n";
    }
}