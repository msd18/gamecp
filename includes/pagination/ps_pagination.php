<?php 

class PS_Pagination
{
    public $php_self = NULL;
    public $rows_per_page = NULL;
    public $total_rows = NULL;
    public $links_per_page = NULL;
    public $sql = NULL;
    public $sql_p2 = NULL;
    public $debug = true;
    public $conn = NULL;
    public $page = NULL;
    public $max_pages = NULL;
    public $offset = NULL;
    public $page_name = NULL;
    public $absolute_path = NULL;
    public $query_id = NULL;
    public $query_count = NULL;

    public function PS_Pagination($connection, $sql, $sql_p2, $rows_per_page = 10, $links_per_page = 5, $url, $query_count = "")
    {
        $this->conn = $connection;
        $this->sql = $sql;
        $this->sql_p2 = $sql_p2;
        $this->rows_per_page = $rows_per_page;
        $this->links_per_page = $links_per_page;
        $this->php_self = $url;
        $this->absolute_path = str_replace("pagination", "", dirname(__FILE__));
        if( DIRECTORY_SEPARATOR == "\\" ) 
        {
            $this->absolute_path = str_replace("\\", "/", $this->absolute_path);
        }

        $this->query_id = $_GET["do"] . "_" . md5($sql);
        $this->query_count = $query_count;
        if( isset($_GET["page_gen"]) ) 
        {
            $this->page = intval($_GET["page_gen"]);
        }

    }

    public function writeCache($content, $filename)
    {
        $fp = fopen($this->absolute_path . "cache/" . $filename, "w");
        fwrite($fp, $content);
        fclose($fp);
    }

    public function readCache($filename, $expiry)
    {
        if( file_exists($this->absolute_path . "cache/" . $filename) ) 
        {
            if( filemtime($this->absolute_path . "cache/" . $filename) < time() - $expiry ) 
            {
                return FALSE;
            }

            $cache = file($this->absolute_path . "cache/" . $filename);
            return implode("", $cache);
        }

        return FALSE;
    }

    public function paginate()
    {
        global $out;
        if( !$this->conn ) 
        {
            if( $this->debug ) 
            {
                $out .= "MSSQL connection missing<br />";
            }

            return false;
        }

        if( $this->query_count == "" ) 
        {
            if( !($this->total_rows = $this->readCache($this->query_id . ".cache", 600)) ) 
            {
                $all_rs = @mssql_query($this->sql);
                if( !$all_rs ) 
                {
                    if( $this->debug ) 
                    {
                        $out .= "SQL query failed. Check your query.<br />";
                    }

                    return false;
                }

                $this->total_rows = mssql_num_rows($all_rs);
                $this->writeCache($this->total_rows, $this->query_id . ".cache");
                @mssql_close($all_rs);
            }

        }
        else
        {
            $this->total_rows = $this->query_count;
        }

        $this->max_pages = ceil($this->total_rows / $this->rows_per_page);
        if( $this->max_pages < $this->page || $this->page <= 0 ) 
        {
            $this->page = 1;
        }

        $this->offset = $this->rows_per_page * ($this->page - 1);
        $new_query_strip = str_replace("SELECT", "" . "SELECT TOP " . $this->rows_per_page, $this->sql);
        $new_queryp2_strip = str_replace("[OFFSET]", "" . $this->offset, $this->sql_p2);
        $new_query = $new_query_strip . " " . $new_queryp2_strip;
        $rs = @mssql_query($new_query, $this->conn);
        if( !$rs ) 
        {
            if( $this->debug ) 
            {
                $out .= "Pagination query failed. Check your query.<br />" . mssql_get_last_message();
            }

            return false;
        }

        return $rs;
    }

    public function renderFirst($tag = "First")
    {
        if( $this->page == 1 ) 
        {
            return "";
        }

        return "<a href=\"" . $this->php_self . "&page_gen=1\"><b>" . $tag . "</b></a>&nbsp;";
    }

    public function renderLast($tag = "Last")
    {
        if( $this->page == $this->max_pages ) 
        {
            return "";
        }

        return "<a href=\"" . $this->php_self . "&page_gen=" . $this->max_pages . "\"><b>" . $tag . "</b></a>";
    }

    public function renderNext($tag = " &gt;&gt;")
    {
        if( $this->page < $this->max_pages ) 
        {
            return "<a href=\"" . $this->php_self . "&page_gen=" . ($this->page + 1) . "\">" . $tag . "</a>&nbsp;";
        }

        return "";
    }

    public function renderPrev($tag = "&lt;&lt;")
    {
        if( 1 < $this->page ) 
        {
            return "<a href=\"" . $this->php_self . "&page_gen=" . ($this->page - 1) . "\">" . $tag . "</a>";
        }

        return "";
    }

    public function renderNav()
    {
        $start = 0;
        $i = 1;
        while( $i <= $this->max_pages ) 
        {
            if( $i <= $this->page ) 
            {
                $start = $i;
            }

            $i += $this->links_per_page;
        }
        if( $this->links_per_page < $this->max_pages ) 
        {
            $end = $start + $this->links_per_page;
            if( $this->max_pages < $end ) 
            {
                $end = $this->max_pages + 1;
            }

        }
        else
        {
            $end = $this->max_pages + 1;
        }

        $links = "";
        for( $i = $start; $i < $end; $i++ ) 
        {
            if( $i == $this->page ) 
            {
                $links .= "" . " <b>" . $i . "</b> ";
            }
            else
            {
                $links .= " <a href=\"" . $this->php_self . "&page_gen=" . $i . "\">" . $i . "</a> ";
            }

        }
        return $links;
    }

    public function renderFullNav()
    {
        global $rs;
        if( isset($rs) ) 
        {
            @mssql_free_result($rs);
        }

        return "" . "[Page " . $this->page . " of " . $this->max_pages . "] " . $this->renderFirst() . $this->renderPrev() . $this->renderNav() . $this->renderNext() . $this->renderLast();
    }

    public function setDebug($debug)
    {
        $this->debug = $debug;
    }

    public function totalResults()
    {
        return $this->total_rows;
    }

}


