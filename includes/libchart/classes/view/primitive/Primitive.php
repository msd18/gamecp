<?php 

class Primitive
{
    private $img = NULL;

    public function Primitive($img)
    {
        $this->img = $img;
    }

    public function line($x1, $y1, $x2, $y2, $color, $width = 1)
    {
        imagefilledpolygon($this->img, array( $x1, $y1 - $width / 2, $x1, $y1 + $width / 2, $x2, $y2 + $width / 2, $x2, $y2 - $width / 2 ), 4, $color->getColor($this->img));
    }

    public function outlinedBox($x1, $y1, $x2, $y2, $color0, $color1)
    {
        imagefilledrectangle($this->img, $x1, $y1, $x2, $y2, $color0->getColor($this->img));
        imagerectangle($this->img, $x1, $y1, $x1 + 1, $y1 + 1, $color1->getColor($this->img));
        imagerectangle($this->img, $x2 - 1, $y1, $x2, $y1 + 1, $color1->getColor($this->img));
        imagerectangle($this->img, $x1, $y2 - 1, $x1 + 1, $y2, $color1->getColor($this->img));
        imagerectangle($this->img, $x2 - 1, $y2 - 1, $x2, $y2, $color1->getColor($this->img));
    }

}


