<?php

namespace Core;

/*
 * This is core class for phpcs.
 */

class PhpCs
{

    private $_mainRootDir = null;
    private $_phpcsPath = "/opt/lampp/bin/phpcs ";

    public function get_mainRootDir()
    {
        return $this->_mainRootDir;
    }

    public function set_mainRootDir($_mainRootDir)
    {
        $this->_mainRootDir = $_mainRootDir;
    }

    public function __construct()
    {
        
    }

    public function scanDirectory($dirPath = null)
    {
        $output = array();
        $dirPath = (is_null($dirPath)) ? $this->get_mainRootDir() : $dirPath;
        if (is_file($dirPath)) {
            $output['report'] = $this->executePHPCSCommand('--standard=Zend ' . $dirPath);
        } else {
            $scan = array_values(array_diff(scandir($dirPath), array('.')));
            $output['path'][] = $dirPath;
            foreach ($scan as $dir) {
                $realPath = $dirPath . '/' . $dir;
                $extesion = $this->getFileExtension($realPath);
                $isPhp = false;
                $isDir = is_dir($dir);
                if (!is_null($extesion)) {
                    $isPhp = ($extesion == 'php') ? true : false;
                } else {
                    $isDir = true;
                }
                $output['scans'][] = array(
                    'name' => $dir,
                    'realPath' => $realPath,
                    'isDir' => $isDir,
                    'isPhp' => $isPhp,
                );
            }
        }
        return $output;
    }

    public function getReport($scan = array(), $isRecursive = false)
    {
        return $this->goRecursive($scan['scans'], $scan['path']);
    }

    public function getFileReport($filePath)
    {
        $output = array();
        $output['file_path'] = $filePath;
        $output['report'] = $this->executePHPCSCommand('--standard=Zend ' . $filePath);
        return $output;
    }

    private function goRecursive($scans, $basePath)
    {
        foreach ($scans as $key => $files) {

            if (!$files['isDir'] && $files['isPhp']) {
                $scans[$key]['report'] = $this->executePHPCSCommand('--standard=Zend ' . $files['realPath']);
            } else if ($files['isDir']) {
                $scanDir = $this->scanDirectory($files['realPath']);
                $scans[$key]['child'] = $this->goRecursive($scanDir['scans'], $scanDir['path']);
            }
        }
        return $scans;
    }

    public function executePHPCSCommand($commandString = '')
    {
        $command = $this->_phpcsPath . ' ' . $commandString;
        $shell_exec = $this->executeShellCommand($command);
        return $shell_exec;
    }

    public function executeShellCommand($command)
    {
        return shell_exec($command);
    }

    public function getFileExtension($filePath)
    {
        $pathinfo = pathinfo($filePath);
        $extension = (isset($pathinfo['extension'])) ? $pathinfo['extension'] : null;
        return $extension;
    }

}
