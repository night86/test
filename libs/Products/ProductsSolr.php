<?php
namespace Signa\Libs\Products;

use Phalcon\Http\Client\Request;
use Signa\Helpers\Date;
use Signa\Models\Products;
use Phalcon\Mvc\User\Component;

/**
 * Class with log functions to read and create logs in MongoDB
 *
 */
class ProductsSolr extends Component
{
    private $searchFields = [
        'id', 'signa_id', 'name', 'description', 'code', 'manufacturer', 'material', 'product_group', 'supplier_name'
    ];

    private function fixQuery($query)
    {
        if (!$query) {
            return '*:*';
        }

        if ($query == '*:*') {
            return $query;
        }

        $queryArr = [];
        foreach ($this->searchFields as $field) {
            $queryArr[] = $field.':*'.preg_replace('/\s+/', '*', $query).'*';
        }

        return implode(' OR ', $queryArr);
    }

    public function getProductsGetArray($shortlistOrganisation = null)
    {

        $query = trim($this->request->getPost('query', null, '*:*'));
        $filter = $this->request->getPost('filter', null, []);
        $page = $this->request->getPost('page', null, 1);
        $limit = $this->request->getPost('limit', null, 6);
        $fields = $this->request->getPost('fields', null, []);
        $sort = $this->request->getPost('sort', null, 'name asc');

        // additional validation, because sometimes empty string is not replaced by defaul value
        if (!$query) { $query = '*:*'; }
        if (!$filter) { $filter = []; }
        if (!$page) { $page = 1; }
        if (!$limit) { $limit = 6; }
        if (!$fields) { $fields = []; }
        if (!$sort) { $sort = 'name asc'; }

//echo $query; die;
//echo $this->filterFacetFormat($query, $filter, 'sub_sub_category_id'); die;

        if (is_string($filter)) {
            $filter = [];
        }
        if ($shortlistOrganisation) {
            $filter['shortlist'] = [
                $shortlistOrganisation,
                $shortlistOrganisation.',*',
                '*,'.$shortlistOrganisation,
                '*,'.$shortlistOrganisation.',*'
            ];
        }

        $facet = [
            'possible_main_category_id' => [
                'type' => 'terms',
                'limit' => -1,
                'field' => 'main_category_id',
                'domain' => [ 'excludeTags' => 'main_category_id,sub_category_id,sub_sub_category_id' ]
            ],
            'possible_sub_category_id' => [
                'type' => 'terms',
                'limit' => -1,
                'field' => 'sub_category_id',
                'domain' => [ 'excludeTags' => 'sub_category_id,sub_sub_category_id' ]
            ],
            'possible_sub_sub_category_id' => [
                'type' => 'terms',
                'limit' => -1,
                'field' => 'sub_sub_category_id',
                'domain' => [ 'excludeTags' => 'sub_sub_category_id' ]
            ],
            'possible_supplier_id' => [
                'type' => 'terms',
                'limit' => -1,
                'field' => 'supplier_id',
                'domain' => [ 'excludeTags' => 'supplier_id' ]
            ],
            'possible_manufacturer' => [
                'type' => 'terms',
                'limit' => -1,
                'field' => 'manufacturer',
                'domain' => [ 'excludeTags' => 'manufacturer' ],
                'sort' => 'index'
            ]
        ];

        $toExclude = [
            'supplier_id' => ['supplier_id'],
            'manufacturer' => ['manufacturer'],
            'main_category_id' => ['main_category_id', 'sub_category_id', 'sub_sub_category_id'],
            'sub_category_id' => ['sub_category_id', 'sub_sub_category_id'],
            'sub_sub_category_id' => ['sub_sub_category_id']
        ];

        $withoutSpaces = ['shortlist'];
//        print_r($facet); die;

        if ($page < 1) { $page = 1; }
        $offset = ($page - 1) * $limit;

        // if quesry is from select2 list:

        $qSplited = explode('  |  ', $query);
        if (count($qSplited) === 3) {
            $filter['signa_id'] = $qSplited[0];
//            $filter['code'] = $qSplited[1];
//            $filter['name'] = $qSplited[2];
            $query = '*:*';
        } else {
            $query = $this->fixQuery($query);
        }
//echo $query; die;
        return [
            'query' => $query,
            'limit' => $limit,
            'offset' => $offset,
            'sort' => $sort,
            'fields' => $fields,
            'filter' => $this->filterFormat($filter, $toExclude, $withoutSpaces),
            'facet' => $facet
        ];
    }

    private function filterFormat($filter, $toExclude = [], $withoutSpaces = [])
    {
        if (empty($filter)) {
            return [];
        }

        $result = [];
        foreach ($filter as $k => $v) {
            $excludeRule = '';
            if (array_key_exists($k, $toExclude)) {
                $excludeRule = sprintf('{!tag=%s}', implode(',', $toExclude[$k]));
            }
            if (is_array($v)) {
                $tmpRes = [];
                foreach ($v as $vE) {
                    if (in_array($k, $withoutSpaces)) {
                        $tmpRes[] = sprintf('%s:%s', $k, $vE);
                    } else {
                        $tmpRes[] = sprintf('%s:"%s"', $k, $vE);
                    }
                }
                $result[] = sprintf('%s(%s)', $excludeRule, implode(' OR ', $tmpRes));
            } else {
                if (in_array($k, $withoutSpaces)) {
                    $result[] = sprintf('%s%s:%s', $excludeRule, $k, $v);
                } else {
                    $result[] = sprintf('%s%s:"%s"', $excludeRule, $k, $v);
                }
            }
        }

        return $result;
    }

    public function createDeleteQuery($ids)
    {
        $query = new \stdClass();
        $query->delete = $ids;
        return $query;
    }

    public function getDbProducts($ids = [], $cron = false)
    {
        if ($cron) {
            echo "\n".sprintf('Getting products from DB...');
        }
        $currentDate = Date::currentDate();
        $products = Products::query()
            ->join('Signa\Models\Organisations', 'o.id = Signa\Models\Products.supplier_id', 'o')
            ->leftJoin('Signa\Models\OrderShortlist', 'Signa\Models\Products.id = os.product_id', 'os')
            ->leftJoin('Signa\Models\Manufacturers', 'Signa\Models\Products.manufacturer_id = m.id', 'm')
//            ->where("Signa\Models\Products.start_date <= '". $currentDate. "' AND deleted = 0 AND approved = 1 AND Signa\Models\Products.active = 1 AND declined = 0 AND o.active = 1")
            ->where("Signa\Models\Products.start_date <= '". $currentDate. "' AND deleted = 0 AND declined = 0 AND approved = 1")
            ->andWhere('Signa\Models\Products.skipped IS NULL')
            ->columns([
                "Signa\\Models\\Products.id",
                "Signa\\Models\\Products.description",
                "Signa\\Models\\Products.name",
                "Signa\\Models\\Products.code",
                "Signa\\Models\\Products.images",
//                "Signa\\Models\\Products.manufacturer",
                "Signa\\Models\\Products.material",
                "Signa\\Models\\Products.external_link_productsheet",
                "Signa\\Models\\Products.barcode_supplier",
                "Signa\\Models\\Products.delivery_time",
                "Signa\\Models\\Products.amount_min",
                "Signa\\Models\\Products.amount_include",
                "Signa\\Models\\Products.price",
                "Signa\\Models\\Products.currency",
                "Signa\\Models\\Products.supplier_id",
                "Signa\\Models\\Products.category_id",
                "Signa\\Models\\Products.main_category_id",
                "Signa\\Models\\Products.sub_category_id",
                "Signa\\Models\\Products.sub_sub_category_id",
                "Signa\\Models\\Products.product_group",
                "Signa\\Models\\Products.special_order",
                "Signa\\Models\\Products.signa_id",
                'shortlist' => 'GROUP_CONCAT(os.organisation_id)',
                "Signa\\Models\\Products.approved",
                'product_active' => "Signa\\Models\\Products.active",
                'supplier_active' => 'o.active',
                'supplier_name' => 'o.name',
                'manufacturer' => 'm.name',
                'product_id' => 'Signa\Models\Products.id'
            ])
            ->groupBy('Signa\\Models\\Products.id');

        if (!empty($ids)) {
            $products->andWhere('Signa\Models\Products.id IN ({ids:array})')
                ->bind([
                    'ids' => $ids
                ]);
        }

        $productsRes = $products->execute()->toArray();

        if ($cron) {
            echo "\n".sprintf('%s products found!!', count($productsRes));
            echo "\n".sprintf('Mapping...');
        }

        foreach ($productsRes as $k => $p) {
            $productsRes[$k] = array_map(function($v){
                return (is_null($v)) ? "" : $v;
            },$p);

            if ($cron) {
                echo "\n".sprintf('- %s / %s done', $k, count($productsRes));
            }
        }

        if ($cron) {
            echo "\n".sprintf('Products ready to send to SOLR');
        }

        return $productsRes;
    }
}

