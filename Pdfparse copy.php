<?php class Pdfparse {
    protected $folderDoc;
    function __construct() {
        libxml_use_internal_errors(true);
    }
    public function parse($pdf) {
        if (strtolower(pathinfo($pdf, PATHINFO_EXTENSION)) != 'pdf') throw new Exception("Cannot parse this file!");
        if (!file_exists($pdf)) throw new Exception("File doesn't exist!");
        $this->folderDoc = $this->bindir(true) . str_replace('.','',basename($pdf, pathinfo($pdf, PATHINFO_EXTENSION)));
        $this->clean();
        $cmd = $this->bindir(true) . $this->binName() . ' -q "' . $pdf . '" "' . $this->folderDoc . '"' . ($this->os() == 'win' ? '' : ' 2>&1');
        print($cmd);
        $output = shell_exec($cmd);
        if (!is_dir($this->folderDoc)) {
            throw new Exception("Failed to parse this file");
        }
        return $this;
    }
    private function clean() {
        if (is_dir($this->folderDoc)) $this->deldir($this->folderDoc);
    }
    private function binName() {
        $ex = "";
        switch (strtolower(php_uname('s'))) {
            case 'winnt':
            case 'windows nt':
                $ex = ".exe";
                break;
            default:
                
                break;
        }
        return 'bin' . $ex;
    }
    public function findText($textFind, $caseSensitive = false) {
        $this->checkDoc();
        if (!is_array($textFind)) {
            $textFind = array($textFind);
        }
        $html = new DOMDocument();
        $Arr = array('page' => array(), 'content' => array());
        for ($page = 1;$page < $this->getMaxPage() + 1;$page++) {
            $html->loadHTMLFile($this->folderDoc . DIRECTORY_SEPARATOR.'page' . ($page) . '.html');
            $pg = $html->getElementById('background');
            if (!array_key_exists('page' . $page, $Arr['page'])) $Arr['page']['page' . $page] = array('width' => $pg->getAttribute('width'), 'height' => $pg->getAttribute('height'));
            foreach ($textFind as $tFind) {
                foreach ($html->getElementsByTagName('div') as $span) {
                    if (stripos($caseSensitive ? $span->textContent : strtolower($span->textContent), $caseSensitive ? $tFind : strtolower($tFind)) !== false) {
                        $data = explode(';', $span->getAttribute('style'));
                        $x = $this->extractNumber($data[1]);
                        $y = $this->extractNumber($data[2]);
                        $t = $this->carikata($span->textContent, $tFind, $caseSensitive);
                        if (!array_key_exists($tFind, $Arr['content'])) $Arr['content'][$tFind] = array();
                        array_push($Arr['content'][$tFind], array('text' => $t, 'x' => $x, 'y' => $y, 'page' => $page));
                    }
                }
            }
        }
        $this->clean();
        return $Arr;
    }
    private function extractNumber($str) {
        $matches=[];
        preg_match_all('!\d+!', $str, $matches);
        return ($matches[0][0]);
    }
    private function carikata($kalimat, $findKata, $caseSensitive = false) {
        //return kata yang dicari dari kalimat
        foreach (explode(' ', $kalimat) as $kata) {
            if (stripos($caseSensitive ? $kata : strtolower($kata), $caseSensitive ? $findKata : strtolower($findKata)) !== false) {
                return $kata;
            }
        }
    }
    private function checkDoc() {
        if (!is_dir($this->folderDoc)) throw new Exception("Failed to find text! file parse not found!");
    }
    private function getMaxPage() {
        $this->checkDoc();
        $html = new DOMDocument();
        $html->loadHTMLFile($this->folderDoc . DIRECTORY_SEPARATOR. 'index.html');
        return sizeof($html->getElementsByTagName('a'));
    }
    private function os(){
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
    private function bindir($fullPath = false) {
        $os = $this->os();
        $arc = php_uname('m');
        switch ($arc) {
            case 'x86_64':
                $arc = "bin64";
            break;
            default:
                $arc = "bin32";
            break;
        }
        return ($fullPath ? $this->thisPath() : '') . 'bin'.DIRECTORY_SEPARATOR. $os . DIRECTORY_SEPARATOR . $arc . DIRECTORY_SEPARATOR;
    }
    private function thisPath() {
        return realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR;
        // return $_SERVER['DOCUMENT_ROOT'] . dirname($_SERVER['PHP_SELF']) . DIRECTORY_SEPARATOR;
    }
    private function deldir($dir) {
        
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . DIRECTORY_SEPARATOR . $object) && !is_link($dir . DIRECTORY_SEPARATOR . $object)) rrmdir($dir . DIRECTORY_SEPARATOR . $object);
                    // echo ($dir. DIRECTORY_SEPARATOR .$object) . "<br>" ;
                    else unlink($dir . DIRECTORY_SEPARATOR . $object);
                    // echo $dir. DIRECTORY_SEPARATOR .$object . "<br>";
                    
                }
            }
            rmdir($dir);
        }
    }
}