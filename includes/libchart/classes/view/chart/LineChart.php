<?php 

class LineChart extends BarChart
{
    public function LineChart($width = 600, $height = 250)
    {
        parent::barchart($width, $height);
        $this->plot->setGraphPadding(new Padding(5, 30, 50, 50));
    }

    protected function computeLayout()
    {
        if( $this->hasSeveralSerie ) 
        {
            $this->plot->setHasCaption(true);
        }

        $this->plot->computeLayout();
    }

    protected function printAxis()
    {
        $minValue = $this->axis->getLowerBoundary();
        $maxValue = $this->axis->getUpperBoundary();
        $stepValue = $this->axis->getTics();
        $img = $this->plot->getImg();
        $palette = $this->plot->getPalette();
        $text = $this->plot->getText();
        $graphArea = $this->plot->getGraphArea();
        $value = $minValue;
        while( $value <= $maxValue ) 
        {
            $y = $graphArea->y2 - (($value - $minValue) * ($graphArea->y2 - $graphArea->y1)) / $this->axis->displayDelta;
            imagerectangle($img, $graphArea->x1 - 3, $y, $graphArea->x1 - 2, $y + 1, $palette->axisColor[0]->getColor($img));
            imagerectangle($img, $graphArea->x1 - 1, $y, $graphArea->x1, $y + 1, $palette->axisColor[1]->getColor($img));
            $text->printText($img, $graphArea->x1 - 5, $y, $this->plot->getTextColor(), $value, $text->fontCondensed, $text->HORIZONTAL_RIGHT_ALIGN | $text->VERTICAL_CENTER_ALIGN);
            $value += $stepValue;
        }
        $pointList = $this->getFirstSerieOfList();
        $pointCount = count($pointList);
        reset($pointList);
        $columnWidth = ($graphArea->x2 - $graphArea->x1) / ($pointCount - 1);
        for( $i = 0; $i < $pointCount; $i++ ) 
        {
            $x = $graphArea->x1 + $i * $columnWidth;
            imagerectangle($img, $x - 1, $graphArea->y2 + 2, $x, $graphArea->y2 + 3, $palette->axisColor[0]->getColor($img));
            imagerectangle($img, $x - 1, $graphArea->y2, $x, $graphArea->y2 + 1, $palette->axisColor[1]->getColor($img));
            $point = current($pointList);
            next($pointList);
            $label = $point->getX();
            $text->printDiagonal($img, $x - 5, $graphArea->y2 + 10, $this->plot->getTextColor(), $label);
        }
    }

    protected function printLine()
    {
        $minValue = $this->axis->getLowerBoundary();
        $maxValue = $this->axis->getUpperBoundary();
        $serieList = $this->getDataAsSerieList();
        $img = $this->plot->getImg();
        $palette = $this->plot->getPalette();
        $text = $this->plot->getText();
        $primitive = $this->plot->getPrimitive();
        $graphArea = $this->plot->getGraphArea();
        $lineColorSet = $palette->lineColorSet;
        $lineColorSet->reset();
        for( $j = 0; $j < count($serieList); $j++ ) 
        {
            $serie = $serieList[$j];
            $pointList = $serie->getPointList();
            $pointCount = count($pointList);
            reset($pointList);
            $columnWidth = ($graphArea->x2 - $graphArea->x1) / ($pointCount - 1);
            $lineColor = $lineColorSet->currentColor();
            $lineColorShadow = $lineColorSet->currentShadowColor();
            $lineColorSet->next();
            $x1 = null;
            $y1 = null;
            for( $i = 0; $i < $pointCount; $i++ ) 
            {
                $x2 = $graphArea->x1 + $i * $columnWidth;
                $point = current($pointList);
                next($pointList);
                $value = $point->getY();
                $y2 = $graphArea->y2 - (($value - $minValue) * ($graphArea->y2 - $graphArea->y1)) / $this->axis->displayDelta;
                if( $x1 ) 
                {
                    $primitive->line($x1, $y1, $x2, $y2, $lineColor, 4);
                    $primitive->line($x1, $y1 - 1, $x2, $y2 - 1, $lineColorShadow, 2);
                }

                $x1 = $x2;
                $y1 = $y2;
            }
        }
    }

    protected function printCaption()
    {
        $labelList = $this->dataSet->getTitleList();
        $caption = new Caption();
        $caption->setPlot($this->plot);
        $caption->setLabelList($labelList);
        $palette = $this->plot->getPalette();
        $lineColorSet = $palette->lineColorSet;
        $caption->setColorSet($lineColorSet);
        $caption->render();
    }

    public function render($fileName = null)
    {
        $this->checkDataModel();
        $this->bound->computeBound($this->dataSet);
        $this->computeAxis();
        $this->computeLayout();
        $this->createImage();
        $this->plot->printLogo();
        $this->plot->printTitle();
        if( !$this->isEmptyDataSet(2) ) 
        {
            $this->printAxis();
            $this->printLine();
            if( $this->hasSeveralSerie ) 
            {
                $this->printCaption();
            }

        }

        $this->plot->render($fileName);
    }

}


