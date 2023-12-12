-- mysql -u root -p zentao
-- use zentao;

-- 每次需要更新SQL段
-- sql.start.banniu_rel{yymmdd}
--  SQL语句
-- sql.end.banniu_rel{yymmdd}

-- sql.start.banniu_rel221102
-- 2022-10-16 22:00:45
ALTER TABLE zt_story ADD bzCategory varchar(16)  DEFAULT 'LKA' NULL COMMENT '客户类型';
ALTER TABLE zt_story ADD prCategory varchar(16)  DEFAULT 'product' NULL COMMENT '需求分类';
ALTER TABLE zt_story ADD uatDate DATE NULL COMMENT 'UAT日期';

ALTER TABLE zt_story ADD purchaser varchar(64) NULL COMMENT '客户名称';


-- 2022-10-16 22:11:41
ALTER TABLE zt_bug ADD purchaser varchar(256) NULL COMMENT '客户名称'; 


-- 2022-10-20 20:30:15
ALTER TABLE zt_bug ADD occursEnv varchar(128)  DEFAULT 'online' NULL COMMENT '发生环境'; 
ALTER TABLE zt_bug ADD feedbackTime datetime   NULL COMMENT '反馈时间'; 
ALTER TABLE zt_bug ADD collectTime datetime   NULL COMMENT '收集时间'; 

-- 2022-10-27 21:09:50
ALTER TABLE zt_story ADD responseResult smallint  DEFAULT 0 NULL COMMENT '响应结果'; 


-- 2022-10-31 10:19:14
CREATE TABLE `zt_purchaser` (
  `ID` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(32) NOT NULL COMMENT '客户编码',
  `name` varchar(128) NOT NULL COMMENT '客户名称',
  `parentID` bigint(20) unsigned NOT NULL DEFAULT 0 COMMENT '父ID',
  `category` varchar(32) NOT NULL COMMENT '客户分层',
  `ctime` datetime NOT NULL DEFAULT current_timestamp(),
  `creator` varchar(32) NOT NULL DEFAULT 'sys',
  `mtime` datetime NOT NULL DEFAULT current_timestamp(),
  `modifier` varchar(32) DEFAULT 'sys',
  `status` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `zt_purchaser_code_IDX` (`code`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户信息';
INSERT INTO zt_purchaser( code, name, category)
VALUES('hzzmrjkjyxgs', '杭州正马软件科技有限公司', 'LKA'),( 'shzmrjkjyxgs', '上海正马软件科技有限公司', 'LKA');

-- sql.end.banniu_rel221102


-- sql.start.banniu_rel221107

--2022-11-7 20:06:38
-- delete from zt_purchaser where mtime = '2022-11-07';   --1107可能有数据
--2022-11-8 16:33:32
-- delete from zt_purchaser where mtime = '2022-11-08';


-- sql.end.banniu_rel221107


-- sql.start.banniu_rel221125

-- 2022-11-18 17:24:37

-- insert into zt_purchaser (name,code, category,mtime) values('杭州正马软件科技有限公司','hzzmrjkjyxgs','LKA','2022-11-25')
-- ,('上海正马软件科技有限公司','shzmrjkjyxgs','LKA','2022-11-25') ;

-- sql.end.banniu_rel221125



-- sql.start.banniu_rel221207

-- 2022-11-28 20:31:58

update zt_purchaser set ctime=mtime ;
ALTER TABLE zt_purchaser MODIFY COLUMN  mtime datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ;

ALTER TABLE zt_story ADD prLevel varchar(32) DEFAULT 'C' NOT NULL COMMENT '需求等级';

ALTER TABLE zt_story MODIFY COLUMN responseResult varchar(32) DEFAULT 'todo' NOT NULL COMMENT '响应结果';
update zt_story set responseResult = 'todo' where responseResult='0';
update zt_story set responseResult = 'reject' where responseResult='1';
update zt_story set responseResult = 'research' where responseResult='2';
update zt_story set responseResult = 'accept' where responseResult='3';
ALTER TABLE zt_story ADD rspRecievedTime datetime NULL   COMMENT '接收时间';
ALTER TABLE zt_story ADD rspRejectTime datetime NULL   COMMENT '拒绝时间';
ALTER TABLE zt_story ADD rspResearchTime datetime NULL  COMMENT '调研时间';
ALTER TABLE zt_story ADD rspAcceptTime datetime NULL  COMMENT '接受时间';


ALTER TABLE zt_story ADD bizProject varchar(64) DEFAULT '' NOT NULL COMMENT '项目名称';

ALTER TABLE zt_storyspec ADD bizStage mediumtext  COMMENT '业务场景';
ALTER TABLE zt_storyspec ADD bizTarget mediumtext COMMENT '达成目标';
ALTER TABLE zt_storyspec ADD bizValue mediumtext  COMMENT '萃取价值';

-- 2022-12-6 17:29:39  可能有20*15字符
ALTER TABLE zt_story MODIFY COLUMN  purchaser varchar(256) NULL COMMENT '客户名称';
ALTER TABLE zt_bug MODIFY COLUMN  purchaser varchar(256) NULL COMMENT '客户名称'; 


-- sql.end.banniu_rel221207



-- sql.start.banniu_rel221215

-- 2022-12-15 22:17:29

--  拼音 , 74===00074
-- select  code, name from zt_purchaser where left (code,1)>'9' or left (code,1)='0'
-- select code, name from zt_purchaser where name in ( select name from zt_purchaser  group by name having  count(name) >1 )
delete from zt_purchaser where left (code,1)>'9' or left (code,1)='0' ;
CREATE UNIQUE INDEX zt_purchaser_name_IDX USING BTREE ON zentao.zt_purchaser (name) ;

-- sql.end.banniu_rel221215


-- sql.start.banniu_rel230104

-- 2022-12-20 18:51:05

-- 星链暂不支持，故手工先插入
-- insert into zt_purchaser (name,code, category,mtime) values('杭州正马软件科技有限公司','hzzmrjkjyxgs','LKA','2022-11-25')
-- ,('上海正马软件科技有限公司','shzmrjkjyxgs','LKA','2022-11-25') ;

-- insert into zt_purchaser (name,code, category,mtime) values('惠州TCL移动通信有限公司','hztclydtxyxgs','B500','2022-12-20');

-- 2022-12-26 12:14:17
-- insert into zt_purchaser (name,code, category,mtime) values('名鞋库网络科技有限公司','mxkwlkjyxgs','B500','2022-12-26');
-- URL添加客户 http://127.0.0.1:81/zentao/story-addPurchaser-[客户名称].json?tid=usb7jpuw

-- B101~500 ==> B500
update zt_purchaser set category='B500' where category like '%101%';
update zt_story set bzCategory='B500' where bzCategory like '%101%';

-- sql.end.banniu_rel230104


-- sql.start.banniu_rel230107

-- 2023-1-7 14:26:03
ALTER TABLE zt_story ADD COLUMN  asort varchar(16) DEFAULT '' NOT NULL COMMENT '绝对序: YYmmNNN';

-- sql.end.banniu_rel230107

-- sql.start.banniu_rel230220

-- 2023-2-17 15:28:28
ALTER TABLE zt_purchaser ADD scoreNum FLOAT DEFAULT 0 NOT NULL COMMENT '行为分.<3表示预警';
ALTER TABLE zt_story ADD scoreNum FLOAT DEFAULT 0 NOT NULL COMMENT '行为分.<3表示预警';

-- sql.end.banniu_rel230220


-- sql.start.banniu_rel230413

-- 2023-3-27 13:48:13

-- 忽略 zentao.tri_story_bu

-- sql.end.banniu_rel230413


-- sql.start.banniu_rel230420

-- 2023-4-18 16:47:11

ALTER TABLE zt_story ADD warning enum('0','1','2','3','4') DEFAULT '0' NOT NULL COMMENT '预警级别';
ALTER TABLE zt_story ADD planReleaseDate DATE NULL COMMENT 'planReleaseDate';

-- sql.end.banniu_rel230420


-- sql.start.banniu_rel230505

-- 2023/4/25 16:36
ALTER TABLE zt_project ADD contractNo VARCHAR(30) NULL comment '合同编号';

-- 2023/4/26 09:26
ALTER TABLE zt_project ADD devEvaluate int NULL comment '产研评估人天';

-- sql.end.banniu_rel230505

-- sql.start.banniu_rel20230807

alter table zt_story add prdReviewTime datetime null comment 'prd内容审核时间';
alter table zt_story add releaseTime datetime null comment '发布时间';

-- sql.end.banniu_rel20230807

-- sql.start.banniu_rel20230811

-- 忽略 zentao.tri_story_bu

-- sql.end.banniu_rel20230811


-- sql.start.banniu_rel20230818

-- 忽略 zentao.tri_story_bu

-- sql.end.banniu_rel20230818


-- sql.start.banniu_rel20230824
alter table zt_story add workType varchar(255) null comment '工时类型';

-- sql.end.banniu_rel20230824


-- sql.start.banniu_rel20230830

-- 忽略 zentao.tri_story_bu

-- sql.end.banniu_rel20230830


-- sql.start.banniu_rel20230911

-- 忽略 zentao.tri_story_bu

-- sql.end.banniu_rel20230911


-- sql.start.banniu_rel20231025

ALTER TABLE zt_project ADD poDays int NULL default 0 comment '合同人天';
ALTER TABLE zt_project ADD outerDays int NULL default 0 comment '外包人天';
ALTER TABLE zt_project ADD selfDays int NULL default 0 comment '定开人天';
ALTER TABLE zt_project ADD saasDays int NULL default 0 comment '标品人天';

-- sql.end.banniu_rel20231025


-- sql.end.banniu_rel20231122

DROP TRIGGER IF EXISTS zentao.tri_story_bu;
delimiter $$
CREATE TRIGGER tri_story_bu BEFORE UPDATE ON zt_story FOR EACH ROW
BEGIN

  -- responseResult及对应时间的处理
  IF( old.rspRecievedTime is null and ( 'recieved' = new.responseResult or 'suspend' = new.responseResult )  ) THEN
    SET new.rspRecievedTime=now();
  ELSEIF( old.rspResearchTime is null and  'research' = new.responseResult  ) THEN
    SET new.rspResearchTime=now();
    set new.rspRecievedTime=ifnull(old.rspRecievedTime, ifnull(new.rspRecievedTime, new.rspResearchTime));
  ELSEIF( old.rspRejectTime is null and 'reject' = new.responseResult  ) THEN
    SET new.rspRejectTime=now();
    set new.rspRecievedTime=ifnull(old.rspRecievedTime, ifnull(new.rspRecievedTime, new.rspRejectTime));
    set new.rspResearchTime=ifnull(old.rspResearchTime, ifnull(new.rspResearchTime, new.rspRejectTime));
    
    set new.prdReviewTime=ifnull(old.prdReviewTime, ifnull(new.prdReviewTime, new.rspRejectTime));
    set new.releaseTime=ifnull(old.releaseTime, ifnull(new.releaseTime, new.rspRejectTime));
  ELSEIF( old.rspAcceptTime is null and 'accept' = new.responseResult ) THEN
    SET new.rspAcceptTime=now();
    set new.rspRecievedTime=ifnull(old.rspRecievedTime, ifnull(new.rspRecievedTime, new.rspAcceptTime));
    set new.rspResearchTime=ifnull(old.rspResearchTime, ifnull(new.rspResearchTime, new.rspAcceptTime));
  ELSEIF( old.rspAcceptTime is null and 'prd' = new.responseResult ) THEN
    SET new.rspAcceptTime=now();
    set new.prdReviewTime=ifnull(new.prdReviewTime, ifnull(old.prdReviewTime, new.rspAcceptTime));
    set new.rspRecievedTime=ifnull(old.rspRecievedTime, ifnull(new.rspRecievedTime, new.rspAcceptTime));
  ELSEIF( 'prd' = new.responseResult and date_format(ifnull(new.prdReviewTime, '0000-00-00'), '%Y-%m-%d')='0000-00-00' ) THEN
    SET new.prdReviewTime=ifnull(new.rspAcceptTime,now());
  END if;
 
  -- status&stage的关闭处理
  IF( 'closed' = new.status or new.stage in ('verified', 'released', 'closed') ) THEN
    if ( date_format(ifnull(new.releaseTime, '0000-00-00'), '%Y-%m-%d')='0000-00-00' ) then
      set new.releaseTime=now();
    end if;
    if ( ifnull(old.assignedTo,'') != '' and old.assignedTo != 'closed' ) then
       set new.assignedTo = ifnull(new.assignedTo, old.assignedTo);
    end if;
    
    if (new.type = 'story' and 'prd' != old.responseResult and 'prd' != new.responseResult) then
      set new.responseResult = 'prd';
      
      if ( date_format(ifnull(new.rspAcceptTime, '0000-00-00'), '%Y-%m-%d')='0000-00-00' ) then
        select min( if( ! (type in ( 'devel','test' ) or status='closed'),CURDATE(), case when DATE_FORMAT(ifnull(realStarted,openedDate),'%Y-%m-%d') = '0000-00-00' then openedDate else realStarted end )) into @task_first_date 
        from zt_task 
        where deleted='0' and openedDate > '2023-01-01' 
          and parent > -1 and status in ( 'doing','done','closed' ) and story = old.id ;
        set new.rspAcceptTime=@task_first_date;
      end if;
                
      set new.rspAcceptTime=ifnull(old.rspAcceptTime, ifnull(new.rspAcceptTime, now()));
      set new.prdReviewTime=ifnull(new.prdReviewTime, ifnull(old.prdReviewTime, new.rspAcceptTime));
      set new.rspRecievedTime=ifnull(old.rspRecievedTime, ifnull(new.rspRecievedTime, new.rspAcceptTime));
      
    end if;
  END if;
  
  -- prdReviewTime或releaseTime有值对responseResult = 'todo'的处理
  IF ( new.responseResult = 'todo'
  		AND ( date_format(ifnull(new.prdReviewTime, '0000-00-00'), '%Y-%m-%d')!='0000-00-00' 
  			OR date_format(ifnull(new.releaseTime, '0000-00-00'), '%Y-%m-%d')!='0000-00-00'  ) ) THEN
    set new.responseResult = IF(new.type = 'story', 'prd', 'accept');
    set new.rspRecievedTime=if(date_format(ifnull(new.prdReviewTime, '0000-00-00'), '%Y-%m-%d')!='0000-00-00', new.prdReviewTime, new.releaseTime);
    set new.rspAcceptTime=new.rspRecievedTime;
     
  END IF;
  
  
  -- status的激活处理
  IF ( old.status = 'closed' and new.status = 'active') THEN
    set new.stage = IF(new.stage = 'closed', 'wait', new.stage);
    -- set new.prdReviewTime=if(date_format(ifnull(old.prdReviewTime, '0000-00-00'), '%H:%i:%S')!='00:00:00',"",old.prdReviewTime);
    set new.releaseTime=if(date_format(ifnull(old.releaseTime, '0000-00-00'), '%H:%i:%S')!='00:00:00',"",old.releaseTime);
  END IF;

  
END;
$$
delimiter ;

-- sql.end.banniu_rel20231122


-- sql.start.banniu_rel20231206
  
-- 项目需求人天统计
CREATE OR REPLACE VIEW ztv_projectstroy_days AS 
  select zt_project.id as proj_id, zt_project.name as proj_name, zt_task.story as story_id
      , sum(zt_task.estimate)/8 as estimate, sum(zt_task.consumed)/8 as consumed 
      , sum(if(ifnull(zt_story.workType,"saas")="saas", zt_task.consumed,0))/8 as consumed_saas
      , sum(if(ifnull(zt_story.workType,"saas")="self", zt_task.consumed,0))/8 as consumed_self
      , sum(if(ifnull(zt_story.workType,"saas")="outer", zt_task.consumed,0))/8 as consumed_outer
  from zt_project
      join zt_projectstory on ( zt_project.id = zt_projectstory.project )
      join zt_story on ( zt_story.id = zt_projectstory.story and zt_story.deleted = '0'  )
      join zt_task on ( zt_task.story = zt_story.id and zt_task.story > 0 and zt_task.deleted = '0' and zt_task.parent > -1  )
  where zt_project.deleted = '0'  and zt_project.path like  ',223,%' 
      and zt_project.type = 'project' 
      and DATEDIFF(NOW(), zt_project.begin) <= (365+183)
  group by proj_id, story_id
 
  union all
  
  --  项目或迭代的未关联需求的任务
  select zt_project.id as proj_id, zt_project.name as proj_name, zt_task.story as story_id
      , sum(zt_task.estimate)/8 as estimate, sum(zt_task.consumed)/8 as consumed 
      , sum(zt_task.consumed)/8 as consumed_saas
      , 0 as consumed_self
      , 0 as consumed_outer
  from zt_project
      join zt_task on ( zt_project.id = zt_task.project and zt_task.story = 0 and zt_task.deleted = '0' and zt_task.parent > -1 ) 
      -- join zt_task on ( zt_project.id = zt_task.project and zt_task.story = 0 and zt_task.deleted = '0' and zt_task.parent <=0 )
  where zt_project.deleted = '0'  and zt_project.path like  ',223,%' 
      and zt_project.type = 'project' 
      and DATEDIFF(NOW(), zt_project.begin) <= (365+183)
  group by proj_id,story_id ;
 
-- 项目人天统计
CREATE OR REPLACE VIEW ztv_projectdays AS
  select proj_id, proj_name
    ,sum(estimate) as estimate, sum(consumed) as consumed
    , sum(consumed_saas) as consumed_saas, sum(consumed_self) as consumed_self, sum(consumed_outer) as consumed_outer
  from ztv_projectstroy_days
  group by proj_id ;


-- select @@event_scheduler;
-- set global event_scheduler = ON;

-- 定时更新项目的人天 
drop event if exists event_upd_project_days ;
delimiter $$
create event event_upd_project_days
on schedule 
	every 60 minute
  starts '2023-12-07 12:00:00'
	comment '定时更新项目的人天'
do
	begin
	  update zt_project , ztv_projectdays set saasDays = round(consumed_saas), selfDays = round(consumed_self)
      , lastEditedBy='system', lastEditedDate=now()
      , `desc` = REGEXP_REPLACE(`desc`, '<p>.*更新人天.*</p>', concat("<p>event_upd_project_days更新人天(saas=",round(consumed_saas),",self=",round(consumed_self),")@", date_format(now(),"%Y-%m-%d %H:%i:%S"), "</p>") )
    where id = proj_id
      and ( saasDays != round(consumed_saas) OR selfDays != round(consumed_self) );
	end
$$
delimiter ;
-- select id,name,saasDays,selfDays from zt_project where `desc` like '%更新人天%' limit 5;
-- select id,name,saasDays,selfDays,`desc` from zt_project
-- WHERE `desc` REGEXP '<p>.*更新人天.*</p>' limit 5

-- sql.end.banniu_rel20231206


