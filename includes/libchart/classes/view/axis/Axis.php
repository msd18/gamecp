<?php 

class Axis
{
    private $min = NULL;
    private $max = NULL;
    private $guide = NULL;
    private $delta = NULL;
    private $magnitude = NULL;
    private $displayMin = NULL;
    private $displayMax = NULL;
    private $tics = NULL;

    public function Axis($min, $max)
    {
        $this->min = $min;
        $this->max = $max;
        $this->guide = 10;
    }

    public function quantizeTics()
    {
        $norm = $this->delta / $this->magnitude;
        $posns = $this->guide / $norm;
        if( 20 < $posns ) 
        {
            $tics = 0.05;
        }
        else
        {
            if( 10 < $posns ) 
            {
                $tics = 0.2;
            }
            else
            {
                if( 5 < $posns ) 
                {
                    $tics = 0.4;
                }
                else
                {
                    if( 3 < $posns ) 
                    {
                        $tics = 0.5;
                    }
                    else
                    {
                        if( 2 < $posns ) 
                        {
                            $tics = 1;
                        }
                        else
                        {
                            if( 0.25 < $posns ) 
                            {
                                $tics = 2;
                            }
                            else
                            {
                                $tics = ceil($norm);
                            }

                        }

                    }

                }

            }

        }

        $this->tics = $tics * $this->magnitude;
    }

    public function computeBoundaries()
    {
        $this->delta = abs($this->max - $this->min);
        if( $this->delta == 0 ) 
        {
            $this->delta = 1;
        }

        $this->magnitude = pow(10, floor(log10($this->delta)));
        $this->quantizeTics();
        $this->displayMin = floor($this->min / $this->tics) * $this->tics;
        $this->displayMax = ceil($this->max / $this->tics) * $this->tics;
        $this->displayDelta = $this->displayMax - $this->displayMin;
        if( $this->displayDelta == 0 ) 
        {
            $this->displayDelta = 1;
        }

    }

    public function getLowerBoundary()
    {
        return $this->displayMin;
    }

    public function getUpperBoundary()
    {
        return $this->displayMax;
    }

    public function getTics()
    {
        return $this->tics;
    }

}


