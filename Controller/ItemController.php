<?php

declare(strict_types=1);

namespace BMClientBundle\Client\Controller;

use BMClientBundle\Client\Entity\Item;
use BMClientBundle\Client\Form\ItemForm;
use BMClientBundle\Client\Services\BMApiRequest;
use RuntimeException;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class ItemController
{
    public const NOTICE_MSG_TYPE = 'notice';
    public const ERROR_MSG_TYPE = 'error';
    public const WARNING_MSG_TYPE = 'warning';

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var BMApiRequest
     */
    private $apiRequest;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        FormFactoryInterface $formFactory,
        SessionInterface $session,
        BMApiRequest $apiRequest,
        Environment $twig,
        RouterInterface $router,
        SerializerInterface $serializer
    ) {
        $this->formFactory = $formFactory;
        $this->session = $session;
        $this->apiRequest = $apiRequest;
        $this->twig = $twig;
        $this->router = $router;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/add", name="client_item_add", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function create(Request $request): Response
    {
        $item = new Item();
        $form = $this->formFactory->create(ItemForm::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $result = $this->apiRequest->request(
                Request::METHOD_POST,
                '/api/v1/items/add',
                $this->serializer->normalize($form->getNormData())
            );

            if (Response::HTTP_CREATED === $result['status']) {
                $this->session->getFlashBag()->add(self::NOTICE_MSG_TYPE, 'Product created');

                return new RedirectResponse($this->router->generate('client_item_add'));
            }

            $this->session->getFlashBag()->add(self::ERROR_MSG_TYPE, $result['title'] . ' ' . $result['detail']);

            return new RedirectResponse($this->router->generate('client_item_add'));
        }

        try {
            $content = $this->twig->render('@BMClient/create.html.twig', ['form' => $form->createView()]);
        } catch (LoaderError | RuntimeError | SyntaxError $error) {
            throw new RuntimeException('Cannot render template');
        }

        return new Response($content);
    }


    /**
     * @Route("/available", name="client_available_items", methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function available(Request $request): Response
    {
        $result = $this->apiRequest->request(Request::METHOD_GET, '/api/v1/items/available');

        try {
            $content = $this->twig->render('@BMClient/items.html.twig', ['items' => $result['data']]);
        } catch (LoaderError | RuntimeError | SyntaxError $error) {
            throw new RuntimeException('Cannot render template');
        }

        return new Response($content);
    }


    /**
     * @Route("/unavailable", name="client_unavailable_items", methods={"GET"})
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function unavailable(): Response
    {
        $result = $this->apiRequest->request(Request::METHOD_GET, '/api/v1/items/unavailable');

        try {
            $content = $this->twig->render('@BMClient/items.html.twig', ['items' => $result['data']]);
        } catch (LoaderError | RuntimeError | SyntaxError $error) {
            throw new RuntimeException('Cannot render template');
        }

        return new Response($content);
    }

    /**
     * @Route("/search", name="client_find_item", methods={"GET", "POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function search(Request $request): Response
    {
        if ($request->getMethod() === 'POST') {
            $requestResult = $request->request->all();
            $result = $this->apiRequest->request(
                Request::METHOD_GET,
                '/api/v1/items/find',
                [
                    'amount' => [
                        'comparison_sign' => $requestResult['comparison_sign'],
                        'value' => $requestResult['amount']
                    ]
                ]
            );

            try {
                $content = $this->twig->render('@BMClient/search.html.twig', ['items' => $result['data']]);

                return new Response($content);
            } catch (LoaderError | RuntimeError | SyntaxError $error) {
                throw new RuntimeException('Cannot render template');
            }
        }

        try {
            $content = $this->twig->render('@BMClient/search.html.twig', ['items' => null]);
        } catch (LoaderError | RuntimeError | SyntaxError $error) {
            throw new RuntimeException('Cannot render template');
        }

        return new Response($content);
    }

    /**
     * @Route("/{id}/update", name="client_update_item", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, int $id): Response
    {
        $result = $this->apiRequest->request(Request::METHOD_GET, '/api/v1/items/' . $id);

        if (Response::HTTP_OK !== $result['status']) {
            $this->session->getFlashBag()->add(self::ERROR_MSG_TYPE, $result['title'] . ' ' . $result['detail']);

            return new RedirectResponse($this->router->generate($this->getRefererUrlName($request)));
        }

        $item = (new Item())
            ->setAmount($result['data']['amount'])
            ->setName($result['data']['name'])
            ->setId($result['data']['id']);

        $form = $this->formFactory->create(ItemForm::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            # nie obsługuję odpowiedzi świadomie ;) ...
            $this->apiRequest->request(
                Request::METHOD_PATCH,
                '/api/v1/items/' . $id,
                $this->serializer->normalize($form->getNormData())
            );

            return new RedirectResponse($this->router->generate('client_available_items'));
        }

        try {
            $content = $this->twig->render('@BMClient/create.html.twig', ['form' => $form->createView()]);
        } catch (LoaderError | RuntimeError | SyntaxError $error) {
            throw new RuntimeException('Cannot render template');
        }

        return new Response($content);
    }

    /**
     * @Route("/{id}/delete", name="client_delete_item", methods={"GET"})
     *
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function delete(Request $request, int $id): Response
    {
        $result = $this->apiRequest->request(Request::METHOD_DELETE, '/api/v1/items/' . $id);

        if (Response::HTTP_OK !== $result['status']) {
            $this->session->getFlashBag()->add(self::ERROR_MSG_TYPE, $result['title'] . ' ' . $result['detail']);
        }

        $urlName = $this->getRefererUrlName($request);

        return new RedirectResponse($this->router->generate($urlName));
    }

    private function getRefererUrlName(Request $request): string
    {
        $refererData = explode('/', $request->headers->get('referer'));
        $referer = end($refererData);

        return $referer === 'available' ? 'client_available_items' : 'client_unavailable_items';
    }
}
