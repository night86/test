<?php

namespace Signa\Controllers\Supplier;

use Signa\Helpers\CsvDelimiterCheck;
use Signa\Helpers\Import;
use Signa\Libs\Convert;
use Signa\Models\ImportColumnNames;
use Signa\Models\ImportMaps;
use Signa\Helpers\Translations as Trans;

class ImportController extends InitController
{
    /** @var Import */
    private $importObj;

    public function beforeExecuteRoute(){

        $currentAction = $this->di->getShared('dispatcher')->getActionName();

        if ($currentAction == 'log') {
            return true;
        }
        $this->importObj = $this->persistent->import;

        if ($this->importObj == null && $currentAction !== 'index') {

            $message = [
                'type' => 'error',
                'content' => 'Import data is missing.'
            ];

            $this->session->set('message', $message);
            $this->view->disable();
            $this->response->redirect("supplier/import/");
            return false;
        }
    }

    public function indexAction(){

        if ($this->request->isPost()){

            // Start form validation
            $errors = array();

            if ($this->request->hasFiles() == true) {

                $file = $this->request->getUploadedFiles()[0];
                $availableExt = array('csv', 'xls', 'xlsx', 'ods');

                if (!in_array($file->getExtension(), $availableExt)) {

                    $errors[] = Trans::make("Excepted file formats csv, ods, xls or xlsx.");
                }
            }
            $post = $this->request->getPost();

            if (!isset($post['importType']) || !isset($post['importDate'])) {

                $errors[] = Trans::make("All fields have to be filled.");
            }

            if (!empty($errors)) {

                $message = [
                    'type' => 'error',
                    'content' => implode(', ', $errors)
                ];

                $this->session->set('message', $message);
                return $this->response->redirect("supplier/import/");
            }
            $this->session->set('importType', $post['importType']);

            // End form validation and start saving form
            $name = $file->getName();
            $path = $this->config->application->productCsvDir;

            // Check path if dont exist create it!
            mkdirR($path);
            $file->moveTo($this->config->application->productCsvDir . $name);

            // Get data from uploaded file
            include __DIR__ . '/../../plugins/phpexcel/PHPExcel.php';

            if ($file->getExtension() == 'csv') {

                $objReader = \PHPExcel_IOFactory::createReader(strtoupper($file->getExtension()));
                $enc = mb_detect_encoding(file_get_contents($this->config->application->productCsvDir . $name), mb_detect_order(), true);

                if ($enc) {
                    $objReader->setInputEncoding($enc);
                }
                else {
                    $objReader->setInputEncoding('CP1252');
                }

                if (isset($post['delimiterType'])) {

                    $testDelimeter = new CsvDelimiterCheck($this->config->application->productCsvDir . $name);

                    if ($testDelimeter->delimiter != $post['delimiterType']) {

                        $message = [
                            'type' => 'error',
                            'content' => 'Seems like delimeter is incorrect'
                        ];
                        $this->session->set('message', $message);
                    };

                    switch ($post['delimiterType']) {

                        case 'semicolon':
                            $objReader->setDelimiter(";");
                            break;
                        case 'comma':
                            $objReader->setDelimiter(",");
                            break;
                        case 'tab':
                            $objReader->setDelimiter("\t");
                            break;
                        default:
                            $objReader->setDelimiter(";");
                    }
                }
                else {
                    $objReader->setDelimiter(";");
                }
                $phpexcel = $objReader->load($this->config->application->productCsvDir . $name);
            }
            else {
                $phpexcel = \PHPExcel_IOFactory::load($this->config->application->productCsvDir . $name);
            }
            $activeSheetData = $phpexcel->getActiveSheet()->toArray(null, null, null, null);
            $headers = $activeSheetData[0];
            unset($activeSheetData[0]);
            $activeSheetData = array_values($activeSheetData);

            $datetime = new \DateTime($post['importDate']);
            $programmingDateFormat = $datetime->format('Y-m-d');
            $importObj = new Import($post['importType'], $headers, $activeSheetData, $programmingDateFormat, $name);
            $this->persistent->import = $importObj;

            return $this->response->redirect("supplier/import/map");
        }

        $this->assets->collection('footer')
            ->addJs("js/app/delimiterCheck.js");

        $this->view->disableSubnav = true;
    }

    public function logAction(){

        $this->db->query("UPDATE import_products SET isopened = 1 WHERE supplier_id = {$this->currentUser->getOrganisationId()} AND isopened IS NULL");
        $this->view->imports = $this->currentUser->Organisation->getImports(['order' => 'created_at DESC']);
        $this->view->disableSubnav = true;
    }

    public function mapAction(){

        if ($this->request->isPost()) {

            $post = $this->request->getPost();
            $invalid = Import::mapUniqueValidation($post['map']);

            if ($invalid) {

                $message = [
                    'type' => 'error',
                    'content' => 'Map values are duplicated.'
                ];

                $this->session->set('message', $message);
                $this->view->disable();
                $this->response->redirect("supplier/import/map");
                return false;
            }
            $this->importObj->setMap($post['map']);

            $importColumnNames = ImportColumnNames::query()
                ->where('type LIKE \'product\'')
                ->columns(['id','name','description','type','req'])
                ->execute()
                ->toArray();

            // why we didnt have any required validation before? how it works before?
            $invalid = Import::mapRequiredValidation($post['map'], $importColumnNames);
            if ($invalid) {
                $message = [
                    'type' => 'error',
                    'content' => 'Please fill out the required fields.'
                ];
                $this->session->set('message', $message);
                $this->view->disable();
                $this->response->redirect("supplier/import/map");
                return false;
            }

            $this->importObj->assignRowsToMap($importColumnNames);
            $this->persistent->import = $this->importObj;

            // Save info about mapping to automatically setting values
            Import::saveMap($post['map'], $this->importObj->getFileName());

            return $this->response->redirect("supplier/import/overview");
        }
        $importMap = new ImportMaps();

        $this->view->map = $importMap->getMapByFile($this->importObj->getFileName());
        $this->view->allMaps = $importMap->getMapByOrganisation();
        $this->view->maps = ImportColumnNames::find(array("type LIKE 'product'", "order" => "description"));
        $this->view->headers = $this->importObj->getHeaders();
        $this->view->column = $this->importObj->getRows()[0];
        $this->view->disableSubnav = true;
        $this->view->importType = $this->session->get('importType');
    }

    public function overviewAction(){

        if ($this->request->isPost()) {

            $this->view->disable();
            $this->response->redirect("supplier/import/confirm");
            return false;
        }

        // If import type is update, get products from db to compare values
        if ($this->importObj->getType() === 'update') {

            $productsToUpdate = $this->importObj->getProductsToUpdate();

            if ($productsToUpdate === 0) {

                $this->persistent->remove('import');
                $this->persistent->remove('importQty');

                $message = [
                    'type' => 'error',
                    'content' => 'There is no products to update.'
                ];
                $this->session->set('message', $message);
                $this->view->disable();
                $this->response->redirect("supplier/import/");
            }
        }
        $this->assets->collection('footer')
            ->addJs("js/bootstrap/tooltip.js")
            ->addJs("js/bootstrap/popover.js");

        $this->view->maps_headers = ImportColumnNames::find('type LIKE \'product\'');
        $this->view->map = $this->importObj->getMap();
        $this->view->rows = $this->importObj->getMappedRows();
        $this->view->headers = $this->importObj->getHeaders();
        $this->view->disableSubnav = true;
        $this->view->importType = $this->session->get('importType');
    }

    public function confirmAction(){

        if ($this->request->isPost()){

            $type = $this->importObj->getType();
            $commencingDate = $this->importObj->getCommencingDate();
            $post = $this->request->getPost();

            if (isset($post['excludeProducts'])) {

                $productsAdded = Import::createQueue($this->importObj->getMappedRows(), $post['excludeProducts'], $type, $commencingDate, $this->importObj->getFileName());
            }
            else {
                $productsAdded = Import::createQueue($this->importObj->getMappedRows(), array(), $type, $commencingDate, $this->importObj->getFileName());
            }
            $this->persistent->importQty = count($productsAdded);
            $this->persistent->import->setMappedRows($productsAdded);
            $this->view->disable();
            $this->response->redirect("supplier/import/complete");
            return false;
        }

        $this->view->type = $this->importObj->getType();
        $this->view->rows = $this->importObj->getMappedRows();
        $this->view->headers = $this->importObj->getHeaders();
        $this->view->disableSubnav = true;
        $this->view->importType = $this->session->get('importType');

        $this->assets->collection('footer')
            ->addJs("js/bootstrap/tooltip.js")
            ->addJs("js/bootstrap/popover.js");
    }

    public function completeAction(){

        $supplierName = $this->currentUser->Organisation->getName();
        $description = '<p>' . $supplierName . ' ' . $this->t->make('has imported') . ' ' . $this->persistent->importQty . ' ' . $this->t->make('with deadline') . ' (' . $this->importObj->getCommencingDate() . ').</p>';
        $description .= '<p>' . $this->t->make('Check them out to approve or decline') . ' <a href=&quot;/signadens/import/approve/' . $this->importObj->getMappedRows()[0]['import_id'] . '&quot;>' . $this->t->make('this import') . '</a>.</p>';

        $this->notifications->addNotification(array(
            'type' => 2,
            'subject' => Trans::make('Import product'),
            'description' => $description
        ), 'ROLE_SIGNADENS_IMPORT_INDEX');

        $this->view->quantity = $this->persistent->importQty;
        $this->persistent->remove('import');
        $this->persistent->remove('importQty');
        $this->view->disableSubnav = true;
    }

    public function ajaxmapAction($id){

        $this->view->disable();
        $importMap = ImportMaps::findFirst($id);
        return json_encode($importMap->getMap());
    }
}
