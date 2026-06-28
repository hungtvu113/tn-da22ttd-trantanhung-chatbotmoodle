<?php

class __Mustache_c17a239624a7fd0f7c24b10f1bad4346 extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $value = $context->find('singleheader');
        $buffer .= $this->sectionE7ca95175df798239b6fdbdc4cc848ae($context, $indent, $value);
        $value = $context->find('header');
        $buffer .= $this->sectionE7ca95175df798239b6fdbdc4cc848ae($context, $indent, $value);
        $value = $context->find('singleheader');
        if (empty($value)) {
            
            $value = $context->find('restrictionlock');
            $buffer .= $this->section40014f3c3b27afa9bac347d13925cdc0($context, $indent, $value);
        }
        $buffer .= $indent . '<div data-region="sectionbadges" class="sectionbadges d-flex align-items-center">
';
        $blockFunction = $context->findInBlock('core_courseformat/local/content/section/badges');
        if (is_callable($blockFunction)) {
            $buffer .= call_user_func($blockFunction, $context);
        } else {
            if ($partial = $this->mustache->loadPartial('core_courseformat/local/content/section/badges')) {
                $buffer .= $partial->renderInternal($context, $indent . '        ');
            }
        }
        $buffer .= $indent . '</div>
';
        $value = $context->find('collapsemenu');
        $buffer .= $this->section97dbfedde9318f46e93025d0fa218ba7($context, $indent, $value);
        $value = $context->find('controlmenu');
        $buffer .= $this->sectionD028a76c4d640970aa1a6d6c554e3ffc($context, $indent, $value);
        $value = $context->find('header');
        $buffer .= $this->section7802735ce2b0d8e041adf5df21aedc92($context, $indent, $value);
        $buffer .= $indent . '</div>
';
        $buffer .= $indent . '<div id="coursecontentcollapseid';
        $value = $this->resolveValue($context->find('id'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '"
';
        $buffer .= $indent . '    class="content ';
        $value = $context->find('iscoursedisplaymultipage');
        if (empty($value)) {
            
            $value = $context->find('sitehome');
            if (empty($value)) {
                
                $value = $context->find('displayonesection');
                if (empty($value)) {
                    
                    $buffer .= 'course-content-item-content collapse ';
                    $value = $context->find('contentcollapsed');
                    if (empty($value)) {
                        
                        $buffer .= 'show';
                    }
                }
            }
        }
        $buffer .= '">
';
        $buffer .= $indent . '    <div class="';
        $value = $context->find('hasavailability');
        $buffer .= $this->section1dee8adaf405bfc6db6cbb048fef1653($context, $indent, $value);
        $buffer .= ' my-3" data-for="sectioninfo">
';
        $value = $context->find('summary');
        $buffer .= $this->sectionB7d088fc9cb45cec0570eba36fa841b1($context, $indent, $value);
        $value = $context->find('availability');
        $buffer .= $this->sectionDa58ced0c5f4b21d2c6f6a96dd9e1715($context, $indent, $value);
        $buffer .= $indent . '</div>
';
        $value = $context->find('cmsummary');
        $buffer .= $this->section4d8c7661b8f358cb971b80b926ef9a06($context, $indent, $value);
        $value = $context->find('cmlist');
        $buffer .= $this->section1e07e3eb7c463b6dd99a6caded38b44a($context, $indent, $value);
        $value = $this->resolveValue($context->find('cmcontrols'), $context);
        $buffer .= $indent . ($value === null ? '' : $value);
        $buffer .= '
';

        return $buffer;
    }

    private function sectionE7ca95175df798239b6fdbdc4cc848ae(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
{{$ core_courseformat/local/content/section/header }}
    {{> core_courseformat/local/content/section/header }}
{{/ core_courseformat/local/content/section/header }}
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $blockFunction = $context->findInBlock('core_courseformat/local/content/section/header');
                if (is_callable($blockFunction)) {
                    $buffer .= call_user_func($blockFunction, $context);
                } else {
                    if ($partial = $this->mustache->loadPartial('core_courseformat/local/content/section/header')) {
                        $buffer .= $partial->renderInternal($context, $indent . '    ');
                    }
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section0b7a74ef365e2e19e174c81d3ebda570(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 't/unlock, core';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 't/unlock, core';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section40014f3c3b27afa9bac347d13925cdc0(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        <div class="align-self-center ml-2">
            {{#pix}}t/unlock, core{{/pix}}
        </div>
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= $indent . '        <div class="align-self-center ml-2">
';
                $buffer .= $indent . '            ';
                $value = $context->find('pix');
                $buffer .= $this->section0b7a74ef365e2e19e174c81d3ebda570($context, $indent, $value);
                $buffer .= '
';
                $buffer .= $indent . '        </div>
';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionE1c5833858b6a763436e852c524f170c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'collapseall';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'collapseall';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section5c42c2ba118f2e9924725a2f71fafad6(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'expandall';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'expandall';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section97dbfedde9318f46e93025d0fa218ba7(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    {{^displayonesection}}
    <div class="flex-fill d-flex justify-content-end mr-2 align-self-start mt-2">
        <a
            id="collapsesections"
            class="section-collapsemenu"
            href="#"
            aria-expanded="true"
            role="button"
            data-toggle="toggleall"
        >
            <span class="collapseall text-nowrap">{{#str}}collapseall{{/str}}</span>
            <span class="expandall text-nowrap">{{#str}}expandall{{/str}}</span>
        </a>
    </div>
    {{/displayonesection}}
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('displayonesection');
                if (empty($value)) {
                    
                    $buffer .= $indent . '    <div class="flex-fill d-flex justify-content-end mr-2 align-self-start mt-2">
';
                    $buffer .= $indent . '        <a
';
                    $buffer .= $indent . '            id="collapsesections"
';
                    $buffer .= $indent . '            class="section-collapsemenu"
';
                    $buffer .= $indent . '            href="#"
';
                    $buffer .= $indent . '            aria-expanded="true"
';
                    $buffer .= $indent . '            role="button"
';
                    $buffer .= $indent . '            data-toggle="toggleall"
';
                    $buffer .= $indent . '        >
';
                    $buffer .= $indent . '            <span class="collapseall text-nowrap">';
                    $value = $context->find('str');
                    $buffer .= $this->sectionE1c5833858b6a763436e852c524f170c($context, $indent, $value);
                    $buffer .= '</span>
';
                    $buffer .= $indent . '            <span class="expandall text-nowrap">';
                    $value = $context->find('str');
                    $buffer .= $this->section5c42c2ba118f2e9924725a2f71fafad6($context, $indent, $value);
                    $buffer .= '</span>
';
                    $buffer .= $indent . '        </a>
';
                    $buffer .= $indent . '    </div>
';
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionD028a76c4d640970aa1a6d6c554e3ffc(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    {{^displayonesection}}
        {{$ core_courseformat/local/content/section/controlmenu }}
            {{> core_courseformat/local/content/section/controlmenu }}
        {{/ core_courseformat/local/content/section/controlmenu }}
    {{/displayonesection}}
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('displayonesection');
                if (empty($value)) {
                    
                    $blockFunction = $context->findInBlock('core_courseformat/local/content/section/controlmenu');
                    if (is_callable($blockFunction)) {
                        $buffer .= call_user_func($blockFunction, $context);
                    } else {
                        if ($partial = $this->mustache->loadPartial('core_courseformat/local/content/section/controlmenu')) {
                            $buffer .= $partial->renderInternal($context, $indent . '            ');
                        }
                    }
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section70fc2265f21857283760f122a5c52abf(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'gotosection, course, {{name}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'gotosection, course, ';
                $value = $this->resolveValue($context->find('name'), $context);
                $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section9ea6be45f1588d6476c3f4a2d3f1e926(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 't/right, moodle';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 't/right, moodle';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section8115b5e8e6b9abaabc1687cbdb72c01c(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 't/left, moodle, {{#str}}gotosection, course, {{name}}{{/str}}';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 't/left, moodle, ';
                $value = $context->find('str');
                $buffer .= $this->section70fc2265f21857283760f122a5c52abf($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionCe39c05efa04bd8055fc47e8bb596ff7(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        {{^displayonesection}}
            {{^controlmenu}}
                <div class="section_goto bulk-hidden ml-auto" data-sectionid="{{id}}">
                    <a href="{{{url}}}"
                    class="btn btn-icon d-flex align-items-center justify-content-center icon-no-margin"
                    title="{{#str}}gotosection, course, {{name}}{{/str}}">
                            <span class="dir-rtl-hide">
                                {{#pix}}t/right, moodle{{/pix}}
                            </span>
                            <span class="dir-ltr-hide">
                                {{#pix}}t/left, moodle, {{#str}}gotosection, course, {{name}}{{/str}}{{/pix}}
                            </span>
                            <span class="sr-only">
                                {{#str}}gotosection, course, {{name}}{{/str}}
                            </span>
                    </a>
                </div>
            {{/controlmenu}}
        {{/displayonesection}}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('displayonesection');
                if (empty($value)) {
                    
                    $value = $context->find('controlmenu');
                    if (empty($value)) {
                        
                        $buffer .= $indent . '                <div class="section_goto bulk-hidden ml-auto" data-sectionid="';
                        $value = $this->resolveValue($context->find('id'), $context);
                        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
                        $buffer .= '">
';
                        $buffer .= $indent . '                    <a href="';
                        $value = $this->resolveValue($context->find('url'), $context);
                        $buffer .= ($value === null ? '' : $value);
                        $buffer .= '"
';
                        $buffer .= $indent . '                    class="btn btn-icon d-flex align-items-center justify-content-center icon-no-margin"
';
                        $buffer .= $indent . '                    title="';
                        $value = $context->find('str');
                        $buffer .= $this->section70fc2265f21857283760f122a5c52abf($context, $indent, $value);
                        $buffer .= '">
';
                        $buffer .= $indent . '                            <span class="dir-rtl-hide">
';
                        $buffer .= $indent . '                                ';
                        $value = $context->find('pix');
                        $buffer .= $this->section9ea6be45f1588d6476c3f4a2d3f1e926($context, $indent, $value);
                        $buffer .= '
';
                        $buffer .= $indent . '                            </span>
';
                        $buffer .= $indent . '                            <span class="dir-ltr-hide">
';
                        $buffer .= $indent . '                                ';
                        $value = $context->find('pix');
                        $buffer .= $this->section8115b5e8e6b9abaabc1687cbdb72c01c($context, $indent, $value);
                        $buffer .= '
';
                        $buffer .= $indent . '                            </span>
';
                        $buffer .= $indent . '                            <span class="sr-only">
';
                        $buffer .= $indent . '                                ';
                        $value = $context->find('str');
                        $buffer .= $this->section70fc2265f21857283760f122a5c52abf($context, $indent, $value);
                        $buffer .= '
';
                        $buffer .= $indent . '                            </span>
';
                        $buffer .= $indent . '                    </a>
';
                        $buffer .= $indent . '                </div>
';
                    }
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section7802735ce2b0d8e041adf5df21aedc92(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    {{#headerdisplaymultipage}}
        {{^displayonesection}}
            {{^controlmenu}}
                <div class="section_goto bulk-hidden ml-auto" data-sectionid="{{id}}">
                    <a href="{{{url}}}"
                    class="btn btn-icon d-flex align-items-center justify-content-center icon-no-margin"
                    title="{{#str}}gotosection, course, {{name}}{{/str}}">
                            <span class="dir-rtl-hide">
                                {{#pix}}t/right, moodle{{/pix}}
                            </span>
                            <span class="dir-ltr-hide">
                                {{#pix}}t/left, moodle, {{#str}}gotosection, course, {{name}}{{/str}}{{/pix}}
                            </span>
                            <span class="sr-only">
                                {{#str}}gotosection, course, {{name}}{{/str}}
                            </span>
                    </a>
                </div>
            {{/controlmenu}}
        {{/displayonesection}}
    {{/headerdisplaymultipage}}
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $value = $context->find('headerdisplaymultipage');
                $buffer .= $this->sectionCe39c05efa04bd8055fc47e8bb596ff7($context, $indent, $value);
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1dee8adaf405bfc6db6cbb048fef1653(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'description';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'description';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionB7d088fc9cb45cec0570eba36fa841b1(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        {{$ core_courseformat/local/content/section/summary }}
            {{> core_courseformat/local/content/section/summary }}
        {{/ core_courseformat/local/content/section/summary }}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $blockFunction = $context->findInBlock('core_courseformat/local/content/section/summary');
                if (is_callable($blockFunction)) {
                    $buffer .= call_user_func($blockFunction, $context);
                } else {
                    if ($partial = $this->mustache->loadPartial('core_courseformat/local/content/section/summary')) {
                        $buffer .= $partial->renderInternal($context, $indent . '            ');
                    }
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionDa58ced0c5f4b21d2c6f6a96dd9e1715(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
        {{$ core_courseformat/local/content/section/availability }}
            {{> core_courseformat/local/content/section/availability }}
        {{/ core_courseformat/local/content/section/availability }}
    ';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $blockFunction = $context->findInBlock('core_courseformat/local/content/section/availability');
                if (is_callable($blockFunction)) {
                    $buffer .= call_user_func($blockFunction, $context);
                } else {
                    if ($partial = $this->mustache->loadPartial('core_courseformat/local/content/section/availability')) {
                        $buffer .= $partial->renderInternal($context, $indent . '            ');
                    }
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section4d8c7661b8f358cb971b80b926ef9a06(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    {{$ core_courseformat/local/content/section/cmsummary }}
        {{> core_courseformat/local/content/section/cmsummary }}
    {{/ core_courseformat/local/content/section/cmsummary }}
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $blockFunction = $context->findInBlock('core_courseformat/local/content/section/cmsummary');
                if (is_callable($blockFunction)) {
                    $buffer .= call_user_func($blockFunction, $context);
                } else {
                    if ($partial = $this->mustache->loadPartial('core_courseformat/local/content/section/cmsummary')) {
                        $buffer .= $partial->renderInternal($context, $indent . '        ');
                    }
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1e07e3eb7c463b6dd99a6caded38b44a(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = '
    {{$ core_courseformat/local/content/section/cmlist }}
        {{> core_courseformat/local/content/section/cmlist }}
    {{/ core_courseformat/local/content/section/cmlist }}
';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $blockFunction = $context->findInBlock('core_courseformat/local/content/section/cmlist');
                if (is_callable($blockFunction)) {
                    $buffer .= call_user_func($blockFunction, $context);
                } else {
                    if ($partial = $this->mustache->loadPartial('core_courseformat/local/content/section/cmlist')) {
                        $buffer .= $partial->renderInternal($context, $indent . '        ');
                    }
                }
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
