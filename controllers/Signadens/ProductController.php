<?php

namespace Signa\Controllers\Signadens;


use Signa\Controllers\NotificationController;
use Signa\Controllers\ProjectsController;
use Signa\Libs\Notifications;
use Signa\Helpers\Date;
use Signa\Helpers\Import;
use Signa\Helpers\Translations as Trans;
use Signa\Libs\Products\ProductsData;
use Signa\Libs\Products\ProductsList;
use Signa\Libs\Recipes as RecipeLib;
use Signa\Models\CodeTariff;
use Signa\Models\DentistGroupDiscount;
use Signa\Models\FilesStorage;
use Signa\Models\Organisations;
use Signa\Models\ProductCategories;
use Signa\Models\Products;
use Signa\Models\RecipeActivity;
use Signa\Models\RecipeCustomField;
use Signa\Models\RecipeCustomFieldOptions;
use Signa\Models\RecipeProduct;
use Signa\Models\Recipes;
use Phalcon\Mvc\View;
use Signa\Models\RecipeStatus;
use Signa\Models\RecipeSetting;
use Signa\Models\RecipeDefaultSetting;
use Signa\Models\RecipeDefaultSettingOption;
use Signa\Models\CodeLedger;

class ProductController extends InitController
{
    public function indexAction(){

        $this->assets->collection('footer')
            ->addJs("js/app/signadensRecipe.js");

        $this->view->recipes = Recipes::find('deleted_at IS NULL and parent_id IS NULL');
    }

    public function addAction()
    {
        $recipe = new Recipes();

        if ($this->request->isPost()) {

            if(!empty($this->request->getPost('statuses_selected'))){

                $selectedStatus = explode(",", $this->request->getPost('statuses_selected'));
                $statuses = array();

                foreach($selectedStatus as $se){

                    $statuses[] = RecipeStatus::findFirst('id = '.$se)->toArray();
                }
                $recipe->setStatuses(json_encode($statuses));
            }
            $recipe->setCode();
            $recipe->setName($this->request->getPost('name'));

            if(!empty($this->request->getPost('recipe_number')) && is_numeric($this->request->getPost('recipe_number'))){

                $recipe->setRecipeNumber($this->request->getPost('recipe_number'));
            }
            else {
                $message = [
                    'type' => 'warning',
                    'content' => Trans::make('Only numbers are allowed in the recipe number. Your input could not be saved'),
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/signadens/product/add');
            }
            $recipe->setDescription($this->request->getPost('description'));

            if ($this->request->hasPost('is_basic')) {
                $recipe->setIsBasic(1);
            }
            else {
                $recipe->setIsBasic(0);
            }
            $s = $recipe->save();

            foreach($this->request->getPost('recipe_settings') as $k => $v){

                if(!empty($v)){
                    $newSetting = new RecipeSetting();
                    $newSetting->setRecipeId($recipe->getId());
                    $newSetting->setSettingId($k);
                    $newSetting->setOptionId($v);
                    $newSetting->save();
                }
            }

            if ($s) {

                if ($this->request->hasPost('activity')) {

                    $activityPost = $this->request->getPost('activity');

                    foreach ($activityPost['tariff'] as $key => $tariff) {

                        $recipeActivity = new RecipeActivity();
                        $recipeActivity->setRecipeId($recipe->getId());
                        $recipeActivity->setTariffId($tariff);
                        $recipeActivity->setDescription($activityPost['description'][$key]);
                        $recipeActivity->setAmount(($activityPost['amount'][$key]) != '' ? $activityPost['amount'][$key] : 0);
                        $recipeActivity->save();
                    }
                }
                if ($this->request->hasPost('product')) {

                    $productPost = $this->request->getPost('product');

                    foreach ($productPost['product'] as $key => $product) {

                        $recipeProduct = new RecipeProduct();
                        $recipeProduct->setRecipeId($recipe->getId());
                        $recipeProduct->setProductId($product);
                        $recipeProduct->setDescription($productPost['description'][$key]);
                        $recipeProduct->setAmount(($productPost['amount'][$key]) != '' ? $productPost['amount'][$key] : 0);
                        $recipeProduct->save();
                    }
                }
                if ($this->request->hasPost('variable')) {

                    $variablePost = $this->request->getPost('variable');

                    foreach ($variablePost['name'] as $key => $name) {

                        $recipeVariable = new RecipeCustomField();
                        $recipeVariable->setRecipeId($recipe->getId());
                        $recipeVariable->setName($name);
                        $recipeVariable->setType($variablePost['type'][$key]);
                        $recipeVariable->setAmount(($variablePost['amount'][$key]) != '' ? $variablePost['amount'][$key] : 0);
                        $recipeVariable->setHasLabCheck($this->request->getPost('params')[$key]['field_lab']);

                        if($variablePost['type'][$key] == 'number'){

                            $recipeVariable->setCustomPriceTariffId($this->request->getPost('params')[$key]['numberprice']);
                        }
                        $recipeVariable->setCustomFieldType('variable');
                        $recipeVariable->save();

                        foreach ($variablePost['options'][$key] as $option) {

                            $value = str_replace(' ', '_', trim($option));

                            $recipeVariableOption = new RecipeCustomFieldOptions();
                            $recipeVariableOption->setRecipeCustomfieldId($recipeVariable->getId());
                            $recipeVariableOption->setOption($option);
                            $recipeVariableOption->setValue(strtolower($value));
                            $recipeVariableOption->save();
                        }
                    }
                }

                if ($this->request->hasPost('optional')) {

                    $optionalPost = $this->request->getPost('optional');

                    foreach ($optionalPost['name'] as $key => $name) {

                        $recipeOptional = new RecipeCustomField();
                        $recipeOptional->setRecipeId($recipe->getId());
                        $recipeOptional->setName($name);
                        $recipeOptional->setType($optionalPost['type'][$key]);
                        $recipeOptional->setAmount(($optionalPost['amount'][$key]) != '' ? $optionalPost['amount'][$key] : 0);
                        $recipeOptional->setHasLabCheck($this->request->getPost('params')[$key]['field_lab']);

                        if($optionalPost['type'][$key] == 'number'){

                            $recipeOptional->setCustomPriceTariffId($this->request->getPost('params')[$key]['numberprice']);
                        }
                        $recipeOptional->setCustomFieldType('optional');
                        $recipeOptional->save();

                        foreach ($optionalPost['options'][$key] as $option) {

                            $value = str_replace(' ', '_', trim($option));

                            $recipeOptionalOption = new RecipeCustomFieldOptions();
                            $recipeOptionalOption->setRecipeCustomfieldId($recipeOptional->getId());
                            $recipeOptionalOption->setOption($option);
                            $recipeOptionalOption->setValue(strtolower($value));
                            $recipeOptionalOption->save();
                        }
                    }
                }

                if ($this->request->hasPost('additional')) {

                    $additionalPost = $this->request->getPost('additional');

                    foreach ($additionalPost['name'] as $key => $name) {

                        $recipeAdditional = new RecipeCustomField();
                        $recipeAdditional->setRecipeId($recipe->getId());
                        $recipeAdditional->setName($name);
                        $recipeAdditional->setType($additionalPost['type'][$key]);
                        $recipeAdditional->setAmount(($additionalPost['amount'][$key]) != '' ? $additionalPost['amount'][$key] : 0);
                        $recipeAdditional->setHasLabCheck($this->request->getPost('params')[$key]['field_lab']);

                        if($additionalPost['type'][$key] == 'number'){

                            $recipeAdditional->setCustomPriceTariffId($this->request->getPost('params')[$key]['numberprice']);
                        }
                        $recipeAdditional->setCustomFieldType('additional');
                        $recipeAdditional->save();

                        foreach ($additionalPost['options'][$key] as $option) {

                            $value = str_replace(' ', '_', trim($option));

                            $recipeAdditionalOption = new RecipeCustomFieldOptions();
                            $recipeAdditionalOption->setRecipeCustomfieldId($recipeAdditional->getId());
                            $recipeAdditionalOption->setOption($option);
                            $recipeAdditionalOption->setValue(strtolower($value));
                            $recipeAdditionalOption->save();
                        }
                    }
                }

                if ($this->request->hasFiles() == true) {

                    $imgDir = $this->config->application->uploadDir . 'images/recipes/';

                    // Print the real file names and their sizes
                    $file = $this->request->getUploadedFiles()[0];
                    $randomString = Import::generateRandomString(6);
                    $filename = $randomString . $file->getName();
                    $availableExt = array('png', 'jpeg', 'jpg');

                    if (in_array($file->getExtension(), $availableExt)) {

                        if (!is_dir($imgDir)) {
                            mkdirR($imgDir);
                        }
                        $file->moveTo($imgDir . $filename);
                        $recipe->setImage($filename);
                        $recipe->save();
                    }
                }

                if ($this->request->hasPost('add_schema')) {

                    $recipe->setHasSchema(1);
                    $recipe->save();
                }
                else {
                    $recipe->setHasSchema(0);
                    $recipe->save();
                }

                $message = [
                    'type' => 'success',
                    'content' => Trans::make('New recipe has been added.'),
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/signadens/product/');
            }
            else {
                $message = [
                    'type' => 'warning',
                    'content' => Trans::make('Something went wrong'),
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/signadens/product/add');
            }
            $this->view->disable();
            return;
        }
        $organisationId = $this->currentUser->Organisation->getId();

        $this->view->tariffs = CodeTariff::find(
            array(
                'organisation_id = ' . $organisationId,
                'active = :active: ',
                'bind' => array(
                    'active' => 1,
                ),
            )
        );

        $this->view->statuses_av = RecipeStatus::find();
        $this->view->recipeSettings = RecipeDefaultSetting::find();
        $this->view->recipe = $recipe;
        $this->view->customfieldCounter = 0;
        $this->view->image_url = '/uploads/images/recipes/';

        $this->assets->collection('footer')
            ->addJs("js/app/addrecipe.js")
            ->addJs("bower_components/Sortable/Sortable.js")
            ->addJs("js/app/recipeStatuses.js");
    }

    public function editAction($code){

        $recipe = Recipes::findFirstByCode($code);

        if ($this->request->isPost()) {

            if(!empty($this->request->getPost('statuses_selected'))){

                $selectedStatus = explode(",", $this->request->getPost('statuses_selected'));
                $statuses = array();

                foreach($selectedStatus as $se) {

                    if ($se) {
                        $statuses[] = (RecipeStatus::findFirst('id = '.$se)) == false ? NULL : RecipeStatus::findFirst('id = '.$se)->toArray();
                    }
                }
                $recipe->setStatuses(json_encode($statuses));
            }
            $recipe->setName($this->request->getPost('name'));
            $recipe->setDescription($this->request->getPost('description'));

            if ((int)$this->request->hasPost('is_basic')) {

                $recipe->setIsBasic(1);

                // Search for all lab recipes connected
                $labRecipes = Recipes::find('parent_id = '.$recipe->getId());

                foreach ($labRecipes as $r){

                    $r->setIsBasic(1);
                    $r->save();
                }
            }
            else {
                $recipe->setIsBasic(0);

                // Search for all lab recipes connected
                $labRecipes = Recipes::find('parent_id = '.$recipe->getId());

                foreach ($labRecipes as $r){

                    $r->setIsBasic(0);
                    $r->save();
                }
            }

            if ($this->request->hasPost('schema_notice')) {

                $recipe->setSchemaNotice($this->request->getPost('schema_notice'));
            }

            if(!empty($this->request->getPost('recipe_number')) && is_numeric($this->request->getPost('recipe_number'))){

                $recipe->setRecipeNumber($this->request->getPost('recipe_number'));
            }
            else {
                $message = [
                    'type' => 'warning',
                    'content' => Trans::make('Only numbers are allowed in the recipe number. Your input could not be saved'),
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/signadens/product/edit/'.$code);
            }
            $recipe->save();

            if ($recipe->save()) {

                foreach($this->request->getPost('recipe_settings_old') as $k => $v){

                    if(!empty($v)){
                        $editSetting = RecipeSetting::findFirst('id='.$k);

                        if($editSetting != false){

                            $editSetting->setOptionId(($v == '-') ? NULL : $v);
                            $editSetting->save();
                        }
                    }
                }

                foreach($this->request->getPost('recipe_settings_new') as $k => $v){

                    if(!empty($v)){

                        $newSetting = new RecipeSetting();
                        $newSetting->setRecipeId($recipe->getId());
                        $newSetting->setSettingId($k);
                        $newSetting->setOptionId(($v == '-') ? NULL : $v);
                        $newSetting->save();
                    }
                }

                RecipeLib::clearRecipeData($recipe);

                if ($this->request->hasPost('activity')) {

                    $activityPost = $this->request->getPost('activity');

                    foreach ($activityPost['tariff'] as $key => $tariff) {

                        $recipeActivity = new RecipeActivity();
                        $recipeActivity->setRecipeId($recipe->getId());
                        $recipeActivity->setTariffId($tariff);
                        $recipeActivity->setDescription($activityPost['description'][$key]);
                        $recipeActivity->setAmount($activityPost['amount'][$key]);
                        $recipeActivity->save();
                    }
                }

                if ($this->request->hasPost('product')) {

                    $productPost = $this->request->getPost('product');

                    foreach ($productPost['product'] as $key => $product) {

                        $recipeProduct = new RecipeProduct();
                        $recipeProduct->setRecipeId($recipe->getId());
                        $recipeProduct->setProductId($product);
                        $recipeProduct->setDescription($productPost['description'][$key]);
                        $recipeProduct->setAmount($productPost['amount'][$key]);
                        $recipeProduct->save();
                    }
                }

                if ($this->request->hasPost('customfield')) {

                    $customfieldPost = $this->request->getPost('customfield');
                    $customfieldPriceOptions = $this->request->getPost('field_option');
                    $customfieldPrice = $this->request->getPost('params');

                    foreach ($customfieldPost['name'] as $key => $name) {

                        $recipeCustomField = new RecipeCustomField();
                        $recipeCustomField->setRecipeId($recipe->getId());
                        $recipeCustomField->setName($name);
                        $recipeCustomField->setType($customfieldPost['type'][$key]);

                        if ($customfieldPost['type'][$key] == 'number') {
                            $recipeCustomField->setCustomPriceTariffId($customfieldPrice[$key]['numberprice']);
                            $recipeCustomField->setCustomPriceType($customfieldPrice[$key]['numberpricechoose']);
                        }
                        $recipeCustomField->save();

                        foreach ($customfieldPost['options'][$key] as $k => $option) {

                            $value = str_replace(' ', '_', trim($option));

                            $recipeCustomFieldOption = new RecipeCustomFieldOptions();
                            $recipeCustomFieldOption->setRecipeCustomfieldId($recipeCustomField->getId());
                            $recipeCustomFieldOption->setOption($option);

                            if (isset($customfieldPriceOptions[$key][$k]['selecttariff'])) {

                                $recipeCustomFieldOption->setTariffId($customfieldPriceOptions[$key][$k]['selecttariff']);
                            }

                            if (isset($customfieldPriceOptions[$key][$k]['numberselectprice'])) {

                                $recipeCustomFieldOption->setCustomPriceTariffId($customfieldPriceOptions[$key][$k]['numberselectprice']);
                            }
                            $recipeCustomFieldOption->setValue(strtolower($value));
                            $recipeCustomFieldOption->save();
                        }
                    }
                }

                if ($this->request->hasPost('variable')) {

                    $variablefieldPost = $this->request->getPost('variable');
                    $variablefieldPriceOptions = $this->request->getPost('field_option');
                    $variablefieldPrice = $this->request->getPost('params');

                    foreach ($variablefieldPost['name'] as $key => $name) {

                        $recipeVariableField = new RecipeCustomField();
                        $recipeVariableField->setRecipeId($recipe->getId());
                        $recipeVariableField->setName($name);
                        $recipeVariableField->setType($variablefieldPost['type'][$key]);
                        $recipeVariableField->setAmount(($variablefieldPost['amount'][$key]) != '' ? $variablefieldPost['amount'][$key] : 0);
                        $recipeVariableField->setCustomFieldType('variable');
                        $recipeVariableField->setHasLabCheck($variablefieldPrice[$key]['field_lab']);

                        if ($variablefieldPost['type'][$key] == 'number') {

                            $recipeVariableField->setCustomPriceTariffId($variablefieldPrice[$key]['numberprice']);
                            $recipeVariableField->setCustomPriceType($variablefieldPrice[$key]['numberpricechoose']);
                        }

                        if ($variablefieldPost['type'][$key] == 'statement') {

                            $recipeVariableField->setCustomPriceTariffId($variablefieldPrice[$key]['statement']);
                        }
                        $recipeVariableField->save();

                        foreach ($variablefieldPost['options'][$key] as $k => $option) {

                            $value = str_replace(' ', '_', trim($option));

                            $recipeVariableFieldOption = new RecipeCustomFieldOptions();
                            $recipeVariableFieldOption->setRecipeCustomfieldId($recipeVariableField->getId());
                            $recipeVariableFieldOption->setOption($option);

                            if (isset($variablefieldPriceOptions[$key][$k]['selecttariff'])) {

                                $recipeVariableFieldOption->setTariffId($variablefieldPriceOptions[$key][$k]['selecttariff']);
                            }
                            if (isset($variablefieldPriceOptions[$key][$k]['numberselectprice'])) {

                                $recipeVariableFieldOption->setCustomPriceTariffId($variablefieldPriceOptions[$key][$k]['numberselectprice']);
                            }
                            $recipeVariableFieldOption->setValue(strtolower($value));
                            $recipeVariableFieldOption->save();
                        }
                    }
                }

                if ($this->request->hasPost('optional')) {

                    $optionalfieldPost = $this->request->getPost('optional');
                    $optionalfieldPriceOptions = $this->request->getPost('field_option');
                    $optionalfieldPrice = $this->request->getPost('params');

                    foreach ($optionalfieldPost['name'] as $key => $name) {

                        $recipeOptionalField = new RecipeCustomField();
                        $recipeOptionalField->setRecipeId($recipe->getId());
                        $recipeOptionalField->setName($name);
                        $recipeOptionalField->setType($optionalfieldPost['type'][$key]);
                        $recipeOptionalField->setCustomFieldType('optional');
                        $recipeOptionalField->setAmount(($optionalfieldPost['amount'][$key]) != '' ? $optionalfieldPost['amount'][$key] : 0);
                        $recipeOptionalField->setHasLabCheck($optionalfieldPrice[$key]['field_lab']);

                        if ($optionalfieldPost['type'][$key] == 'number') {

                            $recipeOptionalField->setCustomPriceTariffId($optionalfieldPrice[$key]['numberprice']);
                            $recipeOptionalField->setCustomPriceType($optionalfieldPrice[$key]['numberpricechoose']);
                        }
                        if ($optionalfieldPost['type'][$key] == 'statement') {

                            $recipeOptionalField->setCustomPriceTariffId($optionalfieldPrice[$key]['statement']);
                        }
                        $recipeOptionalField->save();

                        foreach ($optionalfieldPost['options'][$key] as $k => $option) {

                            $value = str_replace(' ', '_', trim($option));

                            $recipeOptionalFieldOption = new RecipeCustomFieldOptions();
                            $recipeOptionalFieldOption->setRecipeCustomfieldId($recipeOptionalField->getId());
                            $recipeOptionalFieldOption->setOption($option);

                            if (isset($optionalfieldPriceOptions[$key][$k]['selecttariff'])) {

                                $recipeOptionalFieldOption->setTariffId($optionalfieldPriceOptions[$key][$k]['selecttariff']);
                            }
                            if (isset($optionalfieldPriceOptions[$key][$k]['numberselectprice'])) {

                                $recipeOptionalFieldOption->setCustomPriceTariffId($optionalfieldPriceOptions[$key][$k]['numberselectprice']);
                            }
                            $recipeOptionalFieldOption->setValue(strtolower($value));
                            $recipeOptionalFieldOption->save();
                        }
                    }
                }

                if ($this->request->hasPost('additional')) {

                    $additionalfieldPost = $this->request->getPost('additional');
                    $additionalfieldPriceOptions = $this->request->getPost('field_option');
                    $additionalfieldPrice = $this->request->getPost('params');

                    foreach ($additionalfieldPost['name'] as $key => $name) {

                        $recipeAdditionalField = new RecipeCustomField();
                        $recipeAdditionalField->setRecipeId($recipe->getId());
                        $recipeAdditionalField->setName($name);
                        $recipeAdditionalField->setType($additionalfieldPost['type'][$key]);
                        $recipeAdditionalField->setCustomFieldType('additional');
                        $recipeAdditionalField->setAmount(($additionalfieldPost['amount'][$key]) != '' ? $additionalfieldPost['amount'][$key] : 0);
                        $recipeAdditionalField->setHasLabCheck($additionalfieldPrice[$key]['field_lab']);

                        if ($additionalfieldPost['type'][$key] == 'number') {

                            $recipeAdditionalField->setCustomPriceTariffId($additionalfieldPrice[$key]['numberprice']);
                            $recipeAdditionalField->setCustomPriceType($additionalfieldPrice[$key]['numberpricechoose']);
                        }
                        if ($additionalfieldPost['type'][$key] == 'statement') {

                            $recipeAdditionalField->setCustomPriceTariffId($additionalfieldPrice[$key]['statement']);
                        }
                        $recipeAdditionalField->save();

                        foreach ($additionalfieldPost['options'][$key] as $k => $option) {

                            $value = str_replace(' ', '_', trim($option));

                            $recipeAdditionalFieldOption = new RecipeCustomFieldOptions();
                            $recipeAdditionalFieldOption->setRecipeCustomfieldId($recipeAdditionalField->getId());
                            $recipeAdditionalFieldOption->setOption($option);

                            if (isset($additionalfieldPriceOptions[$key][$k]['selecttariff'])) {

                                $recipeAdditionalFieldOption->setTariffId($additionalfieldPriceOptions[$key][$k]['selecttariff']);
                            }
                            if (isset($additionalfieldPriceOptions[$key][$k]['numberselectprice'])) {

                                $recipeAdditionalFieldOption->setCustomPriceTariffId($additionalfieldPriceOptions[$key][$k]['numberselectprice']);
                            }
                            $recipeAdditionalFieldOption->setValue(strtolower($value));
                            $recipeAdditionalFieldOption->save();
                        }
                    }
                }

                if ($this->request->hasFiles() == true) {

                    $imgDir = $this->config->application->uploadDir . 'images/recipes/';

                    // Print the real file names and their sizes
                    $file = $this->request->getUploadedFiles()[0];
                    $randomString = Import::generateRandomString(6);
                    $filename = $randomString . $file->getName();
                    $availableExt = array('png', 'jpeg', 'jpg');

                    if (in_array($file->getExtension(), $availableExt)) {

                        if (!is_dir($imgDir)) {
                            mkdirR($imgDir);
                        }
                        $file->moveTo($imgDir . $filename);
                        $recipe->setImage($filename);
                        $recipe->save();
                    }
                }

                if ($this->request->hasPost('add_schema')) {

                    $recipe->setHasSchema(1);
                    $recipe->save();
                }
                else {
                    $recipe->setHasSchema(0);
                    $recipe->save();
                }

                $message = [
                    'type' => 'success',
                    'content' => Trans::make('Recipe has been saved.'),
                ];
                $this->session->set('message', $message);
                $this->response->redirect(sprintf('/signadens/product/edit/%s', $recipe->getCode()));
            }
            else {
                $message = [
                    'type' => 'warning',
                    'content' => Trans::make('Something went wrong'),
                ];
                $this->session->set('message', $message);
                $this->response->redirect(sprintf('/signadens/product/edit/%s', $recipe->getCode()));
            }

            $this->view->disable();
            return;
        }
        $groups = Organisations::find('is_group = 1');
        $groupDiscount = DentistGroupDiscount::find("code = '".$code."'");
        $previousSettings = RecipeSetting::find('recipe_id='.$recipe->getId());

        foreach ($previousSettings as $setting){

            $selectedSettings[] = $setting->setting_id;
        }
        $this->view->statuses_av = RecipeStatus::find();

        $statuses_se_id = [];

        foreach (json_decode($recipe->getStatuses(), true) as $key => $status){

            if(!empty($status)){
                $statuses_se_id[] = $status['id'];
            }
        }
        $custom_fields = array();
        $i=0; $j=0; $k=0;

        foreach($recipe->RecipeCustomField as $c){

            if($c->custom_field_type == 'variable'){

                $custom_fields['variable'][$i] = $c;
                $i++;
            }
            elseif ($c->custom_field_type == 'optional'){

                $custom_fields['optional'][$j] = $c;
                $j++;
            }
            elseif ($c->custom_field_type == 'additional'){

                $custom_fields['additional'][$k] = $c;
                $k++;
            }
        }
        $this->view->tariffs = CodeTariff::find('active = 1 AND organisation_id = '.$this->currentUser->getOrganisationId());
        $this->view->recipeSettings = RecipeDefaultSetting::find();
        $this->view->previousSettings = $previousSettings;
        $this->view->selectedSettings = $selectedSettings;
        $this->view->statuses_se = json_decode($recipe->getStatuses(), true);
        $this->view->statuses_av = RecipeStatus::find();
        $this->view->statuses_se_id = $statuses_se_id;
        $this->view->recipe = $recipe;
        $this->view->variable = $custom_fields['variable'];
        $this->view->optional = $custom_fields['optional'];
        $this->view->additional = $custom_fields['additional'];
        $this->view->groups = $groups;
        $this->view->code = $code;
        $this->view->groupDiscount = $groupDiscount;
        $this->view->manageUsersContent = $this->getManageGroupsContent('groupmanage');
        $this->view->variableCounter = count($custom_fields['variable']);
        $this->view->optionalCounter = count($custom_fields['optional']);
        $this->view->additionalCounter = count($custom_fields['additional']);
        $this->view->image_url = '/uploads/images/recipes/';

        $this->assets->collection('footer')
            ->addJs("js/app/addrecipe.js")
            ->addJs("bower_components/Sortable/Sortable.js")
            ->addJs("js/app/recipeStatuses.js");
    }

    public function deactivateAction($code){

        $recipe = Recipes::findFirstByCode($code);

        if ($recipe) {

            $recipe->setActive(0);
            $recipe->save();
            $this->session->set('message', array('type' => 'success', 'content' => Trans::make("Recipe has been deactivated.")));
        }
        else {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make("Recipe doesn't exist.")));
        }
        $notification = new Notifications();
        $notification->addNotification(
            [
                'type' => 8,
                'subject' => Trans::make('Status recipe changed'),
                'description' => Trans::make('Signadens set the status of recipe ') . $recipe->getName() . Trans::make(' to inactive. You can no longer offer this recipe to your clients. '),
            ],

            'ROLE_LAB_USER_ADD'
        );
        $this->response->redirect('/signadens/product/');
        $this->view->disable();
        return;
    }

    public function activateAction($code){

        $recipe = Recipes::findFirstByCode($code);

        if ($recipe) {

            $recipe->setActive(1);
            $recipe->save();
            $this->session->set('message', array('type' => 'success', 'content' => Trans::make('Recipe has been activated.')));
        }
        else {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('Recipe doesn\'t exist.')));
        }
        $this->response->redirect('/signadens/product/');
        $this->view->disable();
        return;
    }

    public function deleteAction($code)
    {
        $recipe = Recipes::findFirstByCode($code);

        if ($recipe) {

            $recipe->softDelete();
            $this->session->set('message', array('type' => 'success', 'content' => Trans::make('Recipe has been deleted.')));
        }
        else {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('Recipe doesn\'t exist.')));
        }
        $this->response->redirect('/signadens/product/');
        $this->view->disable();
        return;
    }

    public function ajaxcustomfieldAction(){

        $this->view->disable();

        if ($this->request->isAjax()) {

            $post = $this->request->getPost();

            $options = [];

            if (isset($post['options'])) {

                parse_str($post['options'], $options);
            }

            if (!isset($options['field_option'])) {

                $options['field_option'] = [];
            }

            if (!isset($options['params'])) {

                $options['params'] = null;
            }

            $html = $this->simpleView->render('signadens/product/_customFieldRow', [
                'label_lab' => Trans::make("To be determined by lab"),
                'elementType' => $post['custom_type'],
                'fieldCnt' => $post['counter'],
                'customFieldTypes' => array_map('Signa\Helpers\Translations::make', Recipes::customFieldTypes),
                'fieldType' => $post['type'],
                'fieldName' => $post['label'],
                'fieldLab'  => $post['is_lab'],
                'fieldOptions' => $options['field_option'],
                'fieldNumberParams' => $options['params'],
                'customPriceLabel' => Trans::make('Custom price'),
                'singlePriceLabel' => Trans::make('Single additional price'),
                'itemPriceLabel' => Trans::make('Additional price per item'),
                'tariffs' => CodeTariff::find([
                    'active = :active: ',
                    'bind' => ['active' => 1],
                ]),
            ]);
            return json_encode(array('status' => true, 'content' => $html));
        }
    }

    public function groupmanageAction(){

        $this->view->disable();

        if ($this->request->isPost()) {

            $dgd = new DentistGroupDiscount();
            $groups = $this->request->getPost('groups');
            $discountType = $this->request->getPost('discount_type');
            $productId = $this->request->getPost('product_id');

            foreach ($groups as $orgId) {

                $edgd = DentistGroupDiscount::findFirst('code = '.$productId.' AND organisation_id = '.$orgId);

                if ($edgd) {
                    $this->session->set('message', array('type' => 'error', 'content' => $edgd->Organisation->getName() . ' ' . Trans::make('already has discount for this product.')));
                    return $this->response->redirect($this->request->getHTTPReferer());
                }
                $dgd->setOrganisationId($orgId);
                $dgd->setType($discountType);
                $dgd->setValue($this->request->getPost('value'));
                $dgd->setCode($productId);
            }

            if ($dgd->save()) {
                $this->session->set('message', array('type' => 'success', 'content' => Trans::make('Dentist group discount has been added.')));
            }
            else {
                $this->session->set('message', array('type' => 'error', 'content' => Trans::make('Dentist group discount has not been added.')));
            }
            $this->response->redirect('/signadens/product/edit/' . $productId);

        }
        else {
            $query = $this->request->get('q');
            $org = Organisations::find("is_group = 1 AND name LIKE '%".$query."%'");

            return json_encode($org);
        }
    }

    public function deletegroupAction($id){

        $dgd = DentistGroupDiscount::findFirstById($id);

        if ($dgd->delete()) {
            $this->session->set('message', array('type' => 'success', 'content' => Trans::make('Dentist group discount has been deleted.')));
        }
        else {
            $this->session->set('message', array('type' => 'warning', 'content' => Trans::make('Dentist group discount doesnt exist.')));
        }
        return $this->response->redirect($this->request->getHTTPReferer());
    }

    public function listAction(){

        $this->assets->collection('footer')
            ->addJs("js/app/signadens/productsList.js");
    }

    public function listajaxAction(){

        $pd = new ProductsData();
        $pd->setNoactive(true);
        $pd->setForDatatable(true);

        $orderByMapping = [
            0 => 'signa_id',
            1 => 'code',
            2 => 'name',
            3 => 'supplier_name',
            4 => 'price',
            5 => 'name',
        ];

        // modify post data
        $_POST['limit'] = $this->request->getQuery('length');
        $_POST['page'] = (int)$this->request->getQuery('start') === 0 ?: (int)$this->request->getQuery('start') / (int)$this->request->getQuery('length');
        $_POST['page']++; // because we are starting calc from 0, should be from 1
        if ($this->request->getQuery('order')) {
            $_POST['sort'] = sprintf('%s %s',
                $orderByMapping[$this->request->getQuery('order')[0]['column']],
                $this->request->getQuery('order')[0]['dir']
            );
        }

        if ($this->request->getQuery('search')['value']) {
            $_POST['query'] = $this->request->getQuery('search')['value'];
        }
        $data = $pd->getFilteredProducts();
        $productsArr = [];

        foreach ($data['products'] as $k => $product) {

            $productsArr[$k] = (array)$product;
            $productsArr[$k]['actions'] = '<a href="/signadens/product/listedit/'.$product->id.'" class="btn btn-primary btn-sm"><i class="pe-7s-pen"></i>  '.$this->t->make('Edit').'</a>';
            $productsArr[$k]['actions'] .= '<a href="/signadens/product/listdelete/'.$product->id.'" class="delete-product btn btn-danger btn-sm"><i class="pe-7s-trash"></i>  '.$this->t->make('Delete').'</a>';
        }

        return json_encode([
            'data' => $productsArr,
            'recordsTotal' => $data['recordsTotal'],
            'recordsFiltered' => $data['recordsFiltered']
        ]); die;
    }

    public function listdeleteAction($id){

        $product = Products::findFirst($id);
        $product->delete();

        $this->response->redirect('/signadens/product/list');
        $this->view->disable();
    }

    public function listeditAction($id){

        // Find product
        $product = Products::findFirst($id);
        $file = FilesStorage::findFirst('entity_id = '.$product->getId());
        $productCategories = ProductCategories::find('deleted_by IS NULL AND deleted_at IS NULL AND deleted = 0');

        // Find all ledger codes
        $ledgerCodes = CodeLedger::find('organisation_id = '.$this->currentUser->getOrganisationId().' AND active = 1');

        // Existing purchase/sales ledger codes
        $ledgerPurchase = NULL;
        $ledgerSales = NULL;

        if($product->getSubSubCategoryId() == NULL){

            if($product->getSubCategoryId() == NULL){

                if($product->getMainCategoryId() == NULL){

                }
                else {
                    if($product->MainCategory->LedgerPurchase){
                        $ledgerPurchase = $product->MainCategory->LedgerPurchase;
                    }

                    if($product->MainCategory->LedgerSales){
                        $ledgerSales = $product->MainCategory->LedgerSales;
                    }
                }
            }
            else {
                if($product->SubCategory->LedgerPurchase){
                    $ledgerPurchase = $product->SubCategory->LedgerPurchase;
                }

                if($product->SubCategory->LedgerSales){
                    $ledgerSales = $product->SubCategory->LedgerSales;
                }
            }
        }
        else {
            $subCatChild = ProductCategories::findFirst('id = '.$product->getSubSubCategoryId());

            if($subCatChild->LedgerPurchase){
                $ledgerPurchase = $subCatChild->LedgerPurchase;
            }

            if($subCatChild->LedgerSales){
                $ledgerSales = $subCatChild->LedgerSales;
            }
        }

        if ($this->request->isPost()) {

            $post = $this->request->getPost();

            $product->setLedgerPurchaseId((!empty($post['ledger_purchase_id']) && $post['ledger_purchase_id'] != NULL) ? $post['ledger_purchase_id'] : NULL);
            $product->setLedgerSalesId((!empty($post['ledger_sales_id']) && $post['ledger_sales_id'] != NULL) ? $post['ledger_sales_id'] : NULL);
            $product->save();

            if ($this->request->hasFiles() == true) {

                $filesDir = $this->config->application->filesStorageDir;

                // Print the real file names and their sizes
                foreach ($this->request->getUploadedFiles() as $attachment) {

                    $type = explode('.', $attachment->getKey());
                    $entityName = 'products_' . $type[0];

                    if (!is_dir($filesDir)) {
                        mkdirR($filesDir);
                    }
                    $attachment->moveTo($filesDir . $attachment->getName());
                    $md5 = md5_file($filesDir . $attachment->getName());

                    if ($file = FilesStorage::findFirst(
                        [
                            'md5 = :md5: AND deleted_at is NULL',
                            'bind' => [
                                'md5' => $md5,
                            ],
                        ]
                    )){
                        continue;
                    }
                    $file = new FilesStorage();
                    $file->setEntityId($product->getId());
                    $file->setEntityName($entityName);
                    $file->setName($attachment->getName());
                    $file->setPath($filesDir . $attachment->getName());
                    $file->setExt($attachment->getExtension());
                    $file->setSize($attachment->getSize());
                    $file->setMime($attachment->getType());
                    $file->setMd5($md5);
                    $file->setCreatedAt(Date::currentDatetime());
                    $file->setCreatedBy($this->user->getId());
                    $file->setUpdatedAt(Date::currentDatetime());
                    $file->setUpdatedBy($this->user->getId());
                    $file->save();
                }
            }
            $saved = $product->saveData($post);
            $messages = $product->getMessages();

            if ($saved) {

                $message = [
                    'type' => 'success',
                    'content' => Trans::make('Product has been edited.'),
                ];
                $this->session->set('message', $message);
            }
            else {
                $message = [
                    'type' => 'success',
                    'content' => Trans::make('Product has not been edited.'),
                ];
                $this->session->set('message', $message);
            }
            return $this->response->redirect($this->request->getHTTPReferer());
        }

        // View vars allocation
        $this->view->product = $product;
        $this->view->attachment = $file;
        $this->view->productCategories = $productCategories;
        $this->view->ledgerCodes = $ledgerCodes;
        $this->view->ledgerPurchase = $ledgerPurchase;
        $this->view->ledgerSales = $ledgerSales;
    }

    public function addNewStatusAction(){

        $this->view->pick("modals/addNewStatus");
        $statusId = $this->request->get('id');

        if (!is_null($statusId)) {

            $this->view->statusId = $statusId;
            $this->view->statusName = RecipeStatus::findFirst($statusId)->getName();
        }

        if ($this->request->isAjax() && $this->request->isPost()) {

            $post = $this->request->getPost();
            $id = $post['statusId'];
            $name = $post['statusName'];

            if (empty($id)) {

                $maxSort = RecipeStatus::maximum([
                    'column' => 'sort',
                ]);

                if ($maxSort == null) {
                    $maxSort = 1;
                }
                else {
                    $maxSort++;
                }

                $newRecipe = new RecipeStatus();
                $newRecipe->setName($name);
                $newRecipe->setSort($maxSort);
                $result = $newRecipe->save();

                return json_encode($newRecipe);
            }
            else {
                $recipe = RecipeStatus::findFirst($id);
                $recipe->setName($name);
                $result = $recipe->save();
                return json_encode($recipe);
            }
        }
    }

    public function deleteStatusAction(){

        $this->view->disable();

        if ($this->request->isAjax() && $this->request->isPost()) {

            $id = $this->request->getPost('id');
            $recipe = RecipeStatus::findFirstById($id);
            $result = $recipe->delete();

            if ($result) {
                return json_encode($recipe);
            }
            else {
                return json_encode('error');
            }
        }
    }

    public function deleteimageeditAction($code){

        $recipe = Recipes::findFirst("code = '".$code."'");
        $file = $recipe->getImage();
        $recipe->setImage(null);
        $recipe->save();

        $imgDir = $this->config->application->recipeImagesDir;
        $deleted = unlink($imgDir . $file);

        return $this->response->redirect($this->request->getHTTPReferer());
    }

    public function confirmremovalAction(){

        if ($this->request->isPost()){

            $product = Products::findFirst($this->request->getPost('id'));
            $product->setIsRemoved(1);
            $product->save();
        }
    }

    public function cancelremovalAction(){

        if ($this->request->isPost()){

            $product = Products::findFirst($this->request->getPost('id'));
            $product->setIsRemoved(0);
            $product->save();
        }
    }

    public function duplicateAction(){

        $this->view->disable();

        if ($this->request->isAjax()){

            $recipeCode = $this->request->getPost('code');
            $recipeNewName = $this->request->getPost('newName');
            $oldRecipe = Recipes::findFirst('code ='.$recipeCode);

            $newRecipe = new Recipes();
            $newRecipe->setName($recipeNewName);
            $newRecipe->setCode();
            $newRecipe->setDescription($oldRecipe->description);
            $newRecipe->setImage($oldRecipe->image);
            $newRecipe->setPrice($oldRecipe->price);
            $newRecipe->setCustomCode($oldRecipe->custom_code);
            $newRecipe->setCustomName($oldRecipe->custom_name);
            $newRecipe->setCustomRecipe($oldRecipe->custom_recipe);
            $newRecipe->setDeliveryTime($oldRecipe->delivery_time);
            $newRecipe->setActive($oldRecipe->active);
            $newRecipe->setOrganisationId($oldRecipe->organisation_id);
            $newRecipe->setLabId($oldRecipe->lab_id);
            $newRecipe->setProductId($oldRecipe->product_id);
            $newRecipe->setStatuses($oldRecipe->statuses);
            $newRecipe->setHasSchema($oldRecipe->has_schema);
            $newRecipe->save();

            foreach ($oldRecipe->RecipeActivity as $rpAct){

                $newAct = new RecipeActivity();
                $newAct->setRecipeId($newRecipe->getId());
                $newAct->setTariffId($rpAct->tariff_id);
                $newAct->setDescription($rpAct->description);
                $newAct->setAmount($rpAct->amount);
                $newAct->save();
            }

            foreach ($oldRecipe->RecipeProduct as $rpPro){

                $newPro = new RecipeProduct();
                $newPro->setRecipeId($newRecipe->getId());
                $newPro->setProductId($rpPro->product_id);
                $newPro->setDescription($rpPro->description);
                $newPro->setAmount($rpPro->amount);
                $newPro->save();
            }

            foreach ($oldRecipe->RecipeCustomField as $rpCustom){

                $oldOptions = RecipeCustomFieldOptions::find('recipe_customfield_id ='.$rpCustom->id);

                $newCustom = new RecipeCustomField();
                $newCustom->setRecipeId($newRecipe->getId());
                $newCustom->setName($rpCustom->name);
                $newCustom->setType($rpCustom->type);
                $newCustom->setCustomPriceTariffId($rpCustom->custom_price);
                $newCustom->setCustomPriceType($rpCustom->custom_price_type);
                $newCustom->setParams($rpCustom->params);
                $newCustom->setCustomFieldType($rpCustom->custom_field_type);
                $newCustom->setHasLabCheck($rpCustom->has_lab_check);
                $newCustom->setAmount($rpCustom->amount);
                $newCustom->save();

                foreach($oldOptions as $o){

                    $newOption = new RecipeCustomFieldOptions();
                    $newOption->setRecipeCustomfieldId($newCustom->getId());
                    $newOption->setOption($o->option);
                    $newOption->setValue($o->value);
                    $newOption->setTariffId($o->tariff_id);
                    $newOption->setCustomPriceTariffId($o->custom_price);
                    $newOption->setAmount($o->amount);
                    $newOption->save();
                }
            }

            if($newRecipe->save()){

                $result = json_encode(array(
                    'status' => 'ok',
                    'msg'   => Trans::make('Recipe copied successfully'),
                ));
            }
            else {
                $result = json_encode(array(
                    'status' => 'error',
                    'msg'   => Trans::make('Error while copying the recipe'),
                ));
            }
            return $result;
        }
    }

    public function shopviewAction(){

        if (!$this->session->has('products-filters')) {
            $this->session->set('products-filters', [
                'query' => '',
                'filter' => [],
                'page' => 1,
                'limit' => 6,
            ]);
        }

        $productsList = new ProductsList();
        /** @var ProductsFilters $filters */
        $filters = $productsList->getFilters();

        $this->assets->collection('footerNotCompile')
            ->addJs("js/app/products.js")
            ->addJs("js/app/productsList.js?v=2")
            ->addJs("bower_components/bootstrap3-typeahead/bootstrap3-typeahead.min.js");

        $this->view->filters = $filters;
    }

    private function serializeStatuses($statusString){

        if(!empty($statusString)){

            $statuses = explode(',', $statusString);
            $tmp = [];

            foreach ($statuses as $k => $status){

                $dbStatus = RecipeStatus::findFirst($status);
                $tmp[] = [
                    'id' => $dbStatus->getId(),
                    'name' => $dbStatus->getName(),
                    'sort' => $dbStatus->getSort(),
                ];
            }

            $serializedStatuses = serialize($tmp);
            return $serializedStatuses;
        }
        else {
            return null;
        }
    }

    private function getManageGroupsContent($action){

        $html = '<form id="manageUsersForm" action="/signadens/product/' . $action . '" method="post">';

        $html .= '<input type="hidden" id="input_product_id" name="product_id">';
        $html .= '<label>' . Trans::make("Dentists groups") . '</label>';
        $html .= '<select class="select2-groups" name="groups[]"></select>';
        $html .= '<label for="checkbox_percent_type">' . Trans::make("Percentage") . '</label>';
        $html .= '<input type="radio" name="discount_type" id="checkbox_percent_type" value="1" checked></br>';
        $html .= '<label for="checkbox_price_type">' . Trans::make("Price") . '</label>';
        $html .= '<input type="radio" name="discount_type" id="checkbox_price_type" value="2">';
        $html .= '<input type="text" name="value" class="form-control">';
        $html .= '</form>';

        return $html;
    }
}
