<?php 

class gc_xmlparser
{
    public $params = array(  );
    public $root = NULL;
    public $global_index = -1;
    public $fold = false;

    public function gc_xmlparser($input, $xmlParams = array( XML_OPTION_CASE_FOLDING => 0 ))
    {
        $xmlp = xml_parser_create();
        foreach( $xmlParams as $opt => $optVal ) 
        {
            switch( $opt ) 
            {
                case XML_OPTION_CASE_FOLDING:
                    $this->fold = $optVal;
                    break;
                default:
                    break;
            }
            xml_parser_set_option($xmlp, $opt, $optVal);
        }
        if( xml_parse_into_struct($xmlp, $input, $vals, $index) ) 
        {
            $this->root = $this->_foldCase($vals[0]["tag"]);
            $this->params = $this->xml2ary($vals);
        }

        xml_parser_free($xmlp);
    }

    public function _foldCase($arg)
    {
        return $this->fold ? strtoupper($arg) : $arg;
    }

    public function xml2ary($vals)
    {
        $mnary = array(  );
        $ary =& $mnary;
        foreach( $vals as $r ) 
        {
            $t = $r["tag"];
            if( $r["type"] == "open" ) 
            {
                if( isset($ary[$t]) && !empty($ary[$t]) ) 
                {
                    if( isset($ary[$t][0]) ) 
                    {
                        $ary[$t][] = array(  );
                    }
                    else
                    {
                        $ary[$t] = array( $ary[$t], array(  ) );
                    }

                    $cv =& $ary[$t][count($ary[$t]) - 1];
                }
                else
                {
                    $cv =& $ary[$t];
                }

                $cv = array(  );
                if( isset($r["attributes"]) ) 
                {
                    foreach( $r["attributes"] as $k => $v ) 
                    {
                        $cv[$k] = $v;
                    }
                }

                $cv["_p"] =& $ary;
                $ary =& $cv;
            }
            else
            {
                if( $r["type"] == "complete" ) 
                {
                    if( isset($ary[$t]) && !empty($ary[$t]) ) 
                    {
                        if( isset($ary[$t][0]) ) 
                        {
                            $ary[$t][] = array(  );
                        }
                        else
                        {
                            $ary[$t] = array( $ary[$t], array(  ) );
                        }

                        $cv =& $ary[$t][count($ary[$t]) - 1];
                    }
                    else
                    {
                        $cv =& $ary[$t];
                    }

                    if( isset($r["attributes"]) ) 
                    {
                        foreach( $r["attributes"] as $k => $v ) 
                        {
                            $cv[$k] = $v;
                        }
                    }

                    $cv["VALUE"] = isset($r["value"]) ? $r["value"] : "";
                }
                else
                {
                    if( $r["type"] == "close" ) 
                    {
                        $ary =& $ary["_p"];
                    }

                }

            }

        }
        $this->_del_p($mnary);
        return $mnary;
    }

    public function _del_p(&$ary)
    {
        foreach( $ary as $k => $v ) 
        {
            if( $k === "_p" ) 
            {
                unset($ary[$k]);
            }
            else
            {
                if( is_array($ary[$k]) ) 
                {
                    $this->_del_p($ary[$k]);
                }

            }

        }
    }

    public function GetRoot()
    {
        return $this->root;
    }

    public function GetData()
    {
        return $this->params;
    }

}


