<?php

namespace Renovate\MainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Price
 *
 * @ORM\Table(name="prices")
 * @ORM\Entity
 */
class Price
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
     * @var integer
     *
     * @ORM\Column(name="userid", type="integer")
     */
    private $userid;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="units", type="string", length=255)
     */
    private $units;
    
    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;

    /**
     * @var datetime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;
    
    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="jobs")
     * @ORM\JoinColumn(name="userid")
     * @var User
     */
    private $user;
    
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
     * Set userid
     *
     * @param integer $userid
     * @return Price
     */
    public function setUserid($userid)
    {
    	$this->userid = $userid;
    
    	return $this;
    }
    
    /**
     * Get userid
     *
     * @return integer
     */
    public function getUserid()
    {
    	return $this->userid;
    }
    
    /**
     * Set name
     *
     * @param string $name
     * @return Price
     */
    public function setName($name)
    {
    	$this->name = $name;
    
    	return $this;
    }
    
    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
    	return $this->name;
    }
    
    /**
     * Set units
     *
     * @param string $units
     * @return Price
     */
    public function setUnits($units)
    {
    	$this->units = $units;
    
    	return $this;
    }
    
    /**
     * Get units
     *
     * @return string
     */
    public function getUnits()
    {
    	return $this->units;
    }
    
    /**
     * Set value
     *
     * @param float $value
     * @return Price
     */
    public function setValue($value)
    {
    	$this->value = $value;
    
    	return $this;
    }
    
    /**
     * Get value
     *
     * @return float
     */
    public function getValue()
    {
    	return $this->value;
    }
    
    /**
     * Set created
     *
     * @param \DateTime $created
     * @return Price
     */
    public function setCreated($created)
    {
    	$this->created = $created;
    
    	return $this;
    }
    
    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
    	return $this->created;
    }
    
    /**
     * Set user
     *
     * @param \Renovate\MainBundle\Entity\User $user
     * @return Price
     */
    public function setUser(\Renovate\MainBundle\Entity\User $user = null)
    {
    	$this->user = $user;
    
    	return $this;
    }
    
    /**
     * Get user
     *
     * @return \Renovate\MainBundle\Entity\User
     */
    public function getUser()
    {
    	return $this->user;
    }
    
    public function getInArray()
    {
    	return array(
    			'id' => $this->getId(),
    			'userid' => $this->getUserid(),
    			'name' => $this->getName(),
    			'units' => $this->getUnits(),
    			'value' => $this->getValue(),
    			'created' => $this->getCreated()->getTimestamp()*1000,
    			'user' => $this->getUser()->getInArray()
    	);
    }
    
    public static function getAllPrices($em, $inArray = false)
    {
    	$qb = $em->getRepository("RenovateMainBundle:Price")
    			 ->createQueryBuilder('p');
    	
    	$qb->select('p')
    	   ->orderBy('p.created', 'DESC');
    	
    	$result = $qb->getQuery()->getResult();
    	
    	if ($inArray)
    	{
    		return array_map(function($price){
    			return $price->getInArray();
    		}, $result);
    	}else return $result;
    }
    
    public static function getPrices($em, $parameters, $inArray = false)
    {
    	$qb = $em->getRepository("RenovateMainBundle:Price")
    	->createQueryBuilder('p');
    	 
    	$qb->select('p')
    	   ->orderBy('p.created', 'DESC');
    	
    	if (isset($parameters['offset']) && isset($parameters['limit']))
    	{
    		$qb->setFirstResult($parameters['offset'])
    		   ->setMaxResults($parameters['limit']);
    	}
    	   
    	$result = $qb->getQuery()->getResult();
    	 
    	if ($inArray)
    	{
    		return array_map(function($price){
    			return $price->getInArray();
    		}, $result);
    	}else return $result;
    }
    
    public static function getPricesCount($em)
    {
    	$query = $em->getRepository("RenovateMainBundle:Price")
    				->createQueryBuilder('p')
    				->select('COUNT(p.id)') 
    				->getQuery();
    	
    	$total = $query->getSingleScalarResult();
    	
    	return $total;
    }
    
    public static function addPrice($em, \Renovate\MainBundle\Entity\User $user, $parameters)
    {
    	$price = new Price();
    	$price->setName($parameters->name);
    	$price->setUnits($parameters->units);
    	$price->setValue($parameters->value);
    	$price->setUserid($user->getId());
    	$price->setUser($user);
    	$price->setCreated(new \DateTime());
    	
    	$em->persist($price);
    	$em->flush();
    	
    	return $price;
    }
    
    public static function removePriceById($em, $id)
    {
    	$qb = $em->createQueryBuilder();
    	 
    	$qb->delete('RenovateMainBundle:Price', 'p')
    	->where('p.id = :id')
    	->setParameter('id', $id);
    	 
    	return $qb->getQuery()->getResult();
    }
    
    public static function editPriceById($em, $id, $parameters)
    {
    	$price = $em->getRepository("RenovateMainBundle:Price")->find($id);
    	
    	$price->setName($parameters->name);
    	$price->setUnits($parameters->units);
    	$price->setValue($parameters->value);
    	
    	$em->persist($price);
    	$em->flush();
    	
    	return $price;
    }
}