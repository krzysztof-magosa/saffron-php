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
namespace KM\Saffron;

use KM\Saffron\Route;
use KM\Saffron\RouteCompiled;

class RouteCompiler
{
    /**
     * @param Route $route
     * @return string
     */
    protected function getPrefix(Route $route)
    {
        // @TODO make it not ugly :)
        $pos = strpos($route->getUri(), '{');

        if (false !== $pos) {
            $length = max($pos - 1, 1);
        }
        else {
            $length = strlen($route->getUri());
        }

        return substr($route->getUri(), 0, $length);
    }

    /**
     * @param Route $route
     * @return string
     */
    protected function getUriRegex(Route $route)
    {
        $tokens = preg_split(
            '#([^}]?\{\w+\})#s',
            substr($route->getUri(), 1),
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        $regex = preg_quote(substr($route->getUri(), 0, 1), '#');
        foreach ($tokens as $token) {
            if (preg_match('#^(?P<delimiter>.)?\{(?P<placeholder>\w+)\}$#s', $token, $match)) {
                $regex .= sprintf(
                    '(%s(?P<%s>%s))%s',
                    isset($match['delimiter']) ? preg_quote($match['delimiter'], '#') : '',
                    preg_quote($match['placeholder'], '#'),
                    $route->getRequirement($match['placeholder']),
                    $route->hasDefault($match['placeholder']) ? '?' : ''
                );
            }
            else {
                $regex .= preg_quote($token, '#');
            }
        }

        return '#^'.$regex.'$#s';
    }

    /**
     * @param Route $route
     * @return string
     */
    protected function getDomainRegex(Route $route)
    {
        $tokens = preg_split(
            '#({\w+\}[^{]?)#s',
            $route->getDomain(),
            -1,
            PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
        );

        $regex = '';
        foreach ($tokens as $token) {
            if (preg_match('#^\{(?P<placeholder>\w+)\}(?P<delimiter>.)?$#s', $token, $match)) {
                $regex .= sprintf(
                    '((?P<%s>%s)%s)%s',
                    preg_quote($match['placeholder'], '#'),
                    $route->getRequirement($match['placeholder']),
                    isset($match['delimiter']) ? preg_quote($match['delimiter'], '#') : '',
                    $route->hasDefault($match['placeholder']) ? '?' : ''
                );
            }
            else {
                $regex .= preg_quote($token, '#');
            }
        }

        return '#^'.$regex.'$#s';
    }

    /**
     * @param Route $route
     * @return RouteCompiled
     */
    public function compile(Route $route)
    {
        $compiled = new RouteCompiled(
            $this->getPrefix($route),
            $this->getPrefix($route) != $route->getUri() ? $this->getUriRegex($route) : null,
            $route->hasDomain() ? $this->getDomainRegex($route) : null
        );

        return $compiled;
    }
}
