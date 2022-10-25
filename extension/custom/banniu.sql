
-- 2022-10-16 22:00:45
ALTER TABLE zentao.zt_story ADD bzCategory varchar(16)  DEFAULT 'LKA' NULL COMMENT '客户类型';
ALTER TABLE zentao.zt_story ADD prCategory varchar(16)  DEFAULT 'product' NULL COMMENT '需求分类';
ALTER TABLE zentao.zt_story ADD uatDate DATE NULL COMMENT 'UAT日期';

ALTER TABLE zentao.zt_story ADD purchaser varchar(64) NULL COMMENT '客户名称';


-- 2022-10-16 22:11:41
ALTER TABLE zentao.zt_bug ADD purchaser varchar(256) NULL COMMENT '客户名称'; 


-- 2022-10-20 20:30:15
ALTER TABLE zentao.zt_bug ADD occursEnv varchar(128)  DEFAULT 'online' NULL COMMENT '发生环境'; 
ALTER TABLE zentao.zt_bug ADD feedbackTime datetime   NULL COMMENT '反馈时间'; 
ALTER TABLE zentao.zt_bug ADD collectTime datetime   NULL COMMENT '收集时间'; 