<?php

namespace AFG\Http\Controller;

use AFG\Http\Request\Employer\SearchRequest;
use AFG\Http\Transformer\Employer\EmployerTypeTransformer;
use AFG\Model\Employers\Employer;
use AFG\Model\Employers\EmployerRepository;
use AFG\Model\Employers\EmployerSearchRepository;
use AFG\Model\Employers\EmployerTypeRepository;
use AFG\Model\Filter;
use AFG\Model\Pagination;
use AFG\Service\Azure\Startbank\StartbankClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as Config;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @Config\Route("/employers")
 */
class EmployerController extends ApiController
{
    const ACTION_LIST = 'employer.list';

    /**
     * @Config\Route("")
     * @Config\Method("GET")
     *
     * @param SearchRequest $request
     * @param EmployerSearchRepository $searchRepository
     * @param Pagination $pagination
     * @param Filter $filter
     * @return JsonResponse
     */
    public function employersList(
        SearchRequest $request,
        EmployerSearchRepository $searchRepository,
        Pagination $pagination,
        Filter $filter
    ): JsonResponse {
        $this->denyAccessUnlessGranted(self::ACTION_LIST);

        $filter->setField('q', $request->get('q'));

        return $this->resource(
            $searchRepository->getList(null, $pagination, $filter),
            $this->get('afg.transformer.employer')
        )->asResponse();
    }

    /**
     * @Config\Route("/type")
     * @Config\Method("GET")
     *
     * @param EmployerTypeRepository $repository
     * @param EmployerTypeTransformer $transformer
     * @return JsonResponse
     */
    public function employerTypeList(EmployerTypeRepository $repository, EmployerTypeTransformer $transformer)
    {
        $this->denyAccessUnlessGranted(self::ACTION_LIST);

        return $this->resource(
            $repository->getList(null, null, null, null, false, false),
            $transformer
        )->asResponse();
    }

    /**
     * @Config\Route("/{employer}/check-startbank-availability", requirements={"employer"="[a-z\-A-Z\d]+"})
     * @Config\Method("GET")
     *
     * @param Employer $employer
     * @param StartbankClient $startbankClient
     *
     * @return JsonResponse
     */
    public function checkStartbankAvailability(Employer $employer, StartbankClient $startbankClient)
    {
        $this->denyAccessUnlessGranted(self::ACTION_LIST);

        $startbank = $startbankClient->getStartbankVendor($employer->getNumber());
        if (!empty($startbank->getStartbankId())) {
            $result = [
                'is_available' => true,
                'startbank' => [
                    'startbankId' => $startbank->getStartbankId(),
                    'status' => $startbank->getBlocked(),
                    'prequalified' => $startbank->getPreQualResult(),
                ]
            ];
        } else {
            $result = [
                'is_available' => false,
            ];
        }
        return new JsonResponse($result);
    }
}
