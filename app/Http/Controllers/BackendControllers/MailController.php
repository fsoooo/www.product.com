<?php
namespace App\Http\Controllers\BackendControllers;

use App\Http\Controllers\Controller;
use App\Models\Mail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\AdminUser;
use Illuminate\Support\Facades\Request;
use Mockery\CountValidator\Exception;
use Validator;
use Auth;

class MailController extends Controller
{

                public function index()
                {
                    $user =AdminUser::get();
                    return view("backend.message.sendMessage")->with('users',$user);
                }
                public function  mailSend(){
                    //处理站内信业务：接受站内信内容，发送时间（定时发送），插入数据，返回提示信息，提示收件人等。
                        $receive = $_POST['receive'];
                        $receive_name = $_POST['receive_name'];
                        $title = $_POST['title'];
                        $content = $_POST['content'];
                        $send_id = $_POST['send_id'];
                        if(empty($title)||empty($content)){
                            return (['status'=>'1','message'=>'请输入！！']);
                        }
                        if($receive == '0') {
                            $receive = 'all';
                        }else if($receive == '1'){
                            $receive = $receive_name;
                        }
                        $res = Mail::insert([
                            'send_id'=>$send_id,
                            'receive_id'=>$receive,
                            'title'=>$title,
                            'content'=>$content,
                            'created_at'=>date('YmdHis',time()),
                            'updated_at'=>date('YmdHis',time())
                        ]);
                        if($res){
                            return (['status' => 0, 'message' => "站内信发送成功！"]);
                        }else{
                            return (['status' => 1, 'message' => '站内信发送失败！！']);
                        }
                    }
                    //查看已经发送的站内信
                    public function getSend()
                    {
                        $limit = config('list_num.backend.roles');//每页显示几条
                        $params = "/backend/sms/has_send";
                        $user =AdminUser::get();
                        if (isset($_GET['page'])) {
                            $page = $_GET['page'];
                            $res = Mail::where('delete_id','0')->paginate($limit);
                            $pages = $res->lastPage();//总页数
                            $totals = $res->total();//总数
                            $currentPage = $res->currentPage();//当前页码
                            $result = Mail::orderBy('created_at', 'asc')
                                ->where('delete_id','0')
                                ->skip($page)
                                ->offset(($page - 1) * $limit)
                                ->limit($limit)
                                ->get();
                            return view('backend.message.hasSend')
                                ->with('res', $result)
                                ->with('params', $params)
                                ->with('totals', $totals)
                                ->with('user',$user)
                                ->with('currentPage', $currentPage)
                                ->with('pages', $pages);
                        } else {
                            $page = '1';
                            $res = Mail::where('delete_id','0')->paginate($limit);
                            $pages = $res->lastPage();//总页数
                            $totals = $res->total();//总数
                            $currentPage = $res->currentPage();//当前页码
                            $result = Mail::orderBy('created_at', 'asc')
                                ->where('delete_id','0')
                                ->skip($page)
                                ->offset(($page - 1) * $limit)
                                ->limit($limit)
                                ->get();
                            return view('backend.message.hasSend')
                                ->with('res', $result)
                                ->with('user',$user)
                                ->with('params', $params)
                                ->with('totals', $totals)
                                ->with('currentPage', $currentPage)
                                ->with('pages', $pages);
                        }
                    }
                    public function getDetail()
                    {
                        $detail = Mail::where('id',$_GET['id'])->first();
                        return view('backend.message.detail',compact('detail'));
                    }
}
