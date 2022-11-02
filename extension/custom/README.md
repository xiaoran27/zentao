
# 概述
扩展功能，满足流程过程信息记录.


# 禅道(zentao)扩展方法

参考[zentaoPHP二次开发简介](https://devel.easycorp.cn/book/extension-new/intro-52.html)

# 扩展目录与文件

## 扩展根目录(zbox/app/zentao/extension/custom)

其目录下存放了所有扩展模块功能的文件,目的是升级zentao而不受到影响（PS: 扩展文件可能需要做适配版本调整）. 

## 数据库更改sql文件

### 文件说明

banniu.sql - 扩展功能对数据库的修改sql. 
banniu_uninstall.sql - 删除扩展功能对数据库的修改sql.
banniu_*_rel{yymmdd}.sql - 单次升级的sql文件.

### 文件内容结构

```
-- 每次需要更新SQL段
-- sql.start.banniu_rel{yymmdd}
--  SQL语句
-- sql.end.banniu_rel{yymmdd}
```

banniu.sql  是全量SQL,仅首次执行.

banniu_rel{yymmdd}.sql  是某次发布的SQL,通常是升级时执行。 如: banniu_rel221102.sql

PS:  执行前一定要确定数据库名的正确性(如:  zentao)

# 更新方法

0. 备份

- 备份数据库

- 备份扩展根目录

1. 解压升级包

    建议解压到临时目录，切记不要直接解压覆盖扩展跟目录下，否则无法恢复之前的版本.

2. 升级

    1. 数据库更新

        连接mysql并切换到数据库名,执行需要的sql文件<banniu_rel{yymmdd}.sql>.

        ```
        mysql -uroot -p zentao
        source  {全路径的sql文件<banniu_rel{yymmdd}.sql>}
        ```
    2. 扩展文件更新
    
        用解压后的custom目录直接覆盖扩展根目录

3. 回滚

    1. 回滚数据库

        连接mysql并切换到数据库名,执行需要的sql文件<banniu_uninstall_rel{yymmdd}.sql>.

        ```
        mysql -uroot -p zentao
        source  {全路径的sql文件<banniu_uninstall_rel{yymmdd}.sql>}
        ```
    2. 回滚扩展文件更新
    
        用备份的custom目录直接覆盖扩展根目录

