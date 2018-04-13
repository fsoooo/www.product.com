<?php
/**
 * Created by PhpStorm.
 * User: xyn
 * Date: 2017/8/25
 * Time: 10:51
 */

namespace App\Listeners;

use Illuminate\Database\Events\QueryExecuted;

class SqlListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param  =QueryExecuted  $event
     * @return void
     */
    public function handle(QueryExecuted $event) {

        // log sql query
        $sql = str_replace("?", "'%s'", $event->sql);

        $log = vsprintf($sql, $event->bindings);

        $log = '[' . date('Y-m-d H:i:s') . '] ' . $log . "\r\n";
        $file_path = storage_path('logs'.DIRECTORY_SEPARATOR.date('Ymd').'sql.log');
        file_put_contents($file_path, $log, FILE_APPEND);
    }
}