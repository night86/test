<?php

namespace Signa\Models;

class CategoryTree extends \Phalcon\Mvc\Model
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
    protected $parent_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $name;
    protected $image;
    protected $sort;
    protected $hasActiveRecipes;

    public function initialize()
    {
        $this->belongsTo('parent_id', 'Signa\Models\CategoryTree', 'id', array('alias' => 'ParentCategory'));
        $this->hasManyToMany(
            'id',
            'Signa\Models\CategoryTreeRecipes',
            'category_tree_id',
            'recipe_id',
            'Signa\Models\Recipes',
            'id',
            array('alias' => 'Recipes')
        );
    }

    public function saveData($data)
    {
        $this->setName($data['name']);
        if ($data['id']) {
            $this->setParentId($data['id']);
        } else {
            $this->setParentId(0);
        }

        return $this->save();
    }

    /**
     * Method to set the value of field id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Method to set the value of field parent_id
     *
     * @param integer $parent_id
     * @return $this
     */
    public function setParentId($parent_id)
    {
        $this->parent_id = $parent_id;

        return $this;
    }

    /**
     * Method to set the value of field name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns the value of field id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the value of field parent_id
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parent_id;
    }

    /**
     * Returns the value of field name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return mixed
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * @param mixed $sort
     */
    public function setSort($sort)
    {
        $this->sort = $sort;
    }



    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'category_tree';
    }

    /**
     * @return mixed
     */
    public function getHasActiveRecipes()
    {
        return $this->hasActiveRecipes;
    }

    /**
     * @param mixed $hasActiveRecipes
     */
    public function setHasActiveRecipes($hasActiveRecipes)
    {
        $this->hasActiveRecipes = $hasActiveRecipes;
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return CategoryTree[]
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return CategoryTree
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
