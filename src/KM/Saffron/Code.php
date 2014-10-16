<?php
namespace KM\Saffron;

class Code
{
    protected $code = '';
    protected $tabSize = 4;

    public function append($text)
    {
        $this->code .= $text."\n";
    }

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
