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

class HelpdeskController extends InitController
{
    public function indexAction(){

        try {
            $this->assets->collection('additional')->addJs('bower_components/tinymce/tinymce.min.js');
            $this->assets->collection('footer')->addJs('js/app/helpDesk.js');
            $tpl = HtmlTemplates::findFirst(
                [
                    'deleted_at IS NULL AND page = :page:',
                    'bind' => [
                        'page' => 'helpdesk',
                    ]
                ]
            );
            $this->view->content = isset($tpl) ? $tpl : '';
        }
        catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }

    public function ajaxSaveAction(){

        try {
            $post = $this->request->getPost();
            $content = $post['content'];
            $content = isset($content) ? $content : '';
            $tpl = HtmlTemplates::findFirst(
                [
                    'deleted_at IS NULL AND page = :page:',
                    'bind' => [
                        'page' => 'helpdesk',
                    ]
                ]
            );
            if (!$tpl) {
                $tpl = new HtmlTemplates();
                $tpl->page = 'helpdesk';
                $tpl->created_at = Date::currentDatetime();
                $tpl->created_by = $this->currentUser->getId();
                $tpl->info = 'Helpdesk page tpl';
            }
            $tpl->html = $content;
            $tpl->updated_at = Date::currentDatetime();
            $tpl->updated_by = $this->currentUser->getId();
            $tpl->save();

            return json_encode(["content" => Trans::make('Page saved')]);
        }
        catch (\Exception $e) {
            return json_encode(["Error" => $e->getMessage()]);
        }
    }

    public function viewAction(){

        try {
            $tpl = HtmlTemplates::findFirst(
                [
                    'deleted_at IS NULL AND page = :page:',
                    'bind' => [
                        'page' => 'helpdesk',
                    ]
                ]
            );
            $this->view->content = $tpl;
        }
        catch (\Exception $e) {
            print_r($e->getMessage());
        }
    }

    public function editAction(){

    }

}