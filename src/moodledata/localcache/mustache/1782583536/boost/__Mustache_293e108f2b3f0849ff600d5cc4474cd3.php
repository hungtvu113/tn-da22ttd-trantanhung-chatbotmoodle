<?php

class __Mustache_293e108f2b3f0849ff600d5cc4474cd3 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div class="hidden" data-filterregion="filtertypedata">
';
        $value = $context->find('filtertypes');
        $buffer .= $this->sectionBa0c1c70ca1d836363261b0df2134af0($context, $indent, $value);
        $buffer .= $indent . '</div>
';
        $buffer .= $indent . '<div class="hidden">
';
        $buffer .= $indent . '    <select disabled="disabled" data-filterfield="type" data-filterregion="filtertypelist">
';
        $buffer .= $indent . '        <option value="">';
        $value = $context->find('str');
        $buffer .= $this->sectionA0aa696ff5b0d4bc233752f56169be02($context, $indent, $value);
        $buffer .= '</option>
';
        $value = $context->find('filtertypes');
        $buffer .= $this->sectionB64f93437fbaa1a20a7ce1f7481a1f58($context, $indent, $value);
        $buffer .= $indent . '    </select>
';
        $buffer .= $indent . '</div>
';

        return $buffer;
    }

    private function sectionBa0c1c70ca1d836363261b0df2134af0(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        {{> core/datafilter/filter_type}}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('core/datafilter/filter_type')) {
                    $buffer .= $partial->renderInternal($context, $indent . '        ');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionA0aa696ff5b0d4bc233752f56169be02(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'selectfiltertype';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'selectfiltertype';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB64f93437fbaa1a20a7ce1f7481a1f58(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <option value="{{name}}">{{title}}</option>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <option value="';
                $value = $this->resolveValue($context->find('name'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">';
                $value = $this->resolveValue($context->find('title'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</option>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
