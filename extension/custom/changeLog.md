[toc]


#  rel230110

## added:

1. 优化：搜索选择组件组装条件封装为函数(conditionsBySelectValue)

## fixed:  

1. 修复搜索选择组件选择"all"查询不到数据的缺陷


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
