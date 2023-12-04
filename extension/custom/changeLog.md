[toc]
#  rel231204
## fixed:
1. fixed: 复制bug是携带反馈者 客户名称 反馈时间 收集时间 发生环境 抄送人字段

#  rel231103

## changed:
1. task#27836: 历史记录在通知消息内，且project+execution也通知
2. 

## fixed:
1. fixed: 需求超时是大于而非小于指定的天数


#  rel231025

## changed:
1. 存在cronmethod.php才导入
2. 待响应(todo)的未关闭需求超时(>92d)关闭
3. 项目添加合同人天、外包人天、定开人天、标品人天字段

## fixed:
1. 


#  rel231010

## changed:
1. 优化starLink的客户数据同步
2. 仅自定义cron才打log

## fixed:
1. 搜索列下拉列表动态获取[bug#22816](https://chandao.bytenew.com/zentao/bug-view-22816.html)


# rel20230918

## changed:
1. 需求closed时,添加prdReviewTime,releaseTime必填

#  rel230830

## changed:
1. 需求closed时,记录assigendTo的变化
2. 可定制在后台功能上可配置必填项的选项
3. 项目合同编号赋初始值"无"
4. 项目集、项目、执行默认都是公开(open)



## fixed:
1. 修复需求的工时类型初始值问题


#  rel230828
## changed:
1. id:7522 需求添加工时类型字段
2. id:7235 需求新增业务可以输入抄送人
3. id:7430 业务需求的响应结果若是reject、research、suspend时，无任何动作且未关闭的自动关闭
## fixed:
1. 需求搜索加新字段筛选


#  rel230809
## changed:

1. 添加prd内容审核时间&发布时间字段


#  rel230628

## changed:

1. bug创建人可以由参数指定
3. cron去除运行天数的限制


## fixed:

1. 解决同步星链时code变化关联数据跟随的问题([#19384](https://chandao.bytenew.com/zentao/bug-view-19384.html))
2. helper::isZeroDate 判断空日期
3. 定制buildData的通知信息(钉钉单聊markdown消息)


#  rel230613

## changed:

1. webhook支持钉钉单聊通知
1. 需求通知改为markdown消息

## fixed:

1. 定时任务加log，仅大于当前时间跳过
2. 同步星链允许30s误差，解决名称不同但pinyin相同的缺陷
3. helper::isZeroDate 判断空日期
4. 需求的项目下拉选项([#20008](https://chandao.bytenew.com/zentao/bug-view-20008.html))



#  rel230525

## changed:

1. 产品需求计划上线时间更新到业务需求
2. 产品需求项目名称更新到业务需求

## fixed:

1. 需求类型显示bug修复
2. 项目名称取正马项目集
3. status值错误修复
4. 数组修改为对象取值

#  rel230505

## changed:

1. 通过task<devel>更新产品需求的prd时间
2. 项目新增字段：产研评估人天+合同编号

## fixed:  

1. 

#  rel230420

## changed:

1. 需求新增字段：预警级别 计划上线时间; 支持增删改查，批增批改，统计
2. 

## fixed:  

1. 修复bug编辑时产品都是首个的缺陷
2. 修复按ID更新业务需求权限配置无效缺陷(updateRequirementStatusStageByStoryID->updateReqStatusStageByID)
3. 更新业务需求status和stage的备注改由system添加



#  rel230417

## changed:

1. 客户名称可refresh
2. 二开权限配置

## fixed:  

1. 同same不同code的删除之
2. 变更 不校验客户XXX


#  rel230413

## changed:

1. 更新业务需求的status和stage
2. 版本升级到18.3
3. 业务需求设置指派人时，也设置指派时间

## fixed:  

1. 修复业务需求二开字段必填项无校验的缺陷
2. 


#  rel230310

## changed:

1.
2. 

## fixed:  

1. 定时任务加载同模块(story)的control扩展方法文件仅支持一个，使用cronmethod.php进行整合
2. 




#  rel230303

## changed:

1.
2. 

## fixed:  

1. 客户名称唯一和category判断准确性
2. scoreNum默认0且去required
3. 解决正式禅道的需求页面布局问题
4. 禅道返回值格式差异性的特殊判别处理



#  rel230220

## changed:

1. 人员的电话改为手机(钉钉)
2. 新增 行为分(scoreNum) 并自动选择客户后带出客户分层和行为分(星链已支持)

## fixed:  

#  rel230214

## added:

1. 

## fixed:  

1. 客户同步性能优化
2. 需求钉钉群提醒数据增加查询参数：$program=223, $responseResult='todo'
3. dept改回单选并'/'是无条件


#  rel230207

## added:

1. 需求支持发送钉钉群提醒消息(含需求<未处理+挂起>指派人是''重置)：禅道配置定时任务或手工触发

## fixed:  

1. 


#  rel230111

## added:

1. 优化：搜索选择组件组装条件封装为函数(conditionsBySelectValue)

## fixed:  

1. 修复搜索选择组件{project,execution,product,module,plan}选择"all"查询不到数据的缺陷


#  rel230108

## added:

1. 'control' => 'multi-select' 或 'select'  搜索都支持多选

## fixed:  

1. 
2. 


#  rel230107

## added:

1. 需求新增字段"绝对序"

## fixed:  

1. openedBy,assignedTo 修复选一项或空查询缺陷
2. 

#  rel230105

## added:

1. 

## fixed:  

1. control目录下的文件名全部要是小写，防止linux区分大小写无法重载函数的缺陷
2. 

#  rel230104

## added:

1. openedBy,assignedTo 支持多选查询
2. 支持自加客户名称: http://127.0.0.1:81/zentao/story-addPurchaser-[客户名称*]-[班牛ID]-[客户分层].json?tid=usb7jpuw
3. 支持业务需求细分为产品需求的首行默认值

## fixed:  

1. 
2. 


#  rel221215

## added:

1. 需求分类 调整

## fixed:  

1. 修复pinyin调用问题
2. 修复星链无ID或00开始的ID或重名的问题


#  rel221209

## added:

1. 客户名称定时或触发从星链同步
   
2. 客户名称、客户分层 仅业务需求是必填项


#  rel221207

## fixed:  

1. 需求的"备注" 插入重复缺陷

## added:

1. 客户名称支持多选{bug,需求}, PS:  批量操作暂不支持
   
2. 需求新增字段: 项目名称，需求等级，响应XXX时间(rsp*Time 用于计算业务需求SLA)


#  rel221107

## fixed:  

1. 需求的"清除" 功能不可用缺陷

2. 产品需求的"所处阶段"全显示

3. bug的编辑无法上传附件

## added:

1. 客户名称从星链导入客户数据


#  rel221102

## added:

1. 需求增加字段 {客户名称，uat日期，客户分层，需求分类 }
2. bug增加字段  {客户名称，反馈日期，收集日期 }
