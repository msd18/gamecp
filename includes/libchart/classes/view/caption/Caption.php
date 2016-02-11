<?php 

class Caption
{
    protected $labelBoxWidth = NULL;
    protected $labelBoxHeight = NULL;
    protected $plot = NULL;
    protected $labelList = NULL;
    protected $colorSet = NULL;

    public function Caption()
    {
        $this->labelBoxWidth = 15;
        $this->labelBoxHeight = 15;
    }

    public function render()
    {
        $img = $this->plot->getImg();
        $palette = $this->plot->getPalette();
        $text = $this->plot->getText();
        $primitive = $this->plot->getPrimitive();
        $captionArea = $this->plot->getCaptionArea();
        $colorSet = $this->colorSet;
        $colorSet->reset();
        $i = 0;
        foreach( $this->labelList as $label ) 
        {
            $color = $colorSet->currentColor();
            $colorSet->next();
            $boxX1 = $captionArea->x1;
            $boxX2 = $boxX1 + $this->labelBoxWidth;
            $boxY1 = $captionArea->y1 + 5 + $i * ($this->labelBoxHeight + 5);
            $boxY2 = $boxY1 + $this->labelBoxHeight;
            $primitive->outlinedBox($boxX1, $boxY1, $boxX2, $boxY2, $palette->axisColor[0], $palette->axisColor[1]);
            imagefilledrectangle($img, $boxX1 + 2, $boxY1 + 2, $boxX2 - 2, $boxY2 - 2, $color->getColor($img));
            $text->printText($img, $boxX2 + 5, $boxY1 + $this->labelBoxHeight / 2, $this->plot->getTextColor(), $label, $text->fontCondensed, $text->VERTICAL_CENTER_ALIGN);
            $i++;
        }
    }

    public function setPlot($plot)
    {
        $this->plot = $plot;
    }

    public function setLabelList($labelList)
    {
        $this->labelList = $labelList;
    }

    public function setColorSet($colorSet)
    {
        $this->colorSet = $colorSet;
    }

}


