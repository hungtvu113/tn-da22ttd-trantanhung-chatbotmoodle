<?php

class __Mustache_da10bb209b0771166374409c6c92b812 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div class="initialbar ';
        $value = $this->resolveValue($context->find('class'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= ' d-flex flex-wrap justify-content-center justify-content-md-start">
';
        $buffer .= $indent . '    <span class="initialbarlabel mr-2">';
        $value = $this->resolveValue($context->find('title'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '</span>
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '    <nav class="initialbargroups d-flex flex-wrap justify-content-center justify-content-md-start">
';
        $buffer .= $indent . '        <ul class="pagination pagination-sm">
';
        $buffer .= $indent . '            <li id="';
        $value = $this->resolveValue($context->find('class'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '_page-item_';
        $value = $this->resolveValue($context->find('all'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" class="initialbarall page-item ';
        $value = $context->find('current');
        if (empty($value)) {
            
            $buffer .= 'active';
        }
        $buffer .= '">
';
        $value = $context->find('url');
        $buffer .= $this->section27164adb9a3598f9d8197452650c3f03($context, $indent, $value);
        $value = $context->find('input');
        $buffer .= $this->sectionFb6045f37d4989ea7c7267d7f35d8190($context, $indent, $value);
        $buffer .= $indent . '            </li>
';
        $buffer .= $indent . '        </ul>
';
        $value = $context->find('group');
        $buffer .= $this->section4ff99a054e3cd70b5fb0678d1b1d7c1f($context, $indent, $value);
        $buffer .= $indent . '    </nav>
';
        $buffer .= $indent . '</div>
';

        return $buffer;
    }

    private function section27164adb9a3598f9d8197452650c3f03(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <a data-initial="" class="page-link" href="{{url}}"{{^current}} aria-current="true"{{/current}}>{{all}}</a>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <a data-initial="" class="page-link" href="';
                $value = $this->resolveValue($context->find('url'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"';
                $value = $context->find('current');
                if (empty($value)) {
                    
                    $buffer .= ' aria-current="true"';
                }
                $buffer .= '>';
                $value = $this->resolveValue($context->find('all'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</a>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionFb6045f37d4989ea7c7267d7f35d8190(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <input class="page-link mr-1 rounded" {{^current}} aria-current="true"{{/current}} value="{{all}}" type="button">
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <input class="page-link mr-1 rounded" ';
                $value = $context->find('current');
                if (empty($value)) {
                    
                    $buffer .= ' aria-current="true"';
                }
                $buffer .= ' value="';
                $value = $this->resolveValue($context->find('all'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" type="button">
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section5749c750acb0d7477dd5257d00cc6d53(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'active';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'active';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section6d2531a9bf9666cc7364f9dd9122736e(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = ' aria-current="true"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= ' aria-current="true"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionF6e24fac64f143dcb835f63d9e060092(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                            <a class="page-link" href="{{url}}"{{#selected}} aria-current="true"{{/selected}}>{{name}}</a>
                        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                            <a class="page-link" href="';
                $value = $this->resolveValue($context->find('url'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '"';
                $value = $context->find('selected');
                $buffer .= $this->section6d2531a9bf9666cc7364f9dd9122736e($context, $indent, $value);
                $buffer .= '>';
                $value = $this->resolveValue($context->find('name'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</a>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section4454a0bd2198f0eac28506e3bbfe1d26(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                            <input class="page-link mr-1 rounded" {{#selected}} aria-current="true"{{/selected}} value="{{name}}" type="button">
                        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                            <input class="page-link mr-1 rounded" ';
                $value = $context->find('selected');
                $buffer .= $this->section6d2531a9bf9666cc7364f9dd9122736e($context, $indent, $value);
                $buffer .= ' value="';
                $value = $this->resolveValue($context->find('name'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" type="button">
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE440f3a51792364ab648b06a791dd086(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <li id="{{class}}_page-item_{{name}}" data-initial="{{name}}" class="page-item {{name}} {{#selected}}active{{/selected}}">
                        {{#url}}
                            <a class="page-link" href="{{url}}"{{#selected}} aria-current="true"{{/selected}}>{{name}}</a>
                        {{/url}}
                        {{#input}}
                            <input class="page-link mr-1 rounded" {{#selected}} aria-current="true"{{/selected}} value="{{name}}" type="button">
                        {{/input}}
                    </li>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <li id="';
                $value = $this->resolveValue($context->find('class'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '_page-item_';
                $value = $this->resolveValue($context->find('name'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" data-initial="';
                $value = $this->resolveValue($context->find('name'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" class="page-item ';
                $value = $this->resolveValue($context->find('name'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= ' ';
                $value = $context->find('selected');
                $buffer .= $this->section5749c750acb0d7477dd5257d00cc6d53($context, $indent, $value);
                $buffer .= '">
';
                $value = $context->find('url');
                $buffer .= $this->sectionF6e24fac64f143dcb835f63d9e060092($context, $indent, $value);
                $value = $context->find('input');
                $buffer .= $this->section4454a0bd2198f0eac28506e3bbfe1d26($context, $indent, $value);
                $buffer .= $indent . '                    </li>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section4ff99a054e3cd70b5fb0678d1b1d7c1f(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
            <ul class="pagination pagination-sm">
                {{#letter}}
                    <li id="{{class}}_page-item_{{name}}" data-initial="{{name}}" class="page-item {{name}} {{#selected}}active{{/selected}}">
                        {{#url}}
                            <a class="page-link" href="{{url}}"{{#selected}} aria-current="true"{{/selected}}>{{name}}</a>
                        {{/url}}
                        {{#input}}
                            <input class="page-link mr-1 rounded" {{#selected}} aria-current="true"{{/selected}} value="{{name}}" type="button">
                        {{/input}}
                    </li>
                {{/letter}}
            </ul>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '            <ul class="pagination pagination-sm">
';
                $value = $context->find('letter');
                $buffer .= $this->sectionE440f3a51792364ab648b06a791dd086($context, $indent, $value);
                $buffer .= $indent . '            </ul>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
