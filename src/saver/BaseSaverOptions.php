<?php

namespace omny\parser\saver;


use omny\parser\base\BaseOptions;
use omny\parser\handlers\SaveHandler;

class BaseSaverOptions extends BaseOptions
{
    public $handlers = [];

    private $defaultHandlers = [
        'save' => [
            'className' => SaveHandler::class,
            'config' => [
                'baseUploadDir' => '/www/site.local/web/upload',
                'baseHttpPath' => '/upload',
                'curlTimeout' => 300
            ]
        ]
    ];

    public function init($options)
    {
        parent::init($options);
        $this->mergeHandlersConfig();
    }

    private function mergeHandlersConfig()
    {
        if (empty($this->handlers)) {
            $this->handlers = $this->defaultHandlers;
            return true;
        }

        $result = [];
        foreach ($this->handlers as $name => $options) {
            if (isset($this->defaultHandlers[$name])) {
                $result[$name] = [
                    'className' => isset($options['className']) ? $options['className'] : $this->defaultHandlers[$name]['className'],
                    'config' => $this->extendConfig($name, $options),
                ];
            } else {
                $result[$name] = $options;
            }
        }

        $this->handlers = $result;

        return true;
    }

    private function extendConfig($name, $options)
    {
        $defaultConfig = [];
        if (isset($this->defaultHandlers[$name]['config']) && !empty($this->defaultHandlers[$name]['config'])) {
            $defaultConfig = $this->defaultHandlers[$name]['config'];
        }
        $newConfig = [];
        if (isset($options['config']) && !empty($options['config'])) {
            $newConfig = $options['config'];
        }

        return array_merge($defaultConfig, $newConfig);
    }
}
