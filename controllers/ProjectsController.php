<?php

namespace Signa\Controllers;

use Phalcon\Builder\Project;
use Signa\Models\Products;
use Signa\Models\Projects;
use Signa\Models\ProjectTasks;
use Signa\Models\ProjectUsers;
use Signa\Models\ProjectFiles;
use Signa\Models\ProjectNotes;
use Signa\Models\ProjectNotesFiles;
use Signa\Helpers\Translations as Trans;
use Signa\Helpers\Date;

use Signa\Helpers\General;
use Phalcon\Paginator\Adapter\NativeArray as PaginatorArray;
use Signa\Models\Users;

class ProjectsController extends ControllerBase
{
    protected $projectId;

    public function initialize(){

        parent::initialize();
        $action = $this->dispatcher->getActionName();
        $disallowedActions = array('index', 'add', 'edit', 'delete', 'manage');

        if (in_array($action, $disallowedActions)) {
            $this->projectId = 0;
        }
        else {
            $this->projectId = $this->dispatcher->getParams()[0];
        }
        $this->view->setVar('projectId', $this->projectId);
    }

    public function indexAction(){

        $projects = $this->currentUser->Projects;
        $userProjects = Projects::find('created_by = '.$this->currentUser->getId());
        $allProjects = array();

        // Projects to which user was invited
        foreach ($userProjects as $project) {
            $allProjects[] = $project;
        }

        // Projects current users
        foreach ($projects as $project) {
            $allProjects[] = $project;
        }

        $this->view->projects = $allProjects;
        $this->view->addModalContent = $this->getAddContent('add');
        $this->view->editModalContent = $this->getAddContent('edit');
        $this->view->manageUsersContent = $this->getManageUsersContent('manage');
        $this->view->disableSubnav = true;
    }

    public function addAction(){

        if ($this->request->isPost()) {

            $projects = new Projects();

            $this->view->disable();
            $name = $this->request->getPost('name');
            $user = $this->currentUser->getId();
            $currentDate = Date::currentDatetime();

            $projects->setName($name);
            $projects->setCreatedBy($user);
            $projects->setCreatedAt($currentDate);

            if ($projects->save() !== false) {
                $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $projects->getId(), $projects->getName(), 'success');
                $this->session->set('message', ['type' => 'success', 'content' => Trans::make("New project has been added.")]);
                $this->response->redirect('projects/');
                return;
            }
            else {
                $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $projects->getId(), $projects->getName(), 'false');
                $this->response->redirect('projects/');
                $this->session->set('message', ['type' => 'error', 'content' => Trans::make("Project can't be added.")]);
            }
        }
    }

    public function editAction(){

        if ($this->request->isPost()) {

            $this->view->disable();
            $projectId = $this->request->getPost('projectId');
            $name = $this->request->getPost('name');

            $project = Projects::findFirst($projectId);

            $currentDate = Date::currentDatetime();

            $project->setName($name);
            $project->setEditedAt($currentDate);

            if ($project->save() !== false) {

                $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $projectId, $name, 'success');
                $this->session->set('message', ['type' => 'success', 'content' => Trans::make("Project has been edited.")]);
                $this->response->redirect('projects/');
                return;
            }
            else {
                $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $projectId, $name, 'false');
                $this->response->redirect('projects/');
                $this->session->set('message', ['type' => 'error', 'content' => Trans::make("Project can't be edited.")]);
            }
        }
    }

    public function deleteAction($id){

        $project = Projects::findFirst($id);

        $this->view->disable();

        if ($project) {

            if ($project->delete() !== false) {

                ProjectUsers::find('project_id', $id)->delete();
                $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $project->getId(), $project->getName(), 'success');
                $this->session->set('message', ['type' => 'success', 'content' => Trans::make("Project has been deleted.")]);
                $this->response->redirect('projects/');
                return;
            }
            else {
                $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $project->getId(), $project->getName(), 'fail');
                $this->session->set('message', ['type' => 'error', 'content' => Trans::make("Project can't be deleted.")]);
                $this->response->redirect('projects/');
            }
        }
        else {
            $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $project->getId(), $project->getName(), array('fail', 'project not exits'));
            $this->session->set('message', ['type' => 'error', 'content' => Trans::make("The project does not exist.")]);
            $this->response->redirect('projects/');
        }
    }

    public function leaveAction($id){

        $this->view->disable();

        $project = ProjectUsers::findFirst('project_id = '.$id.' AND user_id = '.$this->currentUser->getId());

        if ($project->delete()) {
            $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $project->getId(), null, 'success');
            $this->session->set('message', ['type' => 'success', 'content' => Trans::make("You are leave project.")]);
            $this->response->redirect('projects/');
            return;
        }
        else {
            $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $project->getId(), null, 'fail');
            $this->session->set('message', ['type' => 'error', 'content' => Trans::make("You aren't leave project.")]);
            $this->response->redirect('projects/');
        }
    }

    public function manageAction(){

        $this->view->disable();

        if ($this->request->isPost()) {

            $projectID = $this->request->getPost('projectId');
            // Array of all users from post
            $users = $this->request->getPost('users');

            // Project Name
            $projectName = Projects::findFirst($projectID)->getName();

            if ($users !== null) {

                // Existing users in project
                $cusers = ProjectUsers::find(
                    [
                        'project_id = :id: AND user_id IN ({users:array})',
                        'bind' => [
                            'id' => $projectID,
                            'users' => $users
                        ]
                    ]
                );

                $dusers = ProjectUsers::find(
                    [
                        'project_id = :id: AND user_id NOT IN ({users:array})',
                        'bind' => [
                            'id' => $projectID,
                            'users' => $users
                        ]
                    ]
                );
                // Declare array of existing users
                $oldusers = array();

                // Delete from post array of users existing users
                foreach ($cusers as $user) {

                    $pos = array_search($user->getUserId(), $users);
                    // Add existing users to array
                    array_push($oldusers, $users[$pos]);
                    unset($users[$pos]);
                }

                // Array only new users
                $newusers = array_values($users);

                // Save new users
                foreach ($newusers as $user) {

                    $projectUsers = new ProjectUsers();
                    $projectUsers->setProjectId($projectID);
                    $projectUsers->setUserId($user);
                    $projectUsers->save();
                }

                $allusers = array_merge($newusers, $oldusers);
                $listOfUsers = Users::find(
                    [
                        'id IN ({id:array})',
                        'bind' => [
                            'id' => $allusers
                        ]
                    ]
                );

                if ($oldusers !== null) {
                    $this->sendNotification($projectName, $listOfUsers, $projectID, $oldusers, "Project edited");
                }

                if ($newusers !== null) {
                    $this->sendNotification($projectName, $listOfUsers, $projectID, $newusers, "You added to the project");
                }

            }
            else {
                $dusers = ProjectUsers::find('project_id = '.$projectID);
            }

            // Delete users
            $deleteusers = array();

            foreach ($dusers as $user) {

                array_push($deleteusers, $user->getUserId());
            }
            $dusers->delete();

            if ($deleteusers !== null) {

                $this->sendNotification($projectName, $listOfUsers, $projectID, $deleteusers, "You were removed from the project", true);
            }

            $this->mongoLogger->createLog(
                array(
                    'datetime' => date('d-m-Y H:i:s'),
                    'page' => $this->router->getRewriteUri(),
                    'user' => $this->currentUser->getEmail(),
                    'project_id' => $projectID,
                    'project_name' => $projectName,
                    'users' => self::createUsersLog($listOfUsers)
                ),
                $this->currentUser->getEmail());

            $this->session->set('message', ['type' => 'success', 'content' => Trans::make("Users are stored.")]);
            $this->response->redirect('projects/');

        }
        else {

            $projectID = $this->request->get('projectId');

            if ($projectID !== null) {

                $selected = ProjectUsers::find("project_id = '" . $projectID . "'");;
                $selected_ids = array();

                foreach ($selected as $select) {
                    $selected_ids[] = $select->getUserId();
                }

                $users = Users::find(
                    [
                        'id IN ({id:array})',
                        'bind' => [
                            'id' => $selected_ids
                        ]
                    ]
                );
            }
            else {
                $currentUser = $this->currentUser->getId();
                $users = Users::find(
                    [
                        'NOT (id = :id:)',
                        'bind' => [
                            'id' => $currentUser
                        ]
                    ]
                );
            }
            return json_encode($users);
        }
    }

    public function viewAction($id){

        $project = Projects::findFirst($id);
        $owner = Users::findFirst($project->getCreatedBy());
        $userProjects = Projects::findFirst('id = '.$id);
        $allUsers = array();

        foreach ($userProjects->Users as $s) {

            $allUsers[$s->getId()] = [
                'name' => $s->getFullName()
            ];
        }
        $allUsers[$owner->getId()] = [
            'name' => $owner->getFullName()
        ];

        $isshared = (count($project->Users) == 0);
        $isyour = $project->getCreatedBy() != $this->currentUser->getId();

        if ($isshared && $isyour) {

            $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $project->getId(), $project->getName(), 'fail no permission');
            $this->session->set('message', ['type' => 'error', 'content' => Trans::make("No permission to view this project.")]);
            $this->response->redirect('projects/');
        }

        $this->assets->collection('footer')
            ->addJs("bower_components/socket.io-client/dist/socket.io.js")
            ->addJs("bower_components/moment/moment.js");

        if ( $_SERVER['HTTP_HOST'] == 'test. ' ) {
            $this->view->port = 3000;
        }
        else if ( $_SERVER['HTTP_HOST'] == 'acc. ' ) {
            $this->view->port = 3001;
        }
        else if ( $_SERVER['HTTP_HOST'] == 'mijn. ' ) {
            $this->view->port = 3002;
        }

        $this->view->project = $project;
        $this->view->allUsers = json_encode($allUsers);
        $this->view->serverName = $this->baseUrl;
    }

    public function noteAction($id){

        $project = Projects::findFirst($id);
        $isshared = (count($project->Users) == 0);
        $isyour = $project->getCreatedBy() != $this->currentUser->getId();

        if ($isshared && $isyour) {

            $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $project->getId(), $project->getName(), 'fail no permission');
            $this->session->set('message', ['type' => 'error', 'content' => Trans::make("No permission to view this project.")]);
            $this->response->redirect('projects/');
        }

        $this->assets->collection('footer')
            ->addJs("bower_components/blueimp-file-upload/js/vendor/jquery.ui.widget.js")
            ->addJs("bower_components/blueimp-file-upload/js/jquery.fileupload.js")
            ->addJs("bower_components/blueimp-file-upload/js/jquery.iframe-transport.js")
            ->addJs("bower_components/socket.io-client/dist/socket.io.js")
            ->addJs("js/app/projectnote.js")
            ->addJs("bower_components/moment/moment.js");

        $files = ProjectNotes::find(array(
            'project_id = :project:',
            'bind' => array(
                'project' => $this->projectId
            )
        ));

        $this->view->files = $files;
        $this->view->project = $project;
        $this->view->uploadContent = $this->getUploadContent();
        $this->view->serverName = $this->baseUrl;
    }

    public function editnoteAction($projectId, $noteId){

        $project_note = ProjectNotes::findFirst($noteId);

        $this->assets->collection('footer')
            ->addJs("bower_components/blueimp-file-upload/js/vendor/jquery.ui.widget.js")
            ->addJs("bower_components/blueimp-file-upload/js/jquery.fileupload.js")
            ->addJs("bower_components/blueimp-file-upload/js/jquery.fileupload-ui.js")
            ->addJs("bower_components/blueimp-file-upload/js/jquery.iframe-transport.js")
            ->addJs("bower_components/socket.io-client/dist/socket.io.js")
            ->addJs("js/app/projectnote.js")
            ->addJs("bower_components/moment/moment.js");

        if ($this->request->isPost()) {

            $content = $this->request->getPost('content');
            $title = $this->request->getPost('title');

            $this->view->disable();

            $project_note->setContent($content);
            $project_note->setTitle($title);
            $project_note->setProjectId($projectId);

            if ($project_note->save() !== false) {

                $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $project_note->getId(), $project_note->Project->getName(), 'success');
                $this->session->set('message', ['type' => 'success', 'content' => Trans::make("New note has been updated.")]);

                echo json_encode($project_note->getId()); die;
            }
            else {
                $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $project_note->getId(), $project_note->Project->getName(), 'false');
                $messages = $project_note->getMessages();
                $errorsString = '';

                foreach ($messages as $message) {
                    $errorsString .= $message . "</br>";
                }
                $tmessage = [
                    'type' => 'error',
                    'content' => $errorsString
                ];
                $this->session->set('message', $tmessage);
                echo json_encode(0); die;
            }
        }
        $this->view->note = $project_note;
        $this->view->status = array($this->t->make('Open'), $this->t->make('Closed'));
    }

    public function addnoteAction($projectId){

        $this->assets->collection('footer')
            ->addJs("bower_components/blueimp-file-upload/js/vendor/jquery.ui.widget.js")
            ->addJs("bower_components/blueimp-file-upload/js/jquery.fileupload.js")
            ->addJs("bower_components/blueimp-file-upload/js/jquery.fileupload-ui.js")
            ->addJs("bower_components/blueimp-file-upload/js/jquery.iframe-transport.js")
            ->addJs("bower_components/socket.io-client/dist/socket.io.js")
            ->addJs("js/app/projectnote.js")
            ->addJs("bower_components/moment/moment.js");

        if ($this->request->isPost()) {

            $project_note = new ProjectNotes();

            $content = $this->request->getPost('content');
            $title = $this->request->getPost('title');

            $this->view->disable();

            $project_note->setContent($content);
            $project_note->setTitle($title);
            $project_note->setProjectId($projectId);

            if ($project_note->save() !== false) {

                $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $project_note->getId(), $project_note->Project->getName(), 'success');
                $this->session->set('message', ['type' => 'success', 'content' => Trans::make("New note has been added.")]);
                $this->response->redirect('projects/editnote/' . $projectId . '/' .$project_note->getId());

            }
            else {
                $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $project_note->getId(), $project_note->Project->getName(), 'false');
                $messages = $project_note->getMessages();
                $errorsString = '';

                foreach ($messages as $message) {
                    $errorsString .= $message . "</br>";
                }
                $tmessage = [
                    'type' => 'error',
                    'content' => $errorsString
                ];
                $this->session->set('message', $tmessage);

                $this->response->redirect('projects/addnote/' . $projectId);
                $this->session->set('message', ['type' => 'error', 'content' => Trans::make("Note can't be added.")]);
            }
        }
        $this->view->status = array($this->t->make('Open'), $this->t->make('Closed'));
    }

    public function uploadnotefileAction(){

        $this->view->disable();

        if ($this->request->isAjax()) {

            if ($this->request->hasFiles() == true) {

                $currentUserId = $this->currentUser->getId();
                $fileDir = $this->config->application->projectDir;
                $projectFileDir = $fileDir . $this->projectId . '/';

                foreach ($this->request->getUploadedFiles() as $file) {

                    $randomString = General::randomString(6);
                    $filename = $randomString . $file->getName();

                    if (!is_dir($fileDir)) {
                        mkdirR($fileDir);
                    }

                    if (!is_dir($projectFileDir)) {
                        mkdirR($projectFileDir);
                    }
                    $file->moveTo($projectFileDir . $filename);

                    $fileModel = new ProjectNotesFiles();
                    $fileModel->setName($filename);
                    $fileModel->setNameOriginal($file->getName());
                    $fileModel->setSize($file->getSize());
                    $fileModel->setType($file->getType());
                    $fileModel->setProjectId($this->projectId);
                    $fileModel->setNoteId($this->request->getPost('note_id'));
                    $fileModel->save();
                }
                return json_encode(array('status' => true));
            }
            else {
                return json_encode(array('status' => false));
            }
        }
        return true;
    }

    public function fileAction($id){

        $project = Projects::findFirst($id);
        $isshared = (count($project->Users) == 0);
        $isyour = $project->getCreatedBy() != $this->currentUser->getId();

        if ($isshared && $isyour) {

            $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $project->getId(), $project->getName(), 'fail no permission');
            $this->session->set('message', ['type' => 'error', 'content' => Trans::make("No permission to view this project.")]);
            $this->response->redirect('projects/');
        }

        $this->assets->collection('footer')
            ->addJs("bower_components/blueimp-file-upload/js/vendor/jquery.ui.widget.js")
            ->addJs("bower_components/blueimp-file-upload/js/jquery.fileupload.js")
            ->addJs("bower_components/blueimp-file-upload/js/jquery.iframe-transport.js")
            ->addJs("bower_components/socket.io-client/dist/socket.io.js")
            ->addJs("js/app/file.js")
            ->addJs("bower_components/moment/moment.js");

        $files = ProjectFiles::find(array(
            'project_id = :project:',
            'bind' => array(
                'project' => $this->projectId
            )
        ));

        $this->view->files = $files;
        $this->view->project = $project;
        $this->view->uploadContent = $this->getUploadContent();
        $this->view->serverName = $this->baseUrl;
    }

    public function uploadfileAction(){

        $this->view->disable();

        if ($this->request->isAjax()) {

            if ($this->request->hasFiles() == true) {

                $currentUserId = $this->currentUser->getId();
                $fileDir = $this->config->application->projectDir;
                $projectFileDir = $fileDir . $this->projectId . '/';

                $file = $this->request->getUploadedFiles()[0];
                $randomString = General::randomString(6);
                $filename = $randomString . $file->getName();

                if (!is_dir($fileDir)) {
                    mkdirR($fileDir);
                }

                if (!is_dir($projectFileDir)) {
                    mkdirR($projectFileDir);
                }
                $file->moveTo($projectFileDir . $filename);

                $fileModel = new ProjectFiles();
                $fileModel->setName($filename);
                $fileModel->setNameOriginal($file->getName());
                $fileModel->setSize($file->getSize());
                $fileModel->setType($file->getType());
                $fileModel->setProjectId($this->projectId);
                $saved = $fileModel->save();

                if ($saved) {
                    $projectUsers = ProjectUsers::find(array(
                        'project_id = :project:',
                        'bind' => array(
                            'project' => $this->projectId
                        ),
                        'columns' => 'user_id'
                    ));
                    foreach ($projectUsers as $projectUser) {

                        if ($projectUser['user_id'] !== $currentUserId) {

                            $this->notifications->addNotification(array(
                                'type' => 3,
                                'subject' => Trans::make('File share'),
                                'description' => self::getSharedNotificationContent($this->projectId, $file->getName())
                            ), null, null, array($projectUser['user_id']));
                        }
                    }
                    $this->session->set('message', ['type' => 'success', 'content' => Trans::make('File has been added.')]);
                    return json_encode(array('status' => true));
                }
                else {
                    $this->session->set('message', ['type' => 'error', 'content' => Trans::make('File cannot be saved.')]);
                    return json_encode(array('status' => false));
                }
            }
            else {
                $this->session->set('message', ['type' => 'error', 'content' => Trans::make('A file is missing.')]);
                return json_encode(array('status' => false));
            }
        }
        return true;
    }

    public function downloadnotefileAction($projectId, $fileId){

        $this->view->disable();
        $file = ProjectNotesFiles::findFirst($fileId);
        $fileDir = $this->config->application->projectDir . $this->projectId . '/';
        $filename = $fileDir . $file->getName();

        if (file_exists($filename)) {

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file->getNameOriginal() . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
            readfile($filename);
            exit;
        }
        else {
            $this->session->set('message', ['type' => 'error', 'content' => Trans::make('File does not exist.')]);
            $this->response->redirect('/project/note/' . $this->projectId);
            return true;
        }
    }

    public function downloadfileAction($projectId, $fileId){

        $this->view->disable();
        $file = ProjectFiles::findFirst($fileId);
        $fileDir = $this->config->application->projectDir . $this->projectId . '/';
        $filename = $fileDir . $file->getName();

        if (file_exists($filename)) {

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file->getNameOriginal() . '"');
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filename));
            readfile($filename);
            exit;
        }
        else {
            $this->session->set('message', ['type' => 'error', 'content' => Trans::make('File does not exist.')]);
            $this->response->redirect('/project/file/' . $this->projectId);
            return true;
        }
    }

    public function removenoteAction($projectId, $noteId){

        $this->view->disable();
        $currentUserId = $this->currentUser->getId();
        $file = ProjectNotes::findFirst('id = ' . $noteId . ' AND created_by = ' . $currentUserId);

        // Remove file with every connected users
        if ($file) {
            $file->delete();
            $this->session->set('message', ['type' => 'success', 'content' => Trans::make('Successfully deleted.')]);
        }
        else {
            $this->session->set('message', ['type' => 'error', 'content' => Trans::make('Not exist.')]);
        }
        $this->response->redirect('/projects/note/' . $this->projectId);
        return true;
    }

    public function deletenotefileAction($projectId, $noteId, $fileId){

        $this->view->disable();
        $currentUserId = $this->currentUser->getId();
        $file = ProjectNotesFiles::findFirst('id = ' . $fileId . ' AND created_by = ' . $currentUserId);
        $fileDir = $this->config->application->projectDir . $this->projectId . '/';
        $filename = $fileDir . $file->getName();

        // Remove file with every connected users
        if (file_exists($filename)) {
            unlink($filename);

            $file->delete();
            $this->session->set('message', ['type' => 'success', 'content' => Trans::make('Successfully deleted file.')]);
        }
        else {
            $file->delete();
            $this->session->set('message', ['type' => 'error', 'content' => Trans::make('File does not exist.')]);
        }
        $this->response->redirect('/projects/editnote/' . $this->projectId . '/' . $noteId);
        return true;
    }

    public function deletefileAction($projectId, $fileId){

        $this->view->disable();
        $currentUserId = $this->currentUser->getId();
        $file = ProjectFiles::findFirst('id = ' . $fileId . ' AND created_by = ' . $currentUserId);
        $fileDir = $this->config->application->projectDir . $this->projectId . '/';
        $filename = $fileDir . $file->getName();

        // Remove file with every connected users
        if (file_exists($filename)) {
            unlink($filename);

            $file->delete();
            $this->session->set('message', ['type' => 'success', 'content' => Trans::make('Successfully deleted file.')]);
        }
        else {
            $file->delete();
            $this->session->set('message', ['type' => 'error', 'content' => Trans::make('File does not exist.')]);
        }
        $this->response->redirect('/projects/file/' . $this->projectId);
        return true;
    }

    public function tasksAction($projectId){

        $this->view->tasks = ProjectTasks::find('project_id = '.$projectId);
    }

    public function viewtaskAction($projectId, $taskId){

        $this->view->task = ProjectTasks::findFirst($taskId);
    }

    public function addtaskAction($projectId){

        $this->assets->collection('footer')
        ->addJs("bower_components/moment/min/moment.min.js");

        if ($this->request->isAjax()) {

            $query = $this->request->get('q');
            $currentUser = $this->currentUser->getId();
            $users = Users::find(
                [
                    'NOT (id = :id:) AND email LIKE \'%' . $query . '%\'',
                    'bind' => [
                        'id' => $currentUser
                    ]
                ]
            );

            $project = Projects::findFirst($projectId);

            return json_encode($project->Users);
        }

        if ($this->request->isPost()) {

            $project_task = new ProjectTasks();

            $status = $this->request->getPost('status');

            if ($status == null) {
                $status = 0;
            }

            $description = $this->request->getPost('description');
            $deadline = $this->request->getPost('deadline');
            $users = $this->request->getPost('users');

            $this->view->disable();

            $project_task->setStatus($status);
            $project_task->setDescription($description);
            $project_task->setDeadline($deadline);
            $project_task->setProjectId($projectId);
            $project_task->setAssigne($users);

            if ($project_task->save() !== false) {

                $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $project_task->getId(), $project_task->Project->getName(), 'success');
                $this->session->set('message', ['type' => 'success', 'content' => Trans::make("New task has been added.")]);
                $this->response->redirect('projects/tasks/' . $projectId);
                $this->taskNotification($projectId, $project_task->getId());
                return;
            }
            else {
                $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $project_task->getId(), $project_task->Project->getName(), 'false');
                $messages = $project_task->getMessages();
                $errorsString = '';

                foreach ($messages as $message) {
                    $errorsString .= $message . "</br>";
                }
                $tmessage = [
                    'type' => 'error',
                    'content' => $errorsString
                ];
                $this->session->set('message', $tmessage);

                $this->response->redirect('projects/addtask/' . $projectId);
            }
        }
        $this->view->status = array($this->t->make('Open'), $this->t->make('Closed'));
    }

    public function edittaskAction($projectId, $taskId){

        $this->assets->collection('footer')
            ->addJs("bower_components/moment/min/moment.min.js");

        if ($this->request->isAjax()) {

            $query = $this->request->get('q');
            $currentUser = $this->currentUser->getId();
            $users = Users::find(
                [
                    'NOT (id = :id:) AND email LIKE \'%' . $query . '%\'',
                    'bind' => [
                        'id' => $currentUser
                    ]
                ]
            );
            return json_encode($users);
        }

        $task = ProjectTasks::findFirst('id = '.$taskId);

        if ($this->request->isPost()) {

            $status = $this->request->getPost('status');

            if ($status == null) {
                $status = 0;
            }
            $description = $this->request->getPost('description');
            $deadline = $this->request->getPost('deadline');
            $users = $this->request->getPost('users');

            $this->view->disable();

            $task->setStatus($status);
            $task->setDescription($description);
            $task->setDeadline($deadline);
            $task->setAssigne($users);

            if ($task->save() !== false) {

                $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $task->getId(), $task->Project->getName(), 'success');
                $this->session->set('message', ['type' => 'success', 'content' => Trans::make("New task has been added.")]);
                $this->response->redirect('projects/tasks/' . $projectId);
                return;
            }
            else {
                $this->mongoLog($this->router->getRewriteUri(), $this->currentUser->getEmail(), $task->getId(), $task->Project->getName(), 'false');
                $messages = $task->getMessages();
                $errorsString = '';

                foreach ($messages as $message) {
                    $errorsString .= $message . "</br>";
                }
                $tmessage = [
                    'type' => 'error',
                    'content' => $errorsString
                ];
                $this->session->set('message', $tmessage);
                $this->response->redirect('projects/edittask/' . $taskId);
            }
        }
        $this->view->status = array($this->t->make('Open'), $this->t->make('Closed'));
        $this->view->task = $task;

    }

    public function taskstatusAction($projectId, $taskId){

        $task = ProjectTasks::findFirst($taskId);
        $currentStatus = $task->status;

        if ($currentStatus == 1) {
            $task->setStatus(0);
        }
        else {
            $task->setStatus(1);
        }
        $saved = $task->save();

        if ($saved) {
            $this->session->set('message', ['type' => 'success', 'content' => Trans::make('Task state has been changed.')]);
        }
        else {
            $this->session->set('message', ['type' => 'error', 'content' => Trans::make('Task state hasn\'t been changed.')]);
        }
        $this->response->redirect('projects/tasks/' . $projectId);
    }

    public function deletetaskAction($projectId, $taskId){

        $task = ProjectTasks::findFirst($taskId);
        $deleted = $task->delete();

        if ($deleted) {
            $this->session->set('message', ['type' => 'success', 'content' => Trans::make('Task has been deleted.')]);
        }
        else {
            $this->session->set('message', ['type' => 'error', 'content' => Trans::make('Task hasn\'t been deleted.')]);
        }
        $this->response->redirect('projects/tasks/' . $projectId);
    }

    private function getAddContent($action){

        $html = '<form id="projectAddFrom" action="/projects/' . $action . '" method="post">';

        if ($action == 'edit') {
            $html .= '<input id="projectId" type="hidden" name="projectId" value="">';
        }
        $html .= '<label>' . Trans::make("Name") . '</label>';
        $html .= '<input id="projectName" type="text" class="form-control input-amount" name="name" />';
        $html .= '</form>';

        return $html;
    }

    private function getManageUsersContent($action){

        $html = '<form id="manageUsersForm" action="/projects/' . $action . '" method="post">';
        $html .= '<label>' . Trans::make("Users") . '</label>';
        $html .= '<input type="hidden" id="manageUsersProjectId" name="projectId" value="">';
        $html .= '<select class="select2-users" name="users[]"></select>';
        $html .= '</form>';

        return $html;
    }

    private function getNotificationContent($users, $projectID, $delete){

        if ($delete) {
            $html = '<p>' . Trans::make("This project is no longer available for you") . '</p>';
        }
        else {
            $html = '<ul>';

            foreach ($users as $user) {

                $email = $user->getEmail();
                $html .= '<li>' . $email . '</li>';
            }
            $html .= '</ul>';
            $html .= '<a class=&quot;btn btn-success&quot; href=&quot;/projects/view/' . $projectID . '&quot;>' . Trans::make("Go to project") . '</a>';
        }
        return $html;
    }

    private function sendNotification($name, $allusers, $id, $towho, $subject, $delete = false){

        $this->notifications->addNotification(array(
            'type' => 4,
            'subject' => Trans::make($subject) . ": " . $name,
            'description' => $this->getNotificationContent($allusers, $id, $delete),
        ), null, null, $towho);
    }

    private static function createUsersLog($users){

        $array = array();

        foreach ($users as $key => $user) {

            $array[$user->getId()] = array(
                'user_fullname' => $user->getFullName(),
                'email' => $user->getEmail()
            );
        }
        return $array;
    }

    private function mongoLog($page, $user, $project_id, $project_name, $state){

        $this->mongoLogger->createLog(
            array(
                'datetime' => date('d-m-Y H:i:s'),
                'page' => $page,
                'user' => $user,
                'project_id' => $project_id,
                'project_name' => $project_name,
                'state' => $state
            ),
            $user);
    }

    private function getUploadContent(){

        $html = '<p>' . Trans::make('Select a file on your computer to upload') . '</p>';
        $html .= '<span class="btn btn-success fileinput-button"><i class="pe-7s-plus"></i><span>'.Trans::make('Add file').'</span>';
        $html .= '<input id="fileupload" type="file" name="file" data-url="/projects/uploadfile/' . $this->projectId . '">';
        $html .= '</span>';
        $html .= '<div id="uploader"></div>';
        $html .= '<div id="progress"><div class="bar" style="width: 0%;"></div></div>';

        return $html;
    }

    private static function getSharedNotificationContent($projectId, $filename){

        $project = Projects::findFirst($projectId);
        $html = '<p>' . Trans::make("To the project") . ' ' . $project->getName() . ' ' . Trans::make("is added a new file") . ' ' . $filename . '</p>';
        return $html;
    }

    private function taskNotification($projectId, $taskId){

        $users = [];
        $project = Projects::findFirst($projectId);
        $users[] = $project->getCreatedBy();

        foreach ($project->Users as $user) {

            $users[] = $user->getId();
        }
        $task = ProjectTasks::findFirst($taskId);
        $assignes = $task->getAssigne();
        $description = $task->getDescription();
        $deadline = $task->getDeadline();
        $url = '/projects/viewtask/' . $projectId . '/' . $taskId;

        foreach ($assignes as $assigne) {

            $pos = array_search($assigne, $users);
            unset($users[$pos]);
        }

        $this->notifications->addNotification(array(
            'type' => 6,
            'subject' => Trans::make('New task'),
            'description' => self::getAssigneMessage(Trans::make('New task in project'), $description, $deadline, $url)
        ), null, null, $users);

        $this->notifications->addNotification(array(
            'type' => 6,
            'subject' => Trans::make('New task'),
            'description' => self::getAssigneMessage(Trans::make('You are assigned to new task'), $description, $deadline, $url)
        ), null, null, $assignes);
    }

    private static function getAssigneMessage($title, $description, $deadline, $url){

        $html = '<div class=&quot;row&quot;>';
        $html .= '<div class=&quot;col-lg-12&quot;>';
        $html .= '<h3>';
        $html .= $title;
        $html .= '</h3>';
        $html .= '<table class=&quot;table table-striped table-bordered&quot;>';
        $html .= '<tr>';
        $html .= '<td>' . Trans::make("Description") . '</td>';
        $html .= '<td>' . $description . '</td>';
        $html .= '</tr>';
        $html .= '<tr>';
        $html .= '<td>' . Trans::make("Deadline") . '</td>';
        $html .= '<td>' . $deadline . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        $html .= '<a href=&quot;' . $url . '&quot; class=&quot;btn btn-primary&quot;>' . Trans::make("Open task") . '</a>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
}
