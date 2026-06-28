<?php

class __Mustache_7b45092495dbcb4779ebb4a1e3671c19 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        if ($partial = $this->mustache->loadPartial('core/datafilter/filter')) {
            $buffer .= $partial->renderInternal($context);
        }
        $buffer .= $indent . '
';
        $value = $context->find('js');
        $buffer .= $this->section8ba8fab0e091431a1157183edd56b6f0($context, $indent, $value);

        return $buffer;
    }

    private function section8ba8fab0e091431a1157183edd56b6f0(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    require([\'core_user/participants_filter\'], function(ParticipantsFilter) {
        ParticipantsFilter.init(\'core-filter-{{uniqid}}\');
    });
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '    require([\'core_user/participants_filter\'], function(ParticipantsFilter) {
';
                $buffer .= $indent . '        ParticipantsFilter.init(\'core-filter-';
                $value = $this->resolveValue($context->find('uniqid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '\');
';
                $buffer .= $indent . '    });
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
