<?php
namespace mf\utils;
    class ClassLoader extends AbstractClassLoader{
        
        public function loadClass(string $classname){
            
            $filename = $this->getFilename($classname);
            $path =$this->makePath($filename);
            if(file_exists($path)){
                require_once $path;
            }
        }

        protected function makePath(string $filename): string{
            $filename = $this->prefix.DIRECTORY_SEPARATOR.$filename;
            return $filename;
        }

        protected function getFileName(string $classname): string{
            $path = str_replace("\\", DIRECTORY_SEPARATOR, $classname);
            return $path.".php";

        }
    }