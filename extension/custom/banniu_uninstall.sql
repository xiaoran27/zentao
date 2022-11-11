-- mysqldump -u root -p123456  --default-character-set=utf8 --single-transaction --triggers --routines --events  zentao >zentao_bk.sql
-- mysql -u root -p123456 zentao < x.sql

-- use zentao;

-- 每次需要更新SQL段
-- sql.start.banniu_rel{yymmdd}
--  时间倒序的SQL语句
-- sql.end.banniu_rel{yymmdd}


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