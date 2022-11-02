-- mysql -u root -p zentao
-- use zentao;

-- 每次需要更新SQL段
-- sql.start.banniu_rel{yymmdd}
--  SQL语句
-- sql.end.banniu_rel{yymmdd}

-- sql.start.banniu_rel221102
-- 2022-10-16 22:00:45
ALTER TABLE zt_story DROP COLUMN  IF EXISTS  bzCategory ;
ALTER TABLE zt_story DROP COLUMN  IF EXISTS  prCategory ;
ALTER TABLE zt_story DROP COLUMN  IF EXISTS  uatDate ;

ALTER TABLE zt_story DROP COLUMN  IF EXISTS  purchaser ;


-- 2022-10-16 22:11:41
ALTER TABLE zt_bug DROP COLUMN  IF EXISTS  purchaser ; 


-- 2022-10-20 20:30:15
ALTER TABLE zt_bug DROP COLUMN  IF EXISTS  occursEnv ; 
ALTER TABLE zt_bug DROP COLUMN  IF EXISTS  feedbackTime ; 
ALTER TABLE zt_bug DROP COLUMN  IF EXISTS  collectTime ; 

-- 2022-10-27 21:09:50
ALTER TABLE zt_story DROP COLUMN  IF EXISTS  responseResult ; 


-- 2022-10-31 10:19:14
DROP TABLE IF EXISTS  `zt_purchaser` ;

-- sql.end.banniu_rel221102