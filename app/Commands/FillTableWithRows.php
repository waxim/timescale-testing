<?php 

namespace App\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\Table;
use Illuminate\Database\Capsule\Manager as Capsule;
use StdClass;
use DateTime;

class FillTableWithRows extends Command
{
    /**
     * Set a constant for a million
     * 
     * @var int
     */
    const ONE_MILLION = 1000000;

    /**
     * What our command called?
     * 
     * @var string
     */
    protected static $defaultName = 'fill';

    protected function configure()
    {
        $this->addOption(
            'table',
            null,
            InputOption::VALUE_REQUIRED,
            'What table would you like us to fill (Must be a Hypertable)?',
            'condtitions'
        );

        $this->addOption(
            'rows',
            null,
            InputOption::VALUE_REQUIRED,
            'How many enteries shall we generate?',
            self::ONE_MILLION
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $start_time = $start = microtime(true);
        $start_mem = memory_get_usage();

        $table_name = $input->getOption('table');
        $rows = $input->getOption('rows');

        // Run our plain loop first to get
        // delta, then run again with actual
        // inserts, to give us "write time"
        $dry_run_start = microtime(true);
        for ($i = 1; $i < $rows; $i++) {
            $row = $this->randomRow();
        }
        $dry_run_end = microtime(true);

        // Actuall write this time
        $start_write_time = microtime(true);
        for ($i = 1; $i < $rows; $i++) {
            $row = $this->randomRow();
            Capsule::table($table_name)
                ->insert($row);
        }
        $finish_write_time = microtime(true);

        $output->writeln("\nStats");
        $section = $output->section();
        $table = new Table($section);
        $time_taken = (microtime(true) - $start_time);

        $mem_used = (memory_get_usage() - $start_mem);
        $mem_peak = memory_get_peak_usage();

        $table->setHeaders(["Stat", "Value"])
            ->addRows([
                ["Table", $table_name],
                ["Records", $rows],
                ["Time Per Row (ms)", (($finish_write_time - $start_write_time) / $rows)],
                ["Time Taken (ms)", $time_taken],
                ["Dry Run Taken (ms)", ($dry_run_end - $dry_run_start)],
                ["Write Taken (ms)", ($finish_write_time - $start_write_time)],
                ["Delta (ms)", (
                    ($finish_write_time - $start_write_time) -
                    ($dry_run_end - $dry_run_start)
                )],
            ]);
        $table->render();
    }

    /**
     * Function to make a row for our table
     * 
     * @return array
     */
    private function randomRow()
    {
        return [
            'time'        => (new DateTime)->format(DateTime::ATOM),
            'location'    => 'office',
            'temperature' => rand(-100, 100),
            'humidity'    => rand(0, 100)
        ];
    }
}