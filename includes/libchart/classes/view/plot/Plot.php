<?php 

class Plot
{
    protected $title = NULL;
    protected $logoFileName = NULL;
    protected $outputArea = NULL;
    protected $outerPadding = NULL;
    protected $imageArea = NULL;
    protected $titleHeight = NULL;
    protected $titlePadding = NULL;
    protected $titleArea = NULL;
    protected $hasCaption = NULL;
    protected $graphCaptionRatio = NULL;
    protected $graphPadding = NULL;
    protected $graphArea = NULL;
    protected $captionPadding = NULL;
    protected $captionArea = NULL;
    protected $text = NULL;
    protected $palette = NULL;
    protected $img = NULL;
    protected $primitive = NULL;
    protected $backGroundColor = NULL;
    protected $textColor = NULL;

    public function Plot($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
        $this->text = new Text();
        $this->palette = new Palette();
        $this->outputArea = new Rectangle(0, 0, $width - 1, $height - 1);
        $this->outerPadding = new Padding(5);
        $this->titleHeight = 26;
        $this->titlePadding = new Padding(5);
        $this->hasCaption = false;
        $this->graphCaptionRatio = 0.5;
        $this->graphPadding = new Padding(50);
        $this->captionPadding = new Padding(15);
    }

    private function computeImageArea()
    {
        $this->imageArea = $this->outputArea->getPaddedRectangle($this->outerPadding);
    }

    private function computeTitleArea()
    {
        $titleUnpaddedBottom = $this->imageArea->y1 + $this->titleHeight + $this->titlePadding->top + $this->titlePadding->bottom;
        $titleArea = new Rectangle($this->imageArea->x1, $this->imageArea->y1, $this->imageArea->x2, $titleUnpaddedBottom - 1);
        $this->titleArea = $titleArea->getPaddedRectangle($this->titlePadding);
    }

    private function computeGraphArea()
    {
        $titleUnpaddedBottom = $this->imageArea->y1 + $this->titleHeight + $this->titlePadding->top + $this->titlePadding->bottom;
        $graphArea = null;
        if( $this->hasCaption ) 
        {
            $graphUnpaddedRight = $this->imageArea->x1 + ($this->imageArea->x2 - $this->imageArea->x1) * $this->graphCaptionRatio + $this->graphPadding->left + $this->graphPadding->right;
            $graphArea = new Rectangle($this->imageArea->x1, $titleUnpaddedBottom, $graphUnpaddedRight - 1, $this->imageArea->y2);
        }
        else
        {
            $graphArea = new Rectangle($this->imageArea->x1, $titleUnpaddedBottom, $this->imageArea->x2, $this->imageArea->y2);
        }

        $this->graphArea = $graphArea->getPaddedRectangle($this->graphPadding);
    }

    private function computeCaptionArea()
    {
        $graphUnpaddedRight = $this->imageArea->x1 + ($this->imageArea->x2 - $this->imageArea->x1) * $this->graphCaptionRatio + $this->graphPadding->left + $this->graphPadding->right;
        $titleUnpaddedBottom = $this->imageArea->y1 + $this->titleHeight + $this->titlePadding->top + $this->titlePadding->bottom;
        $captionArea = new Rectangle($graphUnpaddedRight, $titleUnpaddedBottom, $this->imageArea->x2, $this->imageArea->y2);
        $this->captionArea = $captionArea->getPaddedRectangle($this->captionPadding);
    }

    public function computeLayout()
    {
        $this->computeImageArea();
        $this->computeTitleArea();
        $this->computeGraphArea();
        if( $this->hasCaption ) 
        {
            $this->computeCaptionArea();
        }

    }

    public function createImage()
    {
        $this->img = imagecreatetruecolor($this->width, $this->height);
        $this->primitive = new Primitive($this->img);
        $this->backGroundColor = new Color(255, 255, 255);
        $this->textColor = new Color(0, 0, 0);
        imagefilledrectangle($this->img, 0, 0, $this->width - 1, $this->height - 1, $this->backGroundColor->getColor($this->img));
    }

    public function printTitle()
    {
        $yCenter = $this->titleArea->y1 + ($this->titleArea->y2 - $this->titleArea->y1) / 2;
        $this->text->printCentered($this->img, $yCenter, $this->textColor, $this->title, $this->text->fontCondensedBold);
    }

    public function printLogo()
    {
        $logoImage = @imagecreatefrompng($this->logoFileName);
        if( $logoImage ) 
        {
            imagecopymerge($this->img, $logoImage, 2 * $this->outerPadding->left, $this->outerPadding->top, 0, 0, imagesx($logoImage), imagesy($logoImage), 100);
        }

    }

    public function render($fileName)
    {
        if( isset($fileName) ) 
        {
            imagepng($this->img, $fileName);
        }
        else
        {
            imagepng($this->img);
        }

    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setLogoFileName($logoFileName)
    {
        $this->logoFileName = $logoFileName;
    }

    public function getImg()
    {
        return $this->img;
    }

    public function getPalette()
    {
        return $this->palette;
    }

    public function getText()
    {
        return $this->text;
    }

    public function getPrimitive()
    {
        return $this->primitive;
    }

    public function getOuterPadding()
    {
        return $outerPadding;
    }

    public function setOuterPadding($outerPadding)
    {
        $this->outerPadding = $outerPadding;
    }

    public function setTitleHeight($titleHeight)
    {
        $this->titleHeight = $titleHeight;
    }

    public function setTitlePadding($titlePadding)
    {
        $this->titlePadding = $titlePadding;
    }

    public function setGraphPadding($graphPadding)
    {
        $this->graphPadding = $graphPadding;
    }

    public function setHasCaption($hasCaption)
    {
        $this->hasCaption = $hasCaption;
    }

    public function setCaptionPadding($captionPadding)
    {
        $this->captionPadding = $captionPadding;
    }

    public function setGraphCaptionRatio($graphCaptionRatio)
    {
        $this->graphCaptionRatio = $graphCaptionRatio;
    }

    public function getGraphArea()
    {
        return $this->graphArea;
    }

    public function getCaptionArea()
    {
        return $this->captionArea;
    }

    public function getTextColor()
    {
        return $this->textColor;
    }

}


