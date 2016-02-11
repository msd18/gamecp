<?php 

class Color
{
    private $red = NULL;
    private $green = NULL;
    private $blue = NULL;
    private $alpha = NULL;
    private $gdColor = NULL;

    public function Color($red, $green, $blue, $alpha = 0)
    {
        $this->red = (int) $red;
        $this->green = (int) $green;
        $this->blue = (int) $blue;
        $this->alpha = (int) round(($alpha * 127) / 255);
        $this->gdColor = null;
    }

    public function getColor($img)
    {
        if( !$this->gdColor ) 
        {
            if( $this->alpha == 0 || !function_exists("imagecolorallocatealpha") ) 
            {
                $this->gdColor = imagecolorallocate($img, $this->red, $this->green, $this->blue);
            }
            else
            {
                $this->gdColor = imagecolorallocatealpha($img, $this->red, $this->green, $this->blue, $this->alpha);
            }

        }

        return $this->gdColor;
    }

    public function clip($component)
    {
        if( $component < 0 ) 
        {
            $component = 0;
        }
        else
        {
            if( 255 < $component ) 
            {
                $component = 255;
            }

        }

        return $component;
    }

    public function getShadowColor($shadowFactor)
    {
        $red = $this->clip($this->red * $shadowFactor);
        $green = $this->clip($this->green * $shadowFactor);
        $blue = $this->clip($this->blue * $shadowFactor);
        $shadowColor = new Color($red, $green, $blue);
        return $shadowColor;
    }

}


