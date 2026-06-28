<?php

class __Mustache_48d27562007c0211f5c0eda09020537c extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $value = $context->find('moveicon');
        $buffer .= $this->section2e7d3cdfffee27291ffca5f384d8d23d($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '<div class="activity-grid ';
        $value = $context->find('hasname');
        if (empty($value)) {
            
            $buffer .= 'noname-grid';
        }
        $buffer .= '">
';
        $buffer .= $indent . '
';
        $value = $context->find('hasname');
        $buffer .= $this->section98562e759b94f64881e2f33be51c659e($context, $indent, $value);
        $buffer .= $indent . '
';
        $value = $context->find('dates');
        $buffer .= $this->sectionEce794360729d752cdfff21eeb036df1($context, $indent, $value);
        $buffer .= $indent . '
';
        $blockFunction = $context->findInBlock('core_courseformat/local/content/cm/badges');
        if (is_callable($blockFunction)) {
            $buffer .= call_user_func($blockFunction, $context);
        } else {
            if ($partial = $this->mustache->loadPartial('core_courseformat/local/content/cm/badges')) {
                $buffer .= $partial->renderInternal($context, $indent . '        ');
            }
        }
        $buffer .= $indent . '
';
        $value = $context->find('groupmodeinfo');
        $buffer .= $this->sectionAe4af9e495744085e2aa5f73ccc04a61($context, $indent, $value);
        $buffer .= $indent . '
';
        $value = $context->find('completion');
        $buffer .= $this->section033188067b7a54569cb9b26d211b6990($context, $indent, $value);
        $buffer .= $indent . '
';
        $value = $context->find('controlmenu');
        $buffer .= $this->section4198c68cfe4a0db44117465717d773e6($context, $indent, $value);
        $buffer .= $indent . '
';
        $value = $context->find('altcontent');
        $buffer .= $this->sectionDf977e2cb2fda2326c1417f90acad517($context, $indent, $value);
        $buffer .= $indent . '
';
        $value = $context->find('modavailability');
        $buffer .= $this->section40e5ff1584bce762eb1b8f1bd5366ec3($context, $indent, $value);
        $buffer .= $indent . '
';
        $value = $context->find('afterlink');
        $buffer .= $this->sectionCf055fb2b14388638043f897112cfa69($context, $indent, $value);
        $buffer .= $indent . '</div>
';

        return $buffer;
    }

    private function section2e7d3cdfffee27291ffca5f384d8d23d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' {{{moveicon}}} ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . ' ';
                $value = $this->resolveValue($context->find('moveicon'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= ' ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section0464ab0f2c2189000ac9f82c87d0ba52(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            {{$ core_courseformat/local/content/cm/cmname }}
                {{> core_courseformat/local/content/cm/cmname }}
            {{/ core_courseformat/local/content/cm/cmname }}
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $blockFunction = $context->findInBlock('core_courseformat/local/content/cm/cmname');
                if (is_callable($blockFunction)) {
                    $buffer .= call_user_func($blockFunction, $context);
                } else {
                    if ($partial = $this->mustache->loadPartial('core_courseformat/local/content/cm/cmname')) {
                        $buffer .= $partial->renderInternal($context, $indent . '                ');
                    }
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section98562e759b94f64881e2f33be51c659e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        {{#cmname}}
            {{$ core_courseformat/local/content/cm/cmname }}
                {{> core_courseformat/local/content/cm/cmname }}
            {{/ core_courseformat/local/content/cm/cmname }}
        {{/cmname}}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('cmname');
                $buffer .= $this->section0464ab0f2c2189000ac9f82c87d0ba52($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section67e83a65d8a92f83c7b5347c2d6d3e12(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    {{$core_course/activity_date}}
                        {{>core_course/activity_date}}
                    {{/core_course/activity_date}}
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $blockFunction = $context->findInBlock('core_course/activity_date');
                if (is_callable($blockFunction)) {
                    $buffer .= call_user_func($blockFunction, $context);
                } else {
                    if ($partial = $this->mustache->loadPartial('core_course/activity_date')) {
                        $buffer .= $partial->renderInternal($context, $indent . '                        ');
                    }
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionEf33484e3c6b590d104a27b066c606b5(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <div data-region="activity-dates" class="activity-dates mr-sm-2">
                {{#activitydates}}
                    {{$core_course/activity_date}}
                        {{>core_course/activity_date}}
                    {{/core_course/activity_date}}
                {{/activitydates}}
            </div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <div data-region="activity-dates" class="activity-dates mr-sm-2">
';
                $value = $context->find('activitydates');
                $buffer .= $this->section67e83a65d8a92f83c7b5347c2d6d3e12($context, $indent, $value);
                $buffer .= $indent . '            </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionEce794360729d752cdfff21eeb036df1(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        {{#hasdates}}
            <div data-region="activity-dates" class="activity-dates mr-sm-2">
                {{#activitydates}}
                    {{$core_course/activity_date}}
                        {{>core_course/activity_date}}
                    {{/core_course/activity_date}}
                {{/activitydates}}
            </div>
        {{/hasdates}}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('hasdates');
                $buffer .= $this->sectionEf33484e3c6b590d104a27b066c606b5($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionAe4af9e495744085e2aa5f73ccc04a61(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div
            class="activity-groupmode-info align-self-start ml-sm-2"
            data-region="groupmode"
        >
            {{$ core_courseformat/local/content/cm/groupmode}}
                {{> core_courseformat/local/content/cm/groupmode}}
            {{/ core_courseformat/local/content/cm/groupmode}}
        </div>
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div
';
                $buffer .= $indent . '            class="activity-groupmode-info align-self-start ml-sm-2"
';
                $buffer .= $indent . '            data-region="groupmode"
';
                $buffer .= $indent . '        >
';
                $blockFunction = $context->findInBlock('core_courseformat/local/content/cm/groupmode');
                if (is_callable($blockFunction)) {
                    $buffer .= call_user_func($blockFunction, $context);
                } else {
                    if ($partial = $this->mustache->loadPartial('core_courseformat/local/content/cm/groupmode')) {
                        $buffer .= $partial->renderInternal($context, $indent . '                ');
                    }
                }
                $buffer .= $indent . '        </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section035b4360b69ca486f7bf9bd0d983b832(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <div class="activity-completion align-self-start ml-sm-2">
                {{$ core_courseformat/local/content/cm/activity_info}}
                    {{> core_courseformat/local/content/cm/activity_info}}
                {{/ core_courseformat/local/content/cm/activity_info}}
            </div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <div class="activity-completion align-self-start ml-sm-2">
';
                $blockFunction = $context->findInBlock('core_courseformat/local/content/cm/activity_info');
                if (is_callable($blockFunction)) {
                    $buffer .= call_user_func($blockFunction, $context);
                } else {
                    if ($partial = $this->mustache->loadPartial('core_courseformat/local/content/cm/activity_info')) {
                        $buffer .= $partial->renderInternal($context, $indent . '                    ');
                    }
                }
                $buffer .= $indent . '            </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section033188067b7a54569cb9b26d211b6990(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        {{#hascompletion}}
            <div class="activity-completion align-self-start ml-sm-2">
                {{$ core_courseformat/local/content/cm/activity_info}}
                    {{> core_courseformat/local/content/cm/activity_info}}
                {{/ core_courseformat/local/content/cm/activity_info}}
            </div>
        {{/hascompletion}}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('hascompletion');
                $buffer .= $this->section035b4360b69ca486f7bf9bd0d983b832($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section4198c68cfe4a0db44117465717d773e6(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div class="activity-actions bulk-hidden align-self-start ml-sm-2">
            {{$ core_courseformat/local/content/cm/controlmenu }}
                {{> core_courseformat/local/content/cm/controlmenu }}
            {{/ core_courseformat/local/content/cm/controlmenu }}
        </div>
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div class="activity-actions bulk-hidden align-self-start ml-sm-2">
';
                $blockFunction = $context->findInBlock('core_courseformat/local/content/cm/controlmenu');
                if (is_callable($blockFunction)) {
                    $buffer .= call_user_func($blockFunction, $context);
                } else {
                    if ($partial = $this->mustache->loadPartial('core_courseformat/local/content/cm/controlmenu')) {
                        $buffer .= $partial->renderInternal($context, $indent . '                ');
                    }
                }
                $buffer .= $indent . '        </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE7700784befebc1262586a7c792a229c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'activity-description';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'activity-description';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionDf977e2cb2fda2326c1417f90acad517(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div class="activity-altcontent d-flex text-break {{#hasname}}activity-description{{/hasname}}">
            {{{altcontent}}}
        </div>
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div class="activity-altcontent d-flex text-break ';
                $value = $context->find('hasname');
                $buffer .= $this->sectionE7700784befebc1262586a7c792a229c($context, $indent, $value);
                $buffer .= '">
';
                $buffer .= $indent . '            ';
                $value = $this->resolveValue($context->find('altcontent'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '        </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section40e5ff1584bce762eb1b8f1bd5366ec3(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        {{$ core_courseformat/local/content/cm/availability }}
            {{> core_courseformat/local/content/cm/availability }}
        {{/ core_courseformat/local/content/cm/availability }}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $blockFunction = $context->findInBlock('core_courseformat/local/content/cm/availability');
                if (is_callable($blockFunction)) {
                    $buffer .= call_user_func($blockFunction, $context);
                } else {
                    if ($partial = $this->mustache->loadPartial('core_courseformat/local/content/cm/availability')) {
                        $buffer .= $partial->renderInternal($context, $indent . '            ');
                    }
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionCf055fb2b14388638043f897112cfa69(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div class="activity-afterlink afterlink d-flex align-items-center">
            {{{afterlink}}}
        </div>
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div class="activity-afterlink afterlink d-flex align-items-center">
';
                $buffer .= $indent . '            ';
                $value = $this->resolveValue($context->find('afterlink'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '
';
                $buffer .= $indent . '        </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
