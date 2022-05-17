<?php

/**
 * Pdfparse the PHP tools to get coordinate (x,y) of text on PDF file
 *
 * Requirement
 * - Php version > 5.6
 * - Enable shell_exec
 * - bin folder writable recursive
 * for ubuntu linux run command on teminal <br/> 
 * sudo chmod 775 'bin' 
 * 
 * LICENSE: MIT
 *
 * @category   Tools
 * @package    Pdfparse
 * @author     Saepulawr <adm.ipul@gmail.com>
 * @license    https://raw.githubusercontent.com/Saepulawr/pdfparse/main/LICENSE MIT Licence
 * @version    1.0
 * @link       https://github.com/Saepulawr/pdfparse
 */
class Pdfparse
{
    protected $tmpHash;
    protected $parsedSource;
    function __construct($pdfFile)
    {
        libxml_use_internal_errors(true);
        $this->tmpHash = md5(microtime());
        $this->parsedSource = [];
        $this->_parse($pdfFile);
    }

    public function _parse($pdfFile)
    {
        //check is pdf file
        if ($this->_getExtension($pdfFile) != 'pdf') {
            throw new Exception("Cannot parse this file type,please make sure the file type is pdf!");
        }
        //check file is exist
        if (!file_exists($pdfFile)) {
            throw new Exception("File doesn't exist! ($pdfFile)");
        }
        $pathConverted = $this->_safePath($this->_tempDir() . '/' . basename($pdfFile));
        //convert to html
        $this->_convertToHtml($pdfFile, $pathConverted);
        $pathIndexHtml = $this->_safePath($pathConverted . '/index.html');
        //check parsed file 
        if (!file_exists($pathIndexHtml)) {
            throw new Exception("Failed to parse this pdf $pdfFile");
        }
        $maxPage = $this->_maxPage($pathIndexHtml);
        for ($page = 1; $page < $maxPage + 1; $page++) {
            $pathContent = $this->_safePath($pathConverted . '/page' . $page . '.html');
            $content = $this->_readFile($pathContent);
            array_push($this->parsedSource, $content);
        }
        $this->_delDirRecursive($this->_tempDir());
    }
    private function _maxPage($fileIndexConverted)
    {
        $html = new DOMDocument();
        $html->loadHTMLFile($fileIndexConverted);
        return sizeof($html->getElementsByTagName('a'));
    }
    private function _readFile($filePath)
    {
        try {

            $fp = fopen($filePath, "r");

            $content = fread($fp, filesize($filePath));
            fclose($fp);
            return $content;
        } catch (\Throwable $th) {
            return  file_get_contents($filePath);
        }
    }
    private function _safePath($path)
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $path);
    }
    public function _getExtension($filePath)
    {
        return strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    }
    private function _basePath()
    {
        return realpath(dirname(__FILE__));
    }
    private function _platform()
    {
        $os = strtolower(php_uname('s'));
        switch ($os) {
            case 'darwin':
                $os = 'mac';
                break;
            case 'linux':
                $os = 'linux';
                break;
            case 'winnt':
            case 'windows nt':
                $os = 'win';
                break;
        }
        return $os;
    }
    private function _binDir()
    {
        $arc = php_uname('m');
        switch ($arc) {
            case 'x86_64':
                $arc = "bin64";
                break;
            default:
                $arc = "bin32";
                break;
        }
        return $this->_safePath($this->_basePath() . '/bin/' . $this->_platform() . '/' . $arc);
    }
    private function _binPath()
    {
        $ext = $this->_platform() == 'win' ? '.exe' : '';
        return $this->_safePath($this->_bindir() . '/bin' . $ext);
    }
    private function _tempDir()
    {
        $path = $this->_safePath($this->_binDir() . '/tmp/' . $this->tmpHash);
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        return $path;
    }
    private function _delDirRecursive($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    $path = $this->_safePath('$dir/$object');
                    if (is_dir($path) && !is_link($path)) $this->_delDirRecursive($path);
                    else unlink($path);
                }
            }
            rmdir($dir);
        }
    }
    private function _convertToHtml($inputPath, $output)
    {
        $command = $this->_binPath() . ' -q "' . $inputPath . '" "' . $output . '"';
        shell_exec($command);
    }
}
