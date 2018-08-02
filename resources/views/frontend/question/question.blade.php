<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>线上投保询价</title>
		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
		<link rel="stylesheet" href="../r_frontend/css/questione.css" />
		<link rel="stylesheet" href="../r_frontend/css/swiper-3.4.2.min.css" />
	</head>
	<body>
		<div class="header">
			<div class="header-left"><img src="../r_frontend/img/logo.png" alt="" /></div>
			<h1 class="title">线上投保询价</h1>
		</div>
		{{--<form action="/question/postQuestion" method="post">--}}
		<form id="step1_form">
		<div id="step1" class="content info">
			<div class="form-wrapper">
				<h3 class="title">投保人信息</h3>
				<ul>
					<li>
						<div class="name">贵公司名称</div>
						<input name="company_name" type="text" maxlength="20"/>
					</li>
					<li>
						<div class="name">贵公司网址</div>
						<i class="extra-left">http://www.</i>
						<input  name="website" style="padding-left: 2.3rem;" class="url" type="text" onkeyup="this.value=this.value.replace(/[\u4e00-\u9fa5]/g,'')" maxlength="100" />
					</li>
					<li>
						<div class="name">贵公司注册地</div>
						<input name="sign_up_address" type="text" maxlength="100"/>
					</li>
					<li>
						<div class="name">贵公司地址</div>
						<input name="address" type="text"  maxlength="100" />
					</li>
					<li>
						<div class="name">贵公司经营起始时间</div>
						<input name="start_date" id="date" type="text" />
					</li>
					<li>
						<div class="name">公司营业性质</div>
						<div class="select-group quality">
							<label>
								<span class="btn-select">VC</span>
								<input value="VC" type="radio" name="nature" hidden/>
							</label>
							<label>
								<span class="btn-select">PE</span>
								<input value="PE"  type="radio"  name="nature" hidden/>
							</label>
							<label>
								<span class="btn-select">IPO</span>
								<input value="IPO"  type="radio"  name="nature" hidden/>
							</label>
							<label>
								<span class="btn-select">其他</span>
								<input value="其他"  type="radio"  name="nature" hidden/>
							</label>
						</div>
					</li>
					<li class="ipo1">
						<div class="name">是否有股票在公开市场上发行？</div>
						<div class="select-group publish">
							<label>
								<span class="btn-select">是</span>
								<input  value="0" type="radio" name="has_stock" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input  value="1" type="radio"  name="has_stock" hidden/>
							</label>
						</div>
					</li>
					<li class="ipo2">
						<div class="name">股票发行总数</div>
						<input class="num" type="tel" name="stock_num" value="0"/>
					</li>
					<li class="ipo2">
						<div class="name">流通股比率</div>
						<input data-range="0,100" class="num range" type="number" name="stock_rate" value="0"/><i class="extra">%</i>
					</li>
					<li class="ipo2">
						<div class="name">在哪个证券交易所上市</div>
						<select id="country" name="stock_transact">
							<option data-id="0" value="中国">中国</option>
							<option data-id="1" value="中国香港">中国香港</option>
							<option data-id="2" value="美国">美国</option>
							<option data-id="3" value="英国">英国</option>
							<option data-id="4" value="法国">法国</option>
							<option data-id="5" value="日本">日本</option>
						</select>
					</li>
					<li class="ipo2">
						<div class="name">交易所名称</div>
						<input name="transact_name" type="text" maxlength="20"/>
					</li>
					<li class="upload-img">
						<div class="name">美国证券交易补充投保书</div>
						<div class="photos-wrapper clearfix">
							<div class="photos-add">
								<button type="button" class="btn-upload"></button>
								<input hidden="hidden" type="file" accept="image/*">
							</div>
						</div>
					</li>
					<li class="ipo2">
						<div class="name">股票代码</div>
						<input name="stock_code" class="num" type="tel" maxlength="6"/>
					</li>
					<li class="other">
						<div class="name">公司营业性质</div>
						<input name="detail_nature" type="text" />
					</li>
					<li>
						<div class="name">联系人姓名</div>
						<input name="contact_name" type="text" maxlength="6"/>
					</li>
					<li>
						<div class="name">联系人电话</div>
						<input name="contact_phone" class="tel" type="tel" maxlength="11"/>
					</li>
				</ul>
			</div>
			<div class="btn-box">
				<button type="button" class="btn-primary btn-next" disabled>下一步</button>
			</div>
		</div>
		</form>
		<form id="step2_form">
		<div id="step2" class="content info" style="display: none;">
				<div class="form-wrapper">
					<h3 class="title">被保人信息</h3>
					<ul>
						<li>
							<div class="name">贵公司及其所投公司离职董事和高管总数<span class="tips" style="margin-left: .1rem;">过去12个月中</span></div>
							<input name="leave_count" class="num range" data-range="0,10000" type="tel" maxlength="5" />
						</li>
					</ul>
					<ul class="section">
						<li class="section-title">公司信息<i class="iconfont icon-delete"></i></li>
						<li>
							<div class="name">贵公司所投公司名称</div>
							<div class="tips">包括在未来12个月中有任何合并、收购、兼并或投标竞价活动的公司</div>
							<input name="recognize_company_name" type="text" maxlength="20"/>
						</li>
						<li>
							<div class="name">该公司董事及高管人数</div>
							<input class="num range" data-range="0,10000" name="high_position_count" type="tel" maxlength="5"/>
						</li>
						<li class="area">
							<div class="name">该公司所在地区</div>
							<select class="level1" name="recognize_country"></select>
							<select class="level2" name="recognize_province"></select>
							<select class="level3"  name="recognize_city"></select>
						</li>
						<li>
							<div class="name">最近所投该公司轮次</div>
							<select class="p_num" name="insured_count">
								<option value="天使轮">天使轮</option>
								<option value="A轮">A轮</option>
								<option value="A+轮">A+轮</option>
								<option value="B轮">B轮</option>
								<option value="C轮">C轮</option>
								<option value="IPO">IPO</option>
								<option value="其他">其他</option>
							</select>
						</li>
						<li>
							<div class="name">最近该轮次所投金额</div>
							<input data-range="0,10" class="num range"  type="number" name="insured_money" onkeyup="this.value=this.value.replace(/[^\d\.]/g,'')" /><i class="extra">万元</i>
						</li>
						<li>
							<div class="name">最近所投该公司轮次持股比例</div>
							<input data-range="0,100" class="num range" type="number" name="insured_rate" /><i class="extra">%</i>
						</li>
					</ul>
					<button id="add" class="btn-primary" disabled>添加公司</button>
				</div>
			{{--</form>--}}
			<div class="btns-box">
				<button type="button" class="btn-primary btn-last">上一步</button>
				<button type="button" class="btn-primary  btn-next" disabled>下一步</button>
			</div>
		</div>
		</form>

		<form id="step3_form">
		<div  id="step3" class="content question" style="display: none;">
			<div class="form-wrapper">
				<ul class="page">
					<li data-id="1">
						<div class="text">1. 是否没有实体或个人持有投保公司10%以上的股票</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="has_ten_percent_people" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="has_ten_percent_people" hidden/>
							</label>
						</div>
					</li>
					<li data-id="2">
						<div class="text">2.投保公司是否没有在过去的三年中更换过审计师， 外部律师或外部证券律师</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="change_staff" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="change_staff" hidden/>
							</label>
						</div>
					</li>
					<li data-id="3">
						<div class="text">3.投保公司或其任何董事或高级职员是否在经过询问，知晓任何有可能引起保险单项下的索赔的行动、疏忽、事件或情形</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="know_compensation_info" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="know_compensation_info" hidden/>
							</label>
						</div>
					</li>
					<li data-id="4">
						<div class="text">4.投保公司或其任何董事或高级职员是否因讳反任何可使用的证券法律或法规而导致的任何民生、刑事或行政程序</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="has_criminal" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="has_criminal" hidden/>
							</label>
						</div>
					</li>
					<li data-id="5">
						<div class="text">5.投保公司或其任何董事或高级职员是否与任何代表诉讼、集体诉讼或衍生诉讼事件有关</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="has_litigation" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="has_litigation" hidden/>
							</label>
						</div>
					</li>
					<li data-id="6">
						<div class="text">6.投保公司是否在过去或未来的12个月中已经或将要发行股票(普通股或其他)</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="has_stock" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="has_stock" hidden/>
							</label>
						</div>
					</li>
					<li data-id="7">
						<div class="text">7.投保公司在过去的12个月里是否有裁员</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="has_reduce_staff" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="has_reduce_staff" hidden/>
							</label>
						</div>
					</li>
					<li data-id="8">
						<div class="text">8.投保公司在未来的12个月里是否有裁员计划</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="reduce_staff_plan" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="reduce_staff_plan" hidden/>
							</label>
						</div>
					</li>
					<li data-id="9">
						<div class="text">9.投保公司是否有人力资源部门</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="has_human_resources" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="has_human_resources" hidden/>
							</label>
						</div>
					</li>
				</ul>
				<ul class="page" style="display: none;">
					<li data-id="10">
						<div class="text">10.投保公司是否有向所有雇员发布的员工手册</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="has_staff_manual" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="has_staff_manual" hidden/>
							</label>
						</div>
					</li>
					<li data-id="11">
						<div class="text">11.员工手册是否一至两年都没有更新一次</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="manual_update" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="manual_update" hidden/>
							</label>
						</div>
					</li>
					<li data-id="12">
						<div class="text">12.投保公司对其所投资的公司的董事及高管没有书面的性骚扰及报告程序</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="has_report" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="has_report" hidden/>
							</label>
						</div>
					</li>
					<li data-id="13">
						<div class="text">13.投保公司对其所投资的公司的董事及高管没有书面的书面测评</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="has_assessment" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="has_assessment" hidden/>
							</label>
						</div>
					</li>
					<li data-id="14">
						<div class="text">14.投保公司对其所投资的公司的董事及高管没有书面的行为守则</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="code_of_conduct" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="code_of_conduct" hidden/>
							</label>
						</div>
					</li>
					<li data-id="15">
						<div class="text">15.投保公司对其所投资的公司的董事及高管没有书面的解雇政策</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="fire_policy" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="fire_policy" hidden/>
							</label>
						</div>
					</li>
					<li data-id="16">
						<div class="text">16.投保公司对其所投资的公司的董事及高管没有书面的聘用政策</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="hire_policy" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="hire_policy" hidden/>
							</label>
						</div>
					</li>
					<li data-id="17">
						<div class="text">17.投保公司没有家庭因素申请休假或病假政策</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="vacation_policy" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="vacation_policy" hidden/>
							</label>
						</div>
					</li>
					<li data-id="18">
						<div class="text">18.投保公司没有雇佣歧视及报告程序</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="discriminate_against" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="discriminate_against" hidden/>
							</label>
						</div>
					</li>
				</ul>
				<ul class="page" style="display: none;">
					<li data-id="19">
						<div class="text">19.投保公司或其所投资的公司是否不涉及被国家环境监管的项目</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="has_country_supervision" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="has_country_supervision" hidden/>
							</label>
						</div>
					</li>
					<li data-id="20" class="blur">
						<div class="text">20.投保公司没有经过董事会批准的书面的环境政策</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="has_board_approval" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="has_board_approval" hidden/>
							</label>
						</div>
					</li>
					<li data-id="21" class="blur">
						<div class="text">21.投保公司没成立监察环境政策的委员会</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="has_committees" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="has_committees" hidden/>
							</label>
						</div>
					</li>
					<li data-id="22" class="blur">
						<div class="text">22.投保公司没有履行正式的审计程序以确保环境政策的合规性</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="has_auditing_program" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="has_auditing_program" hidden/>
							</label>
						</div>
					</li>
					<li data-id="23" class="blur">
						<div class="text">23.投保公司没有意识到目前存在环境污染的情形，并且公司正在或将要对此进行赔偿</div>
						<div class="select-group">
							<label>
								<span class="btn-select">是</span>
								<input value="0" type="radio" name="pollution_restitution" hidden/>
							</label>
							<label>
								<span class="btn-select">否</span>
								<input value="1"  type="radio"  name="pollution_restitution" hidden/>
							</label>
						</div>
					</li>
				</ul>
			</div>
			<div class="btns-box">
				<button type="button" class="btn-primary btn-last">上一步</button>
				<button type="button" class="btn-primary btn-next" disabled>下一步</button>
			</div>
		</div>
		</form>
		<div  id="step4" class="content duty" style="display: none;">
			<h3 class="title">您的义务</h3>
			<ul>
				<li>1 如实提供最近亩计过的年报或财务报表</li>
				<li>2 如实提供最新中期财务报表(如有)</li>
				<li>3 如实提供一份公司章程，议事程序或补偿协议</li>
				<li>4 如实提供请提供一份投保公司的所有子公司的详细列表，包括它们的注册地以及投保公司直接或间接的持股比例(不包括最新年报或财务报表中已列明的子公司)</li>
				<li>5 对于决定承担风险并对保险合同项下的风险进行评估(并据此计算应收保费)的保险公司来说，所有的事项都必须按照最大诚信’·原则向其披露。全面坦诚的披露义务应由寻求保险保障(或保险续保、变更)的一方而不是由代表该方的保险经纪人承担。</li>
				<li>6 与保险公司签订保险合同2前，您有义务向保险公司披露您所知道的与其是否接受风险保险、以什么条件接受保险有关的所有事项包括可以合理预知的事项。</li>
				<li>7 续保、扩展、变更或恢复保险合同之前，您同样有义务向我们披露相关的信息。</li>
				<li>8 您的披露义务不包合以下事项:
					<ul>
						<li>a 由保险公司承担的降低风险的事项；</li>
						<li>b 常识；</li>
						<li>c 您的保险公司知道，或根据其业务常识应该知道的事项；</li>
						<li>d 本该属于您承担但已被保险公司免除的义务。</li>
					</ul>
				</li>
				<li>9 如果您没有遵守您的披露义务，保险公司有权减少其在保险合同项下的理赔责任或职消合同。</li>
			</ul>
			<div class="btns-box">
				<button id="refuse" type="button" class="btn-positive">拒绝</button>
				<button id="agree" type="button" class="btn-primary">已阅读并同意</button>
			</div>
		</div>
		<div id="step5" class="content success" style="display: none;">
			<div class="text">
				<p>感谢您的参与，我们会尽快给您答复</p>
				<p>更多咨询请登录<a href="http://dev312.inschos.com">http://dev312.inschos.com</a></p>
			</div>
		</div>
	</body>
					
	<script src="../r_frontend/js/area.min.js"></script>
	<script src="../r_frontend/js/jquery-1.11.3.min.js"></script>
	<script src="../r_frontend/js/swiper-3.4.2.min.js"></script>
	<script src="../r_frontend/js/datePicker.js"></script>
	<script src="../r_frontend/js/question.js"></script>
	<script>
        $(function(){
            var questionData = {step1: {},step2: {},step3: {}}; // 问卷所有结果
            step1.init(function(){
                var jsonData = $('#step1_form').serializeArray();
                for(i in jsonData){
                    questionData.step1[jsonData[i].name] = jsonData[i].value;
                }
                // 提交数据成功之后
                $('#step1').hide();
                $('#step2').show();
                $('body,html').animate({scrollTop: 0}, 0);
                goStep2();
            });
            function goStep2(){
                step2.init(function(){
                    var jsonData = $('#step2_form').serializeArray();
                    var result = {};
                    var company_list = [];
                    var company_item = {};
                    for(i in jsonData){
                        if(i == 0){
                            result.leave_count = jsonData[i].value;
                        }else{
                            company_item[jsonData[i].name] = jsonData[i].value;
                            if(i%8 == 0){
                                company_list.push(company_item);
                                company_item = {};
                            }
                        }
                    }
                    result.company_list = company_list;
                    questionData.step2 = result;

                    $('#step2').hide();
                    $('#step3').show();
                    $('body,html').animate({scrollTop: 0}, 0);
                    goStep3();
                });
            }
            function goStep3(){
                step3.init(function(){
                    var jsonData = $('#step3_form').serializeArray();
                    for(i in jsonData){
                        questionData.step3[jsonData[i].name] = jsonData[i].value;
                    }
                    $('#step3').hide();
                    $('#step4').show();
                    $('body,html').animate({scrollTop: 0}, 0);
                    goStep4();
                });
            }
            function goStep4(){
                step4.init(function(){
                    $.ajax({
						type:'post',
                        url: "/question/postQuestion",
						data: {
							"data": questionData
						},
                        success: function(data) {
                            $('#step4').hide();
                            $('#step5').show();
                            $('body,html').animate({scrollTop: 0}, 0);
                        },
                        error: function() {
                            Mask.alert("网络请求错误!");
                        }
                    });
                });
            }
        });
	</script>
</html>
