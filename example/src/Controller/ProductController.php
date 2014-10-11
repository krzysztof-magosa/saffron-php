<?php
namespace Site\Controller;

class ProductController
{
    public function indexAction($id, $slug)
    {
        echo "Here you can buy a $slug (id: $id)";
    }
}
