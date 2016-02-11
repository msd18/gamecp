<?php 

class Bound
{
    private $lowerBound = null;
    private $upperBound = null;
    private $yMinValue = null;
    private $yMaxValue = null;

    public function computeBound($dataSet)
    {
        $dataSetEmpty = true;
        $serieList = null;
        if( $dataSet instanceof XYDataSet ) 
        {
            $pointList = $dataSet->getPointList();
            $dataSetEmpty = count($pointList) == 0;
            if( !$dataSetEmpty ) 
            {
                $serieList = array(  );
                array_push($serieList, $dataSet);
            }

        }
        else
        {
            if( $dataSet instanceof XYSeriesDataSet ) 
            {
                $serieList = $dataSet->getSerieList();
                if( 0 < count($serieList) ) 
                {
                    $serie = current($serieList);
                    $dataSetEmpty = count($serie) == 0;
                }

            }
            else
            {
                exit( "Error: unknown dataset type" );
            }

        }

        $yMin = 0;
        $yMax = 1;
        if( !$dataSetEmpty ) 
        {
            unset($yMin);
            unset($yMax);
            foreach( $serieList as $serie ) 
            {
                foreach( $serie->getPointList() as $point ) 
                {
                    $y = $point->getY();
                    if( !isset($yMin) ) 
                    {
                        $yMin = $y;
                        $yMax = $y;
                    }
                    else
                    {
                        if( $y < $yMin ) 
                        {
                            $yMin = $y;
                        }

                        if( $yMax < $y ) 
                        {
                            $yMax = $y;
                        }

                    }

                }
            }
        }

        if( isset($this->lowerBound) && $this->lowerBound < $yMin ) 
        {
            $this->yMinValue = $this->lowerBound;
        }
        else
        {
            $this->yMinValue = $yMin;
        }

        if( isset($this->upperBound) && $yMax < $this->upperBound ) 
        {
            $this->yMaxValue = $this->upperBound;
        }
        else
        {
            $this->yMaxValue = $yMax;
        }

    }

    public function getYMinValue()
    {
        return $this->yMinValue;
    }

    public function getYMaxValue()
    {
        return $this->yMaxValue;
    }

    public function setLowerBound($lowerBound)
    {
        $this->lowerBound = $lowerBound;
    }

    public function setUpperBound($upperBound)
    {
        $this->upperBound = $upperBound;
    }

}


