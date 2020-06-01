<?php
/**
 * Created by PhpStorm.
 * User: Bartek
 * Date: 02.08.2016
 * Time: 11:56
 */

namespace Signa\Helpers;

use Phalcon\Http\Client\Exception;
use Signa\Models\Manufacturers;
use Signa\Models\ProductCategories;
use Signa\Models\Products;
use Signa\Models\ImportProducts;
use Signa\Models\ImportMaps;
use Signa\Helpers\Translations as Trans;

class Import
{
    protected $type;
    protected $headers;
    protected $rows;
    protected $commencingDate;
    protected $map;
    protected $mappedRows;
    protected $fileName;

    public function __construct($type, $headers, $rows, $commencingDate, $fileName)
    {
        $this->type = $type;
        $this->headers = serialize($headers);
        $this->rows = serialize($rows);
        $this->commencingDate = $commencingDate;
        $this->fileName = $fileName;
    }

    private function getCategoryTreeNode($parentId)
    {
        $categories = ProductCategories::query()
            ->columns(['id','name'])
            ->where('parent_id = :parent_id:')
            ->andWhere('deleted = 0')
            ->bind([
                'parent_id' => $parentId,
            ])
            ->execute();

        $subtree = [];

        if (count($categories) > 0) {
            foreach ($categories as $category) {
                $subtree[strtolower(trim($category->name))] = [
                    'id' => $category->id,
                    'children' => $this->getCategoryTreeNode($category->id),
                ];
            }
        }

        return $subtree;
    }

    private function buildManufacturers()
    {
        $msArray = [];

        $manufacturers = Manufacturers::query()
            ->columns(['id','name'])
            ->where('deleted_at IS NULL')
            ->execute();

        foreach ($manufacturers as $manufacturer) {
            $slugName = preg_replace('/\s+/', '', strtolower($manufacturer->name));
            $msArray[$slugName] = $manufacturer->id;
        }

        return $msArray;
    }

    private function builCategoryTree()
    {
        $tree = [];
        $categoriesMain = ProductCategories::query()
            ->columns(['id','name'])
            ->where('parent_id IS NULL')
            ->andWhere('deleted = 0')
            ->execute();

        foreach ($categoriesMain as $category) {
            $tree[strtolower(trim($category->name))] = [
                'id' => $category->id,
                'children' => $this->getCategoryTreeNode($category->id),
            ];
        }

        return $tree;
    }

    public function assignRowsToMap(array $importColumnNames)
    {
        $rows = $this->getRows();
        $mapHeaders = $this->getMap();
        $newRowsArr = array();
        $categoriesTree = $this->builCategoryTree();
        $manufacturers = $this->buildManufacturers();

        $codes = [];
        $productsWithDuplicatedCodes = [];

//        \dump($mapHeaders);
//        \dump($rows);

        foreach ($rows as $keyRow => $row)
        {
            foreach ($importColumnNames as $keyImportColumnName => $importColumnName)
            {
//                $newRowsArr[$keyRow][$importColumnName['name']] = null;
                foreach ($mapHeaders as $keyMapHeader => $mapHeader)
                {
                    if($mapHeader == $importColumnName['id']){
                        $newRowsArr[$keyRow][$importColumnName['name']] = $row[$keyMapHeader];
                        if ($importColumnName['name'] == 'image_url' && !$row[$keyMapHeader]) {
                            $newRowsArr[$keyRow][$importColumnName['name']] = '-';
                        }
                    }
                }
//                \dump($newRowsArr[$keyRow]);
                if (!isset($newRowsArr[$keyRow]['image_url'])) {
                    $newRowsArr[$keyRow]['image_url'] = 'previous';
                }
            }
//            \dump($newRowsArr[$keyRow]); die;
            // Validate product if is ok

            $vp = $this->validateProduct($newRowsArr[$keyRow], $categoriesTree, $manufacturers);
            if(!empty($vp['errorArr'])){
                $newRowsArr[$keyRow]['status_array'] = $vp['errorArr'];
                $newRowsArr[$keyRow]['status'] = $this->errorsToString($vp['errorArr']);
            } else {
                $newRowsArr[$keyRow]['status'] = array();
            }
            $newRowsArr[$keyRow]['catIds'] = $vp['catIds'];
            $newRowsArr[$keyRow]['manufacturersId'] = $vp['manufacturersId'];
            $newRowsArr[$keyRow]['customErrorLabel'] = $vp['customErrorLabel'];

            if (!array_key_exists((string)$newRowsArr[$keyRow]['code'], $codes)) {
                $codes[$newRowsArr[$keyRow]['code']] = [];
            }
            $codes[$newRowsArr[$keyRow]['code']][] = $keyRow;
            if (count($codes[$newRowsArr[$keyRow]['code']]) > 1) {
                $productsWithDuplicatedCodes = array_merge($productsWithDuplicatedCodes, $codes[$newRowsArr[$keyRow]['code']]);
            }
//if ($newRowsArr[$keyRow]['code'] == '142681') {
//                \dump($codes[142681]);
//                \dump($keyRow);
//}
        }
//        \dump($newRowsArr); die;

//\dump($productsWithDuplicatedCodes); die;
        $codeErr = Translations::make('There is another product with this same product code in your import file. Are you sure you want to proceed? Please change your import file if this double product code is not correct.');
        foreach ($productsWithDuplicatedCodes as $keyRow) {
            if (empty($newRowsArr[$keyRow]['status'])) {
                $newRowsArr[$keyRow]['status_array'] = [$codeErr];
                $newRowsArr[$keyRow]['status'] = $this->errorsToString([$codeErr]);
            } else {
                $newRowsArr[$keyRow]['status_array'][] = $codeErr;
                $newRowsArr[$keyRow]['status'] .= $codeErr.'<br />';
            }
        }

        $this->setMappedRows($newRowsArr);
    }

    private function validateProduct(array $product, array $categoriesTree, array $manufacturers)
    {
        $errorArr = array();
        $customErrorLabel = '';
        $catIds = [
            'main_product_category' => 0,
            'sub_product_category' => 0,
            'sub_sub_product_category' => 0,
        ];
        $catNames = [
            'main_product_category' => '',
            'sub_product_category' => '',
            'sub_sub_product_category' => '',
        ];
        $manufacturersId = 0;
        $manufacturersName = '';

        // check if needed keys exists
        if (!array_key_exists('name', $product)) {
            array_push($errorArr, Trans::make('Name can not be empty.'));
        }
        if (!array_key_exists('price', $product)) {
            array_push($errorArr, Trans::make('Price can not be empty.'));
        }
        if (!array_key_exists('code', $product)) {
            array_push($errorArr, Trans::make('Code can not be empty.'));
        }
        if (!array_key_exists('main_product_category', $product)) {
            array_push($errorArr, Trans::make('Main category can not be empty.'));
        }

        foreach ($product as $key => $value)
        {
            switch ($key)
            {
                case 'name':
                    if($value === null){ array_push($errorArr, Trans::make('Name can not be empty.')); }
                    break;
                case 'price':
                    if($value === null){ array_push($errorArr, Trans::make('Price can not be empty.')); }
                    break;
                case 'code':
                    if($value === null){ array_push($errorArr, Trans::make('Code can not be empty.')); }
                    break;
                case 'manufacturer':
                    if($value === null){
                        array_push($errorArr, Trans::make('Manufacturer can not be empty.'));
                        $customErrorLabel = 'Blocked';
                    } else if (
                        array_key_exists(
                            preg_replace('/\s+/', '', strtolower($value)),
                            $manufacturers
                        )
                    ) {
                        $manufacturersId = $manufacturers[preg_replace('/\s+/', '', strtolower($value))];
                        $manufacturersName = preg_replace('/\s+/', '', strtolower($value));
                    } else {
                        array_push($errorArr, Trans::make('This product will not be imported because it has a unknown manufacturer'));
                        $customErrorLabel = 'Blocked';
                    }
                    break;
                case 'main_product_category':
                    if($value === null){
                        array_push($errorArr, Trans::make('Main category can not be empty.'));
                    } else if (array_key_exists(strtolower(trim($value)), $categoriesTree)) {
                        $catIds['main_product_category'] = $categoriesTree[strtolower(trim($value))]['id'];
                        $catNames['main_product_category'] = strtolower(trim($value));
                    } else {
                        array_push($errorArr, Trans::make('Wrong main category'));
                    }
                    break;
                case 'sub_product_category':
                    if (empty($categoriesTree[$catNames['main_product_category']]['children'])) {
                        break;
                    }

                    if($value === null){
                        array_push($errorArr, Trans::make('Sub category can not be empty.'));
                    } else if (array_key_exists(strtolower(trim($value)), $categoriesTree[$catNames['main_product_category']]['children'])) {
                        $catIds['sub_product_category'] = $categoriesTree[$catNames['main_product_category']]['children'][strtolower(trim($value))]['id'];
                        $catNames['sub_product_category'] = strtolower(trim($value));
                    } else {
                        array_push($errorArr, Trans::make('Wrong sub category'));
                    }
                    break;
                case 'sub_sub_product_category':
                    if (empty($categoriesTree[$catNames['main_product_category']]['children'][$catNames['sub_product_category']]['children'])) {
                        break;
                    }

                    if($value === null){
                        array_push($errorArr, Trans::make('Subsub category can not be empty.'));
                    } else if (array_key_exists(strtolower(trim($value)), $categoriesTree[$catNames['main_product_category']]['children'][$catNames['sub_product_category']]['children'])) {
                        $catIds['sub_sub_product_category'] = $categoriesTree[$catNames['main_product_category']]['children'][$catNames['sub_product_category']]['children'][strtolower(trim($value))]['id'];
                        $catNames['sub_sub_product_category'] = strtolower(trim($value));
                    } else {
                        array_push($errorArr, Trans::make('Wrong subsub category'));
                    }
                    break;
//                case 'key_words':
//                    if($value === null){ array_push($errorArr, Trans::make('Key words can not be empty.')); }
//                    break;
//                case 'package_unit':
//                    if(!is_int($value)){ array_push($errorArr, Trans::make('Package unit. Wrong format.')); }
//                    break;
//                case 'delivery_time':
//                    if(!is_int($value)){ array_push($errorArr, Trans::make('Delivery time. Wrong format.')); }
//                    break;
                case 'image_url':
                    if($value !== null){
                        $errors = $this->validateImages($value);
                        if($errors != '')
                            array_push($errorArr, 'Invalid images url:'.$errors);
                    }
                    break;
            }
        }
        if(count($errorArr)){}
//            $errorArr = $this->errorsToString($errorArr);

        return [
            'catIds' => $catIds,
            'errorArr' => $errorArr,
            'manufacturersId' => $manufacturersId,
            'manufacturersName' => $manufacturersName,
            'customErrorLabel' => $customErrorLabel
        ];
    }

    private function validateImages($url)
    {
        //$urlArr = explode('#', $urls);
        $errorArr = '';
        //foreach ($urlArr as $url)
        //{
            /*$isValid = getimagesize($url);
            if(!(bool)$isValid)
                $errorArr .= ' <strong>'.$url.'</strong>';
        //}*/
        return $errorArr;
    }

    private function errorsToString(array $errors)
    {
        $errorString = '';
        foreach ($errors as $error)
        {
            $errorString .= $error.'<br />';
        }
        return $errorString;
    }

    public static function mapUniqueValidation(array $map)
    {
        foreach ($map as $key => $value)
        {
            if($value == 0){
                unset($map[$key]);
            }
        }
        if((count($map) !== count(array_unique($map))) || count($map) === 0)
            return true;

        return false;
    }

    public static function mapRequiredValidation(array $map, array $fields)
    {
        $requiredFields = [];
        foreach ($fields as $field) {
            if ($field['req'] == 1) {
                $requiredFields[] = $field['id'];
            }
        }

        $missingReq = array_diff($requiredFields, $map);

        if (count($missingReq) > 0) {
            return true;
        }

        return false;
    }

    public static function fullImport(ImportProducts $import, array $selectedProducts, $categoryId = null)
    {
        $products = $import->selectedProductsToImport($selectedProducts);
        $productsImportedArr = array();
        foreach ($products as $product)
        {
            $product->approveWithCategory($categoryId);
            $productsImportedArr[] = $product->toArray();
        }

        $saved = $import->checkProducts();
        return array('products' => $productsImportedArr, 'saved' => $saved);
    }

    /*
     * ToDo: Add image resizing
     */
    public static function saveProductImages($imagesUrls, $productId, $userId = null)
    {
        $di = \Phalcon\DI::getDefault();
        $configDir = $di->getConfig()->application->productImagesDir;
        $imagesUrlsArr = explode('#', $imagesUrls);
        $serializedImgData = array();

        if($imagesUrlsArr != '')
        {
            foreach ($imagesUrlsArr as $key => $dirtyUrl)
            {
                $url = trim($dirtyUrl);
//                echo "|$url|"; die;
                $fixedUrl = preg_replace('/\s+/', '%20', $url);
                $isValid = getimagesize($fixedUrl);
                if(!(bool)$isValid)
                    continue;
                // Get image name from url
                preg_match('/(([^\/])+)$/', $url, $matches);

                $imageName = General::cleanString($matches[0]);

                // Generate unique image name
                $uniqueImageName = self::generateRandomString().'_'.$imageName;

                // Checking if product folder exist and creating it
                $imgDir = $configDir.$productId . '/';
                if(!is_dir($imgDir)) {
//                    mkdir($imgDir, 0777);
                    mkdirR($imgDir);
                }
                // Getting image from url and save into folder
                copy($fixedUrl, $imgDir.$uniqueImageName);
//                file_put_contents($imgDir.$uniqueImageName, file_get_contents($fixedUrl));


                General::resizeImage($imgDir.$uniqueImageName);

                $serializedImgData[] = array(
                    'added_by' => $userId,
                    'original_name' => $imageName,
                    'unique_name' => $uniqueImageName,
//                    'url' => $url,
                    'url' => '/uploads/images/products/'.$productId.'/'.$uniqueImageName,
                    );
            }
        }else{
            return null;
        }
        return $serializedImgData;
    }

    public static function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function getProductsToUpdate()
    {
        $di = \Phalcon\DI::getDefault();
        $user = $di->getSession()->get('auth');
        $productsList = $this->getMappedRows();
        $oldProductCounter = 0;
//        echo '<br />2.1. Total execution time in seconds: ' . (microtime(true) - START_TIME);

        $currentDate = Date::currentDate();

        $oldProductsArr = [];
        $oldProducts = Products::query()
            ->where('supplier_id = :supplier_id: AND deleted = 0 AND approved = 1 AND active = 1 AND declined = 0')
            ->andWhere('skipped IS NULL')
            ->andWhere('start_date <= :current_date:')
            ->columns([
                'id',
                'name',
                'material',
                'price',
                'currency',
                'code',
                'description',
                'images',
//                'manufacturer',
                'manufacturer' => 'manufacturer_id',
                'amount_min',
                'amount_include',
                'barcode_supplier',
                'delivery_time',
                'product_group',
                'tax_percentage',
                'external_link',
                'external_link_productsheet',
                'main_product_category' => 'main_category_id',
                'sub_product_category' => 'sub_category_id',
                'sub_sub_product_category' => 'sub_sub_category_id',
                'special_order',
            ])
            ->bind([
                'supplier_id' => $user->Organisation->getId(),
                'current_date' => $currentDate,
            ])
            ->orderBy('created_at ASC')
            ->groupBy('Signa\Models\Products.code')
            ->execute()
            ->toArray();

//        echo '<br />2.2. Total execution time in seconds: ' . (microtime(true) - START_TIME);

        foreach ($oldProducts as $oldProduct) {
            $oldProductsArr[trim($oldProduct['code'])] = $oldProduct;
        }

//        echo '<br />2.3. Total execution time in seconds: ' . (microtime(true) - START_TIME);

        foreach ($productsList as $keyNewProduct => $newProduct)
        {
//            $s = microtime(true);

            if (array_key_exists(trim($newProduct['code']), $oldProductsArr)) {
                $oldProductArr = $oldProductsArr[trim($newProduct['code'])];

                if(trim($newProduct['code']) == trim($oldProductArr['code'])){
                    $checkValuesArr = $this->compareProducts($newProduct, $oldProductArr);
                    $productsList[$keyNewProduct]['newValues'] = $checkValuesArr['new'];
                    $productsList[$keyNewProduct]['oldValues'] = $checkValuesArr['old'];
                    $oldProductCounter++;
                }
            }
//            echo '<br />2.4. Total execution time in seconds: ' . (microtime(true) - $s);
        }
//        echo '<br />2.5. Total execution time in seconds: ' . (microtime(true) - START_TIME);
        $this->setMappedRows($productsList);
        return count($productsList);
    }

    /*
     * Values to update: name, description, price, currency, material
     */
    private function compareProducts(array $new, array $old)
    {
        $new['newValues'] = [];
        $new['oldValues'] = [];

        $oldProductImagesUrls = '';
        if($old['images']!='') {
        $oldProductImages = unserialize($old['images']);


            foreach ($oldProductImages as $key => $image) {
                if ($oldProductImagesUrls != '')
                    $oldProductImagesUrls .= '#';
                $oldProductImagesUrls .= $image['url'];
            }
        }
        $new['update'] = false;
        $new['changed_price'] = false;

        /*
         * Compared product fields, after added new in DB, add here
         */
        $comparedFields = array(
            'name',
            'description',
            'price',
            'material',
//            'manufacturer',
            'amount_include',
            'amount_min',
            'special_order',
        );

        foreach ($comparedFields as $field)
        {
            if(array_key_exists($field, $old)) {
                if ($field === 'price') {
                    // make decimal format
                    $new[$field] = preg_replace('/\,/','.', $new[$field]);
                }
                if (strtolower($old[$field]) != strtolower($new[$field]) && !is_null($new[$field])) {
                    $old[$field] = array('value' => $old[$field], 'change' => true);
                    $new[$field] = array('value' => $new[$field], 'change' => true);
                    $new['update'] = true;
                    /*
                     * Check if product price is changed
                     */
                    if ($field === 'price') {
                        $new['changed_price'] = true;
                    }
                } else {
                    $old[$field] = array('value' => $old[$field], 'change' => false);
                    $new[$field] = array('value' => $new[$field], 'change' => false);
                }
            }
        }

        // Specific fields which cannot be easy compared in loop
        if($old['currency'] != $new['price_currency'] && !is_null($new['price_currency'])){
            $old['price_currency'] = array('value' => $old['currency'], 'change' => true);
            $new['price_currency'] = array('value' => $new['price_currency'], 'change' => true);
            $new['update'] = true;
        }else{
            $old['price_currency'] = array('value' => $old['currency'], 'change' => false);
            $new['price_currency'] = array('value' => $new['price_currency'], 'change' => false);
        }
        if($oldProductImagesUrls != $new['image_url'] && !is_null($new['image_url']) && $new['image_url'] != 'previous'){
            $old['image_url'] = array('value' => $oldProductImagesUrls, 'change' => true);
            $new['image_url'] = array('value' => $new['image_url'], 'change' => true);
            $new['update'] = true;
        }else{
            $old['image_url'] = array('value' => $oldProductImagesUrls, 'change' => false);
            $new['image_url'] = array('value' => $new['image_url'], 'change' => false);
        }

        // categories compare
        $catFields = ['main_product_category', 'sub_product_category', 'sub_sub_product_category'];
        foreach ($catFields as $catField) {
            if ($old[$catField]) {
                $cat = ProductCategories::query()
                    ->where('id = :id:')
                    ->bind(['id' => $old[$catField]])
                    ->columns(['name'])
                    ->execute();
                $name = '';
                if (count($cat) > 0) {
                    $name = $cat[0]->name;
                }

                if (strtolower($new[$catField]) != strtolower($name)) {
                    $old[$catField] = ['value' => $name, 'change' => true];
                    $new[$catField] = ['value' => $new[$catField], 'change' => true];
                } else {
                    $old[$catField] = ['value' => $name, 'change' => false];
                    $new[$catField] = ['value' => $new[$catField], 'change' => false];
                }
            }
        }

        // manufacturer !!
        if ($old['manufacturer']) {
            $manufacturer = Manufacturers::query()
                ->where('id = :id:')
                ->bind(['id' => $old['manufacturer']])
                ->columns(['name'])
                ->execute();
            $name = '';
            if (count($manufacturer) > 0) {
                $name = $manufacturer[0]->name;
            }

            $oldName = preg_replace('/\s+/', '', strtolower($new['manufacturer']));
            $newName = preg_replace('/\s+/', '', strtolower($name));

            if ($oldName != $newName) {
                $old['manufacturer'] = ['value' => $name, 'change' => true];
                $new['manufacturer'] = ['value' => $new['manufacturer'], 'change' => true];
            } else {
                $old['manufacturer'] = ['value' => $name, 'change' => false];
                $new['manufacturer'] = ['value' => $new['manufacturer'], 'change' => false];
            }
        }

//        print_r($new);
//        print_r($old); die;

        return array('new' => $new, 'old' => $old);
    }

    public static function updateImport(ImportProducts $import, array $selectedProducts, $cronlog = false)
    {
        $products = $import->selectedProductsToImport($selectedProducts);

        if ($cronlog) {
            echo "\n" . sprintf(':: UPDATE IMPORT :: found %s products', count($products));
        }

//        echo '<br />2.1 Total execution time in seconds: ' . (microtime(true) - START_TIME);
        $productsImportedArr = array();
        $c = 0;
        /** @var Products $product */
        foreach ($products as $product)
        {
            $c++;
//            $ct = microtime(true);
            $product->approveWithoutCategory();
//            echo '<br />2.2.1 Total execution time in seconds: ' . (microtime(true) - $ct);
//            $ct = microtime(true);
            $product->activateNewProduct();
//            echo '<br />2.2.2 Total execution time in seconds: ' . (microtime(true) - $ct);
            $productsImportedArr[] = $product->toArray();
//            echo '<br />===================';

            if ($cronlog) {
                echo "\n" . sprintf(':: UPDATE IMPORT :: [%s / %s] :: product update: %s', $c, count($products), $product->getId());
            }
        }
//        echo '<br />2.3 Total execution time in seconds: ' . (microtime(true) - START_TIME);

//        $saved = $import->checkProducts();
//        echo '<br />2.4 Total execution time in seconds: ' . (microtime(true) - START_TIME);
        return array('products' => $productsImportedArr);
    }

    public static function createQueue(array $products, array $excludedProducts, $type, $commencingDate, $filename)
    {
//        echo '<br />1 Total execution time in seconds: ' . (microtime(true) - START_TIME);
        $di = \Phalcon\DI::getDefault();
        $user = $di->getSession()->get('auth');

        $valuesToSave = array(
            'type' => $type,
            'effective_from' => $commencingDate,
            'filename' => $filename,
            'closed' => 0,
        );

        $import = new ImportProducts();
        $import->saveData($valuesToSave);
        $productsAdded = array();

        // Remove excluded products from products list and import new
//        echo '<br />2 Total execution time in seconds: ' . (microtime(true) - START_TIME);
//        print_r($products); die;

        foreach ($products as $key => &$product)
        {
//            $st = microtime(true);
            if(!in_array($key, $excludedProducts))
            {
                // Null image column to validate them later
                $productImagesUrls = $product['image_url'];
                $product['image_url'] = null;
                $product['start_date'] = $commencingDate;
                $product['import_id'] = $import->getId();
                $product['supplier_id'] = $user->Organisation->getId();

                if($type == 'update')
                {
                    $product['old_product_id'] = $product['oldValues']['id'];
//                    if($product['old_product_id'] == null)
//                    {
//                        continue;
//                    }
                    if($product['newValues']['changed_price'])
                    {
                        $product['changed_price'] = 1;
                    }
                }

                if (!$productImagesUrls && isset($product['oldValues']) && $product['oldValues']['images']) {
                    $images = unserialize($product['oldValues']['images']);
                    if (count($images) > 0) {
                        $productImagesUrls = $images[0]['url'];
                    }
                }
//echo $productImagesUrls; die;
                $newProduct = new Products();

                $newProduct->setDeleted(0);
                $newProduct->setApproved(0);
                $newProduct->setDeclined(0);
                $newProduct->setActive(0);
                $newProduct->setWaitingImages($productImagesUrls);

                $newProduct->saveData($product);

                // Unset values unnecessary in logs
                unset($product['supplier_id']);
                if(count($newProduct->getMessages()) < 1)
                {
                    $productsAdded[] = $product;
                } else {
//                    var_dump($newProduct->getMessages());die;
                }
            }
//            echo '<br />4 Total execution time in seconds: ' . (microtime(true) - $st);
//            if ($key == 100) {
//                break;
//            }
        }
//        echo '<br />6 Total execution time in seconds: ' . (microtime(true) - START_TIME);
//        \dump($products); die;

        if(count($productsAdded) < 1)
        {
            $import->delete();
        }


        return $productsAdded;
    }

    public static function decline(ImportProducts $import, $message, array $messages)
    {
        $productsDeclinedArr = array();
        foreach ($messages as $id => $productDecline)
        {
            $product = Products::findFirst($id);
            $product->setDeclineMessage($productDecline);
            $product->setDeclined(1);
            $product->save();

            $productsDeclinedArr[] = $product->toArray();
        }
        $import->setMessage($message);
        $import->save();
        $import->checkProducts();

        return $productsDeclinedArr;
    }

    public static function saveMap(array $map, $fileName)
    {
        $di = \Phalcon\DI::getDefault();
        $user = $di->getSession()->get('auth');
        $organisationId = $user->Organisation->getId();
        $importMap = ImportMaps::findFirst('organisation_id = '.$organisationId.' AND file LIKE \''.$fileName.'\'');
        $data = array('file' => $fileName, 'organisation_id' => $organisationId, 'map' => $map);

        if($importMap === false)
        {
            $importMap = new ImportMaps();
        }
        $saved = $importMap->saveData($data);
    }


    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getHeaders()
    {
        return unserialize($this->headers);
    }

    /**
     * @return string
     */
    public function getRows()
    {
        return unserialize($this->rows);
    }

    /**
     * @return mixed
     */
    public function getCommencingDate()
    {
        return $this->commencingDate;
    }

    /**
     * @return mixed
     */
    public function getMap()
    {
        return unserialize($this->map);
    }

    /**
     * @param mixed $map
     */
    public function setMap($map)
    {
        $this->map = serialize($map);
    }

    /**
     * @return mixed
     */
    public function getMappedRows()
    {
        return unserialize($this->mappedRows);
    }

    /**
     * @param mixed $mappedRows
     */
    public function setMappedRows($mappedRows)
    {
        $this->mappedRows = serialize($mappedRows);
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param mixed $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

}