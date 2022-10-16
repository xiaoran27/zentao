
-- 2022-10-16 22:00:45
ALTER TABLE zentao.zt_story ADD bzCategory varchar(16) NULL COMMENT '客户类型';
ALTER TABLE zentao.zt_story ADD prCategory varchar(16) NULL COMMENT '需求分类';
ALTER TABLE zentao.zt_story ADD uatDate DATE NULL COMMENT 'UAT日期';

ALTER TABLE zentao.zt_story ADD purchaser varchar(64) NULL COMMENT '客户名称';

