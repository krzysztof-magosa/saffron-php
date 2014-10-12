<?php
namespace KM;

class Benchmark
{
    protected $iterations = 1000;
    protected $barWidth = 60;
    protected $timings = [];

    public function execute($name, \Closure $func)
    {
        $start = microtime(true);
        for ($i = 1; $i <= $this->iterations; $i++) {
            $func();
        }
        $stop = microtime(true);

        $this->timings[$name] = $stop - $start;

        return $this;
    }

    public function summary()
    {
        asort($this->timings, SORT_NUMERIC);
        $max = max($this->timings);

        foreach ($this->timings as $name => $timing) {
            $speed = $this->iterations / $timing / 1000;
            $bar = ($max - $timing) / $max * ($this->barWidth-1);

            echo sprintf(
                "%s %s %s\n",
                sprintf('%13.s', $name),
                sprintf('%5.2f k/s', $speed),
                str_repeat('▒', $bar+1)
            );
        }
    }
}
