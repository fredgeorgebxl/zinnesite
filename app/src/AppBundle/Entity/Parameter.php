<?php

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;
use OpenSky\Bundle\RuntimeConfigBundle\Entity\Parameter as BaseParameter;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Yaml\Inline;
use Symfony\Component\Yaml\ParserException;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ParameterRepository")
 * @ORM\Table(
 *     name="zsf_parameters",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="name_unique", columns={"name"})
 *     }
 * )
 */
class Parameter extends BaseParameter{
   /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="type", type="string", length=64)
     */
    protected $type;

    public function getId()
    {
        return $this->id;
    }
    
    public function setType($type)
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }
    
    /**
     * @Assert\Callback
     */
    public function validateValueAsYaml(ExecutionContext $context)
    {
        try {
            Inline::parse($this->value);
        } catch (ParserException $e) {
            $context->setPropertyPath($context->getPropertyPath() . '.value');
            $context->addViolation('This value is not valid YAML syntax', array(), $this->value);
        }
    }
}
