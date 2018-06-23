<?php
/**
 * Class created only for testing purposes, properties chosen randomly and with no logic
 */
class TestClass
{

    public $a;

    public $b;

    public $c;

    public $d;

    public $e;

    public $f;

    public $g;

    public $h;

    public function __construct()
    {
        $this->a = 'A';
        $this->b = 'B';
        $this->c = new stdClass();
        $this->c->foo = new stdClass();
        $this->c->foo->bar = 'bar';
        $this->c->foo->car = new stdClass();
        $this->c->foo->car->mar = 'mar';
        $this->d = new stdClass();
        $this->d->car = 'cars';
        $this->e = 'E';
        $this->f = 'f';
        $this->g = new stdClass();
        $this->g->gg = 0;
        $this->h = '';
    }
}
