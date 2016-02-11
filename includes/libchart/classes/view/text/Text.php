<?php 

class Text
{
    public $HORIZONTAL_LEFT_ALIGN = 1;
    public $HORIZONTAL_CENTER_ALIGN = 2;
    public $HORIZONTAL_RIGHT_ALIGN = 4;
    public $VERTICAL_TOP_ALIGN = 8;
    public $VERTICAL_CENTER_ALIGN = 16;
    public $VERTICAL_BOTTOM_ALIGN = 32;

    public function Text()
    {
        $baseDir = dirname(__FILE__) . "/../../../";
        $this->fontCondensed = $baseDir . "fonts/DejaVuSansCondensed.ttf";
        $this->fontCondensedBold = $baseDir . "fonts/DejaVuSansCondensed-Bold.ttf";
    }

    public function printText($img, $px, $py, $color, $text, $fontFileName, $align = 0)
    {
        if( !($align & $this->HORIZONTAL_CENTER_ALIGN) && !($align & $this->HORIZONTAL_RIGHT_ALIGN) ) 
        {
            $align |= $this->HORIZONTAL_LEFT_ALIGN;
        }

        if( !($align & $this->VERTICAL_CENTER_ALIGN) && !($align & $this->VERTICAL_BOTTOM_ALIGN) ) 
        {
            $align |= $this->VERTICAL_TOP_ALIGN;
        }

        $fontSize = 6;
        $lineSpacing = 4;
        list($llx, $lly, $lrx, $lry, $urx, $ury, $ulx, $uly) = imageftbbox($fontSize, 0, $fontFileName, $text, array( "linespacing" => $lineSpacing ));
        $textWidth = $lrx - $llx;
        $textHeight = $lry - $ury;
        $angle = 0;
        if( $align & $this->HORIZONTAL_CENTER_ALIGN ) 
        {
            $px -= $textWidth / 2;
        }

        if( $align & $this->HORIZONTAL_RIGHT_ALIGN ) 
        {
            $px -= $textWidth;
        }

        if( $align & $this->VERTICAL_CENTER_ALIGN ) 
        {
            $py += $textHeight / 2;
        }

        if( $align & $this->VERTICAL_TOP_ALIGN ) 
        {
            $py += $textHeight;
        }

        imagettftext($img, $fontSize, $angle, $px, $py, $color->getColor($img), $fontFileName, $text);
    }

    public function printCentered($img, $py, $color, $text, $fontFileName)
    {
        $this->printText($img, imagesx($img) / 2, $py, $color, $text, $fontFileName, $this->HORIZONTAL_CENTER_ALIGN | $this->VERTICAL_CENTER_ALIGN);
    }

    public function printDiagonal($img, $px, $py, $color, $text)
    {
        $fontSize = 7.5;
        $fontFileName = $this->fontCondensed;
        $lineSpacing = 1;
        list($lx, $ly, $rx, $ry) = imageftbbox($fontSize, 0, $fontFileName, $text, array( "linespacing" => $lineSpacing ));
        $textWidth = $rx - $lx;
        $angle = 0 - 45;
        imagettftext($img, $fontSize, $angle, $px, $py, $color->getColor($img), $fontFileName, $text);
    }

}


