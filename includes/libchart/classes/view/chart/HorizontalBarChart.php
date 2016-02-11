<?php 

class HorizontalBarChart extends BarChart
{
    private $emptyToFullRatio = NULL;

    public function HorizontalBarChart($width = 600, $height = 250)
    {
        parent::barchart($width, $height);
        $this->emptyToFullRatio = 1 / 5;
        $this->plot->setGraphPadding(new Padding(5, 30, 30, 50));
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
            $x = $graphArea->x1 + (($value - $minValue) * ($graphArea->x2 - $graphArea->x1)) / $this->axis->displayDelta;
            imagerectangle($img, $x - 1, $graphArea->y2 + 2, $x, $graphArea->y2 + 3, $palette->axisColor[0]->getColor($img));
            imagerectangle($img, $x - 1, $graphArea->y2, $x, $graphArea->y2 + 1, $palette->axisColor[1]->getColor($img));
            $text->printText($img, $x, $graphArea->y2 + 5, $this->plot->getTextColor(), $value, $text->fontCondensed, $text->HORIZONTAL_CENTER_ALIGN);
            $value += $stepValue;
        }
        $pointList = $this->getFirstSerieOfList();
        $pointCount = count($pointList);
        reset($pointList);
        $rowHeight = ($graphArea->y2 - $graphArea->y1) / $pointCount;
        reset($pointList);
        for( $i = 0; $i <= $pointCount; $i++ ) 
        {
            $y = $graphArea->y2 - $i * $rowHeight;
            imagerectangle($img, $graphArea->x1 - 3, $y, $graphArea->x1 - 2, $y + 1, $palette->axisColor[0]->getColor($img));
            imagerectangle($img, $graphArea->x1 - 1, $y, $graphArea->x1, $y + 1, $palette->axisColor[1]->getColor($img));
            if( $i < $pointCount ) 
            {
                $point = current($pointList);
                next($pointList);
                $label = $point->getX();
                $text->printText($img, $graphArea->x1 - 5, $y - $rowHeight / 2, $this->plot->getTextColor(), $label, $text->fontCondensed, $text->HORIZONTAL_RIGHT_ALIGN | $text->VERTICAL_CENTER_ALIGN);
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
        $minValue = $this->axis->getLowerBoundary();
        $maxValue = $this->axis->getUpperBoundary();
        $stepValue = $this->axis->getTics();
        $barColorSet = $palette->barColorSet;
        $barColorSet->reset();
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
            $rowHeight = ($graphArea->y2 - $graphArea->y1) / $pointCount;
            for( $i = 0; $i < $pointCount; $i++ ) 
            {
                $y = $graphArea->y2 - $i * $rowHeight;
                $point = current($pointList);
                next($pointList);
                $value = $point->getY();
                $xmax = $graphArea->x1 + (($value - $minValue) * ($graphArea->x2 - $graphArea->x1)) / $this->axis->displayDelta;
                $yWithMargin = $y - $rowHeight * $this->emptyToFullRatio;
                $rowWidthWithMargin = $rowHeight * (1 - $this->emptyToFullRatio * 2);
                $barWidth = $rowWidthWithMargin / $serieCount;
                $barOffset = $barWidth * $j;
                $y1 = $yWithMargin - $barWidth - $barOffset;
                $y2 = $yWithMargin - $barOffset - 1;
                $text->printText($img, $xmax + 5, $y2 - $barWidth / 2, $this->plot->getTextColor(), $value, $text->fontCondensed, $text->VERTICAL_CENTER_ALIGN);
                imagefilledrectangle($img, $graphArea->x1 + 1, $y1, $xmax, $y2, $shadowColor->getColor($img));
                if( $graphArea->x1 != $xmax ) 
                {
                    imagefilledrectangle($img, $graphArea->x1 + 2, $y1 + 1, $xmax - 4, $y2, $color->getColor($img));
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


