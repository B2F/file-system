<?php

class FileSystemController {
  
    private $basepath = '.';

    public function __construct($basepath = '.') {
        if ($basepath != '') {
            $this->basepath = $this->removeEndingSlash($basepath);
        }
        $this->createDir('');
    }

    public function createDir($dirname, $useBasePath = TRUE)
    {
        $pathTokens = explode('/', $dirname);
        $useBasePath ? $scannedPath = $this->basepath . '/' : $scannedPath = '';
        foreach ($pathTokens as $key => $pathComponent) {
            $scannedPath .= $pathComponent . '/';
            if (!file_exists($scannedPath)) {
                mkdir($scannedPath);
            }
        }
    }

    public function createDirectoryStructure($directoryStructure)
    {
        foreach ($directoryStructure as $dirInfo) {
            $this->createDir($dirInfo['Folder']);           
            $subfolders = explode(';', $dirInfo['Subfolders']);
            foreach ($subfolders as $key => $subfolder) {
                $this->createDir($dirInfo['Folder'] . '/' . $subfolder);
            }
        }
    }

    public function getDirectoryContent($dirPath = FALSE, $useBasePath = TRUE) {
        $scanedDir = '';
        if ($useBasePath) $scanedDir .= $this->basepath;
        if ($useBasePath && $dirPath) $scanedDir .= '/';
        if ($dirPath) $scanedDir .= $dirPath;
        $content = scandir($scanedDir);
        return $content;
    }

    public function setBasepath($basepath) {
        $this->basepath = $this->removeEndingSlash($basepath);
    }

    public function getBasepath() {
        return $this->basepath;
    }

    public function removeEndingSlash($dirpath) {
        if ($dirpath != '' && substr($dirpath, strlen($dirpath) - 1, 1) == '/') {
            $dirpath = substr($dirpath, 0, strlen($dirpath) - 1);
        }
        return $dirpath;
    }

    public function deleteDirectoryRecursively($dir) {
       system('rm -rf ' . escapeshellarg($dir), $retval);
       return $retval == 0;
    }


    public function createFile($filepath, $content = '', $append = FALSE) {
       $filepath = $this->basepath . '/' . $filepath;
       if ($append) {
         file_put_contents($filepath, $content, FILE_APPEND);
       }
       else {
         file_put_contents($filepath, $content);
       }
    }

    public function fileContains($filepath, $search) {
      $filepath = $this->basepath . '/' . $filepath;
      $content = file_get_contents($filepath);
      if ($content !== FALSE) {
        $pattern = '/' . $search . '/';
        return preg_match($pattern, $content);
      }
      return $content;
    }

}
