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

use KM\Saffron\Exception\EmptyCollection;

abstract class Collection extends \ArrayIterator
{
    /**
     * Returns first element in Collection
     * @return mixed
     */
    public function first()
    {
        if (null === ($key = $this->getFirstKey())) {
            throw new EmptyCollection('You cannot fetch first element of empty collection.');
        }

        return $this[$key];
    }

    /**
     * Returns first key in Collection
     * @return mixed
     */
    protected function getFirstKey()
    {
        foreach ($this as $key => $value) {
            return $key;
        }

        return null;
    }

    /**
     * Returns keys of elements in Collection
     */
    public function getKeys()
    {
        $keys = [];

        foreach ($this as $key => $value) {
            $keys[] = $key;
        }

        return $keys;
    }

    /**
     * Creates nested Collection for each group.
     * $func closure needs to return unique value for each group.
     * @param \Closure $func
     * @return Collection
     */
    protected function groupBy(\Closure $func)
    {
        $class = get_called_class();

        $result = new $class;
        foreach ($this as $item) {
            $key = $func($item);

            if (!isset($result[$key])) {
                $result[$key] = new $class;
            }

            $result[$key]->append($item);
        }

        return $result;
    }

    /**
     * Checks whether Collection has something.
     * Based on value returned by $func Closure.
     * @param \Closure $func
     * @return bool
     */
    protected function has(\Closure $func)
    {
        foreach ($this as $item) {
            if ($func($item)) {
                return true;
            }
        }

        return false;
    }
}
