<?php

class __Mustache_f52b56850dd6e1750a87e181395423eb extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div class="container-fluid mb-4">
';
        $buffer .= $indent . '    <div class="row">
';
        $value = $context->find('submit');
        $buffer .= $this->section6bbad5846dbfd351c73a32361c899d96($context, $indent, $value);
        $value = $context->find('previoussubmission');
        $buffer .= $this->section6bbad5846dbfd351c73a32361c899d96($context, $indent, $value);
        $value = $context->find('edit');
        $buffer .= $this->section70cac4628792d3e27f1ecf35cb3f1713($context, $indent, $value);
        $value = $context->find('remove');
        $buffer .= $this->section0828eb66cc5ce74e31ac114c4ccedcc6($context, $indent, $value);
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '</div>
';

        return $buffer;
    }

    private function section99d07c9571d5436bf3577ef619a3fbbd(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                {{>core/single_button}}
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('core/single_button')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                ');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section05aa320cfaae3196b971e10ee8e657b8(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                {{>core/help_icon}}
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('core/help_icon')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                ');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section6bbad5846dbfd351c73a32361c899d96(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div class="col-xs-6 mr-3">
            {{#button}}
                {{>core/single_button}}
            {{/button}}
            {{#help}}
                {{>core/help_icon}}
            {{/help}}
        </div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div class="col-xs-6 mr-3">
';
                $value = $context->find('button');
                $buffer .= $this->section99d07c9571d5436bf3577ef619a3fbbd($context, $indent, $value);
                $value = $context->find('help');
                $buffer .= $this->section05aa320cfaae3196b971e10ee8e657b8($context, $indent, $value);
                $buffer .= $indent . '        </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section59da52b69c6eeb90c10dd3e08a9ba266(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    {{>core/action_link}}
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('core/action_link')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                    ');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE3fdd7a2b146d7ff432f87a81b3e3b36(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                {{#button}}
                    {{>core/action_link}}
                {{/button}}
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('button');
                $buffer .= $this->section59da52b69c6eeb90c10dd3e08a9ba266($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC0a7a31fe8f3d761f2df8d39b4d04247(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    {{>core/single_button}}
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('core/single_button')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                    ');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE376858cf6101e5b36df1c2ecf1c24a3(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                    {{>core/help_icon}}
                ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                if ($partial = $this->mustache->loadPartial('core/help_icon')) {
                    $buffer .= $partial->renderInternal($context, $indent . '                    ');
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section70cac4628792d3e27f1ecf35cb3f1713(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div class="col-xs-6 mr-3">
            {{#begin}}
                {{#button}}
                    {{>core/action_link}}
                {{/button}}
            {{/begin}}
            {{^begin}}
                {{#button}}
                    {{>core/single_button}}
                {{/button}}
                {{#help}}
                    {{>core/help_icon}}
                {{/help}}
            {{/begin}}
        </div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div class="col-xs-6 mr-3">
';
                $value = $context->find('begin');
                $buffer .= $this->sectionE3fdd7a2b146d7ff432f87a81b3e3b36($context, $indent, $value);
                $value = $context->find('begin');
                if (empty($value)) {
                    
                    $value = $context->find('button');
                    $buffer .= $this->sectionC0a7a31fe8f3d761f2df8d39b4d04247($context, $indent, $value);
                    $value = $context->find('help');
                    $buffer .= $this->sectionE376858cf6101e5b36df1c2ecf1c24a3($context, $indent, $value);
                }
                $buffer .= $indent . '        </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section0828eb66cc5ce74e31ac114c4ccedcc6(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div class="col-xs-6">
            {{#button}}
                {{>core/single_button}}
            {{/button}}
        </div>
        ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div class="col-xs-6">
';
                $value = $context->find('button');
                $buffer .= $this->section99d07c9571d5436bf3577ef619a3fbbd($context, $indent, $value);
                $buffer .= $indent . '        </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
