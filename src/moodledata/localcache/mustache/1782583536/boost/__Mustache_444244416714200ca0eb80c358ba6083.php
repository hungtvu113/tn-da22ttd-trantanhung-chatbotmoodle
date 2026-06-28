<?php

class __Mustache_444244416714200ca0eb80c358ba6083 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $value = $context->find('haspages');
        $buffer .= $this->section8edd90642fea915cf79a169ff4890ebc($context, $indent, $value);

        return $buffer;
    }

    private function section645524493d529813a1222916ebeb2ac7(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'previouspage, moodle';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'previouspage, moodle';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section3591a4f4d567b71b33b7b94a2e06d473(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <li class="page-item" data-page-number="{{page}}">
                    <a href="{{url}}" class="page-link">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">{{#str}}previouspage, moodle{{/str}}</span>
                    </a>
                </li>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                <li class="page-item" data-page-number="';
                $value = $this->resolveValue($context->find('page'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $buffer .= $indent . '                    <a href="';
                $value = $this->resolveValue($context->find('url'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" class="page-link">
';
                $buffer .= $indent . '                        <span aria-hidden="true">&laquo;</span>
';
                $buffer .= $indent . '                        <span class="sr-only">';
                $value = $context->find('str');
                $buffer .= $this->section645524493d529813a1222916ebeb2ac7($context, $indent, $value);
                $buffer .= '</span>
';
                $buffer .= $indent . '                    </a>
';
                $buffer .= $indent . '                </li>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section90b252dbdd6f7779dcec8174398852c0(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'pagea, moodle, {{page}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'pagea, moodle, ';
                $value = $this->resolveValue($context->find('page'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE21ff66cb8764f0de565c1c7dd1d0c3d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <li class="page-item" data-page-number="{{page}}">
                    <a href="{{url}}" class="page-link">
                        <span aria-hidden="true">{{page}}</span>
                        <span class="sr-only">{{#str}}pagea, moodle, {{page}}{{/str}}</span>
                    </a>
                </li>
                <li class="page-item disabled" data-page-number="{{page}}">
                    <span class="page-link">&hellip;</span>
                </li>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                <li class="page-item" data-page-number="';
                $value = $this->resolveValue($context->find('page'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $buffer .= $indent . '                    <a href="';
                $value = $this->resolveValue($context->find('url'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" class="page-link">
';
                $buffer .= $indent . '                        <span aria-hidden="true">';
                $value = $this->resolveValue($context->find('page'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</span>
';
                $buffer .= $indent . '                        <span class="sr-only">';
                $value = $context->find('str');
                $buffer .= $this->section90b252dbdd6f7779dcec8174398852c0($context, $indent, $value);
                $buffer .= '</span>
';
                $buffer .= $indent . '                    </a>
';
                $buffer .= $indent . '                </li>
';
                $buffer .= $indent . '                <li class="page-item disabled" data-page-number="';
                $value = $this->resolveValue($context->find('page'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $buffer .= $indent . '                    <span class="page-link">&hellip;</span>
';
                $buffer .= $indent . '                </li>
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

    private function sectionE6922901afa7b60d3ce7403587f8d6c3(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '{{.}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $this->resolveValue($context->last(), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section6dabfc6e3de0f7ff4baa136bb16e58d0(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'aria-current="page"';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'aria-current="page"';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1ff05e1ab549e4d5257315cd8c5c5d4c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <li class="page-item {{#active}}active{{/active}}" data-page-number="{{page}}">
                    <a href="{{#url}}{{.}}{{/url}}{{^url}}#{{/url}}" class="page-link" {{#active}}aria-current="page"{{/active}}>
                        <span aria-hidden="true">{{page}}</span>
                        <span class="sr-only">{{#str}}pagea, moodle, {{page}}{{/str}}</span>
                    </a>
                </li>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                <li class="page-item ';
                $value = $context->find('active');
                $buffer .= $this->section5749c750acb0d7477dd5257d00cc6d53($context, $indent, $value);
                $buffer .= '" data-page-number="';
                $value = $this->resolveValue($context->find('page'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $buffer .= $indent . '                    <a href="';
                $value = $context->find('url');
                $buffer .= $this->sectionE6922901afa7b60d3ce7403587f8d6c3($context, $indent, $value);
                $value = $context->find('url');
                if (empty($value)) {
                    
                    $buffer .= '#';
                }
                $buffer .= '" class="page-link" ';
                $value = $context->find('active');
                $buffer .= $this->section6dabfc6e3de0f7ff4baa136bb16e58d0($context, $indent, $value);
                $buffer .= '>
';
                $buffer .= $indent . '                        <span aria-hidden="true">';
                $value = $this->resolveValue($context->find('page'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</span>
';
                $buffer .= $indent . '                        <span class="sr-only">';
                $value = $context->find('str');
                $buffer .= $this->section90b252dbdd6f7779dcec8174398852c0($context, $indent, $value);
                $buffer .= '</span>
';
                $buffer .= $indent . '                    </a>
';
                $buffer .= $indent . '                </li>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section491a4c7001ab6bec6306f447c5c65172(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <li class="page-item disabled" data-page-number="{{page}}">
                    <span class="page-link">&hellip;</span>
                </li>
                <li class="page-item" data-page-number="{{page}}">
                    <a href="{{url}}" class="page-link">
                        <span aria-hidden="true">{{page}}</span>
                        <span class="sr-only">{{#str}}pagea, moodle, {{page}}{{/str}}</span>
                    </a>
                </li>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                <li class="page-item disabled" data-page-number="';
                $value = $this->resolveValue($context->find('page'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $buffer .= $indent . '                    <span class="page-link">&hellip;</span>
';
                $buffer .= $indent . '                </li>
';
                $buffer .= $indent . '                <li class="page-item" data-page-number="';
                $value = $this->resolveValue($context->find('page'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $buffer .= $indent . '                    <a href="';
                $value = $this->resolveValue($context->find('url'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" class="page-link">
';
                $buffer .= $indent . '                        <span aria-hidden="true">';
                $value = $this->resolveValue($context->find('page'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '</span>
';
                $buffer .= $indent . '                        <span class="sr-only">';
                $value = $context->find('str');
                $buffer .= $this->section90b252dbdd6f7779dcec8174398852c0($context, $indent, $value);
                $buffer .= '</span>
';
                $buffer .= $indent . '                    </a>
';
                $buffer .= $indent . '                </li>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD30ff9b9dd5fef1da6e8aab09ea5dfed(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'nextpage, moodle';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'nextpage, moodle';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section5fd5ad5e3936741fbb61d102ffd65c3a(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
                <li class="page-item" data-page-number="{{page}}">
                    <a href="{{url}}" class="page-link">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">{{#str}}nextpage, moodle{{/str}}</span>
                    </a>
                </li>
            ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '                <li class="page-item" data-page-number="';
                $value = $this->resolveValue($context->find('page'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $buffer .= $indent . '                    <a href="';
                $value = $this->resolveValue($context->find('url'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" class="page-link">
';
                $buffer .= $indent . '                        <span aria-hidden="true">&raquo;</span>
';
                $buffer .= $indent . '                        <span class="sr-only">';
                $value = $context->find('str');
                $buffer .= $this->sectionD30ff9b9dd5fef1da6e8aab09ea5dfed($context, $indent, $value);
                $buffer .= '</span>
';
                $buffer .= $indent . '                    </a>
';
                $buffer .= $indent . '                </li>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section8edd90642fea915cf79a169ff4890ebc(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    <nav aria-label="{{label}}" class="pagination pagination-centered justify-content-center">
        <ul class="mt-1 pagination " data-page-size="{{pagesize}}">
            {{#previous}}
                <li class="page-item" data-page-number="{{page}}">
                    <a href="{{url}}" class="page-link">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">{{#str}}previouspage, moodle{{/str}}</span>
                    </a>
                </li>
            {{/previous}}
            {{#first}}
                <li class="page-item" data-page-number="{{page}}">
                    <a href="{{url}}" class="page-link">
                        <span aria-hidden="true">{{page}}</span>
                        <span class="sr-only">{{#str}}pagea, moodle, {{page}}{{/str}}</span>
                    </a>
                </li>
                <li class="page-item disabled" data-page-number="{{page}}">
                    <span class="page-link">&hellip;</span>
                </li>
            {{/first}}
            {{#pages}}
                <li class="page-item {{#active}}active{{/active}}" data-page-number="{{page}}">
                    <a href="{{#url}}{{.}}{{/url}}{{^url}}#{{/url}}" class="page-link" {{#active}}aria-current="page"{{/active}}>
                        <span aria-hidden="true">{{page}}</span>
                        <span class="sr-only">{{#str}}pagea, moodle, {{page}}{{/str}}</span>
                    </a>
                </li>
            {{/pages}}
            {{#last}}
                <li class="page-item disabled" data-page-number="{{page}}">
                    <span class="page-link">&hellip;</span>
                </li>
                <li class="page-item" data-page-number="{{page}}">
                    <a href="{{url}}" class="page-link">
                        <span aria-hidden="true">{{page}}</span>
                        <span class="sr-only">{{#str}}pagea, moodle, {{page}}{{/str}}</span>
                    </a>
                </li>
            {{/last}}
            {{#next}}
                <li class="page-item" data-page-number="{{page}}">
                    <a href="{{url}}" class="page-link">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">{{#str}}nextpage, moodle{{/str}}</span>
                    </a>
                </li>
            {{/next}}
        </ul>
    </nav>
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '    <nav aria-label="';
                $value = $this->resolveValue($context->find('label'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '" class="pagination pagination-centered justify-content-center">
';
                $buffer .= $indent . '        <ul class="mt-1 pagination " data-page-size="';
                $value = $this->resolveValue($context->find('pagesize'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $buffer .= '">
';
                $value = $context->find('previous');
                $buffer .= $this->section3591a4f4d567b71b33b7b94a2e06d473($context, $indent, $value);
                $value = $context->find('first');
                $buffer .= $this->sectionE21ff66cb8764f0de565c1c7dd1d0c3d($context, $indent, $value);
                $value = $context->find('pages');
                $buffer .= $this->section1ff05e1ab549e4d5257315cd8c5c5d4c($context, $indent, $value);
                $value = $context->find('last');
                $buffer .= $this->section491a4c7001ab6bec6306f447c5c65172($context, $indent, $value);
                $value = $context->find('next');
                $buffer .= $this->section5fd5ad5e3936741fbb61d102ffd65c3a($context, $indent, $value);
                $buffer .= $indent . '        </ul>
';
                $buffer .= $indent . '    </nav>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
