<?php

namespace omny\parser\handlers;


use omny\curl\Curl;
use omny\parser\base\Object;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

class SaveHandler extends Object implements HandlerInterface
{
    public $url = null;
    public $storage = 'media';
    public $name = null;
    public $reSave = true;

    public $baseUploadDir = '/www/site.local/web/upload';
    public $baseHttpPath = '/upload';
    public $curlTimeout = 300;

    /**
     * @param array $params
     * @return mixed|void
     */
    public function load(array $params)
    {
        $class = new \ReflectionClass($this);

        foreach ($params as $name => $value) {
            $property = $class->getProperty($name);

            if (!empty($property) && $property->isPublic() && !$property->isStatic()) {
                $this->$name = $value;
            }
        }
    }

    /**
     * @param bool $validate
     * @return bool|mixed|string
     * @throws \Exception
     */
    public function run($validate = true)
    {
        if ($validate && $this->validate()) {
            return $this->downloadAndSaveFile($this->url, $this->storage, $this->name);
        }

        return false;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        if (empty($this->url) || !is_string($this->url)) {
            return false;
        }

        if (empty($this->storage) || !is_string($this->storage)) {
            return false;
        }

        if (!is_null($this->name) && !is_string($this->name)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $url
     * @param string $storage
     * @param null $name
     * @return bool|string
     * @throws \Exception
     */
    protected function downloadAndSaveFile(string $url, $storage = 'media', $name = null)
    {
        $name = empty($name) ? pathinfo($url, PATHINFO_BASENAME) : $name;
        $fileExtension = pathinfo($url, PATHINFO_EXTENSION);
        $generatedFileName = md5($name) . '.' . $fileExtension;

        $relativePath = $this->createStorage($storage, $name);
        $absolutePath = $this->baseUploadDir . DIRECTORY_SEPARATOR . $relativePath;
        $httpPath = $this->baseHttpPath . DIRECTORY_SEPARATOR . $relativePath;

        $destinationFile = $absolutePath . DIRECTORY_SEPARATOR . $generatedFileName;
        $destinationHttpFile = $httpPath . DIRECTORY_SEPARATOR . $generatedFileName;

        if ($this->reSave || $this->fileNotExist($destinationFile)) {
            if ($this->downloadFile($url, $destinationFile)) {
                echo sprintf("File %s downloaded successfully.", $destinationFile);
                return $destinationHttpFile;
            }
        }

        return false;
    }

    private function fileNotExist($file)
    {
        return !file_exists($file);
    }

    /**
     * @param $storage
     * @param $salt
     * @return string
     * @throws \Exception
     */
    protected function createStorage($storage = 'media', $salt)
    {
        $hash = md5($salt);
        $randomDirectory = substr($hash, 0, 7);

        $relativePath = $storage . DIRECTORY_SEPARATOR . $randomDirectory;
        $absolutePath = $this->baseUploadDir . DIRECTORY_SEPARATOR . $relativePath;

        if (static::createDirectory($absolutePath)) {
            return $relativePath;
        };

        return null;
    }

    /**
     * @param $path
     * @param int $mode
     * @return bool
     * @throws \Exception
     */
    protected function createDirectory($path, $mode = 0775)
    {
        $fileSystem = new Filesystem();

        try {
            $fileSystem->mkdir($path, $mode);
            return true;
        } catch (IOExceptionInterface $exception) {
            throw new \Exception("An error occurred while creating your directory at " . $exception->getPath());
        }
    }

    /**
     * @param string $url
     * @param string $pathToFile
     * @return bool
     */
    protected function downloadFile(string $url, string $pathToFile)
    {
        try {
            sleep(10);

            $curl = new Curl();
            $curl->init();
            $destinationFile = @fopen($pathToFile, "w");
            $curl->setOption(CURLOPT_FILE, $destinationFile);
            $curl->auto_flush = false;
            $this->setUpCurl($curl);

            $curl->get($url);
            fclose($destinationFile);
            $curl->close();

            return true;
        } catch (\Exception $e) {

            return false;
        }
    }

    /**
     * @param Curl $curl
     */
    private function setUpCurl(Curl $curl)
    {
        $curl->setOption(CURLOPT_HEADER, 0);
        $curl->setOption(CURLOPT_MAXREDIRS, 5);
        $curl->setOption(CURLOPT_TIMEOUT, $this->curlTimeout);
        $curl->setOption(CURLOPT_SSL_VERIFYPEER, false);
    }

}
