<?php
namespace Ibw\JobeetBundle\Entity;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;
use Ibw\JobeetBundle\Utils\Jobeet as Jobeet;

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
		$this->application->add($command);
		$input = new ArrayInput(array(
			'command' => 'doctrine:database:drop',
			'--force' => true
		));
		$command->run($input, new NullOutput());
		
		$connection = $this->application->getKernel()->getContainer()->get('doctrine')->getConnection();
		
		if($connection->isConnected()) {
			$connection->close();
		}
		
		$command = new CreateDatabaseDoctrineCommand();
		$this->application->add($command);
		$input = new ArrayInput(array(
				'command' => 'doctrine:database:create'
		));
		$command->run($input, new NullOutput());
		
		$command = new CreateSchemaDoctrineCommand();
		$this->application->add($command);
		$input = new ArrayInput(array(
				'command' => 'doctrine:schema:create'
		));
		$command->run($input, new NullOutput());
		
		$this->em = static::$kernel->getContainer()
			 ->get('doctrine')
			 ->getManager();
		$client = static::createClient();
		$loader =  new \Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader($client->getContainer());
		$loader->loadFromDirectory(static::$kernel->locateResource('@IbwJobeetBundle/DataFixtures/ORM'));
		$purger = new \Doctrine\Common\DataFixtures\Purger\ORMPurger($this->em);
		$executor =  new \Doctrine\Common\DataFixtures\Executor\ORMExecutor($this->em, $purger);
		$executor->execute($loader->getFixtures());
	}
	
	public function testGetCompanySlug()
	{
		$job = $this->em->createQuery('SELECT j FROM IbwJobeetBundle:Job j ')
            ->setMaxResults(1)
            ->getSingleResult();
 
        $this->assertEquals($job->getCompanySlug(), Jobeet::slugify($job->getCompany()));
	}
}