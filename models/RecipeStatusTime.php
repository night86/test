<?php

namespace Signa\Models;

class RecipeStatusTime extends \Phalcon\Mvc\Model
{
    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $recipe_status_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $lab_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $days;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getRecipeStatusId()
    {
        return $this->recipe_status_id;
    }

    /**
     * @param int $recipe_status_id
     */
    public function setRecipeStatusId($recipe_status_id)
    {
        $this->recipe_status_id = $recipe_status_id;
    }

    /**
     * @return int
     */
    public function getLabId()
    {
        return $this->lab_id;
    }

    /**
     * @param int $lab_id
     */
    public function setLabId($lab_id)
    {
        $this->lab_id = $lab_id;
    }

    /**
     * @return int
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * @param int $days
     */
    public function setDays($days)
    {
        if ($days == '') {
            $days = null;
        }
        $this->days = $days;
    }



}
