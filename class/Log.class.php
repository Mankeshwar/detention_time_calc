<?php

/* *
 * Log A logger class which creates logs when an exception is thrown.
 *
 */

class Log {
    # @string, Log directory name

    private $path = './logs/';

    # @void, Default Constructor, Sets the timezone and path of the log files.

    public function __construct() {
        date_default_timezone_set('Europe/Amsterdam');
        $this->path = $this->path;
    }

    /**
     *   @void 
     * 	Creates the log
     *
     *   @param string $message the message which is written into the log.
     * 	@description:
     * 	Checks if directory exists, if not, create one and call this method again.
     *   Checks if log already exists.
     * 	If not, new log gets created. Log is written into the logs folder.
     * 	Logname is current date(Year - Month - Day).
     * 	If log exists, edit method called.
     * 	Edit method modifies the current log.
     */
    public function write($message) {
        $date = new DateTime();
        $log = $this->path . $date->format('Y-m-d') . ".txt";
        echo $log;
        if (is_dir($this->path)) {
            if (!file_exists($log)) {
                $fh = fopen($log, 'a+') or die("Fatal Error !");
                $logcontent = "Time : " . $date->format('H:i:s') . "\r\n" . $message . "\r\n";
                fwrite($fh, $logcontent);
                fclose($fh);
                echo 'exist<br />';
            } else {
                $this->edit($log, $date, $message);
                echo 'exist<br />';
            }
        } else {
            if (mkdir($this->path, 0777) === true) {
                $this->write($message);
            }
        }
    }

    /**
     *  @void
     *  Gets called if log exists. 
     *  Modifies current log and adds the message to the log.
     *
     * @param string $log
     * @param DateTimeObject $date
     * @param string $message
     */
    private function edit($log, $date, $message) {
        $logcontent = "Time : " . $date->format('H:i:s') . "\r\n" . $message . "\r\n\r\n";
        $logcontent = $logcontent . file_get_contents($log);
        file_put_contents($log, $logcontent);
    }

}
?>


