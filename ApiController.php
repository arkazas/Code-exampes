<?php

namespace AFG\Http\Controller;

use AFG\Http\Command\User\UpdateUserCommand;
use AFG\Http\Request\User\UpdateUserRequest;
use AFG\Model\User\User;
use AFG\Service\Fractal\ResponseFinalizer;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Pagination\Paginator;
use FOS\RestBundle\Controller\FOSRestController;
use Monolog\Logger;
use League\Fractal\Pagination\PaginatorInterface;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Exception\ValidatorException;

abstract class ApiController extends FOSRestController
{
    protected function flushChanges()
    {
        $this->get('doctrine.orm.default_entity_manager')->flush();
    }

    /**
     * @internal
     */
    protected function flushChangesUuid()
    {
        try {
            $this->get('doctrine.orm.default_entity_manager')->flush();
        } catch (UniqueConstraintViolationException $exception) {
            $exception->getPrevious();
        }
    }

    protected function item($data, $transformer, $additionalMeta = [])
    {
        return new ResponseFinalizer(
            array_merge($this->get('afg.fractal_service')->item($data, $transformer), $additionalMeta)
        );
    }

    protected function created()
    {
        return new Response(
            '',
            Response::HTTP_CREATED
        );
    }

    protected function collection($data, $transformer, $additionalMeta = [])
    {
        $data = $this->get('afg.fractal_service')->collection($data, $transformer);
        if ($additionalMeta) {
            $data['meta'] = array_merge(isset($data['meta']) ? $data['meta'] : [], $additionalMeta);
        }

        return new ResponseFinalizer(
            $data
        );
    }

    protected function resource($data, $transformer, $additionalMeta = [])
    {
        if (\is_array($data) || $data instanceof Paginator || $data instanceof Pagerfanta) {
            return $this->collection($data, $transformer, $additionalMeta);
        }

        return $this->item($data, $transformer, $additionalMeta);
    }

    protected function dispatch($command)
    {
        $this->get('broadway.command_handling.command_bus')->dispatch($command);
    }

    /**
     * @param User $user
     */
    protected function updateHMS(User $user): void
    {
        if (!$user->hasHMSCard()) {
            throw new ValidatorException('error.user.have_not_hms');
        }
        $request = new UpdateUserRequest();
        $request->setPayload(
            [
                'hms_card_number' => $user->getActualHMSCard()->getCardNumber(),
            ]
        );
        $command = new UpdateUserCommand($request, $user);
        $this->dispatch($command);
    }

    /**
     * @param $command
     */
    protected function dispatchInTransaction($command): void
    {
        /** @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');
        try {
            $em->transactional(
                function () use ($command) {
                    $this->dispatch($command);
                }
            );
        } catch(\Exception $exception) {
            /** @var Logger $logger */
            $logger = $this->container->get('logger');
            $logger->info('Transaction was finished with exception', [$exception]);
            throw $exception;
        }
    }

    protected function isMasterRequest()
    {
        $requestStack = $this->get('request_stack');
        return null === $requestStack->getParentRequest();
    }
}
