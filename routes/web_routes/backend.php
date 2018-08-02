<?php
//后台路由
Route::group(['prefix' => '/backend', 'namespace'=>'BackendControllers'],function () {

    //需要登录
    Route::group(['middleware'=>'admin.login:admin'], function(){
        Route::get('/', 'IndexController@index');
        //权限管理
        Route::group(['prefix'=>'role', 'middleware'=>['admin.role:admin']], function(){
            Route::get("roles","RoleController@roles"); //角色列表
            Route::get("permissions","RoleController@permissions"); //权限列表
            Route::post("post_add_role","RoleController@addRolePost"); //添加角色
            Route::get("omitRole","RoleController@omitRole"); //删除角色
            Route::post("modify","RoleController@modify"); //修改角色
            Route::post("post_add_permission","RoleController@addPermissionPost"); //添加权限
            Route::get("omitpower","RoleController@omitpower"); //删除权限
            Route::post("modifypower","RoleController@modifypower"); //修改权限
            //角色与权限
            Route::get('role_bind_permission',"RoleController@roleBindPermission"); //角色权限查看
            Route::post('role_find_permissions',"RoleController@roleFindPermissions"); //根据角色找权限
            Route::post('attach_permissions',"RoleController@attachPermissions"); //角色权限绑定
            //用户与角色
            Route::get('user_bind_roles',"RoleController@userBindRoles"); //用户角色查看
            Route::post('user_find_roles',"RoleController@userFindRoles"); //根据用户找角色
            Route::post('attach_roles',"RoleController@attachRoles"); //用户角色绑定
        });

        //产品
        Route::group(['prefix'=>'product', 'middleware'=>['worker.role:admin']], function(){
            //分类
            Route::group(['prefix'=>'category'], function(){
                Route::get('/', 'CategoryController@index');
                Route::post('add', 'CategoryController@add'); //分类添加
                Route::get('alter', 'CategoryController@alter'); //分类修改
                Route::get('omit', 'CategoryController@omit'); //分类删除
            });
            //责任
            Route::group(['prefix'=>'duty'], function(){
                Route::any('/', 'DutyController@index');
                Route::post('add', 'DutyController@add');
                Route::any('update/{id}','DutyController@updateDuty');//更新责任
                Route::any('delete/{id}','DutyController@delete');//删除责任
                Route::any('updataDutySubmit','DutyController@updataDutySubmit');//更新责任提交
            });
            //公司
            Route::group(['prefix'=>'company'], function(){
                Route::get('/', 'CompanyController@index');
                Route::post('add', 'CompanyController@add');
                Route::post('update', 'CompanyController@updateCompany');
                Route::get('delete/id/{id}', 'CompanyController@deleteCompany');
                Route::any('updateCompanyIndex/{id}','CompanyController@updateCompanyIndex');
            });
            //费率
            route::group(['prefix'=>'tariff'], function(){
                Route::get('/{clause_id}', 'TariffController@index')->where('clause_id', '[0-9]+');
//                Route::get('push_excel', 'TariffController@pushExcel');
                Route::post('push_excel_post', 'TariffController@pushExcelPost');

            });
            //条款
            Route::group(['prefix'=>'clause'], function(){
                Route::get('/', 'ClauseController@index');
                Route::get('add', 'ClauseController@add');
                Route::post('addPost', 'ClauseController@addPost');
                Route::get('update/{id}', 'ClauseController@update');
                Route::post('update_post', 'ClauseController@updatePost');
                Route::get('delete/{id}', 'ClauseController@delete');
            });
            //投保参数
//             Route::group(['prefix'=>'insure_option'], function(){
//                 Route::get('/', 'InsureOptionController@index');
//                 Route::post('addPost', 'InsureOptionController@addPost');
//             });
            //投保属性
            Route::group(['prefix'=>'insurance_attributes', 'namespace' => 'InsuranceAttribute'], function(){
                // 模块
                Route::get('modules/index/{bind_id}', 'ModuleController@index')->name('insurance_attributes.modules.index');
                Route::get('modules/create/{bind_id}', 'ModuleController@create')->name('insurance_attributes.modules.create');
                Route::get('modules/edit/{mid}', 'ModuleController@edit')->name('insurance_attributes.modules.edit');
                Route::post('modules/store', 'ModuleController@store')->name('insurance_attributes.modules.store');
                Route::put('modules/update/{mid}', 'ModuleController@update')->name('insurance_attributes.modules.update');
                // 产品属性
                Route::get('attributes/index/{mid}', 'AttributeController@index')->name('insurance_attributes.attributes.index');
                Route::get('attributes/create/{mid}', 'AttributeController@create')->name('insurance_attributes.attributes.create');
                Route::get('attributes/edit/{aid}', 'AttributeController@edit')->name('insurance_attributes.attributes.edit');
                Route::post('attributes/store', 'AttributeController@store')->name('insurance_attributes.attributes.store');
                Route::put('attributes/update/{aid}', 'AttributeController@update')->name('insurance_attributes.attributes.update');
                // 属性值
                Route::get('values/index/{aid}', 'ValueController@index')->name('insurance_attributes.values.index');
                Route::get('values/create/{aid}', 'ValueController@create')->name('insurance_attributes.values.create');
                Route::get('values/edit/{vid}', 'ValueController@edit')->name('insurance_attributes.values.edit');
                Route::post('values/store', 'ValueController@store')->name('insurance_attributes.values.store');
                Route::put('values/update/{vid}', 'ValueController@update')->name('insurance_attributes.values.update');
            });
            Route::get('brokerage/show/{bind_id}', 'BrokerageController@index')->where('bind_id', '[0-9]+');
            Route::post('brokerage/edit/do_submit', 'BrokerageController@doSubmit');
            //接口来源
            Route::group(['prefix'=>'api_from'], function(){
                Route::get('/', 'ApiFromController@index');
                Route::get('add', 'ApiFromController@add');
                Route::post('add_post', 'ApiFromController@addPost');
                Route::post('edit/{id}', 'ApiFromController@edit');
                Route::any('delete/{id}', 'ApiFromController@delete');
            });
            //保险产品
            Route::group(['prefix'=>'insurance'], function(){
                Route::get('/', 'InsuranceController@index');
                Route::get('add', 'InsuranceController@add');
                Route::get('bind/list', 'InsuranceController@insList')->name('insurance.bind.list');
                Route::get('bind/template/{bind_id}', 'InsuranceController@template')->name('insurance.bind.template');
                Route::get('bind/{insurance_id?}', 'InsuranceController@bind')->name('insurance.bind.index');
                Route::get('bind/pcode/{bind_id}', 'InsuranceController@bindPCode')->name('insurance.bind.pcode');
                Route::post('bind/pcode', 'InsuranceController@bindPCodeStore')->name('insurance.bind.pcode.store');
                Route::post('bind', 'InsuranceController@bindStore')->name('insurance.bind.store');
                Route::post('addPost', 'InsuranceController@addPost');
                Route::post('bind/uploadTemplate', 'InsuranceController@uploadTemplate')->name('insurance.bind.upload.template');
                Route::get('info', 'InsuranceController@info');
                Route::any('edit/{id}', 'InsuranceController@edit');
                Route::any('editSubmit', 'InsuranceController@editSubmit');
                Route::any('delete/{id}', 'InsuranceController@delete');
                Route::any('do_sell_status', 'InsuranceController@doSetSellStatus');//设置售卖状态
                Route::any('health/{id}', 'InsuranceController@health');//产品健康告知
                Route::any('health_submit', 'InsuranceController@healthSubmit');//添加产品健康告知
                Route::get('other_support/{id}', 'InsuranceController@otherSupport');//其他支持选项
                Route::post('other_support_post', 'InsuranceController@otherSupportPost');//选项变更提交

            });
            // 试算因子
            Route::group(['prefix'=>'restrict_genes', 'namespace' => 'RestrictGene'], function(){
                // 试算因子
                Route::get('index/{bind_id}', 'RestrictGeneController@index')->name('restrict_genes.index');
                Route::get('create/{bind_id}', 'RestrictGeneController@create')->name('restrict_genes.create');
                Route::get('edit/{rid}', 'RestrictGeneController@edit')->name('restrict_genes.edit');
                Route::post('store', 'RestrictGeneController@store')->name('restrict_genes.store');
                Route::put('update/{rid}', 'RestrictGeneController@update')->name('restrict_genes.update');
                // 试算因子选项
                Route::get('values/index/{rid}', 'ValueController@index')->name('restrict_genes.values.index');
                Route::get('values/create/{rid}', 'ValueController@create')->name('restrict_genes.values.create');
                Route::get('values/edit/{vid}', 'ValueController@edit')->name('restrict_genes.values.edit');
                Route::post('values/store', 'ValueController@store')->name('restrict_genes.values.store');
                Route::put('values/update/{vid}', 'ValueController@update')->name('restrict_genes.values.update');
                // Route::get('create_by_api_from/{api_from_uuid}', 'RestrictGenesController@createByApiFrom');
                // Route::post('store_by_api_from/{api_from_uuid}', 'RestrictGenesController@storeByApiFrom');
                // Route::get('create_by_product/{insurance_id}', 'RestrictGenesController@createByProduct');
                // Route::post('store_by_product/{insurance_id}', 'RestrictGenesController@storeByProduct');
                // Route::get('edit_by_product/{insurance_id}', 'RestrictGenesController@editByProduct');
                // Route::post('update_by_product/{insurance_id}', 'RestrictGenesController@updateByProduct');
//                Route::get('create/{code}', 'RestrictGenesController@create');
//                Route::get('values/create/{id}', 'RestrictGenesController@createValues');
//                Route::post('store/{code}', 'RestrictGenesController@store');
//                Route::post('values/store/{id}', 'RestrictGenesController@storeValues');
            });
        });



        //代理商API账户
        Route::group(['middleware' => ['admin.role:admin']], function () {
            Route::get('user', 'UserController@index');
            Route::post('user', 'UserController@store');
            Route::put('user/{id}', 'UserController@update');
            Route::delete('user/{id}', 'UserController@destroy');
        });

        // API参数
        Route::group(['prefix'=>'api_option', 'namespace' => 'ApiOption'], function () {
            //银行
            Route::get('bank', 'BankController@index')->name('api_option.bank.index');
            Route::post('bank', 'BankController@store')->name('api_option.bank.store');
            //职业
            Route::get('profession', 'ProfessionController@index')->name('api_option.profession.index');;
            Route::post('profession', 'ProfessionController@store')->name('api_option.profession.store');;
            //关系
            Route::get('relationship', 'RelationshipController@index')->name('api_option.relationship.index');;
            Route::post('relationship', 'RelationshipController@store')->name('api_option.relationship.store');;
            //证件类型
            Route::get('card_type', 'CardTypeController@index')->name('api_option.card_type.index');;
            Route::post('card_type', 'CardTypeController@store')->name('api_option.card_type.store');;
        });

        //订单统计
        Route::group(['middleware' => ['owner.role:admin']], function () {
            Route::get('order', 'OrderController@index');
            Route::get('order/list/{account_id}/{status?}', 'OrderController@list');
        });

        // 财务统计
        Route::group(['middleware' => ['owner.role:admin']], function () {
            Route::get('agency', 'AgencyController@index'); //代理商统计
            Route::get('details', 'AgencyController@details'); //查看详情
            Route::get('turnover', 'TurnoverController@index'); //产品交易量
            Route::get('brokerage', 'BrokerageController@brokerage'); // 佣金统计列表
        });

        //工单管理路由
        Route::group(['prefix'=>'/special'], function(){
            Route::get('special','ServerController@special');//已处理
            Route::get('nospecial','ServerController@nospecial');//未处理工单
            Route::get('dospecial','ServerController@dospecial');//处理工单
            Route::get('delspecial','ServerController@delSpecial');//删除工单
            Route::get('recspecial','ServerController@recSpecial');//删除工单
        });


        Route::group(['prefix'=>'/sms'], function(){

            //站内信
            Route::get("message","MailController@Index");
            Route::get('get_detail','MailController@getDetail');//查看站内信的详情
            Route::get('has_send','MailController@getSend');
            Route::post('mailsend','MailController@mailSend');//处理站内信

            //短信
            Route::get('sms','SmsController@sms');//短信主页
            Route::get('dosms','SmsController@doSms');//发短信
            Route::get('smsinfo','SmsController@smsInfo');
            Route::get('smsinfolist','SmsController@smsInfoList');
            Route::get('smslistinfo','SmsController@smsListInfo');
            Route::get('addmodels','SmsController@addModels');//添加模板
            //邮件

            Route::any('emaillists','SmsController@emailLists');//邮件总列表
            Route::any('emaillist','SmsController@emailList');//邮件分列表
            Route::any('emaildetail','SmsController@emailDetail');//邮件详情
            Route::any('emailedit','SmsController@emailEdit');//编辑邮件
            Route::any('emailsend','SmsController@emailSend');//发送邮件
            Route::any('emailfilesend','SmsController@emailFileSend');//发送邮件
            Route::any('emails','SmsController@Emails');//邮件首页
            Route::any('emailmodels','SmsController@emailModels');//邮件模板
            Route::any('addemailmodels','SmsController@addEmailModels');//添加邮件模板
            Route::any('doaddmodels','SmsController@doAddEmailModels');//处理添加模板
            Route::any('getemailmodels','SmsController@getEmailModels');//获取模板
            Route::any('emailmodelsinfo','SmsController@emailModelsInfo');//查看模板详情
            Route::any('hasemailinfo','SmsController@hasEmailInfo');//查看已发邮件详情
            Route::any('emailinfos','SmsController@emailInfos');//查看代理公司的邮件
            Route::any('emaillistinfo','SmsController@emailListInfos');//查看代理公司的邮件

            //在线客服
            Route::any('onlineservice','SmsController@onlineService');
            Route::any('addonlines','SmsController@addOnlines');
            Route::any('doaddonlines','SmsController@doAddOnlines');
            Route::any('onlinesinfo','SmsController@onlinesInfo');

        });



        //=====================================从中介系统拿过来的==========================================================

        Route::group(['prefix'=>'maintenance'],function(){//保全管理
            Route::get('index/{type}','MaintenanceController@index');//跳转到保全的默认界面
            Route::get('change_data/{type}','MaintenanceController@changeData');//跳转到资料变更页面
            Route::get('change_data_list/{id}','MaintenanceController@getChangeData');//获取某个客户的所有基本信息修改记录
            Route::get('change_person_list/{id}','MaintenanceController@changePersonData');//获取单个保单的所有人员变动情况
            Route::get('agree_change_person/{change_type}/{product_id}','MaintenanceController@agreeChangePerson');//修改人员
            Route::get('change_insurance/{type}','MaintenanceController@changeInsurance');//跳转到保额修改界面
            Route::post('get_person_change','MaintenanceController@getPersonChangeByTime');//通过时间段查询企业保单人员变更
            Route::get('change_recognizee','MaintenanceController@changeRecognizee');//被保人资料修改
            Route::get('change_person','MaintenanceController@changePerson');//团险人员变更
            Route::get('change_premium','MaintenanceController@changePremium');//跳转到保额修改界面
            Route::get('change_premium_detail/{order_list}','MaintenanceController@changePremiumDetail');//修改保额详情
            Route::get('change_data_detail/{order_list}','MaintenanceController@changeDataDetail');//保全详情
            Route::get('change_submit/{order_list}','MaintenanceController@changeSubmit');//提交保全
        });
        //退保管理
        Route::group(['prefix'=>'/cancel'],function(){
            Route::get('/{type}','CancelController@index'); // 根据退保类型获取退保列表
            Route::get('/cancel_detail/{id}','CancelController@cancelDetail');  //获取退保详情
        });

        //理赔后台
        Route::group(['prefix'=>'/claim'],function(){
            Route::get('index','ClaimController@index');
            Route::get('get_detail/{id}','ClaimController@getClaimDetail');
            Route::get('getorderdetail/{order_code}','ClaimController@getOrderDetail');
            Route::post('add_record','ClaimController@addRecord');
            Route::get('operation/{cid}','ClaimController@operation');
            Route::get('get_record/{cid}','ClaimController@getRecord');
            Route::any('do_claim_email','ClaimController@doClaimEmail');

        });

        Route::group(['prefix'=>'/table_field'],function(){//表和字段管理
            Route::get('index','TableFieldController@index'); //默认跳转到列表页面
            Route::get('add','TableFieldController@addTableField');  //跳转到添加表字段页面
//                Route::get('')
        });
        Route::group(['prefix'=>'/status'],function(){  //状态管理
            Route::get('index','StatusController@index'); // 查看所有的状态
            Route::get('group','StatusController@getGroup');//查看所有的分组
            Route::get('add_group','StatusController@addGroup');//跳转到添加分组页面
            Route::post('add_group_submit','StatusController@addGroupSubmit');//添加分组表单提交
            Route::get('add','StatusController@addStatus'); //添加状态
            Route::post('add_status_submit','StatusController@addStatusSubmit');//添加状态表单提交
            Route::post('get_status_ajax','StatusController@getStatusByGroupAjax');//通过ajax获取所有的状态
            Route::get('status_detail/{id}','StatusController@getStatusDetail');//跳转到状态的详情页面
            Route::get('add_status_relation','StatusController@addStatusRelation');//跳转到添加状态关系页面
            Route::post('add_status_relation_submit','StatusController@addStatusRelationSubmit');//跳转到关系表单提交页面
            Route::post('get_children_status_ajax','StatusController@getChildrenStatusAjax');//通过ajax获取所有的子级状态
            Route::get('group_detail/{group_id}','StatusController@getGroupDetail'); //获取状态分组详情
        });

        //获取分类
        Route::group(['prefix'=>'/classify'],function(){
            Route::post('get_classify','StatusClassifyController@getClassify');
        });



        //=====================================从中介系统拿过来的==========================================================

        Route::group(['prefix'=>''], function () {
            //订单管理
            Route::any('dopay','InterOrdersController@doPay');
            Route::any('doSmsOrder','InterOrdersController@doSmsOrder');
            Route::any('emailorder','InterOrdersController@emailOrder');
            Route::any('doreply','InterOrdersController@doReply');
            Route::any('payinfo','InterOrdersController@payInfo');

        });

    });
    Route::get('login','LoginController@index');
    Route::post('do_login','LoginController@login');
    Route::get('logout','LoginController@logout');


});
