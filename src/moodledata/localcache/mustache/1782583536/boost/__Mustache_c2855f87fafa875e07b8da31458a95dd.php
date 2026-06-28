<?php

class __Mustache_c2855f87fafa875e07b8da31458a95dd extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<nav class="tertiary-navigation-selector">
';
        if ($partial = $this->mustache->loadPartial('core/select_menu')) {
            $buffer .= $partial->renderInternal($context, $indent . '    ');
        }
        $buffer .= $indent . '</nav>
';
        $value = $context->find('js');
        $buffer .= $this->section95f31705f8dbc41f70000d4e47cad32c($context, $indent, $value);

        return $buffer;
    }

    private function section95f31705f8dbc41f70000d4e47cad32c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    document.querySelector(\'#{{baseid}}\').addEventListener(\'change\', function(e) {
        window.location.href = e.target.value;
    });
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '    document.querySelector(\'#';
                $value = $this->resolveValue($context->find('baseid'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '\').addEventListener(\'change\', function(e) {
';
                $buffer .= $indent . '        window.location.href = e.target.value;
';
                $buffer .= $indent . '    });
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
