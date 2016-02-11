<?php 

class PieChart extends Chart
{
    protected $pieCenterX = NULL;
    protected $pieCenterY = NULL;

    public function PieChart($width = 600, $height = 250)
    {
        parent::chart($width, $height);
        $this->plot->setGraphPadding(new Padding(15, 10, 30, 30));
    }

    protected function computeLayout()
    {
        $this->plot->setHasCaption(true);
        $this->plot->computeLayout();
        $graphArea = $this->plot->getGraphArea();
        $this->pieCenterX = $graphArea->x1 + ($graphArea->x2 - $graphArea->x1) / 2;
        $this->pieCenterY = $graphArea->y1 + ($graphArea->y2 - $graphArea->y1) / 2;
        $this->pieWidth = round((($graphArea->x2 - $graphArea->x1) * 4) / 5);
        $this->pieHeight = round((($graphArea->y2 - $graphArea->y1) * 3.7) / 5);
        $this->pieDepth = round($this->pieWidth * 0.05);
    }

    protected function sortPie($v1, $v2)
    {
        return $v1[0] == $v2[0] ? 0 : $v2[0] < $v1[0] ? 0 - 1 : 1;
    }

    protected function computePercent()
    {
        $this->total = 0;
        $this->percent = array(  );
        $pointList = $this->dataSet->getPointList();
        foreach( $pointList as $point ) 
        {
            $this->total += $point->getY();
        }
        foreach( $pointList as $point ) 
        {
            $percent = $this->total == 0 ? 0 : (100 * $point->getY()) / $this->total;
            array_push($this->percent, array( $percent, $point ));
        }
        usort($this->percent, array( "PieChart", "sortPie" ));
    }

    protected function createImage()
    {
        parent::createimage();
        $img = $this->plot->getImg();
        $palette = $this->plot->getPalette();
        $primitive = $this->plot->getPrimitive();
        $graphArea = $this->plot->getGraphArea();
        $primitive->outlinedBox($graphArea->x1, $graphArea->y1, $graphArea->x2, $graphArea->y2, $palette->axisColor[0], $palette->axisColor[1]);
        for( $i = $graphArea->y1 + 2; $i < $graphArea->y2 - 1; $i++ ) 
        {
            $color = $palette->aquaColor[($i + 3) % 4];
            $primitive->line($graphArea->x1 + 2, $i, $graphArea->x2 - 2, $i, $color);
        }
    }

    protected function printCaption()
    {
        $labelList = array(  );
        foreach( $this->percent as $percent ) 
        {
            list($percent, $point) = $percent;
            $label = $point->getX();
            array_push($labelList, $label);
        }
        $caption = new Caption();
        $caption->setPlot($this->plot);
        $caption->setLabelList($labelList);
        $palette = $this->plot->getPalette();
        $pieColorSet = $palette->pieColorSet;
        $caption->setColorSet($pieColorSet);
        $caption->render();
    }

    protected function drawDisc($cy, $colorArray, $mode)
    {
        $img = $this->plot->getImg();
        $i = 0;
        $angle1 = 0;
        $percentTotal = 0;
        foreach( $this->percent as $a ) 
        {
            list($percent, $point) = $a;
            if( $percent <= 0 ) 
            {
                continue;
            }

            $color = $colorArray[$i % count($colorArray)];
            $percentTotal += $percent;
            $angle2 = ($percentTotal * 360) / 100;
            imagefilledarc($img, $this->pieCenterX, $cy, $this->pieWidth, $this->pieHeight, $angle1, $angle2, $color->getColor($img), $mode);
            $angle1 = $angle2;
            $i++;
        }
    }

    protected function drawPercent()
    {
        $img = $this->plot->getImg();
        $palette = $this->plot->getPalette();
        $text = $this->plot->getText();
        $primitive = $this->plot->getPrimitive();
        $angle1 = 0;
        $percentTotal = 0;
        foreach( $this->percent as $a ) 
        {
            list($percent, $point) = $a;
            if( $percent <= 0 ) 
            {
                continue;
            }

            $percentTotal += $percent;
            $angle2 = ($percentTotal * 2 * M_PI) / 100;
            $angle = $angle1 + ($angle2 - $angle1) / 2;
            $label = number_format($percent) . "%";
            $x = (cos($angle) * ($this->pieWidth + 35)) / 2 + $this->pieCenterX;
            $y = (sin($angle) * ($this->pieHeight + 35)) / 2 + $this->pieCenterY;
            $text->printText($img, $x, $y, $this->plot->getTextColor(), $label, $text->fontCondensed, $text->HORIZONTAL_CENTER_ALIGN | $text->VERTICAL_CENTER_ALIGN);
            $angle1 = $angle2;
        }
    }

    protected function printPie()
    {
        $img = $this->plot->getImg();
        $palette = $this->plot->getPalette();
        $text = $this->plot->getText();
        $primitive = $this->plot->getPrimitive();
        $pieColorSet = $palette->pieColorSet;
        $pieColorSet->reset();
        for( $cy = $this->pieCenterY + $this->pieDepth / 2; $this->pieCenterY - $this->pieDepth / 2 <= $cy; $cy-- ) 
        {
            $this->drawDisc($cy, $palette->pieColorSet->shadowColorList, IMG_ARC_EDGED);
        }
        $this->drawDisc($this->pieCenterY - $this->pieDepth / 2, $palette->pieColorSet->colorList, IMG_ARC_PIE);
        $this->drawPercent();
    }

    public function render($fileName = null)
    {
        $this->computePercent();
        $this->computeLayout();
        $this->createImage();
        $this->plot->printLogo();
        $this->plot->printTitle();
        $this->printPie();
        $this->printCaption();
        $this->plot->render($fileName);
    }

}


