<?php

class __Mustache_431b0443d3387a49c1f54f7d7bba92be extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $value = $context->find('isInteractive');
        $buffer .= $this->sectionD686ebd9201c88cc473c1d16b7642bfe($context, $indent, $value);
        $value = $context->find('isInteractive');
        if (empty($value)) {
            
            $value = $context->find('modhiddenfromstudents');
            $buffer .= $this->section71906a28609805c875b943322480328b($context, $indent, $value);
            $value = $context->find('modstealth');
            $buffer .= $this->sectionAe1c68b70914ab0090ec473692a758ca($context, $indent, $value);
        }

        return $buffer;
    }

    private function section77ad8ac7469bf000ef5401332c19203d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        {{< core/local/dropdown/status}}
            {{$ buttonclasses }} badge rounded-pill bg-secondary text-dark dropdown-toggle border-0 {{/ buttonclasses }}
        {{/ core/local/dropdown/status}}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        ';
                if ($parent = $this->mustache->loadPartial('core/local/dropdown/status')) {
                    $context->pushBlockContext(array(
                        'buttonclasses' => array($this, 'block0afdf4c3a010eef8bd8294399b9e7f73'),
                    ));
                    $buffer .= $parent->renderInternal($context, $indent);
                    $context->popBlockContext();
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD686ebd9201c88cc473c1d16b7642bfe(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    <div class="activity-badges my-1" data-region="visibility">
    {{#dropwdown}}
        {{< core/local/dropdown/status}}
            {{$ buttonclasses }} badge rounded-pill bg-secondary text-dark dropdown-toggle border-0 {{/ buttonclasses }}
        {{/ core/local/dropdown/status}}
    {{/dropwdown}}
    </div>
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '    <div class="activity-badges my-1" data-region="visibility">
';
                $value = $context->find('dropwdown');
                $buffer .= $this->section77ad8ac7469bf000ef5401332c19203d($context, $indent, $value);
                $buffer .= '    </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section6a6391930071a7b8ee93dbc237b86962(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'i/show, core';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'i/show, core';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionAa413acb7776474efcb52c4cbf9fc6f9(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'hiddenfromstudents';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'hiddenfromstudents';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section71906a28609805c875b943322480328b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div class="activity-badges my-1 d-flex align-self-start align-items-center" data-region="visibility">
            <span class="badge rounded-pill bg-secondary text-dark">{{#pix}}i/show, core{{/pix}}{{#str}}hiddenfromstudents{{/str}}</span>
        </div>
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div class="activity-badges my-1 d-flex align-self-start align-items-center" data-region="visibility">
';
                $buffer .= $indent . '            <span class="badge rounded-pill bg-secondary text-dark">';
                $value = $context->find('pix');
                $buffer .= $this->section6a6391930071a7b8ee93dbc237b86962($context, $indent, $value);
                $value = $context->find('str');
                $buffer .= $this->sectionAa413acb7776474efcb52c4cbf9fc6f9($context, $indent, $value);
                $buffer .= '</span>
';
                $buffer .= $indent . '        </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD041820803eaff9a17f37390b15c5dc4(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 't/stealth, core';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 't/stealth, core';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section0a071135502ba3f510046b2eb89448a0(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'hiddenoncoursepage';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'hiddenoncoursepage';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionAe1c68b70914ab0090ec473692a758ca(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div class="activity-badges my-1 d-flex align-self-start align-items-center" data-region="visibility">
            <span class="badge rounded-pill bg-secondary text-dark">{{#pix}}t/stealth, core{{/pix}}{{#str}}hiddenoncoursepage{{/str}}</span>
        </div>
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div class="activity-badges my-1 d-flex align-self-start align-items-center" data-region="visibility">
';
                $buffer .= $indent . '            <span class="badge rounded-pill bg-secondary text-dark">';
                $value = $context->find('pix');
                $buffer .= $this->sectionD041820803eaff9a17f37390b15c5dc4($context, $indent, $value);
                $value = $context->find('str');
                $buffer .= $this->section0a071135502ba3f510046b2eb89448a0($context, $indent, $value);
                $buffer .= '</span>
';
                $buffer .= $indent . '        </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    public function block0afdf4c3a010eef8bd8294399b9e7f73($context)
    {
        $indent = $buffer = '';
        $buffer .= ' badge rounded-pill bg-secondary text-dark dropdown-toggle border-0 ';
    
        return $buffer;
    }
}
