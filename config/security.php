<?php
/**
 * acl structure [organisation][controller][action] = roles in array
 */
return new \Phalcon\Config(array(
    'access' => array(
        'dentist' => array(
            'index' => array(
                'index' => array('ROLE_DENTIST_INDEX_INDEX'),
                'ajaxcalendar' => array('ROLE_DENTIST_INDEX_INDEX'),
                'start' => array('ROLE_DENTIST_INDEX_INDEX')
            ),
            'user' => array(
                'index' => array('ROLE_DENTIST_USER_INDEX'),
                'ajaxlist' => array('ROLE_DENTIST_USER_INDEX'),
                'add' => array('ROLE_DENTIST_USER_ADD'),
                'edit' => array('ROLE_DENTIST_USER_EDIT'),
                'organisation' => array('ROLE_DENTIST_USER_EDIT'),
                'delete' => array('ROLE_DENTIST_USER_DELETE'),
                'deleteorganisationimage' => array('ROLE_DENTIST_USER_EDIT'),
                'deactivate' => array('ROLE_DENTIST_USER_DEACTIVATE'),
                'activate' => array('ROLE_DENTIST_USER_ACTIVATE'),
                'loginasuser' => array('ROLE_DENTIST_USER_LOGINASUSER'),
                'backtoadmin' => array('ROLE_DENTIST_USER_BACKTOADMIN'),
                'register' => array('ROLE_GUEST')
            ),
            'organisation' => array(
                'index' => array('ROLE_DENTIST_ORGANISATION_INDEX'),
                'edit' => array('ROLE_DENTIST_ORGANISATION_EDIT')
            ),
            'role' => array(
                'index' => array('ROLE_DENTIST_ROLE_INDEX'),
                'add' => array('ROLE_DENTIST_ROLE_ADD'),
                'edit' => array('ROLE_DENTIST_ROLE_EDIT'),
                'delete' => array('ROLE_DENTIST_ROLE_DELETE'),
                'deactivate' => array('ROLE_DENTIST_ROLE_DEACTIVATE'),
                'activate' => array('ROLE_DENTIST_ROLE_ACTIVATE'),
                'reset' => array('ROLE_DENTIST_ROLE_RESET')
            ),
            'group_contract' => array(
                'index' => array('ROLE_DENTIST_GROUPCONTRACT_INDEX')
            ),
            'group_dentist' => array(
                'index' => array('ROLE_DENTIST_GROUPDENTIST_INDEX'),
                'edit' => array('ROLE_DENTIST_GROUPDENTIST_EDIT'),
                'activate' => array('ROLE_DENTIST_GROUPDENTIST_ACTIVATE'),
                'deactivate' => array('ROLE_DENTIST_GROUPDENTIST_DEACTIVATE'),
                'loginasuser' => array('ROLE_DENTIST_GROUPDENTIST_LOGINASUSER'),
                'backtoadmin' => array('ROLE_DENTIST_GROUPDENTIST_BACKTOADMIN'),
            ),
            'group_invoice' => array(
                'index' => array('ROLE_DENTIST_GROUPINVOICE_INDEX')
            ),
            'order' => array(
                'index' => array('ROLE_DENTIST_ORDER_INDEX'),
                'inprogress' => array('ROLE_DENTIST_ORDER_INDEX'),
                'ajaxFile' => array('ROLE_DENTIST_ORDER_INDEX'),
                'add' => array('ROLE_DENTIST_ORDER_ADD'),
                'search' => array('ROLE_DENTIST_ORDER_ADD'),
                'ajaxnames' => array('ROLE_DENTIST_ORDER_ADD'),
                'create' => array('ROLE_DENTIST_ORDER_CREATE'),
                'edit' => array('ROLE_DENTIST_ORDER_EDIT'),
                'ajaxFileAdd' => array('ROLE_DENTIST_ORDER_EDIT'),
                'deleterecipe' => array('ROLE_DENTIST_ORDER_EDIT'),
                'editproduct' => array('ROLE_DENTIST_ORDER_EDIT'),
                'ajaxeditdentist' => array('ROLE_DENTIST_ORDER_EDIT'),
                'view' => array('ROLE_DENTIST_ORDER_VIEW'),
                'packingpdf' => array('ROLE_DENTIST_ORDER_VIEW'),
                'packingpdfprint' => array('ROLE_DENTIST_ORDER_VIEW'),
                'printlabel' => array('ROLE_DENTIST_ORDER_VIEW'),
                'printlabelprint' => array('ROLE_DENTIST_ORDER_VIEW'),
                'delete' => array('ROLE_DENTIST_ORDER_DELETE'),
                'complete' => array('ROLE_DENTIST_ORDER_COMPLETE'),
                'showproduct' => array('ROLE_DENTIST_ORDER_SHOWPRODUCT'),
                'calculateprice' => array('ROLE_DENTIST_ORDER_SHOWPRODUCT'),
                'download' => array('ROLE_DENTIST_ORDER_DOWNLOAD'),
                'history' => array('ROLE_DENTIST_ORDER_HISTORY'),
                'details' => array('ROLE_DENTIST_ORDER_HISTORY'),
                'recipedetails' => array('ROLE_DENTIST_ORDER_HISTORY'),
                'ajaxfileremove' => array('ROLE_DENTIST_ORDER_EDIT')
            ),
            'invoice' => array(
                'index' => array('ROLE_DENTIST_ORDER_INDEX'),
                'download' => array('ROLE_DENTIST_ORDER_INDEX'),
            ),
            'notification' => array(
                'index' => array('ROLE_DENTIST_INDEX_INDEX'),
                'archive' => array('ROLE_DENTIST_INDEX_INDEX'),
                'read' => array('ROLE_DENTIST_INDEX_INDEX'),
                'print' => array('ROLE_DENTIST_INDEX_INDEX'),
                'ajaxlist' => array('ROLE_DENTIST_INDEX_INDEX'),
                'ajaxreply' => array('ROLE_DENTIST_INDEX_INDEX'),
                'toarchive' => array('ROLE_DENTIST_INDEX_INDEX')
            ),
            'general' => array(
                'account' => array('ROLE_DENTIST_GENERAL_ACCOUNT'),
                'organisation' => array('ROLE_DENTIST_GENERAL_ORGANISATION'),
                'organisationEdit' => array('ROLE_DENTIST_GENERAL_ORGANISATION_EDIT'),
                'preferences' => array('ROLE_DENTIST_GENERAL_PREFERENCES')
            ),
            'file' => array(
                'index' => array('ROLE_DENTIST_FILE_INDEX'),
                'upload' => array('ROLE_DENTIST_FILE_INDEX'),
                'share' => array('ROLE_DENTIST_FILE_INDEX'),
                'editshare' => array('ROLE_DENTIST_FILE_INDEX'),
                'delete' => array('ROLE_DENTIST_FILE_INDEX'),
                'download' => array('ROLE_DENTIST_FILE_INDEX'),
                'ajaxrevoke' => array('ROLE_DENTIST_FILE_INDEX'),
                'ajaxresend' => array('ROLE_DENTIST_FILE_INDEX')
            ),
            'instructions' => array(
                'edit' => array('ROLE_DENTIST_INSTRUCTIONS_EDIT')
            )
        ),
        'lab' => array(
            'index' => array(
                'index' => array('ROLE_LAB_INDEX_INDEX'),
                'dashboard' => array('ROLE_LAB_INDEX_INDEX'),
                'start' => array('ROLE_LAB_INDEX_INDEX'),
                'newProducts' => array('ROLE_LAB_DASHBOARD_PURCHASES_NEW'),
                'priceAlerts' => array('ROLE_LAB_DASHBOARD_PURCHASES_ALERT'),
                'dashboard_purchases_new' => array('ROLE_LAB_DASHBOARD_PURCHASES_NEW'),
                'dashboard_purchases_alert' => array('ROLE_LAB_DASHBOARD_PURCHASES_ALERT'),
                'dashboard_purchases_status' => array('ROLE_LAB_DASHBOARD_PURCHASES_STATUS'),
                'dashboard_purchases_new_in_shortlist' => array('ROLE_LAB_DASHBOARD_PURCHASES_NEW_IN_SHORTLIST'),
                'dashboard_sales_new_order' => array('ROLE_LAB_DASHBOARD_SALES_NEW_ORDER'),
                'dashboard_management_users' => array('ROLE_LAB_DASHBOARD_MANAGEMENT_USERS')
            ),
            'sales_report' => [
                'index' => ['ROLE_LAB_SALESREPORT_INDEX']
            ],
            'avg' => array(
                'index' => array('ROLE_LAB_AVG_INDEX')
            ),
            'user' => array(
                'index' => array('ROLE_LAB_USER_INDEX'),
                'ajaxlist' => array('ROLE_LAB_USER_INDEX'),
                'add' => array('ROLE_LAB_USER_ADD'),
                'edit' => array('ROLE_LAB_USER_EDIT'),
                'organisation' => array('ROLE_LAB_USER_EDIT'),
                'deleteorganisationimage' => array('ROLE_LAB_USER_EDIT'),
                'delete' => array('ROLE_LAB_USER_DELETE'),
                'deactivate' => array('ROLE_LAB_USER_DEACTIVATE'),
                'activate' => array('ROLE_LAB_USER_ACTIVATE'),
                'loginasuser' => array('ROLE_LAB_USER_LOGINASUSER', 'ROLE_LAB_USER_MASTERKEY'),
                'backtoadmin' => array('ROLE_LAB_USER_BACKTOADMIN', 'ROLE_LAB_USER_MASTERKEY'),
                'loginbymasterkey' => array('ROLE_LAB_USER_LOGINASUSER', 'ROLE_LAB_USER_MASTERKEY'),
                'masterkey' => array('ROLE_LAB_USER_MASTERKEY'),
                'ajaxpaymentoption' => array('ROLE_LAB_USER_INDEX'),
                'deletepaymentoption' => array('ROLE_LAB_USER_INDEX')
            ),
            'role' => array(
                'index' => array('ROLE_LAB_ROLE_INDEX'),
                'add' => array('ROLE_LAB_ROLE_ADD'),
                'edit' => array('ROLE_LAB_ROLE_EDIT'),
                'delete' => array('ROLE_LAB_ROLE_DELETE'),
                'deactivate' => array('ROLE_LAB_ROLE_DEACTIVATE'),
                'activate' => array('ROLE_LAB_ROLE_ACTIVATE'),
                'reset' => array('ROLE_LAB_ROLE_RESET')
            ),
            'product' => array(
                'index' => array('ROLE_LAB_PRODUCT_INDEX'),
                'addshortlistbulk' => array('ROLE_LAB_PRODUCT_ADDSHORTLIST'),
                'addshortlistsupplier' => array('ROLE_LAB_PRODUCT_ADDSHORTLIST'),
                'getFilteredProducts' => array('ROLE_LAB_PRODUCT_INDEX','ROLE_LAB_SHORTLIST_INDEX','ROLE_SUPPLIER_PRODUCTSLIST_INDEX'),
                'addcart' => array('ROLE_LAB_PRODUCT_ADDCART'),
                'addshortlist' => array('ROLE_LAB_PRODUCT_ADDSHORTLIST'),
                'ajaxnames' => array('ROLE_LAB_PRODUCT_AJAXNAMES'),
                'ajaxnamessimple' => array('ROLE_LAB_PRODUCT_AJAXNAMES'),
                'show' => array('ROLE_LAB_PRODUCT_SHOW'),
                'ajaxcontactproduct' => array('ROLE_LAB_PRODUCT_INDEX')
            ),
            'order' => array(
                'index' => array('ROLE_LAB_ORDER_INDEX'),
                'ajaxreceivedall' => array('ROLE_LAB_ORDER_INDEX'),
                'ajaxmovetohistory' => array('ROLE_LAB_ORDER_INDEX'),
                'history' => array('ROLE_LAB_ORDER_INDEX'),
                'incoming' => array('ROLE_LAB_ORDER_INDEX'),
                'orderdetails' => array('ROLE_LAB_ORDER_INDEX'),
                'ajaxbuyedproductlist' => array('ROLE_LAB_ORDER_INDEX'),
                'ajaxreceived' => array('ROLE_LAB_ORDER_INDEX'),
                'ajaxorderlist' => array('ROLE_LAB_ORDER_AJAXORDERLIST')
            ),
            'shortlist' => array(
                'index' => array('ROLE_LAB_SHORTLIST_INDEX'),
                'ajaxnamessimple' => array('ROLE_LAB_SHORTLIST_INDEX'),
                'delete' => array('ROLE_LAB_SHORTLIST_DELETE'),
                'ajaxproductamount' => array('ROLE_LAB_SHORTLIST_AJAXPRODUCTAMOUNT'),
                'ajaxsaveamount' => array('ROLE_LAB_SHORTLIST_AJAXSAVEAMOUNT'),
                'ajaxmargin' => array('ROLE_LAB_PRODUCT_INDEX'),
                'edit' => array('ROLE_LAB_SHORTLIST_EDIT'),
                'markasviewed' => array('ROLE_LAB_DASHBOARD_PURCHASES_NEW_IN_SHORTLIST')
            ),
            'cart' => array(
                'index' => array('ROLE_LAB_CART_INDEX'),
                'ajaxsuppliertext' => array('ROLE_LAB_CART_INDEX'),
                'ajaxproductlist' => array('ROLE_LAB_CART_AJAXPRODUCTLIST'),
                'removeproduct' => array('ROLE_LAB_CART_REMOVEPRODUCT'),
                'ajaxbuyedproductlist' => array('ROLE_LAB_CART_AJAXBUYEDPRODUCTLIST'),
                'completeorder' => array('ROLE_LAB_CART_COMPLETEORDER'),
                'saveorder' => array('ROLE_LAB_CART_INDEX')
            ),
            'countlist' => array(
                'index' => array('ROLE_LAB_COUNTLIST_INDEX'),
                'add' => array('ROLE_LAB_COUNTLIST_ADD'),
                'edit' => array('ROLE_LAB_COUNTLIST_EDIT'),
                'save' => array('ROLE_LAB_COUNTLIST_EDIT'),
                'ajaxcountlist' => array('ROLE_LAB_COUNTLIST_AJAXCOUNLIST'),
                'ajaxcountlistview' => array('ROLE_LAB_COUNTLIST_AJAXCOUNLISTVIEW'),
                'ajaxsaveamount' => array('ROLE_LAB_COUNTLIST_AJAXSAVEAMOUNT')
            ),
            'sales_client' => array(
                'index' => array('ROLE_LAB_SALESCLIENT_INDEX'),
                'add' => array('ROLE_LAB_SALESCLIENT_INDEX'),
                'edit' => array('ROLE_LAB_SALESCLIENT_INDEX'),
                'editinvite' => array('ROLE_LAB_SALESCLIENT_INDEX'),
                'pending' => array('ROLE_LAB_SALESCLIENT_INDEX'),
                'deletecontract' => array('ROLE_LAB_SALESCLIENT_INDEX'),
                'validatekvk' => array('ROLE_LAB_SALESCLIENT_INDEX'),
                'recipelist' => array('ROLE_LAB_SALESCLIENT_INDEX'),
                'view' => array('ROLE_LAB_SALESCLIENT_INDEX'),
                'activate' => array('ROLE_LAB_SALESCLIENT_ACTIVATE'),
                'deactivate' => array('ROLE_LAB_SALESCLIENT_DEACTIVATE'),
                'loginasuser' => array('ROLE_LAB_SALESCLIENT_LOGINASUSER'),
                'backtoadmin' => array('ROLE_LAB_SALESCLIENT_BACKTOADMIN'),
            ),
            'sales_import' => array(
                'index' => array('ROLE_LAB_SALESIMPORT_INDEX'),
                'map' => array('ROLE_LAB_SALESIMPORT_MAP'),
                'overview' => array('ROLE_LAB_SALESIMPORT_OVERVIEW'),
                'confirm' => array('ROLE_LAB_SALESIMPORT_CONFIRM'),
                'complete' => array('ROLE_LAB_SALESIMPORT_COMPLETE'),
                'ajaxmap' => array('ROLE_LAB_SALESIMPORT_AJAXMAP')
            ),
            'sales_ledger' => array(
                'index' => array('ROLE_LAB_SALESLEDGER_INDEX'),
                'add' => array('ROLE_LAB_SALESLEDGER_ADD'),
                'edit' => array('ROLE_LAB_SALESLEDGER_EDIT'),
                'activate' => array('ROLE_LAB_SALESLEDGER_ACTIVATE'),
                'deactivate' => array('ROLE_LAB_SALESLEDGER_DEACTIVATE'),
                'map' => array('ROLE_LAB_SALESLEDGER_MAP')
            ),
            'sales_order' => array(
                'index' => array('ROLE_LAB_SALESORDER_INDEX'),
                'history' => array('ROLE_LAB_SALESORDER_INDEX'),
                'incoming' => array('ROLE_LAB_SALESORDER_INDEX'),
                'all' => array('ROLE_LAB_SALESORDER_INDEX'),
                'update' => array('ROLE_LAB_SALESORDER_INDEX'),
                'edit' => array('ROLE_LAB_SALESORDER_INDEX'),
                'getdiscountprice' => array('ROLE_LAB_SALESORDER_INDEX'),
                'download' => array('ROLE_LAB_SALESORDER_INDEX'),
                'view' => array('ROLE_LAB_SALESORDER_VIEW'),
                'print' => array('ROLE_LAB_SALESORDER_VIEW'),
                'printlabel' => array('ROLE_LAB_SALESORDER_VIEW'),
                'pdflabel' => array('ROLE_LAB_SALESORDER_VIEW'),
                'getpdf' => array('ROLE_LAB_SALESORDER_VIEW'),
                'todelivery' => array('ROLE_LAB_SALESORDER_TODELIVERY'),
                'toinprogress' => array('ROLE_LAB_SALESORDER_TODELIVERY')
            ),
            'sales_recipe' => array(
                'index' => array('ROLE_LAB_SALESRECIPE_INDEX'),
                'inactive' => array('ROLE_LAB_SALESRECIPE_INDEX'),
                'edit' => array('ROLE_LAB_SALESRECIPE_EDIT'),
                'productiontime' => array('ROLE_LAB_SALESRECIPE_EDIT'),
                'productiontimeupdate' => array('ROLE_LAB_SALESRECIPE_EDIT'),
                'saverow' => array('ROLE_LAB_SALESRECIPE_SAVEROW'),
                'activate' => array('ROLE_LAB_SALESRECIPE_ACTIVATE'),
                'deactivate' => array('ROLE_LAB_SALESRECIPE_DEACTIVATE')
            ),
            'sales_tariff' => array(
                'index' => array('ROLE_LAB_SALESTARIFF_INDEX'),
                'add' => array('ROLE_LAB_SALESTARIFF_ADD'),
                'edit' => array('ROLE_LAB_SALESTARIFF_EDIT'),
                'activate' => array('ROLE_LAB_SALESTARIFF_ACTIVATE'),
                'deactivate' => array('ROLE_LAB_SALESTARIFF_DEACTIVATE'),
                'map' => array('ROLE_LAB_SALESTARIFF_MAP'),
                'mappingandmargins' => array('ROLE_LAB_SALESTARIFF_MAP'),
                'ajaxmaptariff' => array('ROLE_LAB_SALESTARIFF_MAP'),
                'ajaxremovetariff' => array('ROLE_LAB_SALESTARIFF_MAP'),
                'ajaxremovemargin' => array('ROLE_LAB_SALESTARIFF_MAP'),
                'ajaxmarginsettings' => array('ROLE_LAB_SALESTARIFF_MAP')
            ),
            'notification' => array(
                'index' => array('ROLE_LAB_INDEX_INDEX'),
                'archive' => array('ROLE_LAB_INDEX_INDEX'),
                'read' => array('ROLE_LAB_INDEX_INDEX'),
                'print' => array('ROLE_LAB_INDEX_INDEX'),
                'ajaxlist' => array('ROLE_LAB_INDEX_INDEX'),
                'ajaxreply' => array('ROLE_LAB_INDEX_INDEX'),
                'toarchive' => array('ROLE_LAB_INDEX_INDEX')
            ),
            'general' => array(
                'account' => array('ROLE_LAB_GENERAL_ACCOUNT'),
                'organisation' => array('ROLE_LAB_GENERAL_ORGANISATION'),
                'organisationEdit' => array('ROLE_LAB_GENERAL_ORGANISATION_EDIT'),
                'preferences' => array('ROLE_LAB_GENERAL_PREFERENCES')
            ),
            'file' => array(
                'index' => array('ROLE_LAB_INDEX_INDEX'),
                'download' => array('ROLE_LAB_INDEX_INDEX')
            ),
            'organisation' => array(
                'index' => array('ROLE_LAB_ORGANISATION_INDEX'),
                'edit' => array('ROLE_LAB_ORGANISATION_EDIT')
            ),
            'instructions' => array(
                'edit' => array('ROLE_LAB_INSTRUCTIONS_EDIT')
            ),
            'invoice' => [
                'index' => ['ROLE_LAB_INVOICE_INDEX'],
                'add' => ['ROLE_LAB_INVOICE_INDEX'],
                'deletebulk' => ['ROLE_LAB_INVOICE_INDEX'],
                'processbulk' => ['ROLE_LAB_INVOICE_INDEX'],
                'downloadzip' => ['ROLE_LAB_INVOICE_INDEX']
            ]
        ),
        'signadens' => array(
            'index' => array(
                'index' => array('ROLE_SIGNADENS_EDIT'),
                'start' => array('ROLE_SIGNADENS_EDIT'),
            ),
            'user' => array(
                'index' => array('ROLE_SIGNADENS_USER_INDEX'),
                'add' => array('ROLE_SIGNADENS_USER_ADD'),
                'edit' => array('ROLE_SIGNADENS_USER_EDIT'),
                'delete' => array('ROLE_SIGNADENS_USER_DELETE'),
                'deactivate' => array('ROLE_SIGNADENS_USER_DEACTIVATE'),
                'activate' => array('ROLE_SIGNADENS_USER_ACTIVATE'),
                'loginasuser' => array('ROLE_SIGNADENS_USER_LOGINASUSER'),
                'backtoadmin' => array('ROLE_SIGNADENS_USER_BACKTOADMIN', 'ROLE_GUEST'),
                'ajaxlist' => array('ROLE_SIGNADENS_USER_AJAXLIST')
            ),
            'role' => array(
                'index' => array('ROLE_SIGNADENS_ROLE_INDEX'),
                'add' => array('ROLE_SIGNADENS_ROLE_ADD'),
                'edit' => array('ROLE_SIGNADENS_ROLE_EDIT'),
                'delete' => array('ROLE_SIGNADENS_ROLE_DELETE'),
                'deactivate' => array('ROLE_SIGNADENS_ROLE_DEACTIVATE'),
                'activate' => array('ROLE_SIGNADENS_ROLE_ACTIVATE'),
                'reset' => array('ROLE_SIGNADENS_ROLE_RESET')
            ),
            'import' => array(
                'index' => array('ROLE_SIGNADENS_IMPORT_INDEX'),
                'log' => array('ROLE_SIGNADENS_IMPORT_INDEX'),
                'map' => array('ROLE_SIGNADENS_IMPORT_INDEX'),
                'overview' => array('ROLE_SIGNADENS_IMPORT_INDEX'),
                'confirm' => array('ROLE_SIGNADENS_IMPORT_INDEX'),
                'complete' => array('ROLE_SIGNADENS_IMPORT_INDEX'),
                'categorize' => array('ROLE_SIGNADENS_IMPORT_INDEX'),
                'decline' => array('ROLE_SIGNADENS_IMPORT_INDEX'),
                'approve' => array('ROLE_SIGNADENS_IMPORT_INDEX'),
                'ajaxmap' => array('ROLE_SIGNADENS_IMPORT_INDEX'),
                'ajaxmissingcategory' => array('ROLE_SIGNADENS_IMPORT_INDEX')
            ),
            'importcode' => array(
                'index' => array('ROLE_SIGNADENS_IMPORTCODE_INDEX'),
                'map' => array('ROLE_SIGNADENS_IMPORTCODE_INDEX'),
                'overview' => array('ROLE_SIGNADENS_IMPORTCODE_INDEX'),
                'confirm' => array('ROLE_SIGNADENS_IMPORTCODE_INDEX'),
                'complete' => array('ROLE_SIGNADENS_IMPORTCODE_INDEX'),
                'ajaxmap' => array('ROLE_SIGNADENS_IMPORTCODE_INDEX')
            ),
            'organisation' => array(
                'index' => array('ROLE_SIGNADENS_ORGANISATION_INDEX'),
                'add' => array('ROLE_SIGNADENS_ORGANISATION_ADD'),
                'edit' => array('ROLE_SIGNADENS_ORGANISATION_EDIT'),
                'delete' => array('ROLE_SIGNADENS_ORGANISATION_DELETE'),
                'deleteimageedit' => array('ROLE_SIGNADENS_ORGANISATION_DELETE'),
                'deactivate' => array('ROLE_SIGNADENS_ORGANISATION_DEACTIVATE'),
                'activate' => array('ROLE_SIGNADENS_ORGANISATION_ACTIVATE')
            ),
            'manage' => array(
                'indexcategory' => array('ROLE_SIGNADENS_MANAGE_INDEXCATEGORY'),
                'download' => array('ROLE_SIGNADENS_MANAGE_INDEXCATEGORY'),
                'ajaxsupplierinfo' => array('ROLE_SIGNADENS_MANAGE_INDEXCATEGORY'),
                'index' => array('ROLE_SIGNADENS_MANAGE_INDEXCATEGORY'),
                'view' => array('ROLE_SIGNADENS_MANAGE_INDEXCATEGORY'),
                'addcategory' => array('ROLE_SIGNADENS_MANAGE_ADDCATEGORY'),
                'add' => array('ROLE_SIGNADENS_MANAGE_ADDCATEGORY'),
                'addCategory' => array('ROLE_SIGNADENS_MANAGE_ADDCATEGORY'),
                'editcategory' => array('ROLE_SIGNADENS_MANAGE_EDITCATEGORY'),
                'duplicate' => array('ROLE_SIGNADENS_MANAGE_EDITCATEGORY'),
                'edit' => array('ROLE_SIGNADENS_MANAGE_EDITCATEGORY'),
                'activate' => array('ROLE_SIGNADENS_MANAGE_EDITCATEGORY'),
                'deactivate' => array('ROLE_SIGNADENS_MANAGE_EDITCATEGORY'),
                'deletecategory' => array('ROLE_SIGNADENS_MANAGE_DELETECATEGORY'),
                'filedelete' => array('ROLE_SIGNADENS_MANAGE_DELETECATEGORY'),
                'treeCategory' => array('ROLE_SIGNADENS_MANAGE_TREECATEGORY'),
                'addTreeCategory' => array('ROLE_SIGNADENS_MANAGE_ADDTREECATEGORY'),
                'editTreeCategory' => array('ROLE_SIGNADENS_MANAGE_EDITTREECATEGORY'),
                'sorttreecategory' => array('ROLE_SIGNADENS_MANAGE_EDITTREECATEGORY'),
                'deleteTreeCategory' => array('ROLE_SIGNADENS_MANAGE_DELETETREECATEGORY'),
                'addTreeCategoryProduct' => array('ROLE_SIGNADENS_MANAGE_ADDTREECATEGORYPRODUCT'),
                'indexdepartment' => array('ROLE_SIGNADENS_MANAGE_INDEXDEPARTMENT'),
                'indexDepartment' => array('ROLE_SIGNADENS_MANAGE_INDEXDEPARTMENT'),
                'adddepartment' => array('ROLE_SIGNADENS_MANAGE_ADDDEPARTMENT'),
                'addDepartment' => array('ROLE_SIGNADENS_MANAGE_ADDDEPARTMENT'),
                'editdepartment' => array('ROLE_SIGNADENS_MANAGE_EDITDEPARTMENT'),
                'editDepartment' => array('ROLE_SIGNADENS_MANAGE_EDITDEPARTMENT'),
                'deletedepartment' => array('ROLE_SIGNADENS_MANAGE_DELETEDEPARTMENT'),
                'deleteDepartment' => array('ROLE_SIGNADENS_MANAGE_DELETEDEPARTMENT'),
                'mapledgerstocategories' => array('ROLE_SIGNADENS_MANAGE_INDEXCATEGORY'),
                'saveledgertocategories' => array('ROLE_SIGNADENS_MANAGE_INDEXCATEGORY'),
                'manufacturers' => array('ROLE_SIGNADENS_EDIT'),
                'ajaxmanufacturers' => array('ROLE_SIGNADENS_EDIT'),
                'tariffcoderanges' => array('ROLE_SIGNADENS_EDIT'),
                'ajaxtariffcoderanges' => array('ROLE_SIGNADENS_EDIT'),
            ),
            'notification' => array(
                'index' => array('ROLE_SIGNADENS_INDEX_INDEX'),
                'archive' => array('ROLE_SIGNADENS_INDEX_INDEX'),
                'read' => array('ROLE_SIGNADENS_INDEX_INDEX'),
                'print' => array('ROLE_SIGNADENS_INDEX_INDEX'),
                'ajaxlist' => array('ROLE_SIGNADENS_INDEX_INDEX'),
                'ajaxreply' => array('ROLE_SIGNADENS_INDEX_INDEX'),
                'toarchive' => array('ROLE_SIGNADENS_INDEX_INDEX')
            ),
            'general' => array(
                'account' => array('ROLE_SIGNADENS_GENERAL_ACCOUNT'),
                'organisation' => array('ROLE_SIGNADENS_GENERAL_ORGANISATION'),
                'organisationEdit' => array('ROLE_SIGNADENS_GENERAL_ORGANISATION_EDIT'),
                'preferences' => array('ROLE_SIGNADENS_GENERAL_PREFERENCES')
            ),
            'product' => array(
                'index' => array('ROLE_SIGNADENS_PRODUCT_INDEX'),
                'list' => array('ROLE_SIGNADENS_PRODUCT_INDEX'),
                'listajax' => array('ROLE_SIGNADENS_PRODUCT_INDEX'),
                'add' => array('ROLE_SIGNADENS_PRODUCT_ADD'),
                'edit' => array('ROLE_SIGNADENS_PRODUCT_EDIT'),
                'duplicate' => array('ROLE_SIGNADENS_PRODUCT_EDIT'),
                'cancelremoval' => array('ROLE_SIGNADENS_PRODUCT_EDIT'),
                'confirmremoval' => array('ROLE_SIGNADENS_PRODUCT_EDIT'),
                'deleteimageedit' => array('ROLE_SIGNADENS_PRODUCT_EDIT'),
                'deleteStatus' => array('ROLE_SIGNADENS_PRODUCT_EDIT'),
                'addNewStatus' => array('ROLE_SIGNADENS_PRODUCT_EDIT'),
                'listedit' => array('ROLE_SIGNADENS_PRODUCT_EDIT'),
                'listdelete' => array('ROLE_SIGNADENS_PRODUCT_EDIT'),
                'deletegroup' => array('ROLE_SIGNADENS_PRODUCT_EDIT'),
                'groupmanage' => array('ROLE_SIGNADENS_PRODUCT_EDIT'),
                'delete' => array('ROLE_SIGNADENS_PRODUCT_DELETE'),
                'activate' => array('ROLE_SIGNADENS_PRODUCT_ACTIVATE'),
                'deactivate' => array('ROLE_SIGNADENS_PRODUCT_DEACTIVATE'),
                'ajaxcustomfield' => array('ROLE_SIGNADENS_PRODUCT_INDEX'),
                'shopview' => array('ROLE_SIGNADENS_PRODUCT_INDEX')
            ),
            'map' => array(
                'index' => array('ROLE_SIGNADENS_MAP_INDEX')
            ),
            'tariff' => array(
                'index' => array('ROLE_SIGNADENS_TARIFF_INDEX'),
                'add' => array('ROLE_SIGNADENS_TARIFF_ADD'),
                'edit' => array('ROLE_SIGNADENS_TARIFF_EDIT'),
                'deactivate' => array('ROLE_SIGNADENS_TARIFF_DEACTIVATE'),
                'activate' => array('ROLE_SIGNADENS_TARIFF_ACTIVATE'),
                'ajaxmarginsettings' => array('ROLE_SIGNADENS_TARIFF_INDEX'),
            ),
            'ledger' => array(
                'index' => array('ROLE_SIGNADENS_LEDGER_INDEX'),
                'add' => array('ROLE_SIGNADENS_LEDGER_ADD'),
                'edit' => array('ROLE_SIGNADENS_LEDGER_EDIT'),
                'deactivate' => array('ROLE_SIGNADENS_LEDGER_DEACTIVATE'),
                'activate' => array('ROLE_SIGNADENS_LEDGER_ACTIVATE'),
            ),
            'tree' => array(
                'index' => array('ROLE_SIGNADENS_TREE_INDEX')
            ),
            'invoice' => array(
                'index' => array('ROLE_SIGNADENS_INVOICE_INDEX'),
                'print' => array('ROLE_SIGNADENS_INVOICE_INDEX'),
                'add' => array('ROLE_SIGNADENS_INVOICE_ADD'),
                'addrecord' => array('ROLE_SIGNADENS_INVOICE_ADDRECORD'),
                'edit' => array('ROLE_SIGNADENS_INVOICE_EDIT'),
                'editrecord' => array('ROLE_SIGNADENS_INVOICE_EDITRECORD'),
                'delete' => array('ROLE_SIGNADENS_INVOICE_DELETE'),
                'deleterecord' => array('ROLE_SIGNADENS_INVOICE_DELETERECORD')
            ),
            'file' => array(
                'index' => array('ROLE_SIGNADENS_INDEX_INDEX'),
                'download' => array('ROLE_SIGNADENS_INDEX_INDEX')
            ),
            'helpdesk' => [
                'index' => array('ROLE_SIGNADENS_EDIT'),
                'edit' => array('ROLE_SIGNADENS_EDIT'),
                'view' => array('ROLE_SIGNADENS_EDIT'),
                'ajaxSave' => array('ROLE_SIGNADENS_EDIT'),
            ],
            'instructions' => [
                'edit' => ['ROLE_SIGNADENS_INSTRUCTIONS_EDIT'],
                'index' => ['ROLE_SIGNADENS_INSTRUCTIONS_EDIT'],
                'ajaxSave' => ['ROLE_SIGNADENS_INSTRUCTIONS_EDIT'],
                'view' => ['ROLE_SIGNADENS_INSTRUCTIONS_EDIT']
            ],
            'crud' => [
                'Index' => ['ROLE_SIGNADENS_EDIT'],
                'Add' => ['ROLE_SIGNADENS_EDIT'],
                'Edit' => ['ROLE_SIGNADENS_EDIT'],
                'Delete' => ['ROLE_SIGNADENS_EDIT']
            ],
            'sales_report' => [
                'index' => ['ROLE_SIGNADENS_EDIT']
            ]
        ),
        'supplier' => array(
            'index' => array(
                'index' => array('ROLE_SUPPLIER_INDEX_INDEX'),
                'dashboard_import_product' => array('ROLE_SUPPLIER_DASHBOARD_IMPORT_PRODUCTS'),
                'dashboard_import_notification' => array('ROLE_SUPPLIER_DASHBOARD_IMPORT_NOTIFICATIONS'),
                'dashboard_sales_new' => array('ROLE_SUPPLIER_DASHBOARD_SALES_NEW'),
                'dashboard_management_users' => array('ROLE_SUPPLIER_DASHBOARD_MANAGEMENT_USERS'),
                'start' => array('ROLE_SUPPLIER_INDEX_INDEX')
            ),
            'user' => array(
                'index' => array('ROLE_SUPPLIER_USER_INDEX'),
                'ajaxlist' => array('ROLE_SUPPLIER_USER_INDEX'),
                'add' => array('ROLE_SUPPLIER_USER_ADD'),
                'edit' => array('ROLE_SUPPLIER_USER_EDIT'),
                'organisation' => array('ROLE_SUPPLIER_USER_EDIT'),
                'deleteorganisationimage' => array('ROLE_SUPPLIER_USER_EDIT'),
                'delete' => array('ROLE_SUPPLIER_USER_DELETE'),
                'deactivate' => array('ROLE_SUPPLIER_USER_DEACTIVATE'),
                'activate' => array('ROLE_SUPPLIER_USER_ACTIVATE'),
                'loginasuser' => array('ROLE_SUPPLIER_USER_LOGINASUSER'),
                'backtoadmin' => array('ROLE_SUPPLIER_USER_BACKTOADMIN')
            ),
            'role' => array(
                'index' => array('ROLE_SUPPLIER_ROLE_INDEX'),
                'add' => array('ROLE_SUPPLIER_ROLE_ADD'),
                'edit' => array('ROLE_SUPPLIER_ROLE_EDIT'),
                'delete' => array('ROLE_SUPPLIER_ROLE_DELETE'),
                'deactivate' => array('ROLE_SUPPLIER_ROLE_DEACTIVATE'),
                'activate' => array('ROLE_SUPPLIER_ROLE_ACTIVATE'),
                'reset' => array('ROLE_SUPPLIER_ROLE_RESET')
            ),
            'import' => array(
                'index' => array('ROLE_SUPPLIER_IMPORT_INDEX'),
                'log' => array('ROLE_SUPPLIER_IMPORT_INDEX'),
                'map' => array('ROLE_SUPPLIER_IMPORT_INDEX'),
                'overview' => array('ROLE_SUPPLIER_IMPORT_INDEX'),
                'confirm' => array('ROLE_SUPPLIER_IMPORT_INDEX'),
                'complete' => array('ROLE_SUPPLIER_IMPORT_INDEX'),
                'ajaxmap' => array('ROLE_SUPPLIER_IMPORT_INDEX')
            ),
            'importlog' => array(
                'index' => array('ROLE_SUPPLIER_IMPORTLOG_INDEX'),
                'view' => array('ROLE_SUPPLIER_IMPORTLOG_VIEW')
            ),
            'order' => array(
                'index' => array('ROLE_SUPPLIER_ORDER_INDEX'),
                'ajaxProductSentStatus' => array('ROLE_SUPPLIER_ORDER_INDEX'),
                'edit' => array('ROLE_SUPPLIER_ORDER_EDIT'),
                'history' => array('ROLE_SUPPLIER_ORDER_HISTORY'),
                'historyDetails' => array('ROLE_SUPPLIER_ORDER_HISTORY'),
                'ajaxproductlist' => array('ROLE_SUPPLIER_ORDER_AJAXPRODUCTLIST'),
                'ajaxbuyedproductlist' => array('ROLE_SUPPLIER_ORDER_AJAXBUYEDPRODUCTLIST'),
                'removeproduct' => array('ROLE_SUPPLIER_ORDER_REMOVEPRODUCT'),
                'completeorder' => array('ROLE_SUPPLIER_ORDER_COMPLETEORDER'),
            ),
            'notification' => array(
                'index' => array('ROLE_SUPPLIER_INDEX_INDEX'),
                'archive' => array('ROLE_SUPPLIER_INDEX_INDEX'),
                'read' => array('ROLE_SUPPLIER_INDEX_INDEX'),
                'print' => array('ROLE_SUPPLIER_INDEX_INDEX'),
                'ajaxlist' => array('ROLE_SUPPLIER_INDEX_INDEX'),
                'ajaxreply' => array('ROLE_SUPPLIER_INDEX_INDEX'),
                'toarchive' => array('ROLE_SUPPLIER_INDEX_INDEX')
            ),
            'general' => array(
                'account' => array('ROLE_SUPPLIER_GENERAL_ACCOUNT'),
                'organisation' => array('ROLE_SUPPLIER_GENERAL_ORGANISATION'),
                'organisationEdit' => array('ROLE_SUPPLIER_GENERAL_ORGANISATION_EDIT'),
                'preferences' => array('ROLE_SUPPLIER_GENERAL_PREFERENCES')
            ),
            'file' => array(
                'index' => array('ROLE_SUPPLIER_INDEX_INDEX'),
                'download' => array('ROLE_SUPPLIER_INDEX_INDEX')
            ),
            'organisation' => array(
                'index' => array('ROLE_SUPPLIER_ORGANISATION_INDEX'),
                'edit' => array('ROLE_SUPPLIER_ORGANISATION_EDIT')
            ),
            'productlist' => array(
                'index' => array('ROLE_SUPPLIER_PRODUCTSLIST_INDEX'),
                'listajax' => array('ROLE_SUPPLIER_PRODUCTSLIST_INDEX'),
                'getproductimage' => array('ROLE_SUPPLIER_PRODUCTSLIST_INDEX'),
                'labview' => array('ROLE_SUPPLIER_PRODUCTSLIST_INDEX'),
                'edit' => array('ROLE_SUPPLIER_PRODUCTSLIST_EDIT'),
                'deletesheet' => array('ROLE_SUPPLIER_PRODUCTSLIST_EDIT'),
                'removeproduct' => array('ROLE_SUPPLIER_PRODUCTSLIST_EDIT'),
                'deleteimage' => array('ROLE_SUPPLIER_PRODUCTSLIST_EDIT'),
            ),
            'instructions' => [
                'edit' => ['ROLE_SUPPLIER_INSTRUCTIONS_EDIT']
            ]
        ),
        'guestauth' => array( // allow for all
            'index' => array(
                'index' => array('ROLE_GUEST')
            ),
            'auth' => array(
                'login' => array('ROLE_GUEST'),
                'register' => array('ROLE_GUEST'),
                'logout' => array('ROLE_GUEST'),
                'forgetpassword' => array('ROLE_GUEST'),
                'resetpassword' => array('ROLE_GUEST')
            ),
            'notification' => array(
                'index' => array('ROLE_GUEST'),
                'archive' => array('ROLE_GUEST'),
                'toarchive' => array('ROLE_GUEST'),
                'confirmfile' => array('ROLE_GUEST'),
                'rejectfile' => array('ROLE_GUEST'),
                'read' => array('ROLE_NOTIFICATION_INDEX'),
                'print' => array('ROLE_NOTIFICATION_INDEX'),
                'ajaxlist' => array('ROLE_NOTIFICATION_INDEX'),
                'ajaxreply' => array('ROLE_NOTIFICATION_INDEX')
            ),
            'api' => array(
                'existinguserinvitation' => array('ROLE_GUEST'),
                'newuserinvitation' => array('ROLE_GUEST'),
                'termsofuse' => array('ROLE_GUEST')
            ),
            'cron' => array(
                'zgzkogfjoteyjdmmmexntayzaxmdyowy' => array('ROLE_GUEST')
            ),
            'delivery_note' => [
                'index' => array('ROLE_DELIVERYNOTE_INDEX'),
                'preview' => array('ROLE_DELIVERYNOTE_INDEX'),
                'view' => array('ROLE_DELIVERYNOTE_INDEX')
            ],
            'projects' => [
                'index' => array('ROLE_PROJECTS_INDEX'),
                'view' => array('ROLE_PROJECTS_INDEX'),
                'add' => array('ROLE_PROJECTS_INDEX'),
                'edit' => array('ROLE_PROJECTS_INDEX'),
                'delete' => array('ROLE_PROJECTS_INDEX'),
                'leave' => array('ROLE_PROJECTS_INDEX'),
                'manage' => array('ROLE_PROJECTS_INDEX'),
                'note' => array('ROLE_PROJECTS_INDEX'),
                'editnote' => array('ROLE_PROJECTS_INDEX'),
                'addnote' => array('ROLE_PROJECTS_INDEX'),
                'uploadnotefile' => array('ROLE_PROJECTS_INDEX'),
                'file' => array('ROLE_PROJECTS_INDEX'),
                'uploadfile' => array('ROLE_PROJECTS_INDEX'),
                'downloadnotefile' => array('ROLE_PROJECTS_INDEX'),
                'downloadfile' => array('ROLE_PROJECTS_INDEX'),
                'removenote' => array('ROLE_PROJECTS_INDEX'),
                'deletenotefile' => array('ROLE_PROJECTS_INDEX'),
                'deletefile' => array('ROLE_PROJECTS_INDEX'),
                'viewtask' => array('ROLE_PROJECTS_INDEX'),
                'tasks' => array('ROLE_PROJECTS_INDEX'),
                'addtask' => array('ROLE_PROJECTS_INDEX'),
                'taskstatus' => array('ROLE_PROJECTS_INDEX'),
                'edittask' => array('ROLE_PROJECTS_INDEX'),
                'deletetask' => array('ROLE_PROJECTS_INDEX')
            ],
            'general' => [
                'account' => array('ROLE_GENERAL_INDEX'),
                'organisation' => array('ROLE_GENERAL_INDEX'),
                'organisationEdit' => array('ROLE_GENERAL_INDEX'),
                'preferences' => array('ROLE_GENERAL_INDEX')
            ]
        ),

    )
));