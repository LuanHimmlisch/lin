<?php

namespace LuanHimmlisch\Lin;

use Exception;

class Lin
{
    protected float $a0;
    protected float $a1;
    protected float $a2;
    protected float $a3;
    protected float $a4;
    protected float $b0;
    protected float $b1;
    protected float $b2;
    protected float $r;
    protected float $s;
    protected float $p = 2;
    protected float $q = 2;
    protected array $quotients = [];
    protected int $index = 0;

    protected ?float $delta_p = null;
    protected ?float $delta_q = null;

    protected float $tolerance = 0.001;
    protected string $function;
    protected float $error = 100;

    public function __construct()
    {
    }

    public static function make()
    {
        return new static;
    }

    public function setFunction(string $string): self
    {
        if (!preg_match("/((?:\+|-)?(\d)?x(\^\d)?){4}(?:\+|-)\d/", $string)) {
            throw new Exception("Invalid function", 1);
        }

        $this->function = $string;

        $matches = [];
        preg_match_all("/(\+|-)?\d?x/", $this->function, $matches);

        $this->quotients = array_map(function ($v) {
            preg_match("/(\+|-)?\d?/", $v, $v);
            $v = $v[0];
            return (int) (strlen($v) > 1 ? $v : $v . '1');
        }, $matches[0]);

        return $this;
    }

    public function setTolerance(float $tolerance): self
    {
        $this->tolerance = $tolerance;
        return $this;
    }

    public function execute()
    {
        if (!isset($this->a0)) {
            $this->a0 = round($this->quotients[0], 4);
            $this->a1 = round($this->quotients[1], 4);
            $this->a2 = round($this->quotients[2], 4);
            $this->a3 = round($this->quotients[3], 4);

            $a4 = null;
            preg_match("/(?:\+|-)\d+$/", $this->function, $a4);

            $this->a4 = (float) $a4[0];
        }

        $this->b0 = $this->a0;
        $this->b1 = $this->a1 - $this->p * $this->b0;
        $this->b2 = $this->a2 - $this->p * $this->b1 - $this->q * $this->b0;
        $this->r = $this->a3 - $this->p * $this->b2 - $this->q * $this->b1;
        $this->s = $this->a4 - $this->q * $this->b2;

        $this->prev_delta_p = $this?->delta_p;

        $this->delta_p = $this->r / $this->b2;
        $this->delta_q = $this->s / $this->b2;


        $this->p = $this->p + $this->delta_p;
        $this->q = $this->q + $this->delta_q;

        $this->error = sqrt(pow($this->r, 2) + pow($this->s, 2));
        echo
        "
p: {$this->p}
q: {$this->q}
b0 {$this->b0}
b1: {$this->b1}
b2: {$this->b2}
r: {$this->r}
s: {$this->s}
dp: {$this->delta_p}
dq: {$this->delta_q}
";

        if ($this->error > $this->tolerance) {
            readline("\nPress enter for the next iteration\n");
            $this->index++;
            return $this->execute();
        }
    }
}
