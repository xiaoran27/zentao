
-- use zentao;

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
