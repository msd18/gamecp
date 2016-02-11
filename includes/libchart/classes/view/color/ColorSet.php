<?php 

class ColorSet
{
    public $colorList = NULL;
    public $shadowColorList = NULL;

    public function ColorSet($colorList, $shadowFactor)
    {
        $this->colorList = $colorList;
        $this->shadowColorList = array(  );
        foreach( $colorList as $color ) 
        {
            $shadowColor = $color->getShadowColor($shadowFactor);
            array_push($this->shadowColorList, $shadowColor);
        }
    }

    public function reset()
    {
        reset($this->colorList);
        reset($this->shadowColorList);
    }

    public function next()
    {
        $value = next($this->colorList);
        next($this->shadowColorList);
        if( $value == FALSE ) 
        {
            $this->reset();
        }

    }

    public function currentColor()
    {
        return current($this->colorList);
    }

    public function currentShadowColor()
    {
        return current($this->shadowColorList);
    }

}


