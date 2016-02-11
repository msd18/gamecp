<?php 

class gc_XmlBuilder
{
    public $xml = NULL;
    public $indent = NULL;
    public $stack = array(  );

    public function gc_XmlBuilder($indent = "  ")
    {
        $this->indent = $indent;
        $this->xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>" . "\n";
    }

    public function _indent()
    {
        $i = 0;
        for( $j = count($this->stack); $i < $j; $i++ ) 
        {
            $this->xml .= $this->indent;
        }
    }

    public function Push($element, $attributes = array(  ))
    {
        $this->_indent();
        $this->xml .= "<" . $element;
        foreach( $attributes as $key => $value ) 
        {
            $this->xml .= " " . $key . "=\"" . htmlentities($value) . "\"";
        }
        $this->xml .= ">\n";
        $this->stack[] = $element;
    }

    public function Element($element, $content, $attributes = array(  ))
    {
        $this->_indent();
        $this->xml .= "<" . $element;
        foreach( $attributes as $key => $value ) 
        {
            $this->xml .= " " . $key . "=\"" . htmlentities($value) . "\"";
        }
        $this->xml .= ">" . htmlentities($content) . "</" . $element . ">" . "\n";
    }

    public function EmptyElement($element, $attributes = array(  ))
    {
        $this->_indent();
        $this->xml .= "<" . $element;
        foreach( $attributes as $key => $value ) 
        {
            $this->xml .= " " . $key . "=\"" . htmlentities($value) . "\"";
        }
        $this->xml .= " />\n";
    }

    public function Pop($pop_element)
    {
        $element = array_pop($this->stack);
        $this->_indent();
        if( $element !== $pop_element ) 
        {
            exit( "XML Error: Tag Mismatch when trying to close \"" . $pop_element . "\"" );
        }

        $this->xml .= "" . "</" . $element . ">\n";
    }

    public function GetXML()
    {
        if( count($this->stack) != 0 ) 
        {
            exit( "XML Error: No matching closing tag found for \" " . array_pop($this->stack) . "\"" );
        }

        return $this->xml;
    }

}


