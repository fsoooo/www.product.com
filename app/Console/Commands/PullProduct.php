<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;


class PullProduct extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pull:product 
                            {form=Qx : api uuid}
                            {--code=* : p_code array}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'pull products';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //获取所有值
        $arguments = $this->arguments();
        $form = $arguments['form'];
        //提示信息
        $this->comment($form . ' products pulling');
        // 获取所有选项...
        $options = $this->options();

        $class_name = 'App\Console\Commands\PullProducts\\' . $form . 'PullProduct';
        $pull = new $class_name();

//        $start_time = time();
        $code_array = $pull->pullProducts($options['code']);
        $num = count($code_array);
        if ($num < 1) {
            $this->info('');
            $this->error('product null');
            die;
        }
//        $bar = $this->output->createProgressBar($num);
//        foreach($code_array as $k => $v){
//            $res = $pull->pulling($v);
//            if($res['code'] != 200){
//                $this->error($form . '-' . $v['p_code'] . ' pull error:'. $res['data']);
//                die;
//            }
//            $bar->advance();
//        }
        $array = array_chunk($code_array, 10);
        //todo 多进程
        declare (ticks = 1);
        pcntl_signal(SIGCHLD, function ($signal) {
            while (($pid = pcntl_waitpid(-1, $status, WNOHANG)) > 0) {
            }
        });
        for ($i = 0; $i < ceil($num / 10); $i++) {
            $pid = pcntl_fork();
            if ($pid == -1) {
                die ("cannot fork");
            } else if ($pid > 0) {
//                    echo "parent continue \n";
            } else if ($pid == 0) {
//                    echo "child start, pid ", getmypid(), "\n" ;
                foreach ($array[$i] as $k => $v) {
                    $res = $pull->pulling($v);
                    if ($res['code'] != 200) {
                        $this->error($form . '-' . $v['p_code'] . ' pull error:' . $res['data']);
                    } else {
                        $this->info('pull '. $v['p_code']. ' finish');
                    }
                }
                exit (0);
            }
        }

//        $curl_end_time = time();
//        //成功信息
//        $pull_time = round(($curl_end_time - $start_time) / 60, 2);
//        $this->line('');
//        $this->line(' pull time:' . $pull_time . ' minute');
//        $this->info($form . ' products pull finish');
    }
}
