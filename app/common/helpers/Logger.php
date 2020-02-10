<?php


namespace app\common\helpers;


use app\common\database\DB;
use PDOException;

class Logger
{
    /**
     * @param \Exception $e
     */
    static public function log(\Exception $e)
    {
        $error_name = get_class($e);
        $stack_trace = $e->getTraceAsString();

        try {
            $error_time = (new \DateTime())->getTimestamp();
        } catch (\Exception $e) {
        }

        $connection = DB::getConnection();
        $stmt = $connection->prepare('INSERT INTO log (error_name, stack_trace, error_time) VALUES (?,?,?)');
        if ($stmt) {
            try {
                $stmt->execute([$error_name, $stack_trace, $error_time]);
            } catch (PDOException $e) {
            }
        }
    }
}