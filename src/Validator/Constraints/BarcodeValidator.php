<?php

declare(strict_types=1);

namespace Loevgaard\SyliusBarcodePlugin\Validator\Constraints;

use InvalidArgumentException;
use Safe\Exceptions\StringsException;
use function Safe\sprintf;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use violuke\Barcodes\BarcodeValidator as ActualBarcodeValidator;

class BarcodeValidator extends ConstraintValidator
{
    /**
     * @throws StringsException
     */
    public function validate($value, Constraint $constraint): void
    {
        // custom constraints should ignore null and empty values to allow
        // other constraints (NotBlank, NotNull, etc.) take care of that
        if ('' === $value || null === $value) {
            return;
        }

        if (!$constraint instanceof Barcode) {
            throw new InvalidArgumentException(sprintf('This validator only supports the %s constraint', Barcode::class));
        }

        $validator = new ActualBarcodeValidator($value);
        $valid = (bool) $validator->isValid();

        if (!$valid) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
