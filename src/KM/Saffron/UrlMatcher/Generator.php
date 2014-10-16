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
namespace KM\Saffron\UrlMatcher;

use KM\Saffron\RoutesCollection;
use KM\Saffron\Route;
use KM\Saffron\Code;

class Generator extends \KM\Saffron\Generator
{
    protected $collection;
    protected $code;

    public function __construct(RoutesCollection $collection)
    {
        $this->collection = $collection;
    }

    protected function conditionPrefix($route, &$conditions)
    {
        if ($route->needsUriRegex()) {
            $conditions[] = sprintf(
                '0 === strpos($uri, %s)',
                var_export($route->getPrefix(), true)
            );
        }
        else {
            $conditions[] = sprintf(
                '$uri == %s',
                var_export($route->getPrefix(), true)
            );   
        }
    }

    protected function conditionUriRegex($route, &$conditions)
    {
        if ($route->needsUriRegex()) {
            $conditions[] = sprintf(
                'preg_match(%s, $uri, $uriMatch)',
                var_export($route->getUriRegex(), true)
            );
        }
    }

    protected function conditionMethod($route, &$conditions)
    {
        if ($route->hasMethod()) {
            $conditions[] = sprintf(
                'in_array($method, %s)',
                $this->formatArray($route->getMethod())
            );
        }
    }

    protected function conditionHttps($route, &$conditions)
    {
        $https = $route->getHttps();
        if (null !== $https) {
            $conditions[] = sprintf(
                '$https === %s',
                var_export($https, true)
            );
        }
    }

    protected function getArrays($route)
    {
        $arrays = [];

        if ($route->hasDomain()) {
            $arrays[] = '$domainMatch';
        }
        if ($route->needsUriRegex()) {
            $arrays[] = '$uriMatch';
        }

        return $arrays;
    }

    protected function generateRoute($route)
    {
        $conditions = [];
        $this->conditionPrefix($route, $conditions);
        $this->conditionUriRegex($route, $conditions);
        $this->conditionMethod($route, $conditions);
        $this->conditionHttps($route, $conditions);

        $this->code->append(
            sprintf(
                'if (%s) {',
                implode(' && ', $conditions)
            )
        );

        $this->code->append('return new MatchedRoute(');
        $this->code->append($this->formatArray($route->getTarget()).',');
        $arrays = $this->getArrays($route);
        if ($arrays) {
            $this->code->append(
                sprintf(
                    'array_replace(%s, %s)',
                    $this->formatArray($route->getDefaults()),
                    implode(', ', $arrays)
                )
            );
        }
        else {
            $this->code->append($this->formatArray($route->getDefaults()));
        }
        $this->code->append(');');

        $this->code->append('}');
    }

    protected function generateMatch()
    {
        $this->code->append('public function match(Request $request) {');
        $this->code->append('$uri    = $request->getUri();');
        $this->code->append('$domain = $request->getDomain();');
        $this->code->append('$method = $request->getMethod();');
        $this->code->append('$https  = $request->getHttps();');
        $this->code->append('');

        foreach ($this->collection->groupByDomain() as $routes) {
            if ($routes->first()->hasDomain()) {
                $this->code->append(
                    sprintf(
                        'if (preg_match(%s, $domain, $domainMatch)) {',
                        var_export($routes->first()->getDomainRegex(), true)
                    )
                );
            }

            foreach ($routes as $route) {
                $this->generateRoute($route);
            }

            if ($routes->first()->hasDomain()) {
                $this->code->append('}');
            }
        }

        $this->code->append('}');
    }

    protected function generateHeader($className)
    {
        $this->code->append(
<<<EOB
            <?php
            use KM\Saffron\UrlMatcher;
            use KM\Saffron\Request;
            use KM\Saffron\MatchedRoute;
            use KM\Saffron\UrlMatcher\Base;

            class $className extends Base {
EOB
        );
    }

    protected function generateFooter()
    {
        $this->code->append('}');
    }

    public function generate($className)
    {
        $this->code = new Code();

        $this->generateHeader($className);
        $this->generateMatch();
        $this->generateFooter();

        return (string)$this->code;
    }
}
