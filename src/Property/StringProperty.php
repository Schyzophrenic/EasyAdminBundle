<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Property;

use EasyCorp\Bundle\EasyAdminBundle\Configuration\Property\PropertyConfigTrait;
use EasyCorp\Bundle\EasyAdminBundle\Context\ApplicationContext;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Property\PropertyConfigInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\PropertyDto;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StringProperty implements PropertyConfigInterface
{
    use PropertyConfigTrait;

    private $maxLength = -1;

    public function __construct()
    {
        $this->type = 'string';
        $this->formType = TextType::class;
        $this->templateName = 'property/string';
    }

    public function setMaxLength(int $length): self
    {
        if ($length < 1) {
            throw new \InvalidArgumentException(sprintf('The argument of the "%s()" method must be 1 or higher (%d given).', __METHOD__, $length));
        }

        $this->maxLength = $length;

        return $this;
    }

    public function build(PropertyDto $propertyDto, EntityDto $entityDto, ApplicationContext $applicationContext): PropertyDto
    {
        if (-1 === $this->maxLength) {
            $this->maxLength = 'detail' === $applicationContext->getCrud()->getAction() ? PHP_INT_MAX : 64;
        }

        $formattedValue = mb_substr($propertyDto->getValue(), 0, $this->maxLength);
        if ($formattedValue !== $propertyDto->getValue()) {
            $formattedValue .= '…';
        }

        return $propertyDto->with([
            'customOptions' => [
                'max_length' => $this->maxLength,
            ],
            'formattedValue' => $formattedValue,
        ]);
    }
}