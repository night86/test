<?php

namespace Signa\Models;

use Signa\Helpers\Date;

class ProjectNotesFiles extends \Phalcon\Mvc\Model
{
    protected $id;
    protected $name;
    protected $name_original;
    protected $size;
    protected $type;
    protected $project_id;
    protected $note_id;
    protected $organisation_id;
    protected $created_by;
    protected $created_at;

    public function initialize()
    {
        $this->belongsTo('note_id', 'Signa\Models\ProjectNotes', 'id', array('alias' => 'Note'));
        $this->belongsTo('project_id', 'Signa\Models\Projects', 'id', array('alias' => 'Project'));
        $this->belongsTo('organisation_id', 'Signa\Models\Organisations', 'id', array('alias' => 'Organisation'));
        $this->belongsTo('created_by', 'Signa\Models\Users', 'id', array('alias' => 'CreatedBy'));
    }

    public function beforeCreate()
    {
        $user = $this->getDI()->getSession()->get('auth');

        $this->setOrganisationId($user->Organisation->getId());
        $this->setCreatedBy($user->getId());
        $this->setCreatedAt(Date::currentDatetime());
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getNameOriginal()
    {
        return $this->name_original;
    }

    /**
     * @param mixed $name_original
     */
    public function setNameOriginal($name_original)
    {
        $this->name_original = $name_original;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = $this->size;
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, 1) . ' ' . $units[$pow];
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getProjectId()
    {
        return $this->project_id;
    }

    /**
     * @param mixed $project_id
     */
    public function setProjectId($project_id)
    {
        $this->project_id = $project_id;
    }

    /**
     * @return mixed
     */
    public function getNoteId()
    {
        return $this->note_id;
    }

    /**
     * @param mixed $note_id
     */
    public function setNoteId($note_id)
    {
        $this->note_id = $note_id;
    }

    /**
     * @return mixed
     */
    public function getOrganisationId()
    {
        return $this->organisation_id;
    }

    /**
     * @param mixed $organisation_id
     */
    public function setOrganisationId($organisation_id)
    {
        $this->organisation_id = $organisation_id;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->created_by;
    }

    /**
     * @param mixed $created_by
     */
    public function setCreatedBy($created_by)
    {
        $this->created_by = $created_by;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

}
