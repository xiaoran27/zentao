#!/bin/bash


##
SOURCE_DIR="$(cd `dirname $0` && pwd )"
BIN_DIR=${SOURCE_DIR}
BIN_NAME=$(basename "$0")
BIN_FILE="${BIN_DIR}/${BIN_NAME}"



zbox_dir='/opt/zbox'
zentao_dir="${zbox_dir}/app/zentao"
zentao_ext_dir="${zbox_dir}/app/zentao/extension"
zentao_custom_dir="${zentao_ext_dir}/custom"
zentao_tmp_dir="${zentao_dir}/tmp"
zentao_tmp_backup_dir="${zentao_tmp_dir}/backup"
zentao_tmp_log_dir="${zentao_tmp_dir}/log"
[ -d "${zentao_tmp_backup_dir}" ] || mkdir -p "${zentao_tmp_backup_dir}"
[ -d "${zentao_tmp_log_dir}" ] || mkdir -p "${zentao_tmp_log_dir}"

LOG_FILE="${BIN_DIR}/${BIN_NAME}.log"
zentao_custom_logfile="${zentao_tmp_log_dir}/${BIN_NAME}.log"

log_begin_flag="====BEGIN====@`date`"
log(){
    [ $# -eq 0 ] && {
        echo "${log_begin_flag}"
        echo >> ${LOG_FILE};
        echo "${log_begin_flag}" >> ${LOG_FILE};
        return 0
    };
    [ $# -gt 0 ] && {
        echo "${1}";
        echo "`date`\t${1}"  >> ${LOG_FILE};
    };
    [ $# -gt 1 ] && {
        echo "exit ${2}";
        echo "`date`\t====END(${2})====@`date`"  >> ${LOG_FILE};

        echo  >> ${zentao_custom_logfile};
        grep -A9999 "${log_begin_flag}" ${LOG_FILE} >> ${zentao_custom_logfile};
    };
}
log

action="${1}"
action="${action:=install}"
[ "${action}" = "install" -o "${action}" = "uninstall" ] || {
    log " '${action}' Not in ( 'install', 'uninstall'  ) !" 100;
    exit 100;
}

ver="${2}"
[ "${action}" = "install" -o "${action}" = "uninstall" ] || {
    log " '${action}' Not in ( 'install', 'uninstall'  ) !" 100;
    exit 100;
}


[ -d "${zentao_custom_dir}" ] || {
    log "Not exists dir: ${zentao_custom_dir}" 105;
    exit 105;
}


custom_dir="${SOURCE_DIR}/custom"
[ -d "${custom_dir}" ] || {
    log "Not exists dir: ${custom_dir}" 110;
    exit 110;
}

 #rel221102,rel221102,202211021708,install
banniu_upgrade="${zentao_ext_dir}/banniu_upgrade.txt"
touch ${banniu_upgrade}
upgrade_line="`tail -n1 ${banniu_upgrade} 2>/dev/null`"
upgrade_line="${upgrade_line//[$'\r\n']}"
banniu_upgrade_array=('' '' '' '')
[ "N${upgrade_line}" = "N" ] || {
    #rel221102,rel221102,202211021708,install
    banniu_upgrade_array=(${upgrade_line//,/ })
}
lastest_ver="_${banniu_upgrade_array[0]}";
lastest_ver_end="_${banniu_upgrade_array[1]}";
lastest_YYYYmmddHHMM="${banniu_upgrade_array[2]}";
lastest_action="${banniu_upgrade_array[3]}";




if [ "${action}" = "install" ]; then
    fullsqlfile="${custom_dir}/banniu.sql"
else
    fullsqlfile="${custom_dir}/banniu_uninstall.sql";
fi
[  -f "${fullsqlfile}" ] || {
    log "Not exists file: ${fullsqlfile}!" 140 ;
    exit 140;
}

sql_begin_flag="sql.start.banniu${lastest_ver}"
sql_end_flag="sql.end.banniu${lastest_ver_end}"
sql_begin_flag_line="1"
sql_end_flag_line="$"

## ${action} + ${lastest_ver}
if [ "${action}" = "uninstall"  -a ( "N${lastest_ver}" = "N_" -o "N${lastest_YYYYmmddHHMM}" = "N"  ) ]; then 
    log " Not exists a version for '${action}'!" 130;
    exit 130;
elif [ "${action}" = "install"  -a "N${lastest_ver}" = "N_" ]; then 
    sql_begin_flag="sql.start.banniu_rel"
    lastest_ver_line="`grep -n ${sql_begin_flag} ${fullsqlfile} | head -n 2 |  tail -n1`";
    lastest_ver_line="${lastest_ver_line//[$'\r\n']}"
    # 5:-- sql.start.banniu_rel221102
    lastest_ver="_${lastest_ver_line##*_}";
    sql_begin_flag="sql.start.banniu${lastest_ver}"
    sql_begin_flag_line="${lastest_ver_line%%:*}";

    sql_end_flag="sql.end.banniu_rel"
    lastest_ver_line="`grep -n ${sql_end_flag} ${fullsqlfile} | tail -n1`";
    lastest_ver_line="${lastest_ver_line//[$'\r\n']}"
    # 100:-- sql.end.banniu_rel221102
    lastest_ver_end="_${lastest_ver_line##*_}";
    sql_end_flag="sql.end.banniu${lastest_ver_end}"
    sql_end_flag_line="${lastest_ver_line%%:*}";
elif [ ! "N${lastest_ver}" = "N_" ]; then 
    


    sql_begin_flag="sql.start.banniu_rel"
    lastest_ver_line="`grep -n ${sql_begin_flag} ${fullsqlfile} | head -n 2 |  tail -n1`";
    lastest_ver_line="${lastest_ver_line//[$'\r\n']}"
    # 5:-- sql.start.banniu_rel221102
    lastest_ver="_${lastest_ver_line##*_}";
    sql_begin_flag="sql.start.banniu${lastest_ver}"
    sql_begin_flag_line="${lastest_ver_line%%:*}";

    sql_end_flag="sql.end.banniu_rel"
    lastest_ver_line="`grep -n ${sql_end_flag} ${fullsqlfile} | tail -n1`";
    lastest_ver_line="${lastest_ver_line//[$'\r\n']}"
    # 100:-- sql.end.banniu_rel221102
    lastest_ver_end="_${lastest_ver_line##*_}";
    sql_end_flag="sql.end.banniu${lastest_ver_end}"
    sql_end_flag_line="${lastest_ver_line%%:*}";

    sql_end_flag="sql.end.banniu_rel"
    lastest_ver_line="`grep -n ${sql_end_flag} ${fullsqlfile} | tail -n1`";
    lastest_ver_line="${lastest_ver_line//[$'\r\n']}"
    # 100:-- sql.end.banniu_rel221102
    lastest_ver_end="_${lastest_ver_line##*_}";
    sql_end_flag="sql.end.banniu${lastest_ver_end}"
    sql_end_flag_line="${lastest_ver_line%%:*}";

if



sqlfile="${custom_dir}/banniu_${action}${lastest_ver}${lastest_ver_end}.sql";
[ -f "${sqlfile}" ]  || {
    log "Not exists , but generate file: ${sqlfile}" ;
    sed -n "${sql_begin_flag_line},${sql_end_flag_line}p"  ${fullsqlfile} > ${sqlfile}
}


YYYYmmddHHMM="`date "+%Y%m%d%H%M"`"
zentao_custom_dir_bktgz="${zentao_tmp_backup_dir}/custom${lastest_ver}${lastest_ver_end}_BK${YYYYmmddHHMM}.tgz"
cmd="( cd ${zentao_ext_dir} && tar czf ${zentao_custom_dir_bktgz} custom )"
log "${cmd}...@`date "+%Y%m%d%H%M%S"`"
time eval "${cmd}"
rtnval="$?"
log "${cmd}...finished(${rtnval})@`date "+%Y%m%d%H%M%S"`"
[ "${rtnval}" -eq 0 ] || {
    log "Database connection failed(${rtnval})!!" 150 ;
    exit 150;
}



my_php="${zentao_dir}/config/my.php"
mysql_pwd="`grep 'config->db->password' ${my_php} | awk -F\' '{print $2;}'`"
mysql_host="127.0.0.1"
mysql_port="3306"
mysql_user="root"
mysql_dbname="zentao"

cmd_mysql="${zbox_dir}/run/mysql/mysql"
cmd="${cmd_mysql} -h${mysql_host} -P${mysql_port} -u${mysql_user} --password=${mysql_pwd} ${mysql_dbname} -e 'select current_timestamp();'"
log "${cmd}...@`date "+%Y%m%d%H%M%S"`"
time eval "${cmd}"
rtnval="$?"
log "${cmd}...finished(${rtnval})@`date "+%Y%m%d%H%M%S"`"
[ "${rtnval}" -eq 0 ] || {
    log "Database connection failed(${rtnval})!!" 150 ;
    exit 150;
}

zentao_custom_dir_bkdb="${zentao_tmp_backup_dir}/db${lastest_ver}${lastest_ver_end}_BK${YYYYmmddHHMM}.sql"
cmd_mysqldump="${zbox_dir}/run/mysql/mysqldump"
cmd="${cmd_mysqldump} -h${mysql_host} -P${mysql_port} -u${mysql_user} --password=${mysql_pwd}   --default-character-set=utf8 --single-transaction --triggers --routines --events  ${mysql_dbname} >${zentao_custom_dir_bkdb} "
log "${cmd}...@`date "+%Y%m%d%H%M%S"`"
time eval "${cmd}"
rtnval="$?"
log "${cmd}...finished(${rtnval})@`date "+%Y%m%d%H%M%S"`"
[ "${rtnval}" -eq 0 ] || {
    echo "Database Backup failure(${rtnval})!"
    read -p "Please continue after the database is successfully backed up by youself ... Y/n?" yes_no;
    [ "${yes_no}" = "Y" -o "${yes_no}" = "y" ]  || {
        log "You interrupted a database backup operation!" 160 ;
        exit 160;
    }
}


if [ "${action}" = "uninstall" ]; then
    zentao_custom_dir_bktgz="${zentao_tmp_backup_dir}/custom${lastest_ver}${lastest_ver_end}_BK${lastest_YYYYmmddHHMM}.tgz"
    [ -f "${zentao_custom_dir_bktgz}" ] || {
        log "Not exists backup file: ${zentao_custom_dir_bktgz}!" 165 ;
        exit 165;
    }
    tmpdir="/tmp/custom${lastest_ver}${lastest_ver_end}_BK${lastest_YYYYmmddHHMM}"
    [ -d "${tmpdir}" ] || mkdir -p "${tmpdir}"

    cmd="tar -xzf ${zentao_custom_dir_bktgz} -C ${tmpdir}"
    log "${cmd}...@`date "+%Y%m%d%H%M%S"`"
    time eval "${cmd}"
    rtnval="$?"
    log "${cmd}...finished(${rtnval})@`date "+%Y%m%d%H%M%S"`"

    custom_dir="${tmpdir}/custom"
    [ -d "${custom_dir}" ] || {
        log "Not exists restore dir: ${custom_dir}!" 168 ;
        exit 168;
    }

    mv ${zentao_custom_dir} ${zentao_custom_dir}.BK${YYYYmmddHHMM}
fi

# cp custom
cmd="cp -rf ${custom_dir} ${zentao_ext_dir} "
log "${cmd}...@`date "+%Y%m%d%H%M%S"`"
time eval "${cmd}"
rtnval="$?"
log "${cmd}...finished(${rtnval})@`date "+%Y%m%d%H%M%S"`"
[ "${rtnval}" -eq 0 ] || {
    echo "Upgrade(cp) failed(${rtnval})!"
    read -p "Please continue after the '${custom_dir}/* -> ${zentao_custom_dir}' is successfully copied by youself ... Y/n?" yes_no;
    [ "${yes_no}" = "Y" -o "${yes_no}" = "y" ]  || {
        log "You interrupted the upgrade operation(cp)!" 170 ;
        exit 170;
    }
}


#run SQL
cmd_mysql="${zbox_dir}/run/mysql/mysql"
cmd="${cmd_mysql} -h${mysql_host} -P${mysql_port} -u${mysql_user} --password=${mysql_pwd} ${mysql_dbname} < ${sqlfile} "
log "${cmd}...@`date "+%Y%m%d%H%M%S"`"
time eval "${cmd}"
rtnval="$?"
log "${cmd}...finished(${rtnval})@`date "+%Y%m%d%H%M%S"`"
[ "${rtnval}" -eq 0 ] || {
    echo "Upgrade(mysql) failed(${rtnval})!"
    read -p "Please continue after the 'mysql ...< ${sqlfile}' is successfully excuted by youself ... Y/n?" yes_no;
    [ "${yes_no}" = "Y" -o "${yes_no}" = "y" ]  || {
        log "You interrupted the upgrade operation(mysql)!" 180 ;
        exit 180;
    }
}


echo "${lastest_ver:1}=${YYYYmmddHHMM}=${action}" >> ${banniu_upgrade}

log "Finnished the upgrade operation!"
log "SEE: ${zentao_custom_logfile} for more." 0
