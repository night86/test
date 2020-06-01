<?php

namespace Signa\Controllers\Signadens;

use Phalcon\Exception;
use Signa\Helpers\Date;
use Signa\Helpers\Translations;
use Signa\Libs\Convert;
use Signa\Models\CategoryTree;
use Signa\Models\CategoryTreeRecipes;
use Signa\Helpers\Import;
use Signa\Models\Departments;
use Signa\Models\Discounts;
use Signa\Models\FilesStorage;
use Signa\Models\FrameworkAgreements;
use Signa\Models\Notifications;
use Signa\Models\Organisations;
use Signa\Models\ProductCategories;
use Signa\Models\Products;
use Signa\Models\Recipes;
use Signa\Models\SupplierInfo;
use Signa\Models\UserRoles;
use Signa\Models\Users;
use Signa\Models\CodeLedger;
use Signa\Models\Manufacturers;
use Signa\Models\CodeTariffRanges;
use Signa\Helpers\Translations as Trans;

class ManageController extends InitController
{
    public $counter = 0;

    public function indexAction(){

        $agreements = FrameworkAgreements::find();
        $this->view->agreements = $agreements;
    }

    /**
     * Return info for manage framework agreement supplier
     * @return string
     */
    public function ajaxsupplierinfoAction() {

        $this->view->disable();

        if ($this->request->isPost()) {

            $id = $this->request->get('id');
            $data = [];

            if($id !== null) {

                /** @var SupplierInfo $supplierInfo */
                $supplierInfo = SupplierInfo::findByOrganisation($id);
                /** @var Organisations $supplier */
                $supplier = Organisations::findFirst($id);

                $data = [
                    'address'  => $supplier->getAddress(),
                    'zip'      => $supplier->getZipcode(),
                    'city'     => $supplier->getCity(),
                    'phone'    => $supplier->getTelephone(),
                    'shipping' => $supplierInfo->getText(),
                    'delivery' => $supplierInfo->getDeliveryWorkdays()
                ];
            }
            return json_encode($data);
        }
    }

    public function addAction($id = null) {

        if ($this->request->isPost()) {

            $frameworkAgreement = new FrameworkAgreements();

            $post = $this->request->getPost();
            $agreement = &$post['agreement'];

            $startDate = \DateTime::createFromFormat('d-m-Y', $agreement['start_date']) ?: \DateTime::createFromFormat('Y-m-d', $agreement['start_date']);
            $dueDate = \DateTime::createFromFormat('d-m-Y', $agreement['due_date']) ?: \DateTime::createFromFormat('Y-m-d', $agreement['due_date']);
            $agreement['start_date'] = false !== $startDate ? $startDate->format('Y-m-d') : $agreement['start_date'];
            $agreement['due_date'] = false !== $dueDate ? $dueDate->format('Y-m-d') : $agreement['due_date'];

            $this->db->begin();

            $frameworkAgreement->setDiscountsData($this->request->getPost());
            $frameworkAgreement->setCreatedBy($this->currentUser->getId());

            if ($success = $frameworkAgreement->save($agreement) !== false) {

                $supplierName = Organisations::findFirst($agreement['supplier_id'])
                    ->getName();

                if (isset($agreement['reminder']) && !empty($agreement['reminder'])) {

                    $notification = new Notifications();
                    $notification->setSendAt($dueDate->modify("-$agreement[reminder]  month")
                        ->format('Y-m-d'));
                    $notification->setType(9);
                    $notification->setSubject('Reminder due date framework agreement');
                    $notification->setDescription('The framework agreement for ' . $supplierName . ' is due on ' . $agreement['due_date']);
                    $notification->setUserId($this->currentUser->getId());
                    $notification->setCreatedBy($this->currentUser->getId());
                    $notification->setCreatedAt(date('Y-m-d H:i:s'));
                    $notification->setFrameworkAgreementId($frameworkAgreement->getId());
                }

                $reminders = $this->request->getPost('reminder');

                try {
                    $this->saveFiles($frameworkAgreement);
                    isset($notification) ? $notification->save() : null;
                    $this->saveReminders($reminders, $frameworkAgreement);
                }
                catch (\Phalcon\Db\Exception $e) {
                    $this->db->rollback();
                    $message = [
                        'type'    => 'error',
                        'content' => 'Framework agreement can\'t be added.'
                    ];
                    $this->session->set('message', $message);
                    return $this->response->redirect($this->request->getHTTPReferer());
                }

                $this->db->commit();

                $message = [
                    'type'    => 'success',
                    'content' => 'Framework agreement has been added.'
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/signadens/manage/index');
                $this->view->disable();
                return;
            }
            else {
                $message = [
                    'type'    => 'error',
                    'content' => 'Framework agreement can\'t be added.'
                ];
                $this->session->set('message', $message);
            }
        }
        $this->setViewData();
        $this->assets->collection('footer')
            ->addJs("js/app/addrecipe.js");
    }

    /**
     * @param \Phalcon\Mvc\Model $entity
     * @throws \Phalcon\Exception
     */
    protected function saveFiles($entity) {

        if(!isset($entity)) {
            throw new Exception('Wrong entity');
        }

        if ($this->request->hasFiles() == true) {

            $filesDir = $this->config->application->filesStorageDir;

            // Print the real file names and their sizes
            foreach ($this->request->getUploadedFiles() as $attachment) {

                if($attachment->getError()) {
                    continue;
                }
                $type = explode('.', $attachment->getKey());
                $entityName = "framework_agreement_$type[1]_$type[0]";

                if (!is_dir($filesDir)) {
                    mkdirR($filesDir);
                }
                $attachment->moveTo($filesDir . $attachment->getName());
                $md5 = md5_file($filesDir . $attachment->getName());

                if($file = FilesStorage::findFirst(
                    [
                        'md5 = :md5: AND deleted_at is NULL',
                        'bind' => [
                            'md5' => $md5
                        ]
                    ]
                )) {
                    unlink($this->config->application->uploadDir . 'storage/' . $file->getName());
                    $file->delete();
                }
                $file = new FilesStorage();
                $file->setEntityId($entity->getId());
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
    }

    /**
     * Set up base data for views
     */
    protected function setViewData() {

        $users = Users::query()
            ->innerJoin(UserRoles::class, UserRoles::class . '.user_id = ' . Users::class . '.id AND ' . UserRoles::class . '.role_id = 45') // ROLE_SIGNADENS_USER_BACKTOADMIN
            ->orderBy(Users::class . '.firstname')
            ->execute();

        $suppliers = Organisations::find([
            'organisation_type_id = 1',
            'order' => 'name'
        ]);

        $categories = ProductCategories::find(['deleted_by IS NULL', 'order' => 'name']);

        $recipes = Products::find([
            'product_group IS NOT NULL',
            'columns' => 'product_group',
            'group' => 'product_group',
            'order' => 'product_group',
        ]);

        $this->view->owners = Convert::toIdArray($users);
        $this->view->suppliers = Convert::toIdArray($suppliers);
        $this->view->categories = $categories;
        $this->view->recipes = $recipes;
    }

    public function editAction($id) {

        if( ! $agreement = FrameworkAgreements::findEditable($id)) {
            $message = [
                'type'    => 'error',
                'content' => 'You can`t edit this Framework agreement.'
            ];
            $this->session->set('message', $message);
            return $this->response->redirect('/signadens/manage/index');
        }

        if ($this->request->isPost()) {

            $discounts = $agreement->getRelated("Discounts");
            $agreementPost = $this->request->getPost('agreement');

            if ($agreement->getDiscountType() !== $agreementPost['discount_type']) {

                /** @var Discounts $discount */
                foreach ($discounts as $discount) {
                    $discount->delete();
                }
                $agreement->setDiscountsData($this->request->getPost());
            }
            else {
                $agreement->setDiscountsData($this->request->getPost(), $discounts);
            }

            $startDate = \DateTime::createFromFormat('d-m-Y', $agreementPost['start_date']) ?: \DateTime::createFromFormat('Y-m-d', $agreementPost['start_date']);
            $dueDate = \DateTime::createFromFormat('d-m-Y', $agreementPost['due_date']) ?: \DateTime::createFromFormat('Y-m-d', $agreementPost['due_date']);
            $agreementPost['start_date'] = false !== $startDate ? $startDate->format('Y-m-d') : $agreementPost['start_date'];
            $agreementPost['due_date'] = false !== $dueDate ? $dueDate->format('Y-m-d') : $agreementPost['due_date'];
            $agreement->setUpdatedBy($this->currentUser->getId());
            $oldDueDateReminder = $agreement->getReminder();
            $oldDueDate = $agreement->getDueDate();

            if ($success = $agreement->save($agreementPost) !== false) {

                $this->saveFiles($agreement);

                $reminders = $this->request->getPost('reminder');
                $keepIds = $this->saveReminders($reminders, $agreement);

                $notifications = Notifications::find([
                    'framework_agreement_id = :id:',
                    'bind' => [
                        'id' => $agreement->getId(),
                    ]
                ]);

                foreach ($notifications as $notification) {

                    if (!in_array($notification->id, $keepIds)) {

                        $notification->delete();
                    }
                }

                // Due date notification
                if($oldDueDateReminder !== $agreementPost['reminder'] || (strtotime($oldDueDate) !== strtotime($agreement->getDueDate()))) {
                    $notification = Notifications::findFirst([
                        'type = :type: AND framework_agreement_id = :fa:',
                        'bind' => [
                            'type' => '9',
                            'fa' => $id,
                        ],
                    ]);
                    $notification->setSendAt($dueDate->modify("-$agreementPost[reminder]  month")
                        ->format('Y-m-d'));
                    $notification->update();
                }

                $message = [
                    'type'    => 'success',
                    'content' => 'Framework agreement has been edited.'
                ];
                $this->session->set('message', $message);
                return $this->response->redirect('/signadens/manage/index');
            }
            else {
                $message = [
                    'type'    => 'error',
                    'content' => 'Framework agreement can\'t be edited.'
                ];
                $this->session->set('message', $message);
            }
        }

        $this->view->agreement = $agreement;
        $this->view->retailPriceFiles = $agreement->getFiles(FrameworkAgreements::ENTITY_RETAIL_PRICE);
        $this->view->agreementFiles = $agreement->getFiles(FrameworkAgreements::ENTITY_AGREEMENT);
        $this->view->slaApplicabilityFiles = $agreement->getFiles(FrameworkAgreements::ENTITY_SLA_APPLICABILITY);
        $this->view->supportApplicabilityFiles = $agreement->getFiles(FrameworkAgreements::ENTITY_SUPPORT_APPLICABILITY);
        $this->view->attachmentsFiles = $agreement->getFiles(FrameworkAgreements::ENTITY_ATTACHMENTS);
        $this->view->discounts = $agreement->getDiscounts();
        $this->view->reminders = Notifications::find('framework_agreement_id = '.$agreement->getId());

        $this->setViewData();

        $this->assets->collection('footer')
            ->addJs("js/app/addrecipe.js");
    }

    public function duplicateAction($id) {

        /** @var FrameworkAgreements $agreement */
        $agreement = FrameworkAgreements::findFirst($id);
        $this->db->begin();

        try {
            // Discounts
            $discounts = $agreement->getRelated("Discounts");
            $discountsArr = [];
            /** @var Discounts $discount */
            foreach ($discounts as $key => $discount) {
                $discount->setId(null);
                $discount->setFrameworkAgreementId(null);
                $discountsArr[] = $discount;
            }
            $agreement->Discounts = $discountsArr;

            // Files
            $files = $agreement->getRelated('Files');
            $filesArr = [];

            /** @var FilesStorage $file */
            foreach ($files as $file) {
                $file->setId(null);
                $file->setEntityId(null);
                $filesArr[] = $file;
            }
            $agreement->Files = $filesArr;

            // Notifications
            $notifications = $agreement->getRelated('Notifications');
            $notificationsArr = [];

            /** @var Notifications $notification */
            foreach ($notifications as $notification) {

                $notification->setId(null);
                $notification->setUserId($this->currentUser->getId());
                $notification->setCreatedBy($this->currentUser->getId());
                $notification->setCreatedAt(date('Y-m-d H:i:s'));
                $notification->setFrameworkAgreementId(null);
                $notificationsArr[] = $notification;
            }
            $agreement->Notifications = $notificationsArr;

            $agreement->setId(null);
            $agreement->save();

            $this->db->commit();
            $message = [
                'type'    => 'success',
                'content' => 'Framework agreement has been duplicated.'
            ];
            $this->session->set('message', $message);

            return $this->response->redirect('/signadens/manage/edit/' . $agreement->getId());
        }
        catch(\Phalcon\Db\Exception $e) {

            $this->db->rollback();
            $message = [
                'type'    => 'error',
                'content' => 'Framework agreement can\'t be duplicated.'
            ];
            $this->session->set('message', $message);

            return $this->response->redirect('/signadens/manage/index');
        }
    }

    public function viewAction($id) {

        $agreement = FrameworkAgreements::findFirst($id);

        $this->view->agreement = $agreement;
        $this->view->retailPriceFiles = $agreement->getFiles(FrameworkAgreements::ENTITY_RETAIL_PRICE);
        $this->view->agreementFiles = $agreement->getFiles(FrameworkAgreements::ENTITY_AGREEMENT);
        $this->view->slaApplicabilityFiles = $agreement->getFiles(FrameworkAgreements::ENTITY_SLA_APPLICABILITY);
        $this->view->supportApplicabilityFiles = $agreement->getFiles(FrameworkAgreements::ENTITY_SUPPORT_APPLICABILITY);
        $this->view->attachmentsFiles = $agreement->getFiles(FrameworkAgreements::ENTITY_ATTACHMENTS);

        $this->view->discounts = $agreement->getDiscounts();
        $this->view->reminders = Notifications::find([
            'framework_agreement_id = :id:',
            'bind' => [
                'id' => $agreement->getId(),
            ]
        ]);

        $this->setViewData();
    }

    public function downloadAction($id){

        $this->view->disable();

        $file = FilesStorage::findFirst($id);
        $this->response->setHeader("Content-Type",$file->getMime());
        $this->response->setHeader("Content-Length", $file->getSize());
        $this->response->setFileToSend($this->config->application->uploadDir . 'storage/' . $file->getName(), $file->getName())->send();
    }

    public function filedeleteAction($id) {

        $this->view->disable();

        $file = FilesStorage::findFirst($id);
        unlink($this->config->application->uploadDir . 'storage/' . $file->getName());
        $file->delete();

        return $this->response->redirect($this->request->getHTTPReferer());
    }

    public function deactivateAction($id){

        $fa = FrameworkAgreements::findFirst($id);

        if ($fa) {
            $fa->setActive(0);
            $fa->save();
            $this->session->set('message', array('type' => 'success', 'content' => 'Framework agreement has been deactivated.'));
        }
        else {
            $this->session->set('message', array('type' => 'warning', 'content' => 'Framework agreement doesn\'t exist.'));
        }
        $this->response->redirect('/signadens/manage/index');
        $this->view->disable();
        return;
    }

    public function activateAction($id){

        $fa = FrameworkAgreements::findFirst($id);

        if ($fa) {
            $fa->setActive(1);
            $fa->save();
            $this->session->set('message', array('type' => 'success', 'content' => 'Framework agreement has been activated.'));
        }
        else {
            $this->session->set('message', array('type' => 'warning', 'content' => 'Framework agreement  doesn\'t exist.'));
        }
        $this->response->redirect('/signadens/manage/index');
        $this->view->disable();
        return;
    }

    public function indexcategoryAction(){

        $categories = ProductCategories::find(['deleted = 0', "order" => "sort"]);
        $renderedTree = $this->renderProductsCategoriesTree($this->sortCategories($categories));

        $this->view->rendertree = $renderedTree;
        $this->view->categories = ProductCategories::find(array('deleted = 0'));

        $this->assets->collection('footer')
            ->addJs("bower_components/Sortable/Sortable.js");
    }

    public function addCategoryAction(){

        if ($this->request->isPost()) {

            $category = new ProductCategories();

            $name = $this->request->getPost('name');
            $id = $this->request->getPost('id');

            if ($name !== '') {

                if ($category->saveData(array(
                        'name' => $name,
                        'parent_id' => $id
                    )) !== false
                ) {
                    $msg = array(
                        'status' => 'success',
                        'message' => 'Category has been added.'
                    );
                }
                else {
                    $msg = array(
                        'status' => 'error',
                        'message' => 'Category can\'t be added.'
                    );
                }
            }
            else {
                $msg = array(
                    'status' => 'error',
                    'message' => 'Name cannot be empty'
                );
            }

            $categories = ProductCategories::find(['deleted = 0', "order" => "sort"]);
            $renderedTree = $this->renderProductsCategoriesTree($this->sortCategories($categories));
            $msg['html'] = $renderedTree;

            return json_encode($msg);
        }
    }

    public function editcategoryAction($id){

        $category = ProductCategories::findFirst($id);

        if ($this->request->isPost()) {

            $category->setName($this->request->getPost('name'));

            if ($category->save() !== false) {
                $message = [
                    'type' => 'success',
                    'content' => 'Category has been edited.'
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/signadens/manage/indexcategory');
                $this->view->disable();
                return;
            }
            else {
                $message = [
                    'type' => 'error',
                    'content' => 'Category can\'t be edited.'
                ];
                $this->session->set('message', $message);
            }
        }
        $this->view->category = $category;
    }

    public function deletecategoryAction(){

        $this->view->disable();
        $id = $this->request->getPost('id');

        if (!$id) {
            return false;
        }
        $category = ProductCategories::findFirstById($id);

        if (count($category->Products) == 0) {
            $category->softDelete();
            $message = [
                'type' => 'success',
                'content' => 'Category has been deleted.'
            ];
        }
        else {
            $message = [
                'type' => 'error',
                'content' => 'Category can\'t be deleted because has active products.'
            ];
        }
        return json_encode($message); die;
    }

    public function treeCategoryAction(){

        $this->assets->collection('footer')
            ->addJs("bower_components/Sortable/Sortable.js");

        $this->view->products = Recipes::find('deleted_at IS NULL AND active = 1 AND organisation_id = '.$this->currentUser->getOrganisationId());
        $this->view->rendertree = $this->renderTree($this->sortCategories(CategoryTree::find(["order" => "sort"])));
    }

    public function addTreeCategoryAction(){

        if ($this->request->isPost()) {

            $category = new CategoryTree();

            $name = $this->request->getPost('name');
            $id = $this->request->getPost('id');

            if ($name !== '') {

                if ($category->saveData(array(
                        'name' => $name,
                        'id' => $id
                    )) !== false
                ) {
                    $msg = array(
                        'status' => 'success',
                        'message' => 'Save success category name: ' . $name
                    );
                }
                else {
                    $msg = array(
                        'status' => 'error',
                        'message' => 'Save error'
                    );
                }
            }
            else {
                $msg = array(
                    'status' => 'error',
                    'message' => 'Name cannot be empty'
                );
            }
            $renderedTree = $this->renderTree($this->sortCategories(CategoryTree::find(["order" => "sort"])));
            $msg['html'] = $renderedTree;

            return json_encode($msg);
        }
    }

    public function addTreeCategoryProductAction(){

        if ($this->request->isPost()) {

            $categoryProduct = new CategoryTreeRecipes();

            $product = $this->request->getPost('product');
            $id = $this->request->getPost('id');

            if ($product !== '') {

                if ($categoryProduct->saveData(array(
                        'product' => $product,
                        'id' => $id
                    )) !== false
                ) {
                    $msg = array(
                        'status' => 'success',
                        'message' => 'Save success, product added'
                    );
                }
                else {
                    $msg = array(
                        'status' => 'error',
                        'message' => 'Save error'
                    );
                }

            }
            else {
                $msg = array(
                    'status' => 'error',
                    'message' => 'Something went wrong'
                );
            }
            $renderedTree = $this->renderTree($this->sortCategories(CategoryTree::find(["order" => "sort"])));
            $msg['html'] = $renderedTree;

            return json_encode($msg);
        }
    }

    public function editTreeCategoryAction($id){

        $category = CategoryTree::findFirst($id);

        if ($this->request->isPost()) {

            $category->setName($this->request->getPost('name'));

            if ($this->request->hasFiles() == true) {

                $configDir = $this->config->application->categoryImagesDir;
                $randomString = Import::generateRandomString(6);
                $file = $this->request->getUploadedFiles()[0];
                $fileName = $randomString . $file->getName();
                $imgDir = $configDir;

                if ($file->getRealType() == 'image/jpeg' || $file->getRealType() == 'image/png') {

                    if (!is_dir($imgDir)) {
                        mkdirR($imgDir);
                    }
                    $file->moveTo($configDir . $fileName);
                    $category->setImage($fileName);
                }
            }

            if ($category->save() !== false) {

                $message = [
                    'type' => 'success',
                    'content' => 'Category has been edited.'
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/signadens/manage/treecategory');
                $this->view->disable();
                return;
            }
            else {
                $message = [
                    'type' => 'error',
                    'content' => 'Category can\'t be edited.'
                ];
                $this->session->set('message', $message);
            }
        }
        if ($category->getImage()){
            $this->view->image = '/uploads/images/category_tree/' . $category->getImage();
        }
        $this->view->category = $category;
    }

    public function deleteTreeCategoryAction(){

        if ($this->request->isPost()) {

            $id = $this->request->getPost('id');

            if ($id !== '') {

                if ($this->request->hasPost('productId') && $this->request->getPost('productId')) {

                    $ctr = CategoryTreeRecipes::findFirst('category_tree_id = '.$id.' AND recipe_id = '.$this->request->getPost('productId'));
                    $ctr->delete();

                    $msg = array(
                        'status' => 'success',
                        'message' => 'Product deleted.'
                    );
                }
                else {
                    $toDelete = CategoryTree::findFirst($id);
                    $name = $toDelete->getName();

                    $childs = CategoryTree::find(
                        [
                            "parent_id = '$id'",
                            "order" => "sort"
                        ]
                    );

                    $this->checkChilds($childs);
                    $toDelete->delete();

                    $msg = array(
                        'status' => 'success',
                        'message' => 'Delete success category name: ' . $name . ' and related.'
                    );
                }
            }
            $renderedTree = $this->renderTree($this->sortCategories(CategoryTree::find(["order" => "sort"])));
            $msg['html'] = $renderedTree;

            return json_encode($msg);
        }
    }

    public function sorttreecategoryAction(){

        $this->view->disable();
        $type = 'recipeCategory';

        if ($this->request->has('categoryType')) {

            $type = $this->request->get('categoryType');
        }

        if ($this->request->isAjax()) {

            $start = $this->request->get('start');
            $end = $this->request->get('end');

            foreach ($end as $key => $id) {

                $index = $key + 1;

                if ($type == 'productCategory') {

                    $category = ProductCategories::findFirstById($id);
                }
                else {
                    $category = CategoryTree::findFirstById($id);
                }

                if ($category) {
                    $category->setSort($index);
                    $category->save();
                }
            }
            echo 'done';
            die;
        }
    }

    private function checkChilds($array){

        $ids = array();

        foreach ($array as $child) {

            $ids[] = $child->getId();
            $child->delete();
        }

        if (!empty($ids)) {

            $this->checkChilds(CategoryTree::find(
                [
                    'id = :ids:',
                    'bind' => [
                        'ids' => $ids
                    ],
                    "order" => "sort"
                ]
            ));
        }
    }

    private function sortCategories($categories){

        $new = array();

        foreach ($categories as $a) {

            if ($a->getParentId() == null) {
                $parent_id = 0;
            }
            else {
                $parent_id = $a->getParentId();
            }

            $new[$parent_id][] = array(
                'id' => $a->getId(),
                'sort' => $a->getSort(),
                'parent_id' => $a->getParentId(),
                'name' => $a->getName(),
                'products' => get_class($a) == 'Signa\Models\CategoryTree' ? $a->Recipes : null
            );
        }

        if (!empty($new)) {
            return $this->createTree($new, $new[0]);
        }
        else {
            return false;
        }
    }

    private function createTree(&$list, $parent){

        $tree = array();

        foreach ($parent as $k => $l) {

            if (isset($list[$l['id']])) {
                $l['children'] = $this->createTree($list, $list[$l['id']]);
            }
            $tree[] = $l;
        }
        return $tree;
    }

    private function renderTree($tree){

        $this->counter = $this->counter + 1;

        if (empty($tree)) {
            return '';
        }

        $output = '<ul class="sort-list list-' . $this->counter . '">';

        foreach ($tree as $key => $category) {

            $output .= '<li class="drag-y category_' . $category['id'] . '" data-id="' . $category['id'] . '" data-sort="' . $category['sort'] . '"><div class="tree-row"><i class="pe-7s-folder"></i> ' . $category['name'];

            if (!isset($category['children'])) {
                $output .= '   <a href="javascript:;" class="add-product label label-default" data-id="' . $category['id'] . '"><span class="small"><i class="pe-7s-plus"></i> ' . $this->t->make('Add product') . '</span></a>';
            }

            $output .= '<div class="tree-actions">' .
                '<a href="#" class="btn btn-primary btn-sm add-step" data-parendid="' . $category['id'] . '"><i class="pe-7s-plus"></i> ' . $this->t->make('Add') . '</a>' .
                '<a href="/signadens/manage/edittreecategory/' . $category['id'] . '" class="btn btn-success btn-sm"><i class="pe-7s-pen"></i> ' . $this->t->make('Edit') . '</a>' .
                '<a href="#" class="btn btn-danger btn-sm delete-step" data-parendid="' . $category['id'] . '"><i class="pe-7s-trash"></i> ' . $this->t->make('Delete') . '</a>' .
                '</div>';
            $output .= '</div>';

            if (isset($category['children'])) {
                $output .= $this->renderTree($category['children']);
            }
            if (count($category['products']) > 0) {
                $output .= $this->renderProducts($category);
            }
            $output .= '</li>';
        }
        $output .= '</ul>';

        return $output;
    }

    private function renderProductsCategoriesTree($tree){

        $this->counter = $this->counter + 1;

        if (empty($tree)) return '';

        $output = '<ul class="sort-list list-' . $this->counter . '">';

        foreach ($tree as $key => $category) {

            $output .= '<li class="drag-y category_' . $category['id'] . '" data-id="' . $category['id'] . '" data-sort="' . $category['sort'] . '"><div class="tree-row"><i class="pe-7s-folder"></i> ' . $category['name'];
            $output .= '<div class="tree-actions">' .
                '<a href="javascript:;" class="btn btn-primary btn-sm add-step" data-parendid="' . $category['id'] . '"><i class="pe-7s-plus"></i> ' . $this->t->make('Add') . '</a>' .
                '<a href="/signadens/manage/editcategory/' . $category['id'] . '" class="btn btn-success btn-sm"><i class="pe-7s-pen"></i> ' . $this->t->make('Edit') . '</a>' .
                '<a href="javascript:;" class="btn btn-danger btn-sm delete-step" data-parendid="' . $category['id'] . '"><i class="pe-7s-trash"></i> ' . $this->t->make('Delete') . '</a>' .
                '</div>';
            $output .= '</div>';

            if (isset($category['children'])) {

                $output .= $this->renderProductsCategoriesTree($category['children']);
            }

            if (isset($category['products']) && count((array)$category['products']) > 0) {

                $output .= $this->renderProducts($category);
            }
            $output .= '</li>';
        }
        $output .= '</ul>';

        return $output;
    }

    private function renderProducts($category){

        $output = '<ul>';

        foreach ($category['products'] as $product) {

            $output .= '<li><div class="tree-row"><span class="small"><i class="pe-7s-file"></i><a target="_blank" href="/signadens/product/edit/' . $product->getCode() . '"> ' .$product->getRecipeNumber()." - ".$product->getName() . '</a></span>';

            $output .= '<div class="tree-actions">' .
                '<a href="#" class="btn btn-danger btn-sm delete-product" data-productid="' . $product->getId() . '" data-parendid="' . $category['id'] . '"><i class="pe-7s-trash"></i> ' . $this->t->make('Delete') . '</a>' .
                '</div>';
            $output .= '</div>';
        }
        $output .= '</ul>';

        return $output;
    }

    public function indexDepartmentAction(){

        $this->view->departments = Departments::find(array('deleted_at is NULL'));
    }

    public function addDepartmentAction(){

        if ($this->request->isPost()) {

            $department = new Departments();
            $department->setName($this->request->getPost('name'));
            $department->getMessages();
            $department->setCreatedAt(Date::currentDatetime());
            $department->setCreatedBy($this->currentUser->getId());

            if ($department->save() !== false) {

                $message = [
                    'type' => 'success',
                    'content' => 'Department has been added.'
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/signadens/manage/indexdepartment');
                $this->view->disable();
                return;
            }
            else {
                $message = [
                    'type' => 'error',
                    'content' => 'Department can\'t be added.'
                ];
                $this->session->set('message', $message);
            }
        }
    }

    public function editDepartmentAction($id){

        $department = Departments::findFirst($id);

        if ($this->request->isPost()) {

            $department->setName($this->request->getPost('name'));

            if ($department->save() !== false) {
                $message = [
                    'type' => 'success',
                    'content' => 'Department has been edited.'
                ];
                $this->session->set('message', $message);
                $this->response->redirect('/signadens/manage/indexdepartment');
                $this->view->disable();
                return;
            }
            else {
                $message = [
                    'type' => 'error',
                    'content' => 'Department can\'t be edited.'
                ];
                $this->session->set('message', $message);
            }
        }
        $this->view->department = $department;
    }

    public function deleteDepartmentAction($id){

        $department = Departments::findFirst($id);
        $this->view->disable();

        if ($department) {
            $department->softDelete();
            $message = [
                'type' => 'success',
                'content' => 'Category has been deleted.'
            ];
            $this->session->set('message', $message);
        }
        else {
            $message = [
                'type' => 'error',
                'content' => 'Category can\'t be deleted because has active products.'
            ];
            $this->session->set('message', $message);
        }

        $this->response->redirect('/signadens/manage/indexdepartment');
        return;
    }

    public function manufacturersAction(){

        $this->view->manufacturers = Manufacturers::find('deleted_at IS NULL AND deleted_by IS NULL');
    }

    public function mapledgerstocategoriesAction(){

        // View vars and assets
        $this->view->productCategories = ProductCategories::find(array('deleted = 0'));
        $this->view->ledgerCodes = CodeLedger::find('organisation_id = '.$this->currentUser->getOrganisationId().' AND active = 1');
    }

    public function saveledgertocategoriesAction(){

        $this->view->disable();

        if($this->request->isPost()){

            // If ledger is not null, then edit, else then add new ledger
            if($this->request->getPost('old_ledger') != NULL){

                // Find category to edit ledger code
                $editLedger = ProductCategories::findFirst('id = '.$this->request->getPost('category'));

                // Check type
                if($this->request->getPost('type') == 'purchase'){

                    $editLedger->setLedgerPurchaseId($this->request->getPost('new_ledger'));
                }
                else {
                    $editLedger->setLedgerSalesId($this->request->getPost('new_ledger'));
                }
                $editLedger->save();

                if($editLedger->save()){

                    $result = [
                        "status" => "ok",
                        "msg"    => Translations::make("Ledger code updated successfully")
                    ];
                }
                else {
                    $result = [
                        "status" => "error",
                        "msg"    => Translations::make("Error while updating ledger code")
                    ];
                }
            }
            else {
                // Find category to add ledger code
                $addLedger = ProductCategories::findFirst('id = '.$this->request->getPost('category'));

                // Check type
                if($this->request->getPost('type') == 'purchase'){

                    $addLedger->setLedgerPurchaseId($this->request->getPost('new_ledger'));
                }
                else {
                    $addLedger->setLedgerSalesId($this->request->getPost('new_ledger'));
                }
                $addLedger->save();

                if($addLedger->save()){

                    $result = [
                        "status" => "ok",
                        "msg"    => Translations::make("Ledger code added successfully")
                    ];
                }
                else {
                    $result = [
                        "status" => "error",
                        "msg"    => Translations::make("Error while adding ledger code")
                    ];
                }
            }
        }
        return json_encode($result);
    }

    public function ajaxmanufacturersAction(){

        $this->view->disable();

        if ($this->request->isPost()) {

            if($this->request->getPost('id') !== null && $this->request->getPost('editName') !== null) {

                $checkName = Manufacturers::find("id != ".$this->request->getPost('id')." AND name LIKE '%".$this->request->getPost('editName')."%'");

                if(count($checkName) == 0){

                    $manufacturer = Manufacturers::findFirst($this->request->getPost('id'));
                    $manufacturer->setName($this->request->getPost('editName'));
                    $manufacturer->save();

                    $result = [
                        "status"    => "ok",
                        "msg"       => Trans::make("Manufacturer updated successfully")
                    ];
                }
                else {
                    $result = [
                        "status"    => "error",
                        "msg"       => Trans::make("This manufacturer already exists")
                    ];
                }
            }
            else {
                $checkName = Manufacturers::find("name LIKE '%".$this->request->getPost('newName')."%'");

                if(count($checkName) == 0){

                    $manufacturer = new Manufacturers();
                    $manufacturer->setName($this->request->getPost('newName'));
                    $manufacturer->save();

                    $result = [
                        "status"    => "ok",
                        "msg"       => Trans::make("Manufacturer added successfully")
                    ];
                }
                else {
                    $result = [
                        "status"    => "error",
                        "msg"       => Trans::make("This manufacturer already exists")
                    ];
                }
            }
            return json_encode($result);
        }
    }

    public function ajaxtariffcoderangesAction(){

        $this->view->disable();

        if ($this->request->isPost()) {

            $post = $this->request->getPost();

            if($post['range_from'] >= $post['range_to']){

                $result = [
                    "status" => "error",
                    "type" => "interval",
                    "msg"    => Translations::make("Start range should be lower than end of the range.")
                ];
                return json_encode($result);
            }

            if($this->request->getPost('type') == 'new'){

                // Check if combination of manufacturer and product category doesnt exist
                if(count(CodeTariffRanges::find('manufacturer_id = '.$post['manufacturer_id'].' AND product_category_id = '.$post['product_category_id'])) == 0){

                    // Check if range doesnt exist
                    if(count(CodeTariffRanges::find("(range_from >= ".$post['range_from']." AND range_from <= ".$post['range_to'].") OR (range_to >= ".$post['range_from']." AND range_to <= ".$post['range_to'].")")) == 0){

                        $newTariffCodeRange = new CodeTariffRanges();
                        $newTariffCodeRange->setManufacturerId($post['manufacturer_id']);
                        $newTariffCodeRange->setProductCategoryId($post['product_category_id']);
                        $newTariffCodeRange->setRangeFrom($post['range_from']);
                        $newTariffCodeRange->setRangeTo($post['range_to']);
                        $newTariffCodeRange->save();

                        if($newTariffCodeRange->save()){

                            $result = [
                                "status" => "ok",
                                "msg"    => Translations::make("Tariff code range added successfully.")
                            ];
                        }
                        else {
                            $result = [
                                "status" => "error",
                                "type" => "new",
                                "msg"    => Translations::make("Error while adding tariff code range.")
                            ];
                        }
                    }
                    else {
                        $result = [
                            "status" => "error",
                            "type" => "ran",
                            "msg"    => Translations::make("Part of this range is already in use and therefore can not be saved.")
                        ];
                    }
                }
                else {
                    $result = [
                        "status" => "error",
                        "type"   => "man",
                        "msg"    => Translations::make("This combination of manufacturer and product category already exists and therefore cannot be saved.")
                    ];
                }
            }
            else {

                $editTariffCodeRange = CodeTariffRanges::findFirst('id = '.$post['id']);

                // If combination is the same then check only for range
                if(($post['manufacturer_id'] == $editTariffCodeRange->getManufacturerId()) && ($post['product_category_id'] == $editTariffCodeRange->getProductCategoryId())){

                    // Check if range doesnt exist aside of the edited one
                    if (count(CodeTariffRanges::find("id NOT IN (" . $post['id'] . ") AND ((range_from >= " . $post['range_from'] . " AND range_from <= " . $post['range_to'] . ") OR (range_to >= " . $post['range_from'] . " AND range_to <= " . $post['range_to'] . "))")) == 0) {

                        $editTariffCodeRange->setRangeFrom($post['range_from']);
                        $editTariffCodeRange->setRangeTo($post['range_to']);
                        $editTariffCodeRange->save();

                        if($editTariffCodeRange->save()){

                            $result = [
                                "status" => "ok",
                                "msg"    => Translations::make("Tariff code range updated successfully.")
                            ];
                        }
                        else {
                            $result = [
                                "status" => "error",
                                "type" => "upd",
                                "msg"    => Translations::make("Error while updating tariff code range.")
                            ];
                        }
                    }
                    else {
                        $result = [
                            "status" => "error",
                            "type" => "ran",
                            "msg"    => Translations::make("Part of this range is already in use and therefore can not be saved.")
                        ];
                    }
                }
                else {
                    // Check if combination of manufacturer and product category doesnt exist
                    if(count(CodeTariffRanges::find('manufacturer_id = '.$post['manufacturer_id'].' AND product_category_id = '.$post['product_category_id'])) == 0) {

                        // Check if range doesnt exist aside of the edited one
                        if (count(CodeTariffRanges::find("id NOT IN (" . $post['id'] . ") AND ((range_from >= " . $post['range_from'] . " AND range_from <= " . $post['range_to'] . ") OR (range_to >= " . $post['range_from'] . " AND range_to <= " . $post['range_to'] . "))")) == 0) {

                            $editTariffCodeRange->setManufacturerId($post['manufacturer_id']);
                            $editTariffCodeRange->setProductCategoryId($post['product_category_id']);
                            $editTariffCodeRange->setRangeFrom($post['range_from']);
                            $editTariffCodeRange->setRangeTo($post['range_to']);
                            $editTariffCodeRange->save();

                            if($editTariffCodeRange->save()){

                                $result = [
                                    "status" => "ok",
                                    "msg"    => Translations::make("Tariff code range updated successfully.")
                                ];
                            }
                            else {
                                $result = [
                                    "status" => "error",
                                    "status" => "upd",
                                    "msg"    => Translations::make("Error while updating tariff code range.")
                                ];
                            }
                        }
                        else {
                            $result = [
                                "status" => "error",
                                "type" => "ran",
                                "msg"    => Translations::make("Part of this range is already in use and therefore can not be saved.")
                            ];
                        }
                    }
                    else {
                        $result = [
                            "status" => "error",
                            "type"   => "man",
                            "msg"    => Translations::make("This combination of manufacturer and product category already exists and therefore cannot be saved.")
                        ];
                    }
                }
            }
        }
        return json_encode($result);
    }

    public function tariffcoderangesAction(){

        $categories = [];
        $final = [];
        $productCategories = ProductCategories::find(array('deleted = 0 AND deleted_at IS NULL AND deleted_by IS NULL'));

        // Create product category temporary tree
        foreach($productCategories as $pc){

            if($pc->getParentId() == NULL){
                $categories[$pc->getId()] = $pc->toArray();
            }
            else {
                if($pc->Parent->Parent){
                    $categories[$pc->Parent->getParentId()]['sub'][$pc->getParentId()]['subsub'][$pc->getId()] = $pc->toArray();
                    $categories[$pc->Parent->getParentId()]['sub'][$pc->getParentId()]['subsub'][$pc->getId()]['sub_parent_name'] = $pc->Parent->getName();
                    $categories[$pc->Parent->getParentId()]['sub'][$pc->getParentId()]['subsub'][$pc->getId()]['cat_parent_name'] = $pc->Parent->Parent->getName();
                }
                else {
                    $categories[$pc->getParentId()]['sub'][$pc->getId()] = $pc->toArray();
                    $categories[$pc->getParentId()]['sub'][$pc->getId()]['cat_parent_name'] = $pc->Parent->getName();
                    $categories[$pc->getParentId()]['sub'][$pc->getId()]['sub_parent_name'] = NULL;
                }
            }
        }

        // Create list from lowest level available
        foreach($categories as $cat){

            if($cat['sub'] != NULL && count($cat['sub']) > 0){

                foreach($cat['sub'] as $sub){

                    if($sub['subsub'] && count($sub['subsub']) > 0){

                        foreach($sub['subsub'] as $subsub){

                            if($subsub != NULL){

                                $final[] = $subsub;
                            }
                        }
                    }
                    else {
                        $final[] = $sub;
                    }
                }
            }
            else {
                $final[] = $cat;
            }
        }

        // View vars and assets
        $this->view->codeTariffRanges = CodeTariffRanges::find();
        $this->view->manufacturers = Manufacturers::find();
        $this->view->productCategories = $final;
    }

    /**
     * @param array $reminders
     * @param FrameworkAgreements $agreement
     * @return array
     */
    protected function saveReminders($reminders, $agreement) {

        $keepIds = [];

        foreach ($reminders['subject'] as $key => $subject) {

            $date = \DateTime::createFromFormat('d-m-Y', $reminders['send_at'][$key]);
            $date = false !== $date ? $date->format('Y-m-d') : $reminders['send_at'][$key];

            $r = new Notifications();
            $r->setId($reminders['id'][$key]);
            $r->setSubject($subject);
            $r->setDescription($reminders['description'][$key]);
            $r->setType($reminders['type'][$key] ?? 10);
            $r->setCreatedBy($this->currentUser->getId());
            $r->setUserId($agreement->getCreatedBy());
            $r->setFrameworkAgreementId($agreement->getId());

            if($date){
                $r->setSendAt($date);
            }

            $r->save();
            $keepIds[] = $r->getId();
        }
        return $keepIds;
    }
}
