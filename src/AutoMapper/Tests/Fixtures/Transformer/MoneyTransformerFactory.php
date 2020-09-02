<?php

namespace Jane\AutoMapper\Tests\Fixtures\Transformer;

use Jane\AutoMapper\MapperMetadataInterface;
use Jane\AutoMapper\Transformer\AbstractUniqueTypeTransformerFactory;
use Jane\AutoMapper\Transformer\TransformerInterface;
use Money\Money;
use Symfony\Component\PropertyInfo\Type;

/**
 * @author Baptiste Leduc <baptiste.leduc@gmail.com>
 */
final class MoneyTransformerFactory extends AbstractUniqueTypeTransformerFactory
{
    /**
     * {@inheritdoc}
     */
    protected function createTransformer(Type $sourceType, Type $targetType, MapperMetadataInterface $mapperMetadata): ?TransformerInterface
    {
        $isSourceMoney = $this->isMoneyType($sourceType);
        $isTargetMoney = $this->isMoneyType($targetType);

        if ($isSourceMoney && !$isTargetMoney) {
            return $this->createTransformerForSource($targetType);
        }

        if ($isTargetMoney && !$isSourceMoney) {
            return $this->createTransformerForTarget($sourceType);
        }

        return null;
    }

    protected function createTransformerForSource(Type $targetType): ?TransformerInterface
    {
        if (Type::BUILTIN_TYPE_ARRAY === $targetType->getBuiltinType()) {
            return new MoneyToArrayTransformer();
        }

        return null;
    }

    protected function createTransformerForTarget(Type $sourceType): ?TransformerInterface
    {
        if (Type::BUILTIN_TYPE_ARRAY === $sourceType->getBuiltinType()) {
            return new ArrayToMoneyTransformer();
        }

        return null;
    }

    private function isMoneyType(Type $type): bool
    {
        if (Type::BUILTIN_TYPE_OBJECT !== $type->getBuiltinType()) {
            return false;
        }

        if (Money::class !== $type->getClassName() && !is_subclass_of($type->getClassName(), Money::class)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority(): int
    {
        return 3;
    }
}
