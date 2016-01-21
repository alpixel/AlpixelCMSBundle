<?php

namespace Alpixel\Bundle\CMSBundle\Controller;

use Alpixel\Bundle\CMSBundle\Entity\Node;
use Alpixel\Bundle\SEOBundle\Annotation\MetaTag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class FrontNodeController extends Controller
{
    /**
     * @Route("/page/{slug}", name="alpixel_cms")
     * @MetaTag("node", providerClass="Alpixel\Bundle\CMSBundle\Entity\Node", title="Page de contenu")
     * @ParamConverter("node", options={"mapping" : {"slug": "slug"}})
     * @Method("GET")
     */
    public function dispatchAction(Request $request, Node $node)
    {
        $object = $this->get('cms.helper')->getNodeElementEntityFromNode($node);

        if ($object !== null && $object->getNode()->getPublished()) {
            if (stripos($request->getLocale(), $object->getNode()->getLocale()) !== false) {
                $contentType = $this->get('cms.helper')->getContentTypeFromNodeElementClass($object);
                return $this->forward($contentType['controller'], [
                    '_route'        => $this->getRequest()->attributes->get('_route'),
                    '_route_params' => $this->getRequest()->attributes->get('_route_params'),
                    'object'        => $object,
                ]);
            }
        }

        throw $this->createNotFoundException();
    }

    public function displayNodeAdminBarAction(Node $node)
    {
        $canEdit = $this->get('request')->cookies->get('can_edit');

        if ($canEdit !== null && $canEdit === hash('sha256', 'can_edit'.$this->container->getParameter('secret'))) {
            return $this->render('CMSBundle:admin:blocks/admin_bar_page.html.twig', [
                'link' => $this->generateUrl('admin_alpixel_cms_node_editContent', ['id' => $node->getId()]),
            ]);
        }

        return new Response();
    }

    public function displayCustomAdminBarAction($link)
    {
        $canEdit = $this->get('request')->cookies->get('can_edit');

        if ($canEdit !== null && $canEdit === hash('sha256', 'can_edit'.$this->container->getParameter('secret'))) {
            return $this->render('CMSBundle:admin:blocks/admin_bar_page.html.twig', [
                'link' => $link,
            ]);
        }

        return new Response();
    }
}
