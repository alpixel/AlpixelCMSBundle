<?php

namespace Alpixel\Bundle\CMSBundle\Controller;

use Alpixel\Bundle\CMSBundle\Entity\Node;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;

class NodeController extends Controller
{
    /**
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param         $slug
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function dispatchAction(Request $request, $slug)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $node = $entityManager->getRepository('AlpixelCMSBundle:Node')
            ->findOneBySlugAndLocale($slug, $request->getLocale());

        if ($node !== null) {
            if ($node->getPublished() === false && !$this->isAuthenticated($request)) {
                throw $this->createNotFoundException();
            }

            $contentType = $this->get('alpixel_cms.helper.cms')->getContentTypeFromNodeElementClass($node);
            $controller = explode('::', $contentType['controller']);

            try {
                if (count($controller) !== 2) {
                    throw new \LogicException(
                        'The parameter controller must be a valid callable controller, like "My\Namespace\Controller\Class::method"'
                    );
                } elseif (!class_exists($controller[0]) || !method_exists($controller[0], $controller[1])) {
                    throw new \LogicException(
                        sprintf(
                            'Unable to find the "%s" controller or the method "%s" doesn\'t exist.',
                            $controller[0],
                            $controller[1]
                        )
                    );
                }

                /** Generating the alternate link for SEO */
                $seoHelper = $this->get('sonata.seo.page.default');
                $translatedPages = $entityManager->getRepository('AlpixelCMSBundle:Node')->findTranslations($node);

                $router = $this->get('router');
                foreach ($translatedPages as $translation) {
                    $seoHelper->addLangAlternate(
                        $router->generate(
                            "alpixel_cms",
                            [
                                'slug'    => $translation->getSlug(),
                                '_locale' => $translation->getLocale(),
                            ],
                            Router::ABSOLUTE_URL
                        ),
                        $translation->getLocale()
                    );
                }

                return $this->forward(
                    $contentType['controller'],
                    [
                        '_route'        => $request->attributes->get('_route'),
                        '_route_params' => $request->attributes->get('_route_params'),
                        'object'        => $node,
                    ]
                );
            } catch (\LogicException $e) {
                if (!$this->container->get('kernel')->isDebug()) {
                    $logger = $this->get('logger');
                    $logger->error($e->getMessage());
                } else {
                    throw $e;
                }
            }
        } else {
            //Trying to find another node with this slug, in another language
            $node = $entityManager->getRepository('AlpixelCMSBundle:Node')
                ->findOnePublishedBySlug($slug);

            if ($node !== null) {
                $translation = $entityManager->getRepository('AlpixelCMSBundle:Node')
                    ->findTranslation($node, $request->getLocale());
                if ($translation !== null) {
                    return $this->redirect(
                        $this->generateUrl(
                            'alpixel_cms',
                            [
                                'slug'    => $translation->getSlug(),
                                '_locale' => $translation->getLocale(),
                            ]
                        ), 301
                    );
                }
            }
        }

        throw $this->createNotFoundException();
    }

    /**
     * @param $node
     *
     * @return Response
     */
    public function displayNodeAdminBarAction(Request $request, $node)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $node = $entityManager->getRepository('AlpixelCMSBundle:Node')->find($node);

        $response = new Response();
        $response->setPrivate();
        $response->setMaxAge(900);

        if ($this->isAuthenticated($request)) {
            $content = $this->renderView(
                'AlpixelCMSBundle:admin:blocks/admin_bar_page.html.twig',
                [
                    'node' => $node,
                    'link' => $this->generateUrl(
                        'alpixel_admin_cms_node_forwardEdit',
                        [
                            'type' => $node->getType(),
                            'id'   => $node->getId(),
                        ]
                    ),
                ]
            );
            $response->setContent($content);
        }

        return $response;
    }

    /**
     * @param $link
     *
     * @return Response
     */
    public function displayCustomAdminBarAction(Request $request, $link)
    {
        $response = new Response();
        $response->setPrivate();
        $response->setMaxAge(900);

        if ($this->isAuthenticated($request)) {
            $content = $this->renderView(
                'AlpixelCMSBundle:admin:blocks/admin_bar_page.html.twig',
                [
                    'link' => $link,
                ]
            );
            $response->setContent($content);
        }

        return $response;
    }

    /**
     * @param Request $request
     */
    private function isAuthenticated(Request $request)
    {
        $canEdit = $request->cookies->get('can_edit');

        if (isset($canEdit)) {
            if ($request->getSession()->has('_security_admin')) {
                try {
                    $token = unserialize($request->getSession()->get('_security_admin'));
                    $user = $token->getUser();

                    return $canEdit === hash(
                        'sha256',
                        'can_edit'.$this->container->getParameter('secret').$user->getSalt()
                    );
                } catch (ContextErrorException $e) {
                }
            }
        }

        return false;
    }
}
