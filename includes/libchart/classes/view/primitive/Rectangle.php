<?php 

class Rectangle
{
    public $x1 = NULL;
    public $y1 = NULL;
    public $x2 = NULL;
    public $y2 = NULL;

    public function Rectangle($x1, $y1, $x2, $y2)
    {
        $this->x1 = $x1;
        $this->y1 = $y1;
        $this->x2 = $x2;
        $this->y2 = $y2;
    }

    public function getPaddedRectangle($padding)
    {
        $rectangle = new Rectangle($this->x1 + $padding->left, $this->y1 + $padding->top, $this->x2 - $padding->right, $this->y2 - $padding->bottom);
        return $rectangle;
    }

}


