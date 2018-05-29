<?php
/**
 * UploadedFile.php
 *
 * Creator:    chongyi
 * Created at: 2016/08/09 00:56
 */

namespace Journey\Modules\Swoole\Http;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
use Illuminate\Support\Traits\Macroable;

/**
 * Class UploadedFile
 *
 * @package Swoole\Laravel\Http
 */
class UploadedFile extends SymfonyUploadedFile
{
    use Macroable;

    private $test;

    /**
     * Get the fully qualified path to the file.
     *
     * @return string
     */
    public function path()
    {
        return $this->getRealPath();
    }
    /**
     * Get the file's extension.
     *
     * @return string
     */
    public function extension()
    {
        return $this->guessExtension();
    }
    /**
     * Get the file's extension supplied by the client.
     *
     * @return string
     */
    public function clientExtension()
    {
        return $this->guessClientExtension();
    }
    /**
     * Get a filename for the file that is the MD5 hash of the contents.
     *
     * @param  string  $path
     * @return string
     */
    public function hashName($path = null)
    {
        if ($path) {
            $path = rtrim($path, '/').'/';
        }
        return $path.md5_file($this->path()).'.'.$this->extension();
    }
    /**
     * Create a new file instance from a base instance.
     *
     * @param  \Symfony\Component\HttpFoundation\File\UploadedFile  $file
     * @param  bool $test
     * @return static
     */
    public static function createFromBase(SymfonyUploadedFile $file, $test = false)
    {
        return $file instanceof static ? $file : new static(
            $file->getPathname(),
            $file->getClientOriginalName(),
            $file->getClientMimeType(),
            $file->getClientSize(),
            $file->getError(),
            $test
        );
    }

    /**
     * @param string $directory
     * @param null   $name
     *
     * @return \Symfony\Component\HttpFoundation\File\File
     */
    public function move($directory, $name = null)
    {
        if ($this->isValid()) {
            if ($this->test) {
                return parent::move($directory, $name);
            }

            $target = $this->getTargetFile($directory, $name);

            if (!@rename($this->getPathname(), $target)) {
                $error = error_get_last();
                throw new FileException(sprintf('Could not move the file "%s" to "%s" (%s)', $this->getPathname(), $target, strip_tags($error['message'])));
            }

            @chmod($target, 0666 & ~umask());

            return $target;
        }

        throw new FileException($this->getErrorMessage());
    }


}