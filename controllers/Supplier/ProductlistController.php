<?php

namespace Signa\Controllers\Supplier;


use Signa\Helpers\General;
use Signa\Helpers\Translations;
use Signa\Libs\Products\ProductsData;
use Signa\Libs\Products\ProductsList;
use Signa\Models\Notifications;
use Signa\Models\Products;
use Signa\Models\Users;
use Phalcon\Mvc\Model\Query;

class ProductlistController extends InitController
{

    public function beforeExecuteRoute(){

    }

    public function indexAction(){

        $this->assets->collection('footer')
            ->addJs("js/app/supplier/productsList.js");
    }

    public function getproductimageAction(){

        $result = [
            'image' => '',
            'pid' => 0
        ];

        if ($this->request->hasPost('pid') && $this->request->getPost('pid')) {

            $imagePath = '/uploads/images/products/';
            $product = Products::findFirstById($this->request->getPost('pid'));
            $productImages = $product->images;
            $productImage = null;

            if (!is_null($productImages)) {

                if (count($productImages) > 0) {

                    $productImage = $productImages[0]['unique_name'];
                    $result['image'] = $imagePath . $product->id . '/' . $productImage;
                    $result['pid'] = $product->id;
                }
            }
        }
        return json_encode($result);
    }

    public function listajaxAction(){

        $pd = new ProductsData();
        $pd->setNoactive(true);
        $pd->setForDatatable(true);
        $pd->setSupplierId($this->currentUser->Organisation->getId());

        $orderByMapping = [
            0 => 'signa_id',
            1 => 'code',
            2 => 'name',
            3 => 'price',
            4 => 'name',
        ];

        // modify post data
        $_POST['limit'] = $this->request->getQuery('length');
        $_POST['page'] = (int)$this->request->getQuery('start') === 0 ? 1 : ((int)$this->request->getQuery('start') / (int)$this->request->getQuery('length')) + 1;

        if ($this->request->getQuery('order')) {

            $_POST['sort'] = sprintf('%s %s',
                $orderByMapping[$this->request->getQuery('order')[0]['column']],
                $this->request->getQuery('order')[0]['dir']
            );
        }

        if ($this->request->getQuery('search')['value']) {
            $_POST['query'] = $this->request->getQuery('search')['value'];
        }
        $data = $pd->getFilteredProducts();
        $productsArr = [];

        foreach ($data['products'] as $k => $product) {

            $productsArr[$k] = (array)$product;
            $productsArr[$k]['image'] = '<div data-pid="'.$product->id.'" class="product-img"><img src="http://placehold.it/300x100/ffffff?text=Geen+foto+beschikbaar" alt=""></div>';
            $productsArr[$k]['actions'] = '<a href="/supplier/productlist/edit/'.$product->id.'" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i>  '.$this->t->make('Edit').'</a>';
        }

        return json_encode([
            'data' => $productsArr,
            'recordsTotal' => $data['recordsTotal'],
            'recordsFiltered' => $data['recordsFiltered']
        ]); die;
    }

    public function labviewAction(){

        if (!$this->session->has('products-filters')) {

            $this->session->set('products-filters', [
                'query' => '',
                'filter' => [],
                'page' => 1,
                'limit' => 6,
            ]);
        }

        $productsList = new ProductsList();

        /** @var ProductsFilters $filters */
        $filters = $productsList->getFilters();

        $this->assets->collection('footerNotCompile')
            ->addJs("js/app/products.js")
            ->addJs("js/app/productsList.js?v=3")
            ->addJs("bower_components/bootstrap3-typeahead/bootstrap3-typeahead.min.js");

        $this->view->filters = $filters;
    }

    public function editAction($id){

        $product = Products::findFirst($id);
        $this->view->product = $product;

        if ($this->request->isPost()){

            $product->setSpecialOrder($this->request->getPost('special_order'));

            if ($this->request->hasFiles() == true) {

                foreach($this->request->getUploadedFiles() as $file){

                    $fileName = General::cleanString($file->getName());

                    if($file->getRealType() == 'image/jpeg' || $file->getRealType() == 'image/png'){

                        $serializedImgData = [];
                        $imgDir = $this->config->application->productImagesDir;
                        $imgDir = $imgDir.$id.'/';
                        $randomString = General::randomString(12);

                        if(!is_dir($imgDir)) {
                            mkdirR($imgDir);
                        }
                        $file->moveTo($imgDir.$randomString.$fileName);
                        General::resizeImage($imgDir.$randomString.$fileName);

                        $serializedImgData[] = array(
                            'added_by' => $this->currentUser->getId(),
                            'original_name' => $fileName,
                            'unique_name' => $randomString.$fileName,
                            'url' => $this->baseUrl.'/uploads/images/products/'.$id.'/'.$randomString.$fileName
                        );

                        $product->setImages($serializedImgData);
                    }
                    elseif($file->getRealType() == 'application/pdf'){

                        $pdfDir = $this->config->application->productFilesDir;
                        $pdfDir = $pdfDir.$id.'/';
                        $randomString = General::randomString(12);

                        if(!is_dir($pdfDir)) {
                            mkdirR($pdfDir, 0777);
                        }
                        $file->moveTo($pdfDir.$randomString.$fileName);

                        $product->setInternalLinkProductsheet($this->baseUrl.'/uploads/files/products/'.$id.'/'.$randomString.$fileName);
                        $product->setInternalProductsheet($randomString.$fileName);
                    }
                }
            }

            if ( $product->save()) {

                $transString = Translations::make("Product has been edited.");
                $this->session->set('message', ['type' => 'success', 'content' => $transString . '.']);
            }
            else {
                $transString = Translations::make("Product hasn't been edited.");
                $this->session->set('message', ['type' => 'error', 'content' => $transString . "."]);
            }
        }
    }

    public function deleteimageAction($id){

        $product = Products::findFirst($id);
        $name = $product->getImages()[0]['unique_name'];
        $product->setImages(null);
        $product->save();
        General::clearDirectory($this->config->application->productImagesDir.$id);

        return $this->response->redirect($this->request->getHTTPReferer());
    }

    public function removeproductAction(){

        if ($this->request->isAjax()){

            $user = Users::query()
                ->innerJoin('\Signa\Models\Organisations', 'o.id = \Signa\Models\Users.organisation_id', 'o')
                ->innerJoin('\Signa\Models\OrganisationTypes', 'ot.id = o.organisation_type_id', 'ot')
                ->where("\Signa\Models\Users.active = 1")
                ->andWhere("ot.slug = 'signadens'")
                ->execute();

            $admin = Users::query()
                ->innerJoin('\Signa\Models\Organisations', 'o.id = \Signa\Models\Users.organisation_id', 'o')
                ->innerJoin('\Signa\Models\OrganisationTypes', 'ot.id = o.organisation_type_id', 'ot')
                ->where("\Signa\Models\Users.active = 1")
                ->andWhere("ot.slug = 'signadens'")
                ->limit(1,1)
                ->execute();

            $product = Products::findFirst($this->request->getPost('id'));

            if(is_null($product->removal_request)){

                $product->setRemovalRequest(date('Y-m-d', strtotime($this->request->getPost('date'))));
                $product->save();

                foreach($user as $u){

                    $newNotificationAdmin = new Notifications();
                    $newNotificationAdmin->setType(12);
                    $newNotificationAdmin->setUserId($u->id);
                    $newNotificationAdmin->setSubject(Translations::make('Request product removal'));
                    $newNotificationAdmin->setDescription("<br />".$this->currentUser->getFullName().Translations::make(" heeft een verzoek ingediend voor het verwijderen van het product")." <strong id='product-id' data-id='".$this->request->getPost('id')."'>".$product->name."</strong><br /> ".Translations::make("Requested date: ").$this->request->getPost('date')."<br /> ".Translations::make("Reason: ").$this->request->getPost('msg'));
                    $newNotificationAdmin->setOrganisationFrom($this->currentUser->getOrganisationId());
                    $newNotificationAdmin->setOrganisationTo(3);
                    $newNotificationAdmin->save();
                }

                $newNotificationUser = new Notifications();
                $newNotificationUser->setType(12);
                $newNotificationUser->setUserId($this->currentUser->getId());
                $newNotificationUser->setSubject(Translations::make('Request product removal'));
                $newNotificationUser->setDescription("<br />".Translations::make("You requested the removal of the product")." <strong>".$product->name."</strong><br /> ".Translations::make("Requested date: ").$this->request->getPost('date')."<br /> ".Translations::make("Reason: ").$this->request->getPost('msg'));
                $newNotificationUser->setOrganisationFrom(3);
                $newNotificationUser->setOrganisationTo($this->currentUser->getOrganisationId());
                $newNotificationUser->save();

                $result = [
                    'status' => 'ok',
                    'msg' => Translations::make('Your request is sent to Signadens admin.')
                ];
            }
            else {
                $result = [
                    'status' => 'error',
                    'msg' => Translations::make('Request for removal already sent')
                ];
            }
            return json_encode($result);
        }
    }

    public function deletesheetAction($id){

        $product = Products::findFirst($id);
        $product->setExternalLinkProductsheet(NULL);
        $product->save();

        return $this->response->redirect($this->request->getHTTPReferer());
    }
}
