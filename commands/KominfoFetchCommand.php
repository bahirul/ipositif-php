<?php
namespace app\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class KominfoFetchCommand extends Command
{
	protected function configure()
	{
		$this
        // the name of the command (the part after "bin/console")
		->setName('kominfo:fetch')

        // the short description shown while running "php bin/console list"
		->setDescription('Fetch Domain List.')

        // the full command description shown when running the command with
        // the "--help" option
		->setHelp('This command allows you fetch whitelist or blacklist domain from kominfo.')

        // configure an argument
        ->addArgument('list', InputArgument::REQUIRED, 'Domain list name, whitelist or blacklist.')
    	;
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
        $list_type = $input->getArgument('list');

        /**
        * Validator and Console
        */
        $validator = new \app\base\DomainValidator();

		/**
		 * Kominfo Vars
		 */
		$data_path	= __DIR__ . '/../data/kominfo/';
		$kominfo_url = 'http://trustpositif.kominfo.go.id/files/index.php';
		$category = ['kajian', 'pengaduan', 'porn'];
		$blacklist = [];
        $whitelist = [];

        //unset porn category, removed from kominfo database 2017
        unset($category[2]);

        /**
         * Http CLient
         */
        $httpc = new \GuzzleHttp\Client();

        if($list_type == 'blacklist'){
            //download kominfo raw file
            for($i=0;$i<sizeof($category);$i++){

                $query = ['download' => 'blacklist%2F' . $category[$i]. '%2Fdomains', 'share' => '11'];

                $response = $httpc->request('GET',$kominfo_url,['query' => $query]);

                if(file_put_contents($data_path . 'blacklist/' . $category[$i] . '.tmp.txt', $response->getBody())){
                    $output->writeln("success generate file blacklist " . $category[$i] . ".");
                }else{
                    if ($response->getBody() == "") {
                        $output->writeln("success generate file blacklist " . $category[$i] . ", but no content or domain list.");
                    } else {
                        $output->writeln("error generate file blacklist " . $category[$i] . ".");
                    }
                }

            }

            //validate kominfo raw file
            for($i=0;$i<sizeof($category);$i++){

                $output->writeln("reading blacklist " . $category[$i] . " file.");

                $blacklist_file = file($data_path . 'blacklist/' . $category[$i] . '.tmp.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

                for ($j=0; $j < sizeof($blacklist_file); $j++) {
                    //replace -. caracter with .
                    $domain = str_replace('-.','.', $blacklist_file[$j]);

                    //validate domain
                    if($validator->validate($domain)){
                        //disable valid domain output
                        //$output->writeln($domain . ' is valid.');
                        $blacklist[$category[$i]][] = $domain;
                    }
                }

                $output->writeln("generating file blacklist " . $category[$i]);

                $merge_domain = implode(PHP_EOL, $blacklist[$category[$i]]);

                if (file_put_contents($data_path . 'blacklist/' . $category[$i] . '.domain', $merge_domain)) {
                    $output->writeln("success validate file blacklist " . $category[$i] . ".");
                } else {
                    if ($merge_domain != '') {
                        $output->writeln("error validate file blacklist " . $category[$i]. ".");
                    } else {
                        $output->writeln("file blacklist " . $category[$i] . " validate, but no content or domain list generated.");
                    }
                }

                //remove tmp file
                unlink($data_path . 'blacklist/' . $category[$i] . '.tmp.txt');
            }

            //merge blacklist file
            $all_blaclist_domains = '';

            for($i=0;$i<sizeof($category);$i++){

                $filename = $data_path . 'blacklist/' . $category[$i] . '.domain';

                if(file_exists($filename)){
                    $all_blaclist_domains .= file_get_contents($filename);
                }

                unlink($filename);
            }

            //write to one file
            if(file_put_contents($data_path . 'blacklist/blacklist.domain', $all_blaclist_domains)){
                $output->writeln("success validate file blacklist.");
            }else{
                if ($all_blaclist_domains != '') {
                    $output->writeln("error validate file blacklist.");
                } else {
                    $output->writeln("file blacklist validate, but no content or domain list generated.");
                }
            }
        }


        if($list_type == 'whitelist'){
            //download kominfo raw file
            $query = ['download' => 'whitelist%2Fwhitelist%2Fdomains', 'share' => '11'];

            $response = $httpc->request('GET',$kominfo_url,['query' => $query]);

            if(file_put_contents($data_path . 'whitelist/domain.tmp.txt', $response->getBody())){
                $output->writeln("success generate file whitelist.");
            }else{
                if ($response->getBody() == "") {
                    $output->writeln("success generate file whitelist, but no content or domain list.");
                } else {
                    $output->writeln("error generate file whitelist.");
                }
            }

            //validate kominfo raw file
            $output->writeln("reading whitelist file.");

            $whitelist_file = file($data_path . 'whitelist/domain.tmp.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            for ($j=0; $j < sizeof($whitelist_file); $j++) {
                //replace -. caracter with .
                $domain = str_replace('-.','.', $whitelist_file[$j]);

                //validate domain
                if($validator->validate($domain)){
                    //disable valid domain output
                    //$output->writeln($domain . ' is valid.');
                    $whitelist[] = $domain;
                }
            }

            $output->writeln("generating file whitelist.");

            $merge_domain = implode(PHP_EOL, $whitelist);

            if (file_put_contents($data_path . 'whitelist/whitelist.domain', $merge_domain)) {
                $output->writeln("success validate file whitelist.");
            } else {
                if ($merge_domain != '') {
                    $output->writeln("error validate file whitelist.");
                } else {
                    $output->writeln("file whitelist validate, but no content or domain list generated.");
                }
            }

            //remove tmp file
            unlink($data_path . 'whitelist/domain.tmp.txt');
        }
    	
	}
}