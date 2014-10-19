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

abstract class Generator
{
    /**
     * Formats array into form parsable by php.
     * Why not just var_export? Because output is hard to read and debug.
     *
     * @param array $data Array to be formatted
     * @return string Formatted string
     */
    protected function formatArray(array $data)
    {
        $pairs = [];
        foreach ($data as $key => $value) {
            $pairs[] = sprintf(
                '%s => %s',
                var_export($key, true),
                var_export($value, true)
            );
        }

        return '['.implode(', ', $pairs).']';
    }

    abstract public function generate($className);
}
