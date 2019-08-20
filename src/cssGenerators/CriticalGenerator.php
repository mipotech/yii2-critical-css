<?php

namespace mipotech\criticalcss\cssGenerators;

use yii\base\Component;

use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\Process;
use mipotech\criticalcss\storage\StorageInterface;
use mipotech\criticalcss\htmlFetchers\HtmlFetcherInterface;
/**
 * Generates critical-path CSS using the Critical npm package.
 *
 * @see https://github.com/addyosmani/critical
 */
class CriticalGenerator extends Component
{
    /** @var mipotech\criticalcss\htmlFetchers\HtmlFetcherInterface */
    public $htmlFetcher;
    
    /** @var mipotech\criticalcss\storage\StorageInterface */
    public $storage;
    
    /** @var array $routes */
    public $routes; 
    
    /** @var string */
    protected $criticalBin = 'critical';
    
    /** @var int */
    public $width;
    
    /** @var int */
    public $height;
    
    /** @var array */
    protected $ignore;
    
    /** @var int|null */
    protected $timeout;
    
    /**
     * Set the path to the Critical bin (executable.)
     *
     * @param  string $critical
     *
     * @return void
     */
    public function setCriticalBin($critical)
    {
        $this->criticalBin = $critical;
    }
    /**
     * Set optional options for Critical.
     *
     * @param  int      $width
     * @param  int      $height
     * @param  array    $ignore
     * @param  int|null $timeout
     *
     * @return void
     */
    public function setOptions($width = 375, $height = 812, array $ignore = [], $timeout = null)
    {
        $this->width  = $width;
        $this->height = $height;
        $this->ignore = $ignore;
        $this->timeout = $timeout;
    }
    
    /**
     * Generate critical-path CSS for a given URI.
     *
     * @param  string $uri  The given URI to generate critical-path CSS for.
     *
     * @return bool         Indicating successful write to the StorageInterface.
     *
     * @throws \Alfheim\CriticalCss\CssGenerators\CssGeneratorException
     */
    public function generate($uri, $alias = null)
    {
        $html = $this->htmlFetcher->fetch($uri);
        $builder = new ProcessBuilder;
        //$commandLine = $this->criticalBin . ' ' . $uri . ' -w '.$this->width . ' -h '.$this->height . ' --userAgent \'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1\' > ' . $this->storage->storage;
        //$process = new Process($commandLine, null, null, $html, $this->timeout, []);
        // to preserve the BC with symfony <3.3, we convert the array structure
        // to a string structure to avoid the prefixing with the exec command
        
        $builder->setPrefix($this->criticalBin);
        $builder->setArguments([
            '--base='.getcwd().'/public_html/',
            '--width='.$this->width,
            '--height='.$this->height,
            '--minify',
            '--userAgent=\'Mozilla/5.0 (iPhone; CPU iPhone OS 11_0 like Mac OS X) AppleWebKit/604.1.38 (KHTML, like Gecko) Version/11.0 Mobile/15A372 Safari/604.1\'',
        ]);
        
        $builder->setInput($html);
        $process = $builder->getProcess();
        //$process = $process->setCommandLine($process->getCommandLine());
        $process->run();
        
        if (!$process->isSuccessful()) {
            print_r($process->getErrorOutput());die;
            throw new CssGeneratorException(
                sprintf('Error processing URI [%s]. This is probably caused by '.
                        'the Critical npm package. Checklist: 1) `critical_bin`'.
                        ' is correct, 2) `css` paths are correct 3) run `npm '.
                        'install` again.', $uri)
            );
        }
        return $this->storage->writeCss(
            is_null($alias) ? $uri : $alias,
            $process->getOutput()
        );
    }
}