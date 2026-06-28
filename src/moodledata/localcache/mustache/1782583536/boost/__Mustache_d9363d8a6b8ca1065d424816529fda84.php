<?php

class __Mustache_d9363d8a6b8ca1065d424816529fda84 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div class="langmenu">
';
        $buffer .= $indent . '    <div class="dropdown show">
';
        $buffer .= $indent . '        <a href="#" role="button" id="lang-menu-toggle" data-toggle="dropdown" aria-label="';
        $value = $context->find('str');
        $buffer .= $this->section10ca3eedf9bc5f23a86bca2cd8f38c36($context, $indent, $value);
        $buffer .= '" aria-haspopup="true" aria-controls="lang-action-menu" class="btn dropdown-toggle">
';
        $buffer .= $indent . '            <i class="icon fa fa-language fa-fw mr-1" aria-hidden="true"></i>
';
        $buffer .= $indent . '            <span class="langbutton">
';
        $buffer .= $indent . '                ';
        $value = $this->resolveValue($context->find('title'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '
';
        $buffer .= $indent . '            </span>
';
        $buffer .= $indent . '            <b class="caret"></b>
';
        $buffer .= $indent . '        </a>
';
        $buffer .= $indent . '        <div role="menu" aria-labelledby="lang-menu-toggle" id="lang-action-menu" class="dropdown-menu dropdown-menu-right">
';
        $value = $context->find('items');
        $buffer .= $this->sectionC914d1fa2056ac13e81dbcfa4406dcb9($context, $indent, $value);
        $buffer .= $indent . '        </div>
';
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '</div>
';

        return $buffer;
    }

    private function section10ca3eedf9bc5f23a86bca2cd8f38c36(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'language';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'language';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionFc0c0b051caebb6243b5c2bd6d728967(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'aria-current="true"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'aria-current="true"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section9abeb3f18a4afe9b1cd6477123c4b499(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{key}}="{{value}}" ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $this->resolveValue($context->find('key'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '="';
                $value = $this->resolveValue($context->find('value'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" ';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section058d1692e2f16e028ba4f1d2da1d9ac2(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    <a href="{{{url}}}" class="dropdown-item pl-5" role="menuitem" {{#isactive}}aria-current="true"{{/isactive}}
                            {{#attributes}}{{key}}="{{value}}" {{/attributes}}>
                        {{text}}
                    </a>
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                    <a href="';
                $value = $this->resolveValue($context->find('url'), $context);
                $buffer .= ($value === null ? '' : $value);
                $buffer .= '" class="dropdown-item pl-5" role="menuitem" ';
                $value = $context->find('isactive');
                $buffer .= $this->sectionFc0c0b051caebb6243b5c2bd6d728967($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '                            ';
                $value = $context->find('attributes');
                $buffer .= $this->section9abeb3f18a4afe9b1cd6477123c4b499($context, $indent, $value);
                $buffer .= '>
';
                $buffer .= $indent . '                        ';
                $value = $this->resolveValue($context->find('text'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '
';
                $buffer .= $indent . '                    </a>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC914d1fa2056ac13e81dbcfa4406dcb9(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                {{#link}}
                    <a href="{{{url}}}" class="dropdown-item pl-5" role="menuitem" {{#isactive}}aria-current="true"{{/isactive}}
                            {{#attributes}}{{key}}="{{value}}" {{/attributes}}>
                        {{text}}
                    </a>
                {{/link}}
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('link');
                $buffer .= $this->section058d1692e2f16e028ba4f1d2da1d9ac2($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
