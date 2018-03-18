<?php

namespace omny\parser\components;


use omny\parser\base\Component;
use omny\parser\entities\Article;
use omny\parser\handlers\SaveHandler;

class Saver extends Component
{
    /** @var bool  */
    public $reSave = true;
    /** @var array  */
    public $handlers = [
        'save' => [
            'className' => SaveHandler::class,
            'config' => [
                'baseUploadDir' => '/www/site.local/web/upload',
                'baseHttpPath' => '/upload',
                'curlTimeout' => 300
            ]
        ]
    ];

    /**
     * @param $article Article
     * @throws \Exception
     */
    public function save(Article $article)
    {
        throw new \Exception('Plz, add some code for this method.');
    }

    /**
     * @param $file
     * @return bool|string
     * @throws \Exception
     */
    public function savePreview($file)
    {
        if ($this->hasHandler('save')) {
            /** @var SaveHandler $saveHandler */
            $saveHandler = $this->getHandler('save');
            $saveHandler->load([
                'url' => $file,
                'storage' => 'media',
                'name' => null,
            ]);
            if ($fileName = $saveHandler->run()) {
                return $fileName;
            }
            return $file;
        }

        return $file;
    }

}
