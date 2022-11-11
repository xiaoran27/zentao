-- mysql -u root -p zentao
-- use zentao;

-- 每次需要更新SQL段
-- sql.start.banniu_rel{yymmdd}
--  SQL语句
-- sql.end.banniu_rel{yymmdd}


-- sql.start.banniu_rel221107

--2022-11-8 16:34:01
delete from zt_purchaser where mtime = '2022-11-08';

--2022-11-7 20:06:38
delete from zt_purchaser where mtime = '2022-11-07';

-- sql.end.banniu_rel221107
