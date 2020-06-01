<?php

defined('BASE_PATH') || define('BASE_PATH', getenv('BASE_PATH') ?: realpath(dirname(__FILE__) . '/../..'));
defined('APP_PATH') || define('APP_PATH', BASE_PATH . '/app');


return new \Phalcon\Config(array(
    'application' => array(
        'controllersDir'            => APP_PATH . 'controllers/',
        'modelsDir'                 => APP_PATH . 'models/',
        'viewsDir'                  => APP_PATH . 'views/',
        'layoutsDir'                => APP_PATH . 'views/layouts/',
        'libraryDir'                => APP_PATH . 'library/',
        'cacheDir'                  => APP_PATH . 'cache/',
        'pluginsDir'	            => APP_PATH . 'plugins/',
        'langDir'                   => APP_PATH . 'translations/',
        'securityDir'               => APP_PATH . 'security/',
        'publicDir'                 => BASE_PATH . '/public/',
        'uploadDir'                 => BASE_PATH . '/public/uploads/',
        'bulkInvoicesDir'           => BASE_PATH . '/public/uploads/invoices/bulk/',
        'conceptInvoicesDir'        => BASE_PATH . '/public/uploads/invoices/concept/',
        'confirmedInvoicesDir'      => BASE_PATH . '/public/uploads/invoices/confirmed/',
        'productImagesDir'          => BASE_PATH . '/public/uploads/images/products/',
        'categoryImagesDir'         => BASE_PATH . '/public/uploads/images/category_tree/',
        'recipeImagesDir'           => BASE_PATH . '/public/uploads/images/recipes/',
        'organisationImagesDir'     => BASE_PATH . '/public/uploads/images/organisation/',
        'productCsvDir'             => BASE_PATH . '/public/uploads/attachments/products/csv/',
        'dentistOrderDir'           => BASE_PATH . '/public/uploads/attachments/dentist_order/',
        'filesDir'                  => BASE_PATH . '/public/uploads/files/',
        'projectDir'                => BASE_PATH . '/public/uploads/files/projects/',
        'productFilesDir'           => BASE_PATH . '/public/uploads/files/products/',
        'migrationsDir'             => APP_PATH . 'migrations/',
        'filesStorageDir'           => BASE_PATH . '/public/uploads/storage/',
        'baseUri'                   => '/',
        'labContractsDir'           => BASE_PATH . '/public/uploads/contracts/',
    )
));