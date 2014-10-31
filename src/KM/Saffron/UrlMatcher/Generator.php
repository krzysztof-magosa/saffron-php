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
    /**
     * @var RoutesCollection
     */
    protected $collection;

    /**
     * @var Code
     */
    protected $code;

    public function __construct(RoutesCollection $collection)
    {
        $this->collection = $collection;
        $this->code = new Code();
    }

    protected function conditionPrefix(Route $route, &$conditions)
    {
        $compiled = $route->getCompiled();

        if ($compiled->hasUriRegex()) {
            $conditions[] = sprintf(
                '0 === strpos($uri, %s)',
                var_export($compiled->getPrefix(), true)
            );
        } else {
            $conditions[] = sprintf(
                '$uri == %s',
                var_export($compiled->getPrefix(), true)
            );
        }
    }

    protected function conditionUriRegex(Route $route, array &$conditions)
    {
        $compiled = $route->getCompiled();

        if ($compiled->hasUriRegex()) {
            $conditions[] = sprintf(
                'preg_match(%s, $uri, $uriMatch)',
                var_export($compiled->getUriRegex(), true)
            );
        }
    }

    protected function conditionHttps(Route $route, array &$conditions)
    {
        if ($route->hasHttps()) {
            $conditions[] = sprintf(
                '$https === %s',
                var_export($route->getHttps(), true)
            );
        }
    }

    protected function getArraysOfParameters(Route $route)
    {
        $arrays = [];
        $compiled = $route->getCompiled();

        $arrays[] = sprintf(
            "['_route' => %s, '_request' => \$request]",
            var_export($route->getName(), true)
        );

        if ($route->hasDefaults()) {
            $arrays[] = $this->formatArray($route->getDefaults());
        }

        if ($route->hasDomain()) {
            $arrays[] = '$domainMatch';
        }
        if ($compiled->hasUriRegex()) {
            $arrays[] = '$uriMatch';
        }

        return $arrays;
    }

    protected function generateRoute(Route $route)
    {
        $conditions = [];
        $this->conditionPrefix($route, $conditions);
        $this->conditionUriRegex($route, $conditions);
        $this->conditionHttps($route, $conditions);

        $this->code->append(
            sprintf(
                'if (%s) {',
                implode(' && ', $conditions)
            )
        );

        if ($route->hasMethod()) {
            $this->code->append(
                sprintf(
                    'if (in_array($method, %s)) {',
                    $this->formatArray($route->getMethod())
                )
            );
        }

        $this->code
            ->append('return new RoutingResult(')
            ->append('true,')
            ->append('false,')
            ->append('false,')
            ->append('[],')
            ->append($this->formatArray($route->getTarget()).',');

        $arrays = $this->getArraysOfParameters($route);
        if (count($arrays) >= 2) {
            $this->code->append(
                sprintf(
                    '$this->filterParameters(array_replace(%s))',
                    implode(', ', $this->getArraysOfParameters($route))
                )
            );
        } else {
            $this->code->append(
                sprintf(
                    '$this->filterParameters(%s)',
                    $this->getArraysOfParameters($route)[0]
                )
            );
        }

        $this->code->append(');');

        if ($route->hasMethod()) {
            $this->code->append('}');
            $this->code->append('else {');
            $this->code->append(
                sprintf(
                    '$allowedMethods = array_merge($allowedMethods, %s);',
                    $this->formatArray($route->getMethod())
                )
            );
            $this->code->append('}');
        }

        $this->code->append('}');
    }

    protected function generateMatchMethod()
    {
        $this->code
            ->append('public function match(Request $request) {')
            ->append('$uri = $request->getUri();')
            ->append('$allowedMethods = [];');

        if ($this->collection->hasMethod()) {
            $this->code->append('$domain = $request->getDomain();');
        }

        if ($this->collection->hasHttps()) {
            $this->code->append('$https = $request->getHttps();');
        }

        if ($this->collection->hasMethod()) {
            $this->code->append('$method = $request->getMethod();');
        }

        foreach ($this->collection->groupByDomain() as $routes) {
            if ($routes->first()->hasDomain()) {
                $this->code->append(
                    sprintf(
                        'if (preg_match(%s, $domain, $domainMatch)) {',
                        var_export(
                            $routes->first()->getCompiled()->getDomainRegex(),
                            true
                        )
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

        $this->code
            ->append('return new RoutingResult(')
            ->append('false,')
            ->append('!empty($allowedMethods),')
            ->append('empty($allowedMethods),')
            ->append('array_unique($allowedMethods),')
            ->append('[],')
            ->append('[]')
            ->append(');');

        $this->code->append('}');
    }

    /**
     * @param string $className
     */
    protected function generateHeader($className)
    {
        $this->code->append(
<<<EOB
            <?php
            use KM\Saffron\UrlMatcher;
            use KM\Saffron\Request;
            use KM\Saffron\RoutingResult;
            use KM\Saffron\UrlMatcher\Base;

            class $className extends Base {
EOB
        );
    }

    protected function generateFooter()
    {
        $this->code->append('}');
    }

    /**
     * @param string $className
     * @return string
     */
    public function generate($className)
    {
        $this->generateHeader($className);
        $this->generateMatchMethod();
        $this->generateFooter();

        return (string)$this->code;
    }
}
