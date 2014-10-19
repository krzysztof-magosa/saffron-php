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

/**
 * Simple class to do basic formatting of generated PHP code.
 * It's here just for easier debugging of generated code.
 * There is no plan to make it full code formatter.
 */
class Code
{
    protected $code = '';
    protected $tabSize = 4;

    /**
     * @param string $text Line of code
     * @return Code
     */
    public function append($text)
    {
        $this->code .= $text."\n";
        return $this;
    }

    /**
     * @return strign Formatted code
     */
    public function __toString()
    {
        $lines = explode("\n", $this->code);
        $indent = 0;

        $result = '';
        foreach ($lines as $line) {
            $line = trim($line);

            if (preg_match('#{|\($#', $line)) {
                $result .= str_repeat(' ', $indent);
                $result .= $line."\n";
                $indent += $this->tabSize;
            }
            elseif (preg_match('#}$#', $line) || preg_match('#^\);$#', $line)) {
                $indent = max(0, $indent - $this->tabSize);
                $indent = max($indent, 0);
                $result .= str_repeat(' ', $indent);
                $result .= $line."\n";
            }
            else {
                $result .= str_repeat(' ', $indent);
                $result .= $line."\n";
            }
        }

        return $result;
    }
}
