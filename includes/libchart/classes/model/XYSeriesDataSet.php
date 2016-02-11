<?php 

class XYSeriesDataSet extends DataSet
{
    private $titleList = NULL;
    private $serieList = NULL;

    public function XYSeriesDataSet()
    {
        $this->titleList = array(  );
        $this->serieList = array(  );
    }

    public function addSerie($title, $serie)
    {
        array_push($this->titleList, $title);
        array_push($this->serieList, $serie);
    }

    public function getTitleList()
    {
        return $this->titleList;
    }

    public function getSerieList()
    {
        return $this->serieList;
    }

}


