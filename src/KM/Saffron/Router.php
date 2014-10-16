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

class Router
{
    protected $factory;
    protected $urlMatcher;
    protected $urlBuilder;

    public function __construct(RouterFactory $factory)
    {
        $this->factory = $factory;
    }

    protected function getUrlMatcher()
    {
        if (!$this->urlMatcher) {
            $this->urlMatcher = $this->factory->getUrlMatcher();
        }

        return $this->urlMatcher;
    }

    protected function getUrlBuilder()
    {
        if (!$this->urlBuilder) {
            $this->urlBuilder = $this->factory->getUrlBuilder();
        }

        return $this->urlBuilder;
    }

    public function match(Request $request)
    {
        return $this->getUrlMatcher()->match($request);
    }

    /**
     * Builds the link for named route.
     * 
     * @param string $name Name of route
     * @param array $parameters Parameters to be put into link
     * @return string Builded link
     */
    public function assemble($name, array $parameters = [])
    {
        return $this->getUrlBuilder()->assemble($name, $parameters);
    }
}
