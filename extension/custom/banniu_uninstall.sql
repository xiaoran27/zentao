-- mysqldump -u root -p123456  --default-character-set=utf8 --single-transaction --triggers --routines --events  zentao >zentao_bk.sql
-- mysql -u root -p123456 zentao < x.sql

-- use zentao;

-- 每次需要更新SQL段
-- sql.start.banniu_rel{yymmdd}
--  时间倒序的SQL语句
-- sql.end.banniu_rel{yymmdd}



-- sql.start.banniu_rel221207

-- 2022-11-28 20:31:58

ALTER TABLE zt_purchaser MODIFY COLUMN  mtime datetime  NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ;

ALTER TABLE zt_story ADD prLevel varchar(32) DEFAULT 'C' NOT NULL COMMENT '需求等级';

ALTER TABLE zt_story MODIFY COLUMN responseResult varchar(32) DEFAULT '0' NOT NULL COMMENT '响应结果';
update zt_story set responseResult='0' where responseResult = 'todo';
update zt_story set responseResult='1' where responseResult = 'reject';
update zt_story set responseResult='2' where responseResult = 'research';
update zt_story set responseResult='3' where responseResult = 'accept';
ALTER TABLE zt_story DROP COLUMN   IF EXISTS rspRecievedTime ;
ALTER TABLE zt_story DROP COLUMN   IF EXISTS rspRejectTime ;
ALTER TABLE zt_story DROP COLUMN   IF EXISTS rspResearchTime ;
ALTER TABLE zt_story DROP COLUMN   IF EXISTS rspAcceptTime ;

DROP TRIGGER IF EXISTS zentao.tri_story_bu;

ALTER TABLE zt_story DROP COLUMN   IF EXISTS  bizProject ;

ALTER TABLE zt_storyspec DROP COLUMN   IF EXISTS bizStage  ;
ALTER TABLE zt_storyspec DROP COLUMN   IF EXISTS bizTarget ;
ALTER TABLE zt_storyspec DROP COLUMN   IF EXISTS bizValue ;

-- sql.end.banniu_rel221207

-- sql.start.banniu_rel221125

-- 2022-11-18 17:27:01
delete from zt_purchaser where mtime = '2022-11-25';

-- sql.end.banniu_rel221125



-- sql.start.banniu_rel221125

-- 2022-11-18 17:27:01
delete from zt_purchaser where mtime = '2022-11-25';

-- sql.end.banniu_rel221125


-- sql.start.banniu_rel221107

--2022-11-8 16:34:01
delete from zt_purchaser where mtime = '2022-11-08';

--2022-11-7 20:06:38
delete from zt_purchaser where mtime = '2022-11-07';

-- sql.end.banniu_rel221107

-- sql.start.banniu_rel221102

-- 2022-10-31 10:19:14
DROP TABLE IF EXISTS  `zt_purchaser` ;

-- 2022-10-27 21:09:50
ALTER TABLE zt_story DROP COLUMN  IF EXISTS  responseResult ; 


-- 2022-10-20 20:30:15
ALTER TABLE zt_bug DROP COLUMN  IF EXISTS  occursEnv ; 
ALTER TABLE zt_bug DROP COLUMN  IF EXISTS  feedbackTime ; 
ALTER TABLE zt_bug DROP COLUMN  IF EXISTS  collectTime ; 


-- 2022-10-16 22:11:41
ALTER TABLE zt_bug DROP COLUMN  IF EXISTS  purchaser ; 


-- 2022-10-16 22:00:45
ALTER TABLE zt_story DROP COLUMN  IF EXISTS  bzCategory ;
ALTER TABLE zt_story DROP COLUMN  IF EXISTS  prCategory ;
ALTER TABLE zt_story DROP COLUMN  IF EXISTS  uatDate ;

ALTER TABLE zt_story DROP COLUMN  IF EXISTS  purchaser ;



-- sql.end.banniu_rel221102