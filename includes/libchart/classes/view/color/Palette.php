<?php 

class Palette
{
    public $red = NULL;
    public $axisColor = NULL;
    public $aquaColor = NULL;
    public $barColorSet = NULL;
    public $lineColorSet = NULL;
    public $pieColorSet = NULL;

    public function Palette()
    {
        $this->red = new Color(255, 0, 0);
        $this->axisColor = array( new Color(201, 201, 201), new Color(158, 158, 158) );
        $this->aquaColor = array( new Color(242, 242, 242), new Color(231, 231, 231), new Color(239, 239, 239), new Color(253, 253, 253) );
        $this->barColorSet = new ColorSet(array( new Color(42, 71, 181), new Color(243, 198, 118), new Color(128, 63, 35), new Color(195, 45, 28), new Color(224, 198, 165), new Color(239, 238, 218), new Color(40, 72, 59), new Color(71, 112, 132), new Color(167, 192, 199), new Color(218, 233, 202) ), 0.75);
        $this->lineColorSet = new ColorSet(array( new Color(172, 172, 210), new Color(2, 78, 0), new Color(148, 170, 36), new Color(233, 191, 49), new Color(240, 127, 41), new Color(243, 63, 34), new Color(190, 71, 47), new Color(135, 81, 60), new Color(128, 78, 162), new Color(121, 75, 255), new Color(142, 165, 250), new Color(162, 254, 239), new Color(137, 240, 166), new Color(104, 221, 71), new Color(98, 174, 35), new Color(93, 129, 1) ), 0.75);
        $this->pieColorSet = new ColorSet(array( new Color(2, 78, 0), new Color(148, 170, 36), new Color(233, 191, 49), new Color(240, 127, 41), new Color(243, 63, 34), new Color(190, 71, 47), new Color(135, 81, 60), new Color(128, 78, 162), new Color(121, 75, 255), new Color(142, 165, 250), new Color(162, 254, 239), new Color(137, 240, 166), new Color(104, 221, 71), new Color(98, 174, 35), new Color(93, 129, 1) ), 0.7);
    }

}


