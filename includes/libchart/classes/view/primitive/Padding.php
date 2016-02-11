<?php 

class Padding
{
    public $top = NULL;
    public $right = NULL;
    public $bottom = NULL;
    public $left = NULL;

    public function Padding($top, $right = null, $bottom = null, $left = null)
    {
        $this->top = $top;
        if( $right == null ) 
        {
            $this->right = $top;
            $this->bottom = $top;
            $this->left = $top;
        }
        else
        {
            $this->right = $right;
            $this->bottom = $bottom;
            $this->left = $left;
        }

    }

}


