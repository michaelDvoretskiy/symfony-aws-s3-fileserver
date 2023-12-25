<?php 

namespace App\Dto;

//use Jawira\CaseConverter\Convert;

use App\Service\JsonResponsesService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractJsonRequest
{
    protected bool $allowExtraFields = false;
    protected $extraFieldErrors = [];

    public function __construct(
        protected ValidatorInterface $validator,
        protected RequestStack $requestStack,
        protected EntityManagerInterface $em,
        protected JsonResponsesService $jsonRespService
    ) {
        $this->populate();
        $this->validate();
    }

    public function getRequest(): Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    protected function populate(): void
    {
        $request = $this->getRequest();
        $requestData = array_merge(
            $request->query->all(),
            json_decode($request->getContent(), true) ?? []
        );
        $reflection = new \ReflectionClass($this);

        foreach ($requestData as $property => $value) {
            $attribute = self::camelCase($property);
            if (property_exists($this, $attribute)) {
                $reflectionProperty = $reflection->getProperty($attribute);
                $reflectionProperty->setValue($this, $value);
            } elseif (!$this->allowExtraFields) {
                $this->extraFieldErrors[] = [
                    'property' => $attribute,
                    'value' => $value,
                    'message' => $attribute . " field is not allowed",
                ];
            }
        }
    }

    protected function validate(): void
    {
        $violations = $this->validator->validate($this);
        if (count($violations) < 1 && count($this->extraFieldErrors) < 1) {
            return;
        }

        $errors = [];

        /** @var \Symfony\Component\Validator\ConstraintViolation */
        foreach ($violations as $violation) {
            $attribute = self::snakeCase($violation->getPropertyPath());
            $errors[] = [
                'property' => $attribute,
                'value' => $violation->getInvalidValue(),
                'message' => $violation->getMessage(),
            ];
        }

        $this->jsonRespService->validationError(array_merge($errors,$this->extraFieldErrors));
    }

    private static function camelCase(string $attribute): string
    {
        return $attribute;
        //return (new Convert($attribute))->toCamel();
    }

    private static function snakeCase(string $attribute): string
    {
        return $attribute;
        // return (new Convert($attribute))->toSnake();
    }
}
