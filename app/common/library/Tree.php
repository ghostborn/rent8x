<?php

namespace app\common\library;

class Tree
{
    protected $idName = 'id';
    protected $pidName = 'pid';
    protected $subName = 'sub';
    protected $data = [];
    protected $levelName = 'level';

    public function __construct(array $data = [])
    {
        $this->data($data);
    }

    public function data(array $data = [])
    {
        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function tree($data, $pid = 0)
    {
        $result = [];
        foreach ($data as $v) {
            if ($v[$this->pidName] === $pid) {
                $sub = $this->tree($data, $v[$this->idName]);
                $v[$this->subName] = $sub;
                $result[] = $v;
            }
        }
        return $result;
    }


}