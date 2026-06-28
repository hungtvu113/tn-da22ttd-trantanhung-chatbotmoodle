<?php

class __Mustache_095804b3e845cedd18b699927468411d extends Mustache_Template
{
    private $lambdaHelper;

    public function renderInternal(Mustache_Context $context, $indent = '')
    {
        $this->lambdaHelper = new Mustache_LambdaHelper($this->mustache, $context);
        $buffer = '';

        $buffer .= $indent . '<div id="local-chatbot-root" data-userid="';
        $value = $this->resolveValue($context->find('userid'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '" data-userfullname="';
        $value = $this->resolveValue($context->find('userfullname'), $context);
        $buffer .= ($value === null ? '' : call_user_func($this->mustache->getEscape(), $value));
        $buffer .= '">
';
        $buffer .= $indent . '    <button type="button"
';
        $buffer .= $indent . '            id="local-chatbot-launcher"
';
        $buffer .= $indent . '            class="local-chatbot-launcher"
';
        $buffer .= $indent . '            aria-label="';
        $value = $context->find('str');
        $buffer .= $this->section6c9e78f8d31124b4846d04b0cead7c4d($context, $indent, $value);
        $buffer .= '"
';
        $buffer .= $indent . '            title="';
        $value = $context->find('str');
        $buffer .= $this->section6c9e78f8d31124b4846d04b0cead7c4d($context, $indent, $value);
        $buffer .= '">
';
        $buffer .= $indent . '        <i class="fa fa-comments" aria-hidden="true"></i>
';
        $buffer .= $indent . '    </button>
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '    <div id="local-chatbot-panel" class="local-chatbot-panel" hidden aria-hidden="true" role="dialog"
';
        $buffer .= $indent . '         aria-labelledby="local-chatbot-title">
';
        $buffer .= $indent . '        <header class="local-chatbot-header">
';
        $buffer .= $indent . '            <div class="local-chatbot-header-text">
';
        $buffer .= $indent . '                <div id="local-chatbot-title" class="local-chatbot-title">
';
        $buffer .= $indent . '                    ';
        $value = $context->find('str');
        $buffer .= $this->section80d7606e9550be9dc2a5e030dd842d9b($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '                </div>
';
        $buffer .= $indent . '                <div class="local-chatbot-subtitle">
';
        $buffer .= $indent . '                    ';
        $value = $context->find('str');
        $buffer .= $this->section088fcdf6762da7441103df0e47fbbeac($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '                </div>
';
        $buffer .= $indent . '            </div>
';
        $buffer .= $indent . '            <button type="button"
';
        $buffer .= $indent . '                    id="local-chatbot-close"
';
        $buffer .= $indent . '                    class="local-chatbot-close"
';
        $buffer .= $indent . '                    aria-label="';
        $value = $context->find('str');
        $buffer .= $this->section8cbcf0eea4eff2f9f6f58d114b641d37($context, $indent, $value);
        $buffer .= '">
';
        $buffer .= $indent . '                <i class="fa fa-times" aria-hidden="true"></i>
';
        $buffer .= $indent . '            </button>
';
        $buffer .= $indent . '        </header>
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '        <div id="local-chatbot-messages" class="local-chatbot-messages" aria-live="polite">
';
        $buffer .= $indent . '            <div class="local-chatbot-msg local-chatbot-msg-bot">
';
        $buffer .= $indent . '                <div class="local-chatbot-bubble">
';
        $buffer .= $indent . '                    ';
        $value = $context->find('str');
        $buffer .= $this->section1138dc53960073c64efc8de178259a44($context, $indent, $value);
        $buffer .= '
';
        $buffer .= $indent . '                </div>
';
        $buffer .= $indent . '            </div>
';
        $buffer .= $indent . '        </div>
';
        $buffer .= $indent . '
';
        $buffer .= $indent . '        <form id="local-chatbot-form" class="local-chatbot-form" autocomplete="off">
';
        $buffer .= $indent . '            <textarea id="local-chatbot-input"
';
        $buffer .= $indent . '                      class="local-chatbot-input"
';
        $buffer .= $indent . '                      rows="1"
';
        $buffer .= $indent . '                      placeholder="';
        $value = $context->find('str');
        $buffer .= $this->sectionC892f9593c11678a48f0a223d39dcf74($context, $indent, $value);
        $buffer .= '"
';
        $buffer .= $indent . '                      aria-label="';
        $value = $context->find('str');
        $buffer .= $this->sectionC892f9593c11678a48f0a223d39dcf74($context, $indent, $value);
        $buffer .= '"></textarea>
';
        $buffer .= $indent . '            <button type="submit" class="local-chatbot-send" aria-label="';
        $value = $context->find('str');
        $buffer .= $this->section4df3bdd755e0d3dbb3b63f0ba9fb8227($context, $indent, $value);
        $buffer .= '">
';
        $buffer .= $indent . '                <i class="fa fa-paper-plane" aria-hidden="true"></i>
';
        $buffer .= $indent . '            </button>
';
        $buffer .= $indent . '        </form>
';
        $buffer .= $indent . '    </div>
';
        $buffer .= $indent . '</div>
';

        return $buffer;
    }

    private function section6c9e78f8d31124b4846d04b0cead7c4d(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'widget_open, local_chatbot';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'widget_open, local_chatbot';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section80d7606e9550be9dc2a5e030dd842d9b(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'widget_title, local_chatbot';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'widget_title, local_chatbot';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section088fcdf6762da7441103df0e47fbbeac(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'widget_subtitle, local_chatbot';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'widget_subtitle, local_chatbot';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section8cbcf0eea4eff2f9f6f58d114b641d37(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'widget_close, local_chatbot';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'widget_close, local_chatbot';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section1138dc53960073c64efc8de178259a44(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'widget_welcome, local_chatbot';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'widget_welcome, local_chatbot';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function sectionC892f9593c11678a48f0a223d39dcf74(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'widget_placeholder, local_chatbot';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'widget_placeholder, local_chatbot';
                $context->pop();
            }
        }
    
        return $buffer;
    }

    private function section4df3bdd755e0d3dbb3b63f0ba9fb8227(Mustache_Context $context, $indent, $value)
    {
        $buffer = '';
    
        if (!is_string($value) && is_callable($value)) {
            $source = 'widget_send, local_chatbot';
            $result = (string) call_user_func($value, $source, $this->lambdaHelper);
            $buffer .= $result;
        } elseif (!empty($value)) {
            $values = $this->isIterable($value) ? $value : array($value);
            foreach ($values as $value) {
                $context->push($value);
                
                $buffer .= 'widget_send, local_chatbot';
                $context->pop();
            }
        }
    
        return $buffer;
    }

}
