<?php

/**
 * Detention Time Calculator 
 *
 * @category    Calculator
 * @author      Mankeshwar Mishra
 *   
 */

namespace detention;

class DetentionTimeCalc {

    const HNT = 1;
    const STL = 2;
    const FIT = 0.5;
    const UNTD = 1;
    const LYNG = 1.5;
    const SCD = 1;

    public $badTimePercent = 10;
    public $goodTimePercent = 10;

    # @object, object of database class
    private $objDB;

    function __construct($objDB) {

        $this->objDB = $objDB;
    }

    /**
     * Offence Types 
     * @return array
     */
    static function toOffenceTypeArray() {
        return array('HNT' => 'Homework Not Done'
            , 'STL' => 'Stealing'
            , 'FIT' => 'Fighting'
            , 'UNTD' => 'Untidyness'
            , 'LYNG' => 'Lying'
            , 'SCD' => 'School Property Damage'
        );
    }

    /**
     * Get offence types value 
     * 
     * @param string $calcTime
     * @return string or null
     */
    function getDetentionTimeValue($calcTime) {
        if (!empty($calcTime)) {
            switch ($calcTime) {

                case "HNT" :
                    return self::HNT;
                    break;
                case "STL" :
                    return self::STL;
                    break;
                case "FIT" :
                    return self::FIT;
                    break;
                case "UNTD" :
                    return self::UNTD;
                    break;
                case "LYNG" :
                    return self::LYNG;
                    break;
                case "SCD" :
                    return self::SCD;
                    break;
                default:
                    return null;
            }
        }
    }

    /**
     * Save data into database
     * 
     * @param array $data
     * @return mixed
     */
    function saveData(array $data) {
        try {
            if (!empty($data['studentName'])) {
                if (!$this->checkStudentExist($data['studentName'])) {
                    $sql = "INSERT INTO student VALUES(NULL,'$data[studentName]')";
                    $result = $this->objDB->query($sql);
                    $lastInsertedId = $this->objDB->lastInsertId();
                }


                $detenionHours = $this->getDetentionTimeValue($data['offenseTypes']);
                $sql = "INSERT INTO detention_time(`offence_type`,
									`time_mode`,
									`calculation_mode`,
									`detention_date`,
									`detention_hours`,
									`student_id`) 
		   VALUES('$data[offenseTypes]','$data[timeMode]','$data[clcMode]',NOW(),'$detenionHours','$lastInsertedId')";
                $result = $this->objDB->query($sql);

                $result = $this->calculateDetentionTime($data);
                return $result;
            }
        } catch (Exception $e) {
            # Write into log and display Exception
            $this->objDB->ExceptionLog($e->getMessage());
        }
    }

    /**
     * Calculate Detention time 
     * 
     * @param array $data
     * @return mixed
     */
    function calculateDetentionTime(array $data) {
        try {
            if (!empty($data)) {
                $sql = "SELECT * 
		        FROM student 
		        INNER JOIN detention_time on student.id = detention_time.student_id
				WHERE student.name='$data[studentName]' 
				AND detention_time.time_mode ='$data[timeMode]'
				AND calculation_mode ='$data[clcMode]' ";

                $result = $this->objDB->query($sql);
                if (empty($result)) {
                    return null;
                } else {
                    $detentionInMnts = 60 * $result[0]['detention_hours'];

                    return $this->calucaulateInPercent($detentionInMnts, strtolower($result[0]['time_mode']));
                }
            }
        } catch (Exception $e) {
            # Write into log and display Exception
            $this->objDB->ExceptionLog($e->getMessage());
        }
    }

    /**
     * Calculate offence in percent 
     * 
     * @param string $time
     * @param string $type
     * @return string
     */
    function calucaulateInPercent($time, $type) {
        if ($type == 'badtime') {
            $time = ($time - $time * $this->badTimePercent / 100);
        }

        if ($type == 'goodtime') {
            $time = ($time + $time * $this->goodTimePercent / 100);
        }
        return $time;
    }

    /**
     * Check user exist or not 
     * @param string $studentName
     * @return boolean
     */
    function checkStudentExist($studentName) {
        try {
            $sql = "SELECT name FROM student WHERE name='$studentName'";

            $result = $this->objDB->row($sql);
            if (!empty($result['name'])) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            # Write into log and display Exception
            $this->objDB->ExceptionLog($e->getMessage());
        }
    }

}

?>