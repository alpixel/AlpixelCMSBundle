<?php
namespace Alpixel\Bundle\CMSBundle\Controller;

use Alpixel\Bundle\CMSBundle\Entity\Node;
use Alpixel\Bundle\SEOBundle\Annotation\MetaTag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;


class FrontNodeController extends Controller
{

    /**
     * @Route("/page/{slug}", name="front_cms")
     * @MetaTag("node", providerClass="Alpixel\Bundle\CMSBundle\Entity\Node", title="Page de contenu")
     * @ParamConverter("node", options={"mapping" : {"slug": "slug"}})
     * @Method("GET")
     */
    public function dispatchAction(Node $node)
    {
        $entities      = array();
        $entityManager = $this->get('doctrine.orm.entity_manager');
        $meta          = $entityManager->getMetadataFactory()->getAllMetadata();

        foreach ($meta as $m) {
            $relations = $m->getAssociationMappings();
            if(array_key_exists('node', $relations) && $relations['node']['targetEntity'] == 'Alpixel\Bundle\CMSBundle\Entity\Node') {
                $entities[] = $m;
            }
        }

        foreach($entities as $entity) {
            $object = $entityManager
                        ->getRepository($entity->getName())
                        ->findOneByNode($node)
                    ;

            if($object !== null && $object->getNode()->getPublished()) {
                $contentTypes = $this->container->getParameter('cms.content_types');
                foreach($contentTypes as $contentType) {
                    if($contentType['class'] == get_class($object)) {
                        return $this->forward($contentType['controller'], array(
                            '_route'        => $this->getRequest()->attributes->get('_route'),
                            '_route_params' => $this->getRequest()->attributes->get('_route_params'),
                            'object'        => $object,
                        ));
                    }
                }
            }
        }
        throw $this->createNotFoundException();
    }

    public function displayNodeAdminBarAction(Node $node) {
        $canEdit = $this->get('request')->cookies->get('can_edit');

        if($canEdit !== null && $canEdit === hash('sha256', 'can_edit'.$this->container->getParameter('secret'))) {
            return $this->render('CMSBundle:admin:blocks/admin_bar_page.html.twig', array(
                'link' => $this->generateUrl('admin_alpixel_cms_node_editContent', array('id' => $node->getId()))
            ));
        }
        return new Response;
    }

    public function displayCustomAdminBarAction($link) {
        $canEdit = $this->get('request')->cookies->get('can_edit');

        if($canEdit !== null && $canEdit === hash('sha256', 'can_edit'.$this->container->getParameter('secret'))) {
            return $this->render('CMSBundle:admin:blocks/admin_bar_page.html.twig', array(
                'link' => $link
            ));
        }
        return new Response;
    }
}
