<?php 
define("L_OFF", 0);
define("L_ERR", 1);
define("L_RQST", 2);
define("L_RESP", 4);
define("L_ERR_RQST", L_ERR | L_RQST);
define("L_ALL", L_ERR | L_RQST | L_RESP);

class GoogleLog
{
    public $errorLogFile = NULL;
    public $messageLogFile = NULL;
    public $logLevel = L_ERR_RQST;

    public function GoogleLog($errorLogFile, $messageLogFile, $logLevel = L_ERR_RQST, $die = true)
    {
        $this->logLevel = $logLevel;
        if( $logLevel == L_OFF ) 
        {
            $this->logLevel = L_OFF;
        }
        else
        {
            if( !($this->errorLogFile = @fopen($errorLogFile, "a")) ) 
            {
                header("HTTP/1.0 500 Internal Server Error");
                $log = "Cannot open " . $errorLogFile . " file.\n" . "Logs are not writable, set them to 777";
                error_log($log, 0);
                if( $die ) 
                {
                    exit( $log );
                }

                echo $log;
                $this->logLevel = L_OFF;
            }

            if( !($this->messageLogFile = @fopen($messageLogFile, "a")) ) 
            {
                fclose($this->errorLogFile);
                header("HTTP/1.0 500 Internal Server Error");
                $log = "Cannot open " . $messageLogFile . " file.\n" . "Logs are not writable, set them to 777";
                error_log($log, 0);
                if( $die ) 
                {
                    exit( $log );
                }

                echo $log;
                $this->logLevel = L_OFF;
            }

        }

        $this->logLevel = $logLevel;
    }

    public function LogError($log)
    {
        if( $this->logLevel & L_ERR ) 
        {
            fwrite($this->errorLogFile, sprintf("\n%s:- %s\n", date("D M j G:i:s T Y"), $log));
            return true;
        }

        return false;
    }

    public function LogRequest($log)
    {
        if( $this->logLevel & L_RQST ) 
        {
            fwrite($this->messageLogFile, sprintf("\n%s:- %s\n", date("D M j G:i:s T Y"), $log));
            return true;
        }

        return false;
    }

    public function LogResponse($log)
    {
        if( $this->logLevel & L_RESP ) 
        {
            $this->LogRequest($log);
            return true;
        }

        return false;
    }

}


