<?php
/**
 * Created by PhpStorm.
 * User: vad
 * Date: 20.03.17
 * Time: 10:31
 */

namespace Signa\Controllers\Signadens;


use Signa\Helpers\Date;
use Signa\Models\HtmlTemplates;
use Signa\Helpers\Translations as Trans;
use Signa\Models\OrganisationTypes;

class InstructionsController extends InitController
{
    public function indexAction(){

    }

    public function ajaxSaveAction(){

    }

    public function viewAction(){

    }

    public function editAction(){

        if($this->request->isPost()){

            foreach($this->request->getPost('start_page') as $k => $v){

                $editStartPage = OrganisationTypes::findFirst("slug ='".$k."'");
                $editStartPage->setStartPage($v);
                $editStartPage->save();
            }
            $this->session->set('message', array('type' => 'success', 'content' => Trans::make("Content successfully edited")));
            $this->response->redirect('/signadens/instructions/edit/');
            $this->view->disable();
            return;
        }
        $this->assets->collection('additional')->addJs('bower_components/tinymce/tinymce.min.js');

        //for now available for dentists only
        $this->view->organisationTypes = OrganisationTypes::findFirst("slug ='dentist'");
    }
}