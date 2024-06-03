
select
        DATE_FORMAT(contract.sign_time,'%Y-%m') 月份,
        product1.product_name 产品,
				tag1.tag_name 版本,
				count(DISTINCT product.customer_id) 客户数量,
        count(distinct contract.id) 合同数量,
        ifnull(sum(case when contract.id is not null then product.actually_price end),0) 合同金额
        from (select a.contract_id,a.customer_id,a.receivables_id,a.module_id,a.product_id,sum(a.actually_price) actually_price
        from (
        SELECT receivables.contract_id,
        receivables.customer_id,
        product.receivables_id,
        product.module_id,
        product.product_id,
        product.product_item_id,
        actually_price
        FROM t_salecrm_receivables_product product
        left join t_salecrm_receivables receivables on product.receivables_id = receivables.id
        where receivables.deleted = 0
        GROUP BY receivables.contract_id, product.product_item_id) a group by a.contract_id, a.product_id) product
        left join t_salecrm_contract contract on contract.id = product.contract_id
        left join t_salecrm_customer customer on customer.id = product.customer_id
				left join t_salecrm_product product1 on product1.id = product.product_id
				left join t_salecrm_tags tag1 on product.module_id = tag1.id
        left join (select contract_id,sum(payment_amount) as payment_amount from t_salecrm_payment where
        deleted = 0
        and exists(select id from t_salecrm_receivables where id = receivables_id and deleted = 0)
        group by contract_id) payment on payment.contract_id = contract.id
        where
            contract.deleted = 0
            and contract.status in (3,4,6)
            and DATE_FORMAT(contract.sign_time,'%Y-%m') between '2023-01' and '2023-12'
        group by DATE_FORMAT(contract.sign_time,'%Y-%m'),product.product_id,product.module_id



-- 需求交付SLA
--  业务需求|产品需求
--  Y|-
--  N|Y


select zt_story.id as bz_id , zt_story.title as bz_title , zt_story.openedDate as bz_openedDate, ifnull(zt_story.rspAcceptTime,ifnull(zt_story.rspRejectTime,ifnull(zt_story.rspResearchTime,ifnull(zt_story.rspRecievedTime,(case when (zt_story.closedDate is null or zt_story.closedDate='0000-00-00') then now() else zt_story.closedDate end ))))) as bz_rspTime
    , zt_story.planReleaseDate as bz_planReleaseDate, zt_story.warning as bz_warning, zt_story.type as bz_type, zt_story.responseResult as bz_responseResult, zt_story.status as bz_status, zt_story.stage as bz_stage
    , zs2.id as pd_id , zs2.title as pd_title , zs2.openedDate as pd_openedDate, ifnull(zs2.rspAcceptTime,ifnull(zs2.rspRejectTime,ifnull(zs2.rspResearchTime,ifnull(zs2.rspRecievedTime,(case when (zs2.closedDate is null or zs2.closedDate='0000-00-00') then now() else zs2.closedDate end ))))) as pd_rspTime
    , zs2.planReleaseDate as pd_planReleaseDate, zs2.warning as pd_warning, zs2.type as pd_type, zs2.responseResult as pd_responseResult, zs2.status as pd_status, zs2.stage as pd_stage
    , (case when (zs2.closedDate is null or zs2.closedDate='0000-00-00') then now() else zs2.closedDate end ) as dev_endTime
    , timestampdiff( minute, zt_story.openedDate, ifnull(zt_story.rspAcceptTime,ifnull(zt_story.rspRejectTime,ifnull(zt_story.rspResearchTime,ifnull(zt_story.rspRecievedTime,(case when (zt_story.closedDate is null or zt_story.closedDate='0000-00-00') then now() else zt_story.closedDate end ))))) )/60/24 as sla_bz
    , timestampdiff( minute, ifnull(zt_story.rspAcceptTime,ifnull(zt_story.rspRejectTime,ifnull(zt_story.rspResearchTime,ifnull(zt_story.rspRecievedTime,(case when (zt_story.closedDate is null or zt_story.closedDate='0000-00-00') then now() else zt_story.closedDate end ))))), ifnull(zs2.rspAcceptTime,ifnull(zs2.rspRejectTime,ifnull(zs2.rspResearchTime,ifnull(zs2.rspRecievedTime,(case when (zs2.closedDate is null or zs2.closedDate='0000-00-00') then now() else zs2.closedDate end ))))) )/60/24 as sla_pd
    , timestampdiff( minute, ifnull(zs2.rspAcceptTime,ifnull(zs2.rspRejectTime,ifnull(zs2.rspResearchTime,ifnull(zs2.rspRecievedTime,(case when (zs2.closedDate is null or zs2.closedDate='0000-00-00') then now() else zs2.closedDate end ))))), (case when (zs2.closedDate is null or zs2.closedDate='0000-00-00') then now() else zs2.closedDate end ) )/60/24 as sla_dev
    , zt_purchaser.name as bz_purchaser_name 
    , zt_product.name as bz_product_name
    
from zt_story 
    left join zt_relation zr on ( zt_story.id = zr.aid and zr.atype = 'requirement' and zr.btype = 'story' )
    left join zt_story zs2 on ( zr.bid = zs2.id and zs2.type = 'story'  ) 
    join zt_product on ( zt_story.product = zt_product.ID and zt_product.program = 223 )
    join zt_purchaser on ( case when zt_story.purchaser like  ',%' then substring_index(SUBSTRING(zt_story.purchaser,2),',',1) else substring_index(zt_story.purchaser,',',1) end )  = zt_purchaser.code

where zt_story.type = 'requirement' and  zt_story.deleted = '0' and timestampdiff( day, zt_story.openedDate, now()) <= 183 
    and zs2.deleted = '0' and timestampdiff( day, zs2.openedDate, now()) <= 183 and zt_story.responseResult = 'suspend'
    [[ and {{bz_title}} ]] [[ and {{bz_openedDate}} ]] [[ and {{bz_planReleaseDate}} ]] [[ and {{bz_purchaser_name}} ]] [[ and {{bz_product_name}} ]]
    
union all

select '' as bz_id , '' as bz_title , zt_story.openedDate as bz_openedDate, ifnull(zt_story.rspAcceptTime,ifnull(zt_story.rspRejectTime,ifnull(zt_story.rspResearchTime,ifnull(zt_story.rspRecievedTime,(case when (zt_story.closedDate is null or zt_story.closedDate='0000-00-00') then now() else zt_story.closedDate end ))))) as bz_rspTime
    , zt_story.planReleaseDate as bz_planReleaseDate, zt_story.warning as bz_warning, zt_story.type as bz_type, zt_story.responseResult as bz_responseResult, zt_story.status as bz_status, zt_story.stage as bz_stage
    , zt_story.id as pd_id , zt_story.title as pd_title , zt_story.openedDate as pd_openedDate, ifnull(zt_story.rspAcceptTime,ifnull(zt_story.rspRejectTime,ifnull(zt_story.rspResearchTime,ifnull(zt_story.rspRecievedTime,(case when (zt_story.closedDate is null or zt_story.closedDate='0000-00-00') then now() else zt_story.closedDate end ))))) as pd_rspTime
    , zt_story.planReleaseDate as pd_planReleaseDate, zt_story.warning as pd_warning, zt_story.type as pd_type, zt_story.responseResult as pd_responseResult, zt_story.status as pd_status, zt_story.stage as pd_stage
    , (case when (zt_story.closedDate is null or zt_story.closedDate='0000-00-00') then now() else zt_story.closedDate end ) as dev_endTime
    , timestampdiff( minute, zt_story.openedDate, ifnull(zt_story.rspAcceptTime,ifnull(zt_story.rspRejectTime,ifnull(zt_story.rspResearchTime,ifnull(zt_story.rspRecievedTime,(case when (zt_story.closedDate is null or zt_story.closedDate='0000-00-00') then now() else zt_story.closedDate end ))))) )/60/24 as sla_bz
    , timestampdiff( minute, ifnull(zt_story.rspAcceptTime,ifnull(zt_story.rspRejectTime,ifnull(zt_story.rspResearchTime,ifnull(zt_story.rspRecievedTime,(case when (zt_story.closedDate is null or zt_story.closedDate='0000-00-00') then now() else zt_story.closedDate end ))))), ifnull(zt_story.rspAcceptTime,ifnull(zt_story.rspRejectTime,ifnull(zt_story.rspResearchTime,ifnull(zt_story.rspRecievedTime,(case when (zt_story.closedDate is null or zt_story.closedDate='0000-00-00') then now() else zt_story.closedDate end ))))) )/60/24 as sla_pd
    , timestampdiff( minute, ifnull(zt_story.rspAcceptTime,ifnull(zt_story.rspRejectTime,ifnull(zt_story.rspResearchTime,ifnull(zt_story.rspRecievedTime,(case when (zt_story.closedDate is null or zt_story.closedDate='0000-00-00') then now() else zt_story.closedDate end ))))), (case when (zt_story.closedDate is null or zt_story.closedDate='0000-00-00') then now() else zt_story.closedDate end ) )/60/24 as sla_dev
    , zt_purchaser.name as bz_purchaser_name 
    , zt_product.name as bz_product_name
    
from zt_story 
    join zt_product on ( zt_story.product = zt_product.ID and zt_product.program = 223 )
    join zt_purchaser on ( case when zt_story.purchaser like  ',%' then substring_index(SUBSTRING(zt_story.purchaser,2),',',1) else substring_index(zt_story.purchaser,',',1) end )  = zt_purchaser.code

where  zt_story.type = 'story' and zt_story.deleted = '0' and timestampdiff( day, zt_story.openedDate, now()) <= 183 
    and zt_story.deleted = '0' and timestampdiff( day, zt_story.openedDate, now()) <= 183 
    and zt_story.id not in ( select distinct zr.bid from zt_relation zr where  zr.btype = 'story' )
    [[ and {{bz_title}} ]] [[ and {{bz_openedDate}} ]] [[ and {{bz_planReleaseDate}} ]] [[ and {{bz_purchaser_name}} ]] [[ and {{bz_product_name}} ]]




-- B100的产品需求未关联大客定开项目的清单
select distinct zt.id as story_id, zt.title as story_title, ifnull(b100_bound.story_id,0) as bound 
from zt_story zt
    join zt_purchaser ztp on  ( ztp.category in ('B5','B100') and  locate( concat(',',ztp.code,',') , concat(',',zt.purchaser,',') ) >0  and ztp.name not like '%OKR%' )
    left join (
            select distinct zps.story as story_id
            from zt_projectstory zps 
                join zt_project zp on ( zps.project = zp.id and (zp.path like  ',223,371,%' or zp.path like  ',223,447,%' ) and zp.type = 'project'  and zp.deleted = '0' )
        ) b100_bound on ( zt.id = b100_bound.story_id )
where zt.deleted = '0' and zt.type = 'story' 
order by zt.id

select distinct zt.id as story_id, zt.title as story_title, ifnull(zps.story,0) as bound 
from zt_story zt
    join zt_purchaser ztp on  ( ztp.category in ('B5','B100') and  locate( zt.purchaser, concat(',',ztp.code,',') ) >0  and ztp.name not like '%OKR%' )
    left join zt_projectstory zps on ( zt.id = zps.story  )
    left join zt_project zp on ( zps.project = zp.id and (zp.path like  ',223,371,%' or zp.path like  ',223,447,%' ) and zp.type = 'project'  and zp.deleted = '0' )
where zt.deleted = '0' and zt.type = 'story' 
order by zt.id


SELECT *
FROM zt_purchaser ztp 
WHERE NAME LIKE '%宝洁%' or NAME LIKE '%雅诗兰黛%' or NAME LIKE '%欧莱雅%' or NAME LIKE '%博西%' or NAME LIKE '%安踏%' 

--  大客定开产品需求清单
select zps.story as B100_storyID, '大客' as B100_purchaser_type, min(zp.openedDate) as B100_project_minOpenDate
from zt_projectstory zps 
    join zt_project zp on ( zps.project = zp.id and (zp.path like  ',223,371,%' or zp.path like  ',223,447,%' ) and zp.type = 'project'  and zp.deleted = '0' )
group  by  zps.story

--  指定产品需求的任务清单
select  zt.story, zt.name, zt.consumed, ze.id, ze.consumed

from zt_task zt 
    left join zt_effort ze on  ( zt.id = ze.objectID and ze.objectType = 'task' )

where zt.deleted = '0' and zt.parent > -1 and TIMESTAMPDIFF(day, zt.openedDate, now()) <= 183
  --  and ( zt.story = 3885 or  ( zt.story between 4213 and 4218 )  )
    

--  所有项目或迭代的任务工时记录: 以关联产品需求的任务为准
select ifnull(zl.value,zt.`type` ) as task_type_name
    , (case when zs.purchaser like  ',%' then substring_index(SUBSTRING(zs.purchaser,2),',',1) else substring_index(zs.purchaser,',',1) end ) as zs_purchaser_first ,  zt_pur_story.name as purchaser_name
    , (case when zs_req.purchaser like  ',%' then substring_index(SUBSTRING(zs_req.purchaser,2),',',1) else substring_index(zs_req.purchaser,',',1) end ) as zs_req_purchaser_first ,  zt_pur_req.name as req_purchaser_name
    , (case when (zp_proj.path like  ',223,371,%' ) then '大客' when ( zp_proj.path like  ',223,447,%') then '大客' else '标品' end ) as purchaser_type 
    , (case when (zd.name like  '物流%' ) then '物流' when (zd.name like  '%产品市场%'  ) then '产品市场' when (zd.name like  '%解决方案%'  ) then '解决方案' 
        when (zd.name like  '%业务%' or zd.name like  '%上海%' or zd.name like  '%中间件%' or zd.name =  '研发中心'  ) then '研发' when (zd.name like  '%效能%'  ) then '效能改进'
        when (zd.name like  '%产品%' or zd.name =  '产品团队' ) then '产品' when (zd.name like  '%保障%' or zd.name =  '运维团队' ) then 'SRE' 
        when (zd.name like  '%服务%' or zd.name =  '客户成功' ) then '服务' else '其他' end ) as dept_name
    , zt.`id` AS `id`, zt.`project` AS `project`, zt.`parent` AS `parent`, zt.`execution` AS `execution`, zt.`module` AS `module`, zt.`design` AS `design`, zt.`story` AS `story`, zt.`storyVersion` AS `storyVersion`
        , zt.`designVersion` AS `designVersion`, zt.`fromBug` AS `fromBug`, zt.`fromIssue` AS `fromIssue`, zt.`feedback` AS `feedback`, zt.`name` AS `name`, zt.`type` AS `type`, zt.`mode` AS `mode`, zt.`pri` AS `pri`
        , zt.`estimate` AS `estimate`, zt.`consumed` AS `consumed`, zt.`left` AS `left`, zt.`deadline` AS `deadline`, zt.`status` AS `status`, zt.`subStatus` AS `subStatus`, zt.`color` AS `color`, zt.`mailto` AS `mailto`
        , zt.`desc` AS `desc`, zt.`version` AS `version`, zt.`openedBy` AS `openedBy`, zt.`openedDate` AS `openedDate`, zt.`assignedTo` AS `assignedTo`, zt.`assignedDate` AS `assignedDate`, zt.`estStarted` AS `estStarted`
        , zt.`realStarted` AS `realStarted`, zt.`finishedBy` AS `finishedBy`, zt.`finishedDate` AS `finishedDate`, zt.`finishedList` AS `finishedList`, zt.`canceledBy` AS `canceledBy`, zt.`canceledDate` AS `canceledDate`
        , zt.`closedBy` AS `closedBy`, zt.`closedDate` AS `closedDate`, zt.`realDuration` AS `realDuration`, zt.`planDuration` AS `planDuration`, zt.`closedReason` AS `closedReason`, zt.`lastEditedBy` AS `lastEditedBy`
        , zt.`lastEditedDate` AS `lastEditedDate`, zt.`activatedDate` AS `activatedDate`, zt.`order` AS `order`, zt.`repo` AS `repo`, zt.`mr` AS `mr`, zt.`entry` AS `entry`, zt.`lines` AS `lines`
        , zt.`v1` AS `v1`, zt.`v2` AS `v2`, zt.`deleted` AS `deleted`, zt.`vision` AS `vision` 
	, zp_exec.id as exec_id, zp_exec.name as exec_name, zp_exec.`code` as exec_code, zp_exec.budget as exec_budget, zp_exec.parent as exec_parent, zp_exec.path as exec_path
	    , zp_exec.`begin` as exec_begin, zp_exec.`end` as exec_end, zp_exec.realBegan as exec_realBegan, zp_exec.realEnd as exec_realEnd, zp_exec.`status` as exec_status, zp_exec.openedBy as exec_openedBy, zp_exec.openedDate as exec_openedDate
	    , zp_exec.planDuration as exec_planDuration, zp_exec.realDuration as exec_realDuration, zp_exec.PM as exec_PM
	    , zp_exec.PO as exec_PO, zp_exec.QD as exec_QD, zp_exec.RD as exec_RD
	, zp_proj.id as proj_id, zp_proj.name as proj_name, zp_proj.code as proj_code, zp_proj.budget as proj_budget, zp_proj.parent as proj_parent, zp_proj.path as proj_path
	    , zp_proj.`begin` as proj_begin, zp_proj.`end` as proj_end, zp_proj.realBegan as proj_realBegan, zp_proj.realEnd as proj_realEnd, zp_proj.`status` as proj_status, zp_proj.openedBy as proj_openedBy, zp_proj.openedDate as proj_openedDate
	    , zp_proj.planDuration as proj_planDuration, zp_proj.realDuration as proj_realDuration, zp_proj.PM as proj_PM
	, zs.id as zs_id, zs.parent as zs_parent, zs.product as zs_product, zs.title as zs_title, zs.estimate as zs_estimate, zs.status as zs_status
	    , zs.stage as zs_stage, zs.openedBy as zs_openedBy, zs.openedDate as zs_openedDate, zs.assignedTo as zs_assignedTo, zs.assignedDate as zs_assignedDate
	    , zs.bzCategory as zs_bzCategory, zs.prCategory as zs_prCategory, zs.uatDate as zs_uatDate, zs.purchaser as zs_purchaser, zs.responseResult as zs_responseResult, zs.prLevel as zs_prLevel, zs.bizProject as zs_bizProject
	, zp_prod.id as prod_id, zp_prod.program as prod_program, zp_prod.name as prod_name, zp_prod.`status` as prod_status, zp_prod.PO as prod_PO
	, zs_req.id as req_id, zs_req.parent as req_parent, zs_req.product as req_product, zs_req.title as req_title, zs_req.estimate as req_estimate, zs_req.`status` as req_status
	    , zs_req.stage as req_stage, zs_req.openedBy as req_openedBy, zs_req.openedDate as req_openedDate, zs_req.assignedTo as req_assignedTo, zs_req.assignedDate as req_assignedDate
	    , zs_req.bzCategory as req_bzCategory, zs_req.prCategory as req_prCategory, zs_req.uatDate as req_uatDate, zs_req.purchaser as req_purchaser, zs_req.responseResult as req_responseResult, zs_req.prLevel as req_prLevel, zs_req.bizProject as req_bizProject
	, ze.`date` AS ze_date, ze.`consumed` AS ze_consumed, timestampdiff(day,ifnull(ze.`date`,date_add(now(), interval 1 day) ),now()) as ze_interval_days
	, zu.realname as zu_realname
	, zd.name as dept_name_team

from zt_task zt 
	INNER JOIN zt_lang zl  ON ( zt.`type` = zl.key and zl.lang = 'zh-cn' and zl.module = 'task' and zl.`section` = 'typeList' )
	inner join zt_project zp_exec on ( zt.execution = zp_exec.id and zp_exec.`type` = 'sprint' )
	inner join zt_project zp_proj on ( zt.project = zp_proj.id 	and zp_proj.`type` = 'project' )
	left join zt_story zs on ( zt.story = zs.id and zs.`type` = 'story' )
	left join zt_product zp_prod  on zs.product  = zp_prod.id
	left join zt_relation zr on ( zt.story = zr.aid 	and zr.atype = 'story'	)
	left join zt_story zs_req on zr.bid = zs_req.id
	left join zt_effort ze on zt.id = ze.objectID
	left join zt_user zu on ze.account = zu.account 
	left join zt_dept zd on zu.dept = zd.id 
	left join zt_purchaser zt_pur_story  on (case when zs.purchaser like  ',%' then substring_index(SUBSTRING(zs.purchaser,2),',',1) else substring_index(zs.purchaser,',',1) end ) = zt_pur_story.code
	left join zt_purchaser zt_pur_req  on (case when zs_req.purchaser like  ',%' then substring_index(SUBSTRING(zs_req.purchaser,2),',',1) else substring_index(zs_req.purchaser,',',1) end ) = zt_pur_req.code
	
where zt.deleted = '0' and zt.parent > -1 and TIMESTAMPDIFF(day, zt.openedDate, now()) <= 183

