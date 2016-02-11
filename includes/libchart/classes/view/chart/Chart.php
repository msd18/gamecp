<?php 

abstract class Chart
{
    protected $dataSet = NULL;
    protected $plot = NULL;

    protected function Chart($width, $height)
    {
        $this->plot = new Plot($width, $height);
        $this->plot->setTitle("Untitled chart");
        $this->plot->setLogoFileName(dirname(__FILE__) . "/../../../images/PoweredBy.png");
    }

    protected function checkDataModel()
    {
        if( !$this->dataSet ) 
        {
            exit( "Error: No dataset defined." );
        }

    }

    protected function createImage()
    {
        $this->plot->createImage();
    }

    public function setDataSet($dataSet)
    {
        $this->dataSet = $dataSet;
    }

    public function getPlot()
    {
        return $this->plot;
    }

    public function setTitle($title)
    {
        $this->plot->setTitle($title);
    }

}


