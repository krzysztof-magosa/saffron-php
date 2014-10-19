<?php
/**
 * Copyright 2014 Krzysztof Magosa
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

//
// Below Closure is called only when cache is empty.
// When you change something here, you need to empty cache directory.
//
return function ($collection) {
    /**
     * // You will use name later to generate links.
     * // Each name has to be unique.
     * $collection->route('name')
     *  ->setUri('/some/route/with/{parameter1}/and/{parameter2}')
     *  ->setDomain('www.example.{tld}')
     *  ->setMethod('GET') - you can also pass array with more methods
     *  ->setHttp(false) - resource is accessible only by NON-https connection
     *  ->setRequirements(
     *      [
     *          'parameter1' => '\w+', // parameter1 must be alphanumeric
     *          'parameter2' => '\d+', // parameter2 must be a number
     *          'tld' => 'com|org', // tld in domain must be com or org
     *      ]
     *  )
     *  ->setDefaults(
     *      [
     *          'parameter2' => 'value2', // when link doesn't contain parameter2, it has 'value2'
     *      ]
     *  )
     *  ->setTarget('HomeController', 'indexAction'); // you can omit action, the default is 'indexAction'
     */

    $collection->route('home')
        ->setUri('/')
        ->setMethod(['POST', 'PUT'])
        ->setTarget('Site\Controller\HomeController');

    $collection->route('product')
        ->setUri('/product/{slug}/{id}')
        ->setTarget('Site\Controller\ProductController')
        ->setRequirements(
            [
                'slug' => '\w+',
                'id' => '\d+',
            ]
        );
};
