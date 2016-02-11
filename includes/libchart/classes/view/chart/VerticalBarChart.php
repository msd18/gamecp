<?php 

class VerticalBarChart extends BarChart
{
    private $emptyToFullRatio = NULL;

    public function VerticalBarChart($width = 600, $height = 250)
    {
        parent::barchart($width, $height);
        $this->emptyToFullRatio = 1 / 5;
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
        $columnWidth = ($graphArea->x2 - $graphArea->x1) / $pointCount;
        for( $i = 0; $i <= $pointCount; $i++ ) 
        {
            $x = $graphArea->x1 + $i * $columnWidth;
            imagerectangle($img, $x - 1, $graphArea->y2 + 2, $x, $graphArea->y2 + 3, $palette->axisColor[0]->getColor($img));
            imagerectangle($img, $x - 1, $graphArea->y2, $x, $graphArea->y2 + 1, $palette->axisColor[1]->getColor($img));
            if( $i < $pointCount ) 
            {
                $point = current($pointList);
                next($pointList);
                $label = $point->getX();
                $text->printDiagonal($img, $x + ($columnWidth * 1) / 3, $graphArea->y2 + 10, $this->plot->getTextColor(), $label);
            }

        }
    }

    protected function printBar()
    {
        $serieList = $this->getDataAsSerieList();
        $img = $this->plot->getImg();
        $palette = $this->plot->getPalette();
        $text = $this->plot->getText();
        $graphArea = $this->plot->getGraphArea();
        $barColorSet = $palette->barColorSet;
        $barColorSet->reset();
        $minValue = $this->axis->getLowerBoundary();
        $maxValue = $this->axis->getUpperBoundary();
        $stepValue = $this->axis->getTics();
        $serieCount = count($serieList);
        for( $j = 0; $j < $serieCount; $j++ ) 
        {
            $serie = $serieList[$j];
            $pointList = $serie->getPointList();
            $pointCount = count($pointList);
            reset($pointList);
            $color = $barColorSet->currentColor();
            $shadowColor = $barColorSet->currentShadowColor();
            $barColorSet->next();
            $columnWidth = ($graphArea->x2 - $graphArea->x1) / $pointCount;
            for( $i = 0; $i < $pointCount; $i++ ) 
            {
                $x = $graphArea->x1 + $i * $columnWidth;
                $point = current($pointList);
                next($pointList);
                $value = $point->getY();
                $ymin = $graphArea->y2 - (($value - $minValue) * ($graphArea->y2 - $graphArea->y1)) / $this->axis->displayDelta;
                $xWithMargin = $x + $columnWidth * $this->emptyToFullRatio;
                $columnWidthWithMargin = $columnWidth * (1 - $this->emptyToFullRatio * 2);
                $barWidth = $columnWidthWithMargin / $serieCount;
                $barOffset = $barWidth * $j;
                $x1 = $xWithMargin + $barOffset;
                $x2 = ($xWithMargin + $barWidth + $barOffset) - 1;
                $text->printText($img, $x1 + $barWidth / 2, $ymin - 5, $this->plot->getTextColor(), $value, $text->fontCondensed, $text->HORIZONTAL_CENTER_ALIGN | $text->VERTICAL_BOTTOM_ALIGN);
                imagefilledrectangle($img, $x1, $ymin, $x2, $graphArea->y2 - 1, $shadowColor->getColor($img));
                if( $ymin != $graphArea->y2 ) 
                {
                    imagefilledrectangle($img, $x1 + 1, $ymin + 1, $x2 - 4, $graphArea->y2 - 1, $color->getColor($img));
                }

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
        $barColorSet = $palette->barColorSet;
        $caption->setColorSet($barColorSet);
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
        if( !$this->isEmptyDataSet(1) ) 
        {
            $this->printAxis();
            $this->printBar();
            if( $this->hasSeveralSerie ) 
            {
                $this->printCaption();
            }

        }

        $this->plot->render($fileName);
    }

}


