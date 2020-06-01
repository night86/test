<?php
namespace Signa\Libs\Products;

use Phalcon\Http\Client\Request;
use Phalcon\Mvc\User\Component;
use Signa\Helpers\Translations;
use Signa\Libs\Solr;

/**
 * Class with log functions to read and create logs in MongoDB
 *
 */
class ProductsData extends Component
{
    /** @var bool  */
    private $useSession = false;

    /** @var bool  */
    private $autosuggest = false;

    /** @var bool  */
    private $shortlist = false;

    /** @var bool  */
    private $noactive = false;

    /** @var bool  */
    private $forDatatable = false;

    /** @var null|int  */
    private $supplierId = null;

    /** @var null|int  */
    private $limitPerPage = null;

    /**
     * get and prepare array result based on filters
     *
     * @param bool $useSession
     * @param bool $autosuggest
     * @param bool $shortlist
     * @return array
     */
    public function getFilteredProducts()
    {
        $solr = new Solr();

        $productAmountArr = [];
        if ($this->request->hasPost('shortlist') || $this->isShortlist()) {
            $source = 'shortlist';

//            $shortlist = OrderShortlist::find(array('organisation_id = '.$this->currentUser->Organisation->getId()));

            $solr->setShortlistOrganisation($this->session->get('auth')->Organisation->getId());

//            /** @var OrderShortlist $productAmount */
//            foreach ($shortlist as $productAmount)
//            {
//                $productAmountArr[$productAmount->getProductId()]['value'] = $productAmount->getAmountMin();
//                $productAmountArr[$productAmount->getProductId()]['shortlist'] = $productAmount->getId();
//                $productAmountArr[$productAmount->getProductId()]['margin_price'] = $productAmount->getProductPrice();
//
//            }

        } else {
            $source = 'product';
        }

//        print_r($_POST);
        if ($this->isUseSession() && $this->request->getPost('hash')) {
            parse_str(ltrim($this->request->getPost('hash'), '#'), $post);
            $_POST = $post;
            $this->session->set($source.'-filters', $post);
        } else if ($this->isUseSession() && $this->session->has($source.'-filters')) {
            $_POST = $this->session->get($source.'-filters');
        } else {
            $this->session->set($source.'-filters', $this->request->getPost());
        }
//        print_r($_POST);
        // if auto suggest then we need to set correct values
        if ($this->isAutosuggest()) {
            parse_str($this->request->getPost('form'), $post);
            $post['page'] = 1;
            $post['limit'] = 8; // default auto suggest items count
            $post['query'] = $this->request->getPost('query');
            $_POST = $post;
        }

        // check if we should get not active products
        if (!$this->isNoactive()) {
            $_POST['filter']['approved'] = 1;
            $_POST['filter']['product_active'] = 1;
            $_POST['filter']['supplier_active'] = 1;
        }
//print_r($_POST);
        if ($this->getSupplierId()) {
            $_POST['filter']['supplier_id'] = [$this->getSupplierId()];
        }

        if ($this->getLimitPerPage()) {
            $_POST['limit'] = $this->getLimitPerPage();
        }

//        print_r($_POST); die;
        $productsResponse = $solr->getProducts();
        $data = json_decode($productsResponse->body);
//print_r($data); die;
        // if auto suggest then we need different output
        if ($this->isAutosuggest()) {
            return $this->buildAutoSuggestResult($data);
        }

        // if result should be prepared for datatable
        if ($this->isForDatatable()) {
            return $this->buildDataTableResult($data);
        }

//print_r($data); die;

        $currentPage = $this->request->getPost('page', null, 1) ?: 1;
        $totalPages = ceil($data->response->numFound / ((int)$this->request->getPost('limit', null, 6) ?: 6));
        $htmlPagination = $this->simpleView->render('lab/'.$source.'/index/_productsPagination', [
            'current_page' => $currentPage,
            'total_pages' => $totalPages,
            'before_page' => $currentPage - 1,
            'after_page' => $currentPage + 1,
        ]);

        $docsArr = json_decode(json_encode($data->response->docs), true);
//\dump($docsArr); die;
        $htmlProductsGrid = $this->simpleView->render('lab/'.$source.'/index/_productsGrid', [
            'products' => $docsArr,
            'currentUser' => $this->session->get('auth'),
            'productAmount' => $productAmountArr,
        ]);

        $htmlProductsList = $this->simpleView->render('lab/'.$source.'/index/_productsList', [
            'products' => $docsArr,
            'currentUser' => $this->session->get('auth'),
            'productAmount' => $productAmountArr,
        ]);

        $query = $this->request->getPost('query');

        $error = '';
        $errorCode = '';
        if ($data->response->numFound == 0) {
            $error = Translations::make('There is no products to display with active criteria');
            $errorCode = '1';
            $sessionData = $this->session->get($source.'-filters');
            $sessionData['query'] = '';
            $this->session->set($source.'-filters', $sessionData);
            $query = '';
        }

        return [
            'facets' => $data->facets,
            'htmlPagination' => $htmlPagination,
            'htmlProductsGrid' => $htmlProductsGrid,
            'htmlProductsList' => $htmlProductsList,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalItems' => $data->response->numFound,
            'error' => $error,
            'errorCode' => $errorCode,
            'post' => [
                'query' => $query,
                'page' => $this->request->getPost('page'),
                'filter' => $this->request->getPost('filter'),
                'limit' => $this->request->getPost('limit'),
            ],
        ];
    }

    private function buildAutoSuggestResult($data)
    {
        $docsArr = $data->response->docs;
        $productsNameArr = [];
        foreach ($docsArr as $product) {
            $productsNameArr[] = $product->signa_id.'  |  '.$product->code.'  |  '.$product->name;
        }

        return $productsNameArr;
    }

    private function buildDataTableResult($data)
    {
        $docsArr = $data->response->docs;
        $productsNameArr = [];
        foreach ($docsArr as $product) {
            $productsNameArr[] = $product;
        }

        return [
            'products' => $productsNameArr,
            'recordsTotal' => $data->response->numFound,
            'recordsFiltered' => $data->response->numFound
        ];
    }

    /**
     * @return bool
     */
    public function isUseSession()
    {
        return $this->useSession;
    }

    /**
     * @param bool $useSession
     */
    public function setUseSession($useSession)
    {
        $this->useSession = $useSession;
    }

    /**
     * @return bool
     */
    public function isAutosuggest()
    {
        return $this->autosuggest;
    }

    /**
     * @param bool $autosuggest
     */
    public function setAutosuggest($autosuggest)
    {
        $this->autosuggest = $autosuggest;
    }

    /**
     * @return bool
     */
    public function isShortlist()
    {
        return $this->shortlist;
    }

    /**
     * @param bool $shortlist
     */
    public function setShortlist($shortlist)
    {
        $this->shortlist = $shortlist;
    }

    /**
     * @return bool
     */
    public function isNoactive()
    {
        return $this->noactive;
    }

    /**
     * @param bool $noactive
     */
    public function setNoactive($noactive)
    {
        $this->noactive = $noactive;
    }

    /**
     * @return bool
     */
    public function isForDatatable()
    {
        return $this->forDatatable;
    }

    /**
     * @param bool $forDatatable
     */
    public function setForDatatable($forDatatable)
    {
        $this->forDatatable = $forDatatable;
    }

    /**
     * @return int|null
     */
    public function getSupplierId()
    {
        return $this->supplierId;
    }

    /**
     * @param int|null $supplierId
     */
    public function setSupplierId($supplierId)
    {
        $this->supplierId = $supplierId;
    }

    /**
     * @return int|null
     */
    public function getLimitPerPage()
    {
        return $this->limitPerPage;
    }

    /**
     * @param int|null $limitPerPage
     */
    public function setLimitPerPage($limit)
    {
        $this->limitPerPage = $limit;
    }
}

