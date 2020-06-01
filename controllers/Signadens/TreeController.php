<?php

namespace Signa\Controllers\Signadens;

use Signa\Models\CategoryTree;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class TreeController extends InitController
{
    public function indexAction($id = '0'){

        $currentPage = isset($_GET["page"]) && $_GET["page"] != null ? (int) $_GET["page"] : 1;

        $result = CategoryTree::find(
            [
                "parent_id = '$id'",
                "order" => "sort"
            ]
        );
        $currentCategory = CategoryTree::findFirst($id);
        $categories = [];

        foreach ($result as $key => $category) {

            $thisId = $category->getId();
            $childs = CategoryTree::findFirst(
                [
                    "parent_id = '$thisId'",
                    "order" => "sort"
                ]
            );
            $categories[] = $category->toArray();

            if (!$childs) {
                $categories[]['empty'] = 1;
            }
            else {
                $categories[]['empty'] = 0;
            }
        }

        $paginator = new PaginatorModel(
            [
                "data"  => $result,
                "limit" => 6,
                "page"  => $currentPage,
            ]
        );
        $page = $paginator->getPaginate();

        $this->view->currentid = $id;
        $this->view->page = $page;
        $this->view->currentCategory = $currentCategory;
        $this->view->categoryImage = '/uploads/images/category_tree/';
        $this->view->recipeImage = '/uploads/images/recipes/';
    }
}
