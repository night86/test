<?php

namespace Signa\Controllers\Lab;

use Signa\Models\MapLabTariffLedger;
use Signa\Models\RecipeCustomField;
use Signa\Models\RecipeCustomFieldOptions;
use Signa\Models\Recipes;
use Signa\Libs\Recipes as RecipesLib;
use Signa\Models\CodeTariff;
use Signa\Models\Products;
use Signa\Helpers\Import;
use Signa\Models\RecipeStatus;
use Signa\Models\RecipeStatusTime;

class SalesRecipeController extends InitController
{
    public function indexAction(){

        $recipes = Recipes::find(
            [
                'deleted_at IS NULL AND ( (lab_id IS NULL AND active = :active: ) OR lab_id = :labid: )',
                'bind' => [
                    'active' => 1,
                    'labid' => $this->currentUser->getOrganisationId(),
                ],
                "order" => "created_at ASC",
            ]
        );
        $filteredRecipes = array();

        foreach ($recipes as $recipe) {

            if ( ($recipe->ParentRecipe && !$recipe->ParentRecipe->getDeletedAt() && $recipe->ParentRecipe->getActive() == 1) || !$recipe->ParentRecipe) {

                if (array_key_exists($recipe->getCode(), $filteredRecipes) || ($recipe->ParentRecipe && array_key_exists($recipe->ParentRecipe->getCode(), $filteredRecipes))) {

                    $filteredRecipes[$recipe->ParentRecipe->getCode()] = $recipe;
                    continue;
                }
                $filteredRecipes[$recipe->getCode()] = $recipe;
            }
        }
        $this->view->recipes = $filteredRecipes;
    }

    public function productiontimeAction(){

        $labId = $this->currentUser->getOrganisationId();
        $statusesTime = RecipeStatusTime::query()
            ->where('lab_id = :lab_id:')
            ->bind([
                'lab_id' => $labId,
            ])
            ->execute();

        $statusesTimeArr = [];

        /** @var RecipeStatusTime $statusTime */
        foreach ($statusesTime as $statusTime) {

            $statusesTimeArr[$statusTime->getRecipeStatusId()] = $statusTime;
        }

        $this->view->statuses_times = $statusesTimeArr;
        $this->view->statuses_av = RecipeStatus::find();
    }

    public function productiontimeupdateAction(){

        if (!$this->request->hasPost('statusTimeId')) {
            return false;
        }

        /** @var RecipeStatusTime $statusTime */
        $statusTime = RecipeStatusTime::findFirstById($this->request->getPost('statusTimeId'));

        if (!$statusTime) {
            $statusTime = new RecipeStatusTime();
            $statusTime->setLabId($this->currentUser->getOrganisationId());
            $statusTime->setRecipeStatusId($this->request->getPost('statusId'));
        }

        $statusTime->setDays($this->request->getPost('days'));
        $s = $statusTime->save();

        $result = [
            'status' => (bool)$s,
            'error' => $statusTime->getMessages(),
            'element' => $statusTime->toArray()
        ];

        return json_encode($result); die;
    }

    public function inactiveAction(){

        $this->view->recipes = Recipes::find('deleted_at IS NULL AND lab_id IS NULL AND active = 0');

        $this->assets->collection('footer')
            ->addJs("js/app/signadensRecipe.js");
    }

    public function saverowAction(){

        $result = array(
            'status'    => 'error',
            'message'   => 'Nothing to do.',
            'reload'    => 0,
        );

        if($this->request->isPost()) {

            $recipe = Recipes::findFirst($this->request->getPost('id'));

            if ($recipe->getLabId() == null) {

                // create new lab recipe
                $saved = RecipesLib::createLabRecipe(
                    $recipe,
                    $this->request->getPost('code'),
                    $this->request->getPost('name'),
                    $this->currentUser->Organisation->getId()
                );

                if ($saved) {
                    $result = array(
                        'status'    => 'success',
                        'message'   => 'Recipe updated.',
                        'reload'    => 1,
                    );
                }
                else {
                    $result = array(
                        'status'    => 'error',
                        'message'   => 'Something went wrong.',
                        'reload'    => 0,
                    );
                }
            }
            else {
                // update exist lab recipe
                $recipe->setCustomCode($this->request->getPost('code'));
                $recipe->setCustomName($this->request->getPost('name'));
                $recipe->save();

                $result = array(
                    'status'    => 'success',
                    'message'   => 'Recipe updated.',
                    'reload'    => 0,
                );
            }
        }

        $this->view->disable();
        echo json_encode($result);
    }

    public function activateAction($id){

        $recipe = Recipes::findFirst($id);

        if ($recipe->checkIfCanBeActivated()) {

            $status = $recipe->activateDeactivate(1);

            if ($status) {
                $message = [
                    'type' => 'success',
                    'content' => 'Recipe has been activated.',
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/lab/sales_recipe/edit/' . $recipe->getCode());
                $this->view->disable();
                return;
            }
        }
        $message = [
            'type' => 'warning',
            'content' => 'Recipe cannot be activated.',
        ];
        $this->session->set('message', $message);
        $this->response->redirect('/lab/sales_recipe/edit/' . $recipe->getCode());
        $this->view->disable();
        return;
    }

    public function deactivateAction($id){

        $recipe = Recipes::findFirst($id);
        $status = $recipe->activateDeactivate(0);

        if($status){
            $message = [
                'type' => 'success',
                'content' => 'Recipe has been deactivated.',
            ];
            $this->session->set('message', $message);
            $this->response->redirect('/lab/sales_recipe/edit/' . $recipe->getCode());
            $this->view->disable();
            return;
        }
        $message = [
            'type' => 'warning',
            'content' => 'Recipe cannot be deactivated.',
        ];
        $this->session->set('message', $message);
        $this->response->redirect('/lab/sales_recipe/edit/' . $recipe->getCode());
        $this->view->disable();
        return;
    }

    /**
     * $activation is 1 when deactivate and 2 when activate action
     *
     * @param $code
     * @param null $activation
     */
    public function editAction($code, $activation = null){

        $recipe = Recipes::findFirstByCode($code);

        if($this->request->isPost()) {

            $recipe->setPriceType($this->request->getPost('price_type'));
            $recipe->setPrice($this->request->getPost('price'));
            $price = 0;

            if ($this->request->getPost('price_type') === '1') {

                $price = $this->request->getPost('price_composite');
            }
            if ($this->request->getPost('price_type') === '2') {

                $price = $this->request->getPost('price');
            }
            $recipe->setPrice($price);

            if ($this->request->hasPost('delivery_time') && $this->request->getPost('delivery_time') != '') {

                $recipe->setDeliveryTime($this->request->getPost('delivery_time'));
            }
            else {
                $recipe->setDeliveryTime(null);
            }
            $recipe->setCustomRecipe($this->request->getPost('custom_recipe'));
            $recipe->setIsBasic($recipe->ParentRecipe->is_basic);
            $s = $recipe->save();

            if($s){
                if ($this->request->hasFiles() == true) {

                    $imgDir = $this->config->application->recipeImagesDir;

                    // Print the real file names and their sizes
                    $file = $this->request->getUploadedFiles()[0];
                    $randomString = Import::generateRandomString(6);
                    $filename = $randomString.$file->getName();
                    $availableExt = array('png', 'jpeg', 'jpg');

                    if(in_array($file->getExtension(), $availableExt)){

                        if(!is_dir($imgDir)) {
                            mkdirR($imgDir);
                        }
                        $file->moveTo($imgDir.$filename);
                        $recipe->setImage($filename);
                        $recipe->save();
                    }
                }
                $message = [
                    'type' => 'success',
                    'content' => 'Recipe has been saved.',
                ];
                $this->session->set('message', $message);

                if ($activation) {

                    if ($activation == 1) {

                        return $this->response->redirect(sprintf('/lab/sales_recipe/deactivate/%s',$recipe->getId()));
                    }
                    elseif ($activation == 2) {
                        return $this->response->redirect(sprintf('/lab/sales_recipe/activate/%s', $recipe->getId()));
                    }
                }
                $this->response->redirect(sprintf('/lab/sales_recipe/edit/%s',$recipe->getCode()));
            }
            else {
                $message = [
                    'type' => 'warning',
                    'content' => 'Something went wrong',
                ];
                $this->session->set('message', $message);
                $this->response->redirect(sprintf('/lab/sales_recipe/edit/%s',$recipe->getCode()));
            }

            $this->view->disable();
            return;
        }
        $tariffs = CodeTariff::find('active = 1');

        $myTariffs = array();

        foreach ($tariffs as $tariff) {

            if ($tariff->getOrganisationId() == $this->currentUser->Organisation->getId()) {

                $map = MapLabTariffLedger::findFirstByTariffId($tariff->getId());

                if ($map) {
                    $myTariffs[$map->getSignaTariffId()] = $tariff;
                }
            }
        }
        $customFields = RecipeCustomField::find('recipe_id = '.$recipe->getParentId());

        foreach ($customFields as $customField) {

            $customFieldValues = RecipeCustomFieldOptions::findFirst('recipe_customfield_id = ' . $customField->getId());

            if(!$customFieldValues) {
                continue;
            }
            $customFieldValues = $customFieldValues->toArray();

            $customFieldAr[] =
                [
                'option' => $customFieldValues['option'],
                'value' => $customFieldValues['value'],
                ];
        }

        $this->view->recipe     = $recipe;
        $this->view->tariffs    = $tariffs;
        $this->view->myTariffs  = $myTariffs;
        $this->view->image_url  = '/uploads/images/recipes/';
        $this->view->customFieldsOptions = $customFieldAr;
        $this->view->customFields = $customFields;

        $this->assets->collection('footer')
            ->addJs("js/app/editrecipe.js");
    }
}
