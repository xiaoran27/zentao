<?php

class bytenewTesttask extends TesttaskModel
{

    /**
     * Get results by runID or caseID
     *
     * @param  int    $runID
     * @param  int    $caseID
     * @param  string $status all|done
     * @access public
     * @return void
     */
    public function getResults($runID, $caseID = 0, $status = 'all')
    {
        if($runID > 0)
        {
            $results = $this->dao->select('*')->from(TABLE_TESTRESULT)
                ->where('run')->eq($runID)
                ->beginIF($status == 'done')->andWhere('caseResult')->ne('')->fi()
                ->orderBy('id desc')
                ->fetchAll('id');
        }
        else
        {
            $results = $this->dao->select('*')->from(TABLE_TESTRESULT)
                ->where('`case`')->eq($caseID)
                ->beginIF($status == 'done')->andWhere('caseResult')->ne('')->fi()
                ->orderBy('id desc')
                ->fetchAll('id');
        }

        if(!$results) return array();

        $relatedVersions = array();
        $runIdList       = array();
        $nodeIdList      = array();
        foreach($results as $result)
        {
            $runIdList[$result->run] = $result->run;
            $relatedVersions[]       = $result->version;
            $runCaseID               = $result->case;
            if(!empty($result->node)) $nodeIdList[] = $result->node;
        }
        $relatedVersions = array_unique($relatedVersions);

        $relatedSteps = $this->dao->select('*')->from(TABLE_CASESTEP)
            ->where('`case`')->eq($runCaseID)
            ->andWhere('version')->in($relatedVersions)
            ->orderBy('id')
            ->fetchGroup('version', 'id');
        $runs = $this->dao->select('t1.id,t2.build')->from(TABLE_TESTRUN)->alias('t1')
            ->leftJoin(TABLE_TESTTASK)->alias('t2')->on('t1.task=t2.id')
            ->where('t1.id')->in($runIdList)
            ->fetchPairs();
        $nodes = $this->dao->select('id,name')->from(TABLE_ZAHOST)
            ->where('id')->in(array_unique($nodeIdList))
            ->fetchPairs();

        $this->loadModel('file');
        $files = $this->dao->select('*')->from(TABLE_FILE)
            ->where("(objectType = 'caseResult' or objectType = 'stepResult')")
            ->andWhere('objectID')->in(array_keys($results))
            ->andWhere('extra')->ne('editor')
            ->orderBy('id')
            ->fetchAll();
        $resultFiles = array();
        $stepFiles   = array();
        foreach($files as $file)
        {
            $this->file->setFileWebAndRealPaths($file);
            if($file->objectType == 'caseResult')
            {
                $resultFiles[$file->objectID][$file->id] = $file;
            }
            elseif($file->objectType == 'stepResult' and $file->extra !== '')
            {
                $stepFiles[$file->objectID][(int)$file->extra][$file->id] = $file;
            }
        }
        foreach($results as $resultID => $result)
        {
            $result->stepResults = unserialize($result->stepResults);
            $result->build       = $result->run ? zget($runs, $result->run, 0) : 0;
            $result->nodeName    = $result->node ? zget($nodes, $result->node, '') : '';

            if(!empty($result->ZTFResult))
            {
                $result->ZTFResult = $this->formatZtfLog($result->ZTFResult, $result->stepResults);
            }

            $result->files = zget($resultFiles, $resultID, array()); //Get files of case result.
            if(isset($relatedSteps[$result->version]))
            {
                $relatedStep = $relatedSteps[$result->version];
                foreach($relatedStep as $stepID => $step)
                {
                    $relatedStep[$stepID] = (array)$step;
                    $relatedStep[$stepID]['desc']   = html_entity_decode($relatedStep[$stepID]['desc']);
                    $relatedStep[$stepID]['expect'] = html_entity_decode($relatedStep[$stepID]['expect']);

                    $relatedStep[$stepID]['desc']   = htmlspecialchars_decode($relatedStep[$stepID]['desc']);
                    $relatedStep[$stepID]['expect'] = htmlspecialchars_decode($relatedStep[$stepID]['expect']);

                    if(isset($result->stepResults[$stepID]))
                    {
                        $relatedStep[$stepID]['result'] = $result->stepResults[$stepID]['result'];
                        $relatedStep[$stepID]['real']   = $result->stepResults[$stepID]['real'];

                        $relatedStep[$stepID]['result'] = htmlspecialchars_decode($result->stepResults[$stepID]['result']);
                        $relatedStep[$stepID]['real']   = htmlspecialchars_decode($result->stepResults[$stepID]['real']);
                    }
                }
                $result->stepResults = $relatedStep;
            }

            /* Get files of step result. */
            if(!empty($result->stepResults)) foreach($result->stepResults as $stepID => $stepResult) $result->stepResults[$stepID]['files'] = isset($stepFiles[$resultID][$stepID]) ? $stepFiles[$resultID][$stepID] : array();
        }
        return $results;
    }
    
}