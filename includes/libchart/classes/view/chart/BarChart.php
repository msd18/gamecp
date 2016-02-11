<?php 

abstract class BarChart extends Chart
{
    protected $bound = NULL;
    protected $axis = NULL;
    protected $hasSeveralSerie = NULL;

    protected function BarChart($width, $height)
    {
        parent::chart($width, $height);
        $this->bound = new Bound();
        $this->bound->setLowerBound(0);
    }

    protected function computeAxis()
    {
        $this->axis = new Axis($this->bound->getYMinValue(), $this->bound->getYMaxValue());
        $this->axis->computeBoundaries();
    }

    protected function createImage()
    {
        parent::createimage();
        $img = $this->plot->getImg();
        $palette = $this->plot->getPalette();
        $text = $this->plot->getText();
        $primitive = $this->plot->getPrimitive();
        $graphArea = $this->plot->getGraphArea();
        for( $i = $graphArea->y1; $i < $graphArea->y2; $i++ ) 
        {
            $color = $palette->aquaColor[($i + 3) % 4];
            $primitive->line($graphArea->x1, $i, $graphArea->x2, $i, $color);
        }
        imagerectangle($img, $graphArea->x1 - 1, $graphArea->y1, $graphArea->x1, $graphArea->y2, $palette->axisColor[0]->getColor($img));
        imagerectangle($img, $graphArea->x1 - 1, $graphArea->y2, $graphArea->x2, $graphArea->y2 + 1, $palette->axisColor[0]->getColor($img));
    }

    protected function isEmptyDataSet($minNumberOfPoint)
    {
        if( $this->dataSet instanceof XYDataSet ) 
        {
            $pointList = $this->dataSet->getPointList();
            $pointCount = count($pointList);
            return $pointCount < $minNumberOfPoint;
        }

        if( $this->dataSet instanceof XYSeriesDataSet ) 
        {
            $serieList = $this->dataSet->getSerieList();
            reset($serieList);
            if( 0 < count($serieList) ) 
            {
                $serie = current($serieList);
                $pointList = $serie->getPointList();
                $pointCount = count($pointList);
                return $pointCount < $minNumberOfPoint;
            }

        }
        else
        {
            exit( "Error: unknown dataset type" );
        }

    }

    protected function checkDataModel()
    {
        if( !$this->dataSet ) 
        {
            exit( "Error: No dataset defined." );
        }

        if( $this->dataSet instanceof XYDataSet ) 
        {
            $this->hasSeveralSerie = false;
        }
        else
        {
            if( $this->dataSet instanceof XYSeriesDataSet ) 
            {
                unset($lastPointCount);
                $serieList = $this->dataSet->getSerieList();
                for( $i = 0; $i < count($serieList); $i++ ) 
                {
                    $serie = $serieList[$i];
                    $pointCount = count($serie->getPointList());
                    if( isset($lastPointCount) && $pointCount != $lastPointCount ) 
                    {
                        exit( "Error: serie <" . $i . "> doesn't have the same number of points as last serie (last one: <" . $lastPointCount . ">, this one: <" . $pointCount . ">)." );
                    }

                    $lastPointCount = $pointCount;
                }
                $this->hasSeveralSerie = true;
            }
            else
            {
                exit( "Error: Bar chart accept only XYDataSet and XYSeriesDataSet" );
            }

        }

    }

    protected function getDataAsSerieList()
    {
        $serieList = null;
        if( $this->dataSet instanceof XYSeriesDataSet ) 
        {
            $serieList = $this->dataSet->getSerieList();
        }
        else
        {
            if( $this->dataSet instanceof XYDataSet ) 
            {
                $serieList = array(  );
                array_push($serieList, $this->dataSet);
            }

        }

        return $serieList;
    }

    protected function getFirstSerieOfList()
    {
        $pointList = null;
        if( $this->dataSet instanceof XYSeriesDataSet ) 
        {
            $serieList = $this->dataSet->getSerieList();
            reset($serieList);
            $serie = current($serieList);
            $pointList = $serie->getPointList();
        }
        else
        {
            if( $this->dataSet instanceof XYDataSet ) 
            {
                $pointList = $this->dataSet->getPointList();
            }

        }

        return $pointList;
    }

    public function getBound()
    {
        return $this->bound;
    }

}


