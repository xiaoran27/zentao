<?php

    public function batchCreate($productID = 0, $branch = 0, $type = 'story')
    {
        return $this->loadExtension('bytenew')->batchCreate($productID , $branch , $type );
    }
    
    /**
     * Batch create stories.
     *
     * @access public
     * @return int|bool the id of the created story or false when error.
     * @return type requirement|story
     */
    public function batchCreate0($productID = 0, $branch = 0, $type = 'story')
    {
        $forceReview = $this->checkForceReview();

        $reviewers = '';
        foreach($_POST['title'] as $index => $value)
        {
            if($_POST['title'][$index] and isset($_POST['reviewer'][$index]) and empty($_POST['reviewDitto'][$index])) $reviewers = $_POST['reviewer'][$index] = array_filter($_POST['reviewer'][$index]);
            if($_POST['title'][$index] and isset($_POST['reviewer'][$index]) and !empty($_POST['reviewDitto'][$index])) $_POST['reviewer'][$index] = $reviewers;
            if($_POST['title'][$index] and empty($_POST['reviewer'][$index]) and $forceReview)
            {
                dao::$errors[] = $this->lang->story->errorEmptyReviewedBy;
                return false;
            }
        }

        $this->loadModel('action');
        $branch    = (int)$branch;
        $productID = (int)$productID;
        $now       = helper::now();
        $mails     = array();
        $stories   = fixer::input('post')->get();

        $saveDraft = false;
        if(isset($stories->status))
        {
            if($stories->status == 'draft') $saveDraft = true;
            unset($stories->status);
        }

        $result  = $this->loadModel('common')->removeDuplicate('story', $stories, "product={$productID}");
        $stories = $result['data'];

        $module = 0;
        $plan   = '';
        $pri    = 0;
        $source = '';
        $bzCategory = '';
        $prCategory = '';
        $responseResult = '0';

        foreach($stories->title as $i => $title)
        {
            $module = $stories->module[$i] == 'ditto' ? $module : $stories->module[$i];
            $plan   = isset($stories->plan[$i]) ? ($stories->plan[$i] == 'ditto' ? $plan : $stories->plan[$i]) : '';
            $pri    = $stories->pri[$i]    == 'ditto' ? $pri    : $stories->pri[$i];
            $source = $stories->source[$i] == 'ditto' ? $source : $stories->source[$i];
            $stories->module[$i] = (int)$module;
            $stories->plan[$i]   = $plan;
            $stories->pri[$i]    = (int)$pri;
            $stories->source[$i] = $source;


            $bzCategory = $stories->bzCategory[$i] == 'ditto' ? $bzCategory : $stories->bzCategory[$i];
            $prCategory = $stories->prCategory[$i] == 'ditto' ? $prCategory : $stories->prCategory[$i];
            $responseResult = $stories->responseResult[$i] == 'ditto' ? $responseResult : $stories->responseResult[$i];
            $stories->bzCategory[$i] = $bzCategory;
            $stories->prCategory[$i] = $prCategory;
            $stories->responseResult[$i] = $responseResult;
        }

        if(isset($stories->uploadImage)) $this->loadModel('file');

        $extendFields = $this->getFlowExtendFields();
        $data         = array();
        foreach($stories->title as $i => $title)
        {
            if(empty($title)) continue;

            if(empty($stories->reviewer[$i]) and empty($stories->reviewerDitto[$i])) $stories->reviewer[$i] = array();

            $story = new stdclass();
            $story->type       = $type;
            $story->branch     = isset($stories->branch[$i]) ? $stories->branch[$i] : 0;
            $story->module     = $stories->module[$i];
            $story->plan       = $stories->plan[$i];
            $story->color      = $stories->color[$i];
            $story->title      = $stories->title[$i];
            $story->bzCategory     = $stories->bzCategory[$i];
            $story->prCategory     = $stories->prCategory[$i];
            $story->responseResult     = $stories->responseResult[$i];
            $story->uatDate     = $stories->uatDate[$i];
	        $story->purchaser     = $stories->purchaser[$i];
            $story->source     = $stories->source[$i];
            $story->category   = $stories->category[$i];
            $story->pri        = $stories->pri[$i];
            $story->estimate   = $stories->estimate[$i];
            $story->status     = $saveDraft ? 'draft' : ((empty($stories->reviewer[$i]) and !$forceReview) ? 'active' : 'reviewing');
            $story->stage      = ($this->app->tab == 'project' or $this->app->tab == 'execution') ? 'projected' : 'wait';
            $story->keywords   = $stories->keywords[$i];
            $story->sourceNote = $stories->sourceNote[$i];
            $story->product    = $productID;
            $story->openedBy   = $this->app->user->account;
            $story->vision     = $this->config->vision;
            $story->openedDate = $now;
            $story->version    = 1;

            foreach($extendFields as $extendField)
            {
                $story->{$extendField->field} = $this->post->{$extendField->field}[$i];
                if(is_array($story->{$extendField->field})) $story->{$extendField->field} = join(',', $story->{$extendField->field});

                $story->{$extendField->field} = htmlSpecialString($story->{$extendField->field});
            }

            foreach(explode(',', $this->config->story->create->requiredFields) as $field)
            {
                $field = trim($field);
                if(empty($field)) continue;
                if($type == 'requirement' and $field == 'plan') continue;

                if(!isset($story->$field)) continue;
                if(!empty($story->$field)) continue;
                if($field == 'estimate' and strlen(trim($story->estimate)) != 0) continue;

                dao::$errors['message'][] = sprintf($this->lang->error->notempty, $this->lang->story->$field);
                return false;
            }
            $data[$i] = $story;
        }

        $link2Plans = array();
        foreach($data as $i => $story)
        {
            $this->dao->insert(TABLE_STORY)->data($story)->autoCheck()->checkFlow()->exec();
            if(dao::isError())
            {
                echo js::error(dao::getError());
                return print(js::reload('parent'));
            }

            $storyID = $this->dao->lastInsertID();
            $this->setStage($storyID);

            /* Update product plan stories order. */
            if($story->plan)
            {
                $this->updateStoryOrderOfPlan($storyID, $story->plan);
                $link2Plans[$story->plan] = empty($link2Plans[$story->plan]) ? $storyID : "{$link2Plans[$story->plan]},$storyID";
            }

            $specData = new stdclass();
            $specData->story   = $storyID;
            $specData->version = 1;
            $specData->title   = $stories->title[$i];
            $specData->spec    = '';
            $specData->verify  = '';
            if(!empty($stories->spec[$i]))  $specData->spec   = nl2br($stories->spec[$i]);
            if(!empty($stories->verify[$i]))$specData->verify = nl2br($stories->verify[$i]);

            if(!empty($stories->uploadImage[$i]))
            {
                $fileName = $stories->uploadImage[$i];
                $file     = $this->session->storyImagesFile[$fileName];

                $realPath = $file['realpath'];
                unset($file['realpath']);

                if(!is_dir($this->file->savePath)) mkdir($this->file->savePath, 0777, true);
                if($realPath and rename($realPath, $this->file->savePath . $this->file->getSaveName($file['pathname'])))
                {
                    $file['addedBy']    = $this->app->user->account;
                    $file['addedDate']  = $now;
                    $file['objectType'] = 'story';
                    $file['objectID']   = $storyID;
                    if(in_array($file['extension'], $this->config->file->imageExtensions))
                    {
                        $file['extra'] = 'editor';
                        $this->dao->insert(TABLE_FILE)->data($file)->exec();

                        $fileID = $this->dao->lastInsertID();
                        $specData->spec .= '<img src="{' . $fileID . '.' . $file['extension'] . '}" alt="" />';
                    }
                    else
                    {
                        $this->dao->insert(TABLE_FILE)->data($file)->exec();
                    }
                }
            }

            $this->dao->insert(TABLE_STORYSPEC)->data($specData)->exec();

            /* Save the story reviewer to storyreview table. */
            foreach($_POST['reviewer'][$i] as $reviewer)
            {
                if(empty($reviewer)) continue;

                $reviewData = new stdclass();
                $reviewData->story    = $storyID;
                $reviewData->version  = 1;
                $reviewData->reviewer = $reviewer;
                $this->dao->insert(TABLE_STORYREVIEW)->data($reviewData)->exec();
            }

            $this->executeHooks($storyID);

            $actionID = $this->action->create('story', $storyID, 'Opened', '');
            if(!dao::isError()) $this->loadModel('score')->create('story', 'create',$storyID);
            $mails[$i] = new stdclass();
            $mails[$i]->storyID  = $storyID;
            $mails[$i]->actionID = $actionID;
        }

        /* Remove upload image file and session. */
        if(!empty($stories->uploadImage) and $this->session->storyImagesFile)
        {
            $classFile = $this->app->loadClass('zfile');
            $file = current($_SESSION['storyImagesFile']);
            $realPath = dirname($file['realpath']);
            if(is_dir($realPath)) $classFile->removeDir($realPath);
            unset($_SESSION['storyImagesFile']);
        }
        if(!dao::isError())
        {
            $this->loadModel('score')->create('ajax', 'batchCreate');
            foreach($link2Plans as $planID => $stories) $this->action->create('productplan', $planID, 'linkstory', '', $stories);
        }
        return $mails;
    }

