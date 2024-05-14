

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
  IF ( new.responseResult = 'todo' 	AND ( date_format(ifnull(new.prdReviewTime, '0000-00-00'), '%Y-%m-%d') !='0000-00-00' 	OR date_format(ifnull(new.releaseTime, '0000-00-00'), '%Y-%m-%d')!='0000-00-00'  ) ) THEN
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


END $$
delimiter ;

