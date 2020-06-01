<?php
namespace Signa\Libs;

use Phalcon\Http\Client\Request;
use Signa\Helpers\Date;
use Signa\Models\Products;
use Phalcon\Mvc\User\Component;
use Signa\Libs\Products\ProductsSolr;

/**
 * Class with log functions to read and create logs in MongoDB
 *
 */
class Solr extends Component
{
    private $provider = null;

    private $ids = [];

    private $shortlist_organisation;

    public function __construct()
    {
        $this->createProvider();
    }

    public function getProducts()
    {
//        print_r((new ProductsSolr())->getProductsGetArray($this->getShortlistOrganisation()));
//
//        print_r($this->provider->post(
//            'query',
//            json_encode((new ProductsSolr())->getProductsGetArray($this->getShortlistOrganisation()))
//        )); die;

        return $this->provider->post(
            'query',
            json_encode((new ProductsSolr())->getProductsGetArray($this->getShortlistOrganisation()))
        );
    }

    /**
     * @param bool $cron
     * @return Phalcon\Http\Client\Response
     */
    public function updateProducts($cron = false)
    {
        if ($cron) {
            echo "\n".sprintf('Preparing POST action...');
        }
        return $this->provider->post(
            'update?commit=true&optimize=true',
            json_encode((new ProductsSolr())->getDbProducts($this->getIds(), $cron))
        );
    }

    /**
     * @return Phalcon\Http\Client\Response
     */
    public function deleteAllProducts()
    {
        $this->provider->header->set('Content-Type', 'text/xml');
        $this->provider->setOption('CURLOPT_POSTFIELDS', '<delete><query>*:*</query></delete>');
        return $this->provider->post(
            'update?commit=true'
        );
    }

    /**
     * @return Phalcon\Http\Client\Response
     */
    public function deleteProducts()
    {
        if (empty($this->getIds())) {
            return false;
        }
        return $this->provider->post(
            'update?commit=true&optimize=true',
            json_encode((new ProductsSolr())->createDeleteQuery($this->getIds()))
        );
    }

    private function createProvider()
    {
        $this->provider = Request::getProvider();
        $this->provider->setBaseUri(sprintf(
            '%s://%s:%s/solr/%s/',
            $this->config->solr->protocol,
            $this->config->solr->host,
            $this->config->solr->port,
            $this->config->solr->collection
        ));
        $this->provider->setOption('CURLOPT_TIMEOUT', 300);
        $this->provider->header->set('Content-Type', 'application/json');
    }

    /**
     * @return array
     */
    public function getIds()
    {
        return $this->ids;
    }

    /**
     * @param array $ids
     */
    public function setIds($ids)
    {
        $this->ids = $ids;
    }

    /**
     * @return mixed
     */
    public function getShortlistOrganisation()
    {
        return $this->shortlist_organisation;
    }

    /**
     * @param mixed $shortlist_organisation
     */
    public function setShortlistOrganisation($shortlist_organisation)
    {
        $this->shortlist_organisation = $shortlist_organisation;
    }
}

