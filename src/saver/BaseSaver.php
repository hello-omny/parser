<?php

namespace omny\parser\saver;


use omny\parser\entities\Article;
use omny\parser\handlers\SaveHandler;
use omny\parser\base\Component;

class BaseSaver extends Component
{
    /**
     * @param $article Article
     * @throws \Exception
     */
    public function save(Article $article)
    {
        throw new \Exception('No code in ' . __METHOD__);
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