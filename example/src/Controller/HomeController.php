<?php
namespace Site\Controller;

class HomeController
{
    public function indexAction()
    {
        /**
         * I don't recomment using global variables.
         * This example shows just usage of Saffron.
         * In real life you probably have FrontController which holds Router.
         */
        global $router;

        echo sprintf(
            '<a href="%s">Click here to buy a chair</a>',
            $router->assemble(
                'product',
                [
                    'slug' => 'chair',
                    'id' => 100
                ]
            )
        );
    }
}
