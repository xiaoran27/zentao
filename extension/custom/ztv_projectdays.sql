  
-- 项目需求人天统计
-- 取消的任务不计算工时
CREATE OR REPLACE VIEW ztv_projectstroy_days AS 
  select zt_project.id as proj_id, zt_project.name as proj_name
      , zt_task.story as story_id, ifnull(zt_story.workType,"saas") as work_type
      , sum(ifnull(zt_task.estimate,0) * (case when zt_task.status='wait' then 0 when (zt_task.status='doing'  or ( zt_task.status in ('cancel','pause') and ifnull(zt_task.consumed,0)>0 )) then 0.5 when zt_task.status in ('done','closed') then 1.0 else 0.0 end) )/8  as bcwp  
      , sum(if(zt_task.status='cancel',0,ifnull(zt_task.estimate,0)))/8 as estimate   -- bcws_all
      , sum(if(zt_task.estStarted>curdate() or zt_task.status='cancel',0,ifnull(zt_task.estimate,0)) ) /8 as bcws
      , sum(zt_task.consumed)/8 as consumed   -- acwp_all,  acwp=consumed_self+consumed_outer
      , sum(if(ifnull(zt_story.workType,"saas")="saas", zt_task.consumed,0))/8 as consumed_saas
      , sum(if(ifnull(zt_story.workType,"saas")="self", zt_task.consumed,0))/8 as consumed_self
      , sum(if(ifnull(zt_story.workType,"saas")="outer", zt_task.consumed,0))/8 as consumed_outer
  from zt_project
      join zt_projectstory on ( zt_project.id = zt_projectstory.project )
      join zt_story on ( zt_story.id = zt_projectstory.story and zt_story.deleted = '0'  )
      join zt_task on ( zt_task.story = zt_story.id and zt_task.story > 0 and zt_task.deleted = '0' and zt_task.parent > -1  and  not ( zt_task.status = 'cancel' or zt_task.closedReason = 'cancel' ) )
  where zt_project.deleted = '0'  and zt_project.path like  ',223,%' 
      and zt_project.type = 'project' 
      and DATEDIFF(NOW(), zt_project.begin) <= (365+183)
  group by proj_id, story_id,work_type
 
  union all
  
  --  项目或迭代的未关联需求的任务
  select zt_project.id as proj_id, zt_project.name as proj_name
      , zt_task.story as story_id, "self" as work_type
      , sum(ifnull(zt_task.estimate,0) * (case when zt_task.status='wait' then 0 when (zt_task.status='doing'  or ( zt_task.status in ('cancel','pause') and ifnull(zt_task.consumed,0)>0 )) then 0.5 when zt_task.status in ('done','closed') then 1.0 else 0.0 end) )/8  as bcwp  
      , sum(if(zt_task.status='cancel',0,ifnull(zt_task.estimate,0)))/8 as estimate   -- bcws_all
      , sum(if(zt_task.estStarted>curdate() or zt_task.status='cancel',0,ifnull(zt_task.estimate,0)) ) /8 as bcws
      , sum(zt_task.consumed)/8 as consumed   -- acwp_all,  acwp=consumed_self+consumed_outer
      , 0 as consumed_saas
      , sum(zt_task.consumed)/8 as consumed_self
      , 0 as consumed_outer
  from zt_project
      join zt_task on ( zt_project.id = zt_task.project and zt_task.story = 0 and zt_task.deleted = '0' and zt_task.parent > -1  and not ( zt_task.status = 'cancel' or zt_task.closedReason = 'cancel' )  ) 
      -- join zt_task on ( zt_project.id = zt_task.project and zt_task.story = 0 and zt_task.deleted = '0' and zt_task.parent <=0 )
  where zt_project.deleted = '0'  and zt_project.path like  ',223,%' 
      and zt_project.type = 'project' 
      and DATEDIFF(NOW(), zt_project.begin) <= (365+183)
  group by proj_id,story_id,work_type ;
 
-- 项目人天统计
CREATE OR REPLACE VIEW ztv_projectdays AS
  select proj_id, proj_name
    ,sum(bcwp) as bcwp
    ,sum(estimate) as estimate
    ,sum(bcws) as bcws
    , sum(consumed) as consumed
    ,sum(if(work_type = 'saas',0,bcwp)) as notsaas_bcwp
    ,sum(if(work_type = 'saas',0,estimate)) as notsaas_estimate
    ,sum(if(work_type = 'saas',0,bcws)) as notsaas_bcws
    , sum(if(work_type = 'saas',0,consumed)) as notsaas_consumed
    ,sum(if(work_type != 'self',0,bcwp)) as self_bcwp
    ,sum(if(work_type != 'self',0,estimate)) as self_estimate
    ,sum(if(work_type != 'self',0,bcws)) as self_bcws
    , sum(if(work_type != 'self',0,consumed)) as self_consumed
    , sum(consumed_saas) as consumed_saas, sum(consumed_self) as consumed_self, sum(consumed_outer) as consumed_outer
  from ztv_projectstroy_days
  group by proj_id ;