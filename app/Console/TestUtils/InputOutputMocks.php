<?php

namespace app\Console\Commands\TestUtils;

class InputMock
{
    private $options;

    public function __construct($options)
    {
        $this->options = $options;
    }

    public function getOption($key)
    {
        return (isset($this->options[$key]) ? $this->options[$key] : null);
    }

    public function getArgument($key)
    {
        return $this->getOption($key);
    }
}

class OutputMock
{
    private $options;

    /*
     * @param $totalCount integer
     * @return ProgressBarMock
     */
    public static function createProgressBar($totalCount)
    {
        $m = $totalCount;
        $bar = new ProgressBarMock();

        return $bar;
    }
}

class ProgressBarMock
{
    public function setFormat($format)
    {

    }

    public function setMessage($message)
    {
        fwrite(STDERR, $message . "\n");
        //echo $message."\n";
    }

    public function start()
    {

    }

    public function advance()
    {

    }

    public function finish()
    {

    }

    public function clear()
    {

    }
}

//todo: find proper realworld laravel classes to substitute above mocks, hints are as follows:

//failed command isolated input and output creation- needs more work - using an output mock instead
//$input = new ArgvInput();
//$output = new ConsoleOutput();
//$this->laravel = new Application(getcwd());
//$this->output = $this->laravel->make(
//    OutputStyle::class, ['input' => $input, 'output' => $output]
//);
//$output = new SymfonyStyle($input, $output);
