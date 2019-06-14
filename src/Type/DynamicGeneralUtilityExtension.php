<?php

declare(strict_types=1);

namespace DMK\PHPStan\Type;

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\ShouldNotHappenException;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

class DynamicGeneralUtilityExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return \tx_rnbase::class;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return 'makeInstance' === $methodReflection->getName();
    }

    public function getTypeFromStaticMethodCall(MethodReflection $methodReflection, StaticCall $methodCall, Scope $scope): Type
    {
        if (0 === count($methodCall->args)) {
            throw new ShouldNotHappenException();
        }

        $arg = $methodCall->args[0]->value;

        if ($arg instanceof ClassConstFetch) {
            return new ObjectType((string) $arg);
        }

        return ParametersAcceptorSelector::selectSingle($methodReflection->getVariants())->getReturnType();
    }

}
