<?php

namespace Ibw\JobeetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Ibw\JobeetBundle\Entity\Category;

class CategoryController extends Controller
{
	public function showAction($slug, $page)
	{
		$em = $this->getDoctrine()->getManager();
		$category = $em->getRepository('IbwJobeetBundle:Category')->findOneBySlug($slug);
		if(!$category)
		{
			throw $this->createNotFoundException('Unable to find Category entity.');
		}
		$total_jobs = $em->getRepository('IbwJobeetBundle:Job')->countActiveJobs($category->getId());
		$jobs_pre_page = $this->container->getParameter('max_jobs_on_category');
		$last_page = ceil($total_jobs / $jobs_pre_page);
		$previous_page = $page > 1 ? $page -1 : 1;
		$next_page = $page < $last_page ? $page + 1 : $last_page;
		$category->setActiveJobs($em->getRepository('IbwJobeetBundle:Job')->getActiveJobs($category->getId(), $jobs_pre_page, ($page-1) * $jobs_pre_page));

		return $this->render('IbwJobeetBundle:Category:show.html.twig', array(
			'category'=>$category,
			'last_page' => $last_page,
	        'previous_page' => $previous_page,
	        'current_page' => $page,
	        'next_page' => $next_page,
	        'total_jobs' => $total_jobs
		));
	}
}