<?php

namespace Renovate\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Estimation
 *
 * @ORM\Table(name="estimations")
 * @ORM\Entity
 */
class Estimation
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="customer", type="string", length=255)
     */
    private $customer;

    /**
     * @var string
     *
     * @ORM\Column(name="performer", type="string", length=255)
     */
    private $performer;

    /**
     * @var float
     *
     * @ORM\Column(name="materials_amount", type="float")
     */
    private $materialsAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="works_amount", type="float")
     */
    private $worksAmount;

    /**
     * @var float
     *
     * @ORM\Column(name="total_amount", type="float")
     */
    private $totalAmount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime")
     */
    private $updated;

    /**
     * @ORM\OneToMany(targetEntity="EstimationCost", mappedBy="estimation")
     * @var array
     */
    private $estimationCosts;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set customer
     *
     * @param string $customer
     * @return Estimation
     */
    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return string 
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Set performer
     *
     * @param string $performer
     * @return Estimation
     */
    public function setPerformer($performer)
    {
        $this->performer = $performer;

        return $this;
    }

    /**
     * Get performer
     *
     * @return string 
     */
    public function getPerformer()
    {
        return $this->performer;
    }

    /**
     * Set materialsAmount
     *
     * @param float $materialsAmount
     * @return Estimation
     */
    public function setMaterialsAmount($materialsAmount)
    {
        $this->materialsAmount = $materialsAmount;

        return $this;
    }

    /**
     * Get materialsAmount
     *
     * @return float 
     */
    public function getMaterialsAmount()
    {
        return $this->materialsAmount;
    }

    /**
     * Set worksAmount
     *
     * @param float $worksAmount
     * @return Estimation
     */
    public function setWorksAmount($worksAmount)
    {
        $this->worksAmount = $worksAmount;

        return $this;
    }

    /**
     * Get worksAmount
     *
     * @return float 
     */
    public function getWorksAmount()
    {
        return $this->worksAmount;
    }

    /**
     * Set totalAmount
     *
     * @param float $totalAmount
     * @return Estimation
     */
    public function setTotalAmount($totalAmount)
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    /**
     * Get totalAmount
     *
     * @return float 
     */
    public function getTotalAmount()
    {
        return $this->totalAmount;
    }

    public function calculateAmount()
    {
    	$worksAmount = 0;
    	$materialsAmount = 0;
    	
    	foreach ($this->getEstimationCosts() as $estimationCost){
    		switch ($estimationCost->getCost()->getCategory()->getType()){
    			case "works": $worksAmount = $worksAmount + $estimationCost->getTotal(); break;
    			case "materials": $materialsAmount = $materialsAmount + $estimationCost->getTotal(); break;
    		}
    	}
    	
    	$this->setWorksAmount($worksAmount);
    	$this->setMaterialsAmount($materialsAmount);
    	$this->setTotalAmount($worksAmount+$materialsAmount);
    	$this->setUpdated(new \DateTime());
    }
    
    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return Estimation
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }
    
    /**
     * Add estimationCosts
     *
     * @param \Renovate\MainBundle\Entity\EstimationCost $estimationCosts
     * @return Estimation
     */
    public function addEstimationCost(\Renovate\MainBundle\Entity\EstimationCost $estimationCosts)
    {
    	$this->estimationCosts[] = $estimationCosts;
    
    	return $this;
    }
    
    /**
     * Remove estimationCosts
     *
     * @param \Renovate\MainBundle\Entity\EstimationCost $estimationCosts
     */
    public function removeEstimationCost(\Renovate\MainBundle\Entity\EstimationCost $estimationCosts)
    {
    	$this->estimationCosts->removeElement($estimationCosts);
    }
    
    /**
     * Get estimationCosts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEstimationCosts()
    {
    	return $this->estimationCosts;
    }
    
    public function getCostCategories()
    {
    	$categories = array();
    	foreach ($this->estimationCosts as $estimationCost){
    		if (!in_array($estimationCost->getCost()->getCategory()->getType(),array_map(function($category){ return $category['name'];},$categories))){
    			$categories[] = array('name' => $estimationCost->getCost()->getCategory()->getType(), 'items' => array());
    		}	
    		
    		foreach ($categories as $key=>$category){
    			if ($category['name'] === $estimationCost->getCost()->getCategory()->getType()){
    				array_push($categories[$key]['items'], $estimationCost);
    			}
    		}
    	}
    	
    	return $categories;
    }
    
    /**
     * Constructor
     */
    public function __construct()
    {
    	$this->estimationCosts = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function getInArray()
    {
    	return array(
    			'id' => $this->getId(),
    			'customer' => $this->getCustomer(),
    			'performer' => $this->getPerformer(),
    			'materialsAmount' => $this->getMaterialsAmount(),
    			'worksAmount' => $this->getWorksAmount(),
    			'totalAmount' => $this->getTotalAmount(),
    			'updated' => $this->getUpdated()->getTimestamp()*1000,
    			'estimationCosts' => ($this->getEstimationCosts() == null ) ? array() : array_map(function($estimationCost){return $estimationCost->getInArray();}, $this->getEstimationCosts()->toArray())
    	);
    }
    
    public static function getEstimations($em, $parameters, $inArray = false)
    {
    	$qb = $em->getRepository("RenovateMainBundle:Estimation")
    	->createQueryBuilder('e');
    
    	$qb->select('e')
    	->addOrderBy('e.updated');
    	 
    	if (isset($parameters['offset']) && isset($parameters['limit']))
    	{
    		$qb->setFirstResult($parameters['offset'])
    		->setMaxResults($parameters['limit']);
    	}
    	
    	if (isset($parameters['from']))
    	{
    		$qb->andWhere('e.updated > :from')
    		->setParameter('from', $parameters['from']);
    	}
    	 
    	if (isset($parameters['to']))
    	{
    		$qb->andWhere('e.updated < :to')
    		->setParameter('to', $parameters['to']);
    	}
    	 
    	if (isset($parameters['search']))
    	{
    		$qb->andWhere($qb->expr()->orX(
    				$qb->expr()->like('e.id', $qb->expr()->literal('%'.$parameters['search'].'%')),
    				$qb->expr()->like('e.customer', $qb->expr()->literal('%'.$parameters['search'].'%')),
    				$qb->expr()->like('e.performer', $qb->expr()->literal('%'.$parameters['search'].'%'))
    		));
    	}
    	 
    	$result = $qb->getQuery()->getResult();
    
    	if ($inArray)
    	{
    		return array_map(function($estimation){
    			return $estimation->getInArray();
    		}, $result);
    	}else return $result;
    }
    
    public static function getEstimationsCount($em, $parameters)
    {
    	$qb = $em->getRepository("RenovateMainBundle:Estimation")
    	->createQueryBuilder('e');
    	 
    	$qb->select('COUNT(e.id)');
    	 
    	if (isset($parameters['from']))
    	{
    		$qb->andWhere('e.updated > :from')
    		->setParameter('from', $parameters['from']);
    	}
    	 
    	if (isset($parameters['to']))
    	{
    		$qb->andWhere('e.updated < :to')
    		->setParameter('to', $parameters['to']);
    	}
    	 
    	if (isset($parameters['search']))
    	{
    		$qb->andWhere($qb->expr()->orX(
    				$qb->expr()->like('e.id', $qb->expr()->literal('%'.$parameters['search'].'%')),
    				$qb->expr()->like('e.customer', $qb->expr()->literal('%'.$parameters['search'].'%')),
    				$qb->expr()->like('e.performer', $qb->expr()->literal('%'.$parameters['search'].'%'))
    		));
    	}
    	 
    	$total = $qb->getQuery()->getSingleScalarResult();
    
    	return $total;
    }
    
    public static function saveEstimation($em, $parameters)
    {
    	if (isset($parameters->id)){
    		$estimation = $em->getRepository("RenovateMainBundle:Estimation")->find($parameters->id);
    	}else{
    		$estimation = new Estimation();
    		$estimation->setMaterialsAmount(0);
    		$estimation->setWorksAmount(0);
    		$estimation->setTotalAmount(0);
    	}
    	
    	
    	$estimation->setCustomer($parameters->customer);
    	$estimation->setPerformer($parameters->performer);
    	
    	$estimation->setUpdated(new \DateTime());
    	
    	$em->persist($estimation);
    	$em->flush();
    	
    	return $estimation;
    }
    
    public static function removeEstimationById($em, $id)
    {
    	$estimation = $em->getRepository("RenovateMainBundle:Estimation")->find($id);
    	
    	foreach($estimation->getEstimationCosts() as $estimationCost){
    		$em->remove($estimationCost);
    		$em->flush();
    	}
    	
    	$em->remove($estimation);
    	$em->flush();
    	
    	return true;
    }
}