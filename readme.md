-----------------------------------## 数据库更新日志


2017.10.11
#### 表明 ins_order_insure_info
#### 表说明 订单记录表
#### 迁移文件 2017_06_16_022602_create_ins_order_table.php
- 操作：添加  字段：member_id  作用：会员编号   原类型:varchar   目标类型：无   默认值：无  原因：泰康理赔时用


2017.10.26
#### 表明 insurance
#### 表说明 产品表
#### 迁移文件 2017_05_25_091752_create_insurance_table.php
- 操作：添加  字段：base_price  作用：基础保费   原类型:varchar   目标类型：无   默认值：无  原因：产品基础信息展示 可空：是
- 操作：添加  字段：base_stages_way  作用：基础佣金比缴别   原类型:varchar   目标类型：无   默认值：无  原因：产品基础信息展示 可空：是
- 操作：添加  字段：base_ratio  作用：基础佣金比   原类型:int   目标类型：无   默认值：无  原因：产品基础信息展示 可空：是

2017.10.27
#### 表明 insurance
#### 表说明 产品接口来源表
#### 迁移文件 2017_08_14_110031_create_insurance_api_from_table.php
- 操作：添加  字段：sell_status  作用：可售状态 0配置中 1可售  原类型:tinyInteger   目标类型：无   默认值：0  原因：控制代理商系统是否可同步 可空：否

2017.11.28
#### 表名 ins_order
#### 表说明 订单表
#### 迁移文件 2017_06_16_022602_create_ins_order_table.php
- 操作：添加  字段：start_time end_time  作用：记录生效、失效时间  类型:timestamp   可空：是

2017.12.19
#### 表名 ins_users
#### 表说明 用户表
#### 迁移文件 2017_06_16_022602_create_ins_users_table.php
#### 操作人  王石磊
- 操作：添加  字段：sell_status  作用：中介渠道同步产品码  类型:int   默认值：0

#### 表名 insurance
#### 表说明 产品表
#### 迁移文件 2017_05_25_091752_create_insurance_table.php
#### 操作人  王石磊
- 操作：添加  字段： insure_resourse  作用：产品资源文件路径（费率表、职业信息、地区信息等）  类型:varchar   可空：是

#### 表名 insurance_health
#### 表说明 产品健康告知关联表
#### 迁移文件 2017_05_25_091752_create_insurance_table.php
#### 操作人  王石磊
- 操作：新增  字段：insurance_id  作用：产品ID，关联产品表  类型:int
- 操作：新增  字段：content       作用：健康告知内容  类型:varchar   
- 操作：新增  字段：order         作用：显示顺序      类型:int   
- 操作：新增  字段：ondition      作用：限制条件      类型:varchar
- 操作：新增  字段：checked       作用：默认选中值    类型:varchar 

2017.12.22
#### 表名 insurance_api_from
#### 表说明 产品信息表
#### 迁移文件 2017_08_14_110031_create_insurance_api_from_table
#### 操作人  陶明扬
- 操作：添加  字段：template_url  作用：团险模版  类型:varchar  可空：是

#### 表名 ins_order_insure_info
#### 表说明 保险订单被保人信息表
#### 迁移文件 2017_08_14_110031_create_insurance_api_from_table.php
#### 操作人  陶明扬
- 操作：添加  字段： e_policy_url  作用：电子保单存放地址  类型:varchar   可空：是

