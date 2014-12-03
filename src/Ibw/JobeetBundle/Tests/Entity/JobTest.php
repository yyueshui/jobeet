<?php
namespace Ibw\JobeetBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

class Jobtest extends WebTestCase
{
	private $em;
	private $application;
	
	public function setUp()
	{
		static::$kernel = static::createKernel();
		static::$kernel->boot();
		$this->application = new Application(static::$kernel);
		
		$command = new DropDatabaseDoctrineCommand();
		$input = new ArrayInput(array(
			'command' => 'doctrine:database:drop',
			'--force' => true
		));
		$command->run($input, new NullOutput());
		
		$connection = $this->application->getKernel()->get('doctrine')->getConnection();
		
		if($connection->isConnected()) {
			$connection->close();
		}
		
	}
}