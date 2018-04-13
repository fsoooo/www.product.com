<?php
namespace App\Http\Controllers\FrontendControllers;

use App\Models\QuestionSecondStep;
use App\Models\QuestionThirdStep;
use App\Services\UploadImage;
use Illuminate\Http\Request;
use App\Http\Controllers\FrontendControllers\BaseController;
use Cache;
use App\Models\QuestionFirstStep;

class QuestionController extends BaseController
{

    private $firstStep;
    private $secondStep;
    private $thirdStep;

    public function __construct(Request $request,
                                QuestionFirstStep $firstStep,
                                QuestionSecondStep $secondStep,
                                QuestionThirdStep $thirdStep)
    {
        parent::__construct($request);
        $this->firstStep = $firstStep;
        $this->secondStep = $secondStep;
        $this->thirdStep = $thirdStep;
    }

    public function question(Request $request)
    {
        $input = $request->all()['data'];
        //删除此ip的图片url缓存
        Cache::forget($request->getClientIp());

        //生成唯一code码
        $question_code = rand(1000,9999).time();
        $this->step1($input['step1'],$question_code);
        $this->step2($input['step2'],$question_code);
        $this->step3($input['step3'],$question_code);
//        return true;
    }

    private function step1($input, $question_code)
    {
        $input['question_code'] = $question_code;
        //入库
        $this->firstStep->fill($input)->save();
//        return redirect('/question/step2?question_code='.$input['question_code']);
    }

    private function step2($input, $question_code)
    {
//        $input = '{"data":{"question_code":"95921513593727","leave_count":"3","company_list":[{"recognize_company_name":"1","high_position_count":"1","recognize_country":"\u4e2d\u56fd","recognize_province":"\u5317\u4eac","recognize_city":"\u4e1c\u57ce","insured_count":"1","insured_money":"1","insured_rate":"1"},{"recognize_company_name":"2","high_position_count":"1","recognize_country":"\u4e2d\u56fd","recognize_province":"\u5317\u4eac","recognize_city":"\u4e1c\u57ce","insured_count":"2","insured_money":"2","insured_rate":"2"}]}}';
//        $input = json_decode($input,true)['data'];
        $company_list = $input['company_list'];
        foreach ($company_list as $key=>$value){
            $secondStep = new QuestionSecondStep();
            $company_list[$key]['leave_count'] = $input['leave_count'];
            $company_list[$key]['question_code'] = $question_code;
            $secondStep->fill($company_list[$key])->save();
        }

//        return redirect('/question/step3?question_code='.$input['question_code']);
    }

    private function step3($input, $question_code)
    {
        $input['question_code'] = $question_code;
        $this->thirdStep->fill($input)->save();

//        return redirect('/question/step3?question_code='.$input['question_code']);
    }

    /**
     * 把base64转换成image上传到服务器
     *
     * @param Request $request
     * @return bool|string
     */
    public function uploadImage(Request $request)
    {
        $base64 = $request->get('url');

        $path = 'frontend/question/' . date("Ymd") .'/';

        $ip = $request->getClientIp();
        //如果当前ip已经上传过图片
        if(Cache::has($ip)){
            //删除掉旧图片
            $image_url = Cache::get($ip);
            unlink(substr($image_url,1,strlen($image_url)));
        }
        $output_file = UploadImage::uploadImageWithBase($base64,$path);

        Cache::put($ip,'/upload/'.$path.$output_file,60*24);
        return '/upload/'.$path.$output_file;
    }

}
