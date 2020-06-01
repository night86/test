<?php

namespace Signa\Controllers\Signadens;

use Signa\Helpers\Importcode;
use Signa\Models\ImportColumnNames;
use Signa\Models\ImportMaps;
use Signa\Models\CodeTariff;
use Signa\Helpers\Translations as Trans;

class ImportcodeController extends InitController
{
    private $importObj;

    public function beforeExecuteRoute()
    {
        $currentAction = $this->di->getShared('dispatcher')->getActionName();
        $this->importObj = $this->persistent->import;

        if($this->importObj == null && $currentAction !== 'index'){

            $message = [
                'type' => 'error',
                'content' => 'Import data is missing.'
            ];
            $this->session->set('message', $message);
            $this->view->disable();
            $this->response->redirect("signadens/importcode/");
            return false;
        }
    }

    public function indexAction()
    {
        if ($this->request->isPost()){

            // Start form validation
            $errors = array();

            if ($this->request->hasFiles() == true){

                $file = $this->request->getUploadedFiles()[0];
                $availableExt = array('csv', 'xls', 'xlsx', 'ods');

                if(in_array($file->getExtension(), $availableExt)){

                    $errors[] = "Excepted file formats csv, ods, xls or xlsx.";
                }
            }
            $post = $this->request->getPost();

            if(!isset($post['importType']) || !isset($post['importDate'])){
                $errors[] = "All fields have to be filled.";
            }

            if(!count($errors)){
                $message = [
                    'type' => 'error',
                    'content' => $errors
                ];
                $this->session->set('message', $message);
                return $this->response->redirect("signadens/importcode/");
            }

            // End form validation and start saving form
            $name = $file->getName();
            $path = $this->config->application->productCsvDir;

            // Check path if dont exist create it!
            mkdirR($path);
            $file->moveTo($this->config->application->productCsvDir . $name);

            // Get data from uploaded file

            include __DIR__.'/../../plugins/phpexcel/PHPExcel.php';

            if($file->getExtension() == 'csv'){

                $objReader = \PHPExcel_IOFactory::createReader(strtoupper($file->getExtension()));
                $objReader->setDelimiter(";");
                $phpexcel = $objReader->load($this->config->application->productCsvDir . $name);
            }
            else {
                $phpexcel = \PHPExcel_IOFactory::load($this->config->application->productCsvDir . $name);
            }
            $activeSheetData = $phpexcel->getActiveSheet()->toArray(null, null, null, null);
            $headers = $activeSheetData[0];
            unset($activeSheetData[0]);
            $activeSheetData = array_values($activeSheetData);

            $importObj = new Importcode($post['importType'], $headers, $activeSheetData, $post['importDate'], $name);
            $this->persistent->import = $importObj;

            return $this->response->redirect("signadens/importcode/map");
        }
    }

    public function mapAction(){

        if ($this->request->isPost()){

            $post = $this->request->getPost();

            $invalid = Importcode::mapUniqueValidation($post['map']);

            if($invalid){
                $message = [
                    'type' => 'error',
                    'content' => 'Map values are duplicated.'
                ];
                $this->session->set('message', $message);
                $this->view->disable();
                $this->response->redirect("signadens/importcode/map");
                return false;
            }

            $this->importObj->setMap($post['map']);
            $this->importObj->assignRowsToMap(ImportColumnNames::find("type LIKE '".$this->importObj->getType()."'")->toArray());
            $this->persistent->import = $this->importObj;

            // Save info about mapping to automatically setting values
            Importcode::saveMap($post['map'], $this->importObj->getFileName());
            return $this->response->redirect("signadens/importcode/overview");
        }

        $importMap = new ImportMaps();
        $this->view->map = $importMap->getMapByFile($this->importObj->getFileName());
        $this->view->allMaps = $importMap->getMapByOrganisation();
        $this->view->maps = ImportColumnNames::find(array("type LIKE '".$this->importObj->getType()."'", "order" => "description"));
        $this->view->headers = $this->importObj->getHeaders();
        $this->view->column = $this->importObj->getRows()[0];
    }

    public function overviewAction(){

        if ($this->request->isPost()){

            $this->view->disable();
            $this->response->redirect("signadens/importcode/confirm");
            return false;
        }

        $this->assets->collection('footer')
            ->addJs("js/bootstrap/tooltip.js")
            ->addJs("js/bootstrap/popover.js");

        $outputRows = [];

        foreach ($this->importObj->getMappedRows() as $ca){

            $checkCode = CodeTariff::findFirst('code = '.$ca['code'].' AND organisation_id = '.$this->currentUser->getOrganisationId());

            if($checkCode == false){

                $outputRows[] = $ca;
            }
            else {
                $ca['status'] = "Deze tariefcode bestaat al en kan daarom niet worden opgeslagen. Een tariefcode moet uniek zijn.";
                $outputRows[] = $ca;
            }
        }

        $this->view->maps_headers = ImportColumnNames::find("type LIKE '".$this->importObj->getType()."'");
        $this->view->map = $this->importObj->getMap();
        $this->view->rows = $outputRows;
        $this->view->headers = $this->importObj->getHeaders();
    }

    public function confirmAction(){

        if ($this->request->isPost()){

            $type = $this->importObj->getType();
            $commencingDate = $this->importObj->getCommencingDate();
            $post = $this->request->getPost();

            if(isset($post['excludeProducts'])){

                $codesAdded = Importcode::beginImport($this->importObj->getMappedRows(), $post['excludeProducts'], $type);
            }
            else {
                $codesAdded = Importcode::beginImport($this->importObj->getMappedRows(), array(), $type);
            }
            
            $this->persistent->importQty = count($codesAdded);
            $this->view->disable();
            $this->response->redirect("signadens/importcode/complete");
            return false;
        }

        $outputRows = [];

        foreach ($this->importObj->getMappedRows() as $ca){

            $checkCode = CodeTariff::findFirst('code = '.$ca['code'].' AND organisation_id = '.$this->currentUser->getOrganisationId());

            if($checkCode == false){

                $outputRows[] = $ca;
            }
            else {
                $ca['status'] = "ERROR";
                $outputRows[] = $ca;
            }
        }

        $this->view->type = $this->importObj->getType();
        $this->view->rows = $outputRows;
        $this->view->headers = $this->importObj->getHeaders();
    }

    public function completeAction(){

        $this->notifications->addNotification(array(
            'type' => 2,
            'subject' => Trans::make('Import code'),
            'description' => '<em><h3>'.$this->t->make('Import codes DONE!').'</h3></em>'
        ),'ROLE_SIGNADENS_IMPORT_INDEX');

        $user = $this->session->get('auth');
        $this->mongoLogger->createLog(
            array(
                'datetime' => date('d-m-Y H:i:s'),
                'page' => $this->router->getRewriteUri(),
                'user' => $user->getEmail(),
                'import_type' => $this->importObj->getType().' codes',
                'organisation_id' => $user->Organisation->getId(),
                'file' => array(
                    'name' => $this->importObj->getFileName(),
                    'type' => $this->importObj->getType(),
                    'rows' => $this->importObj->getMappedRows(),
                    'map' => $this->importObj->getMap()
                    )
            ),
        $user->getEmail());

        $this->view->quantity = $this->persistent->importQty;
        $this->view->type = $this->importObj->getType();
        $this->persistent->remove('import');
        $this->persistent->remove('importQty');
    }

    public function ajaxmapAction($id){

        $this->view->disable();

        $importMap = ImportMaps::findFirst($id);
        return json_encode($importMap->getMap());
    }
}
