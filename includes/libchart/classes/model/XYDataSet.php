<?php 

class XYDataSet extends DataSet
{
    private $pointList = NULL;

    public function XYDataSet()
    {
        $this->pointList = array(  );
    }

    public function addPoint($point)
    {
        array_push($this->pointList, $point);
    }

    public function getPointList()
    {
        return $this->pointList;
    }

}


