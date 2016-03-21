<?php

namespace Alpixel\Bundle\CMSBundle\Controller;

use Alpixel\Bundle\CMSBundle\Entity\Node;
use Alpixel\Bundle\SEOBundle\Annotation\MetaTag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class NodeController extends Controller
{
    /**
     * @MetaTag("node", providerClass="Alpixel\Bundle\CMSBundle\Entity\Node", title="Page de contenu")
     * @Method("GET")
     */
    public function dispatchAction(Request $request, $slug)
    {
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $node = $entityManager->getRepository('AlpixelCMSBundle:Node')
                              ->findOnePublishedBySlugAndLocale($slug, $request->getLocale());

        if ($node !== null) {
            $contentType = $this->get('alpixel_cms.helper')->getContentTypeFromNodeElementClass($node);
            $controller = explode('::', $contentType['controller']);

            try {
                if (count($controller) !== 2) {
                    throw new \LogicException('The parameter controller must be a valid callable controller, like "My\Namespace\Controller\Class::method"');
                } elseif (!class_exists($controller[0]) || !method_exists($controller[0], $controller[1])) {
                    throw new \LogicException(sprintf(
                        'Unable to find the "%s" controller or the method "%s" doesn\'t exist.',
                        $controller[0],
                        $controller[1]
                    ));
                }

                return $this->forward($contentType['controller'], [
                    '_route'        => $request->attributes->get('_route'),
                    '_route_params' => $request->attributes->get('_route_params'),
                    'object'        => $node,
                ]);
            } catch(\LogicException $e) {
                $environment = $this->container->get('kernel')->getEnvironment();
                if ($environment === 'prod') {
                    $logger = $this->get('logger');
                    $logger->error($e->getMessage());
                } else {
                    throw $e;
                }
            }
        }

        throw $this->createNotFoundException();
    }

    public function displayNodeAdminBarAction(Node $node)
    {
        $canEdit = $this->get('request')->cookies->get('can_edit');

        if ($canEdit !== null && $canEdit === hash('sha256', 'can_edit'.$this->container->getParameter('secret'))) {
            return $this->render('AlpixelCMSBundle:admin:blocks/admin_bar_page.html.twig', [
                'link' => $this->generateUrl('cms_node_editContent', ['id' => $node->getId()]),
            ]);
        }

        return new Response();
    }

    public function displayCustomAdminBarAction($link)
    {
        $canEdit = $this->get('request')->cookies->get('can_edit');

        if ($canEdit !== null && $canEdit === hash('sha256', 'can_edit'.$this->container->getParameter('secret'))) {
            return $this->render('AlpixelCMSBundle:admin:blocks/admin_bar_page.html.twig', [
                'link' => $link,
            ]);
        }

        return new Response();
    }
}
