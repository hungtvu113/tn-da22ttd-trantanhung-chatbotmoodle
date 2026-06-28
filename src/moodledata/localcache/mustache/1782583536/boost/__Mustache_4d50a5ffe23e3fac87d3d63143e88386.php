<?php

class __Mustache_4d50a5ffe23e3fac87d3d63143e88386 extends Mustache_Template
{
    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $buffer = '';

        $buffer .= $indent . '<div
';
        $buffer .= $indent . '    id="';
        $blockFunction = $context->findInBlock('drawerid');
        if (is_callable($blockFunction)) {
            $buffer .= call_user_func($blockFunction, $context);
        } else {
            $buffer .= 'drawer-';
            $value = $this->resolveValue($context->find('uniqid'), $context);
            $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        }
        $buffer .= '"
';
        $buffer .= $indent . '    class="';
        $blockFunction = $context->findInBlock('drawerclasses');
        if (is_callable($blockFunction)) {
            $buffer .= call_user_func($blockFunction, $context);
        }
        $buffer .= ' drawer bg-white ';
        $value = $context->find('show');
        if (empty($value)) {
            
            $buffer .= 'hidden';
        }
        $buffer .= '"
';
        $buffer .= $indent . '    ';
        $value = $context->find('show');
        if (empty($value)) {
            
            $buffer .= 'aria-hidden="true"';
        }
        $buffer .= '
';
        $buffer .= $indent . '    data-region="right-hand-drawer"
';
        $buffer .= $indent . '    role="region"
';
        $buffer .= $indent . '    tabindex="0"
';
        $buffer .= $indent . '>
';
        $buffer .= $indent . '    ';
        $blockFunction = $context->findInBlock('drawercontent');
        if (is_callable($blockFunction)) {
            $buffer .= call_user_func($blockFunction, $context);
        }
        $buffer .= '
';
        $buffer .= $indent . '</div>
';

        return $buffer;
    }
}
