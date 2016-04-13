<?php

namespace Alpixel\Bundle\CMSBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AdminNodeController extends Controller
{
    public function createTranslationAction(Request $request)
    {
        $object = $this->admin->getSubject();
        $locale = $request->query->get('locale');

        if ($locale === null || $object === null) {
            return $this->createNotFoundException();
        }

        $entityManager = $this->get('doctrine.orm.entity_manager');
        $translation = $entityManager->getRepository('AlpixelCMSBundle:Node')
            ->findTranslation($object, $locale);

        if ($translation !== null) {
            return $this->redirect($this->admin->generateUrl('edit', ['id' => $translation->getId()]));
        } else {
            $translatedContent = $this->get('alpixel_cms.helper.cms')->createTranslation($object, $locale);
            $entityManager->persist($translatedContent);
            $entityManager->flush();

            return $this->redirect($this->admin->generateUrl('edit', ['id' => $translatedContent->getId()]));
        }
    }

    public function seeAction(Request $request)
    {
        $object = $this->admin->getSubject();
        $contentTypes = $this->admin->getCMSTypes();

        foreach ($contentTypes as $key => $contentType) {
            if ($key === $object->getType()) {
                if (isset($contentType['controller'])) {
                    return $this->redirectToRoute('alpixel_cms', ['slug' => $object->getSlug()]);
                } elseif ($contentType['admin'] !== null && $contentType['admin']->showCustomURL($object) !== null) {
                    return $this->redirect($contentType['admin']->showCustomURL($object));
                }
            }
        }

        $this->get('session')->getFlashBag()->add('warning', 'Impossible de trouver une adresse pour cette page');

        return $this->redirectTo($object);
    }

    public function listAction(Request $request = null)
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        if (!$this->container->hasParameter('alpixel_cms.content_types')) {
            throw $this->createNotFoundException('alpixel_cms.content_types parameters has not been  not found, maybe you must be configured cms.yml file');
        }

        $cmsContentType = $this->container->getParameter('alpixel_cms.content_types');
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render($this->admin->getTemplate('list'), [
            'action'         => 'list',
            'cmsContentType' => $cmsContentType,
            'form'           => $formView,
            'datagrid'       => $datagrid,
            'csrf_token'     => $this->getCsrfToken('sonata.batch'),
        ], null, $request);
    }

    /**
     * {@inheritdoc}
     */
    protected function redirectTo($object)
    {
        $request = $this->getRequest();

        $url = $backToNodeList = false;
        $instanceAdmin = $this->admin->getConfigurationPool()->getInstance('alpixel_cms.admin.node');

        if (null !== $request->get('btn_update_and_list') || null !== $request->get('btn_create_and_list')) {
            $backToNodeList = true;
        }

        if (null !== $request->get('btn_create_and_create')) {
            $params = [];
            if ($this->admin->hasActiveSubClass()) {
                $params['subclass'] = $request->get('subclass');
            }
            $url = $this->admin->generateUrl('create', $params);
        }

        if ($this->getRestMethod() === 'DELETE') {
            $backToNodeList = true;
        }

        if (!$url) {
            foreach (['edit', 'show'] as $route) {
                if ($this->admin->hasRoute($route) && $this->admin->isGranted(strtoupper($route), $object)) {
                    $url = $this->admin->generateObjectUrl($route, $object);
                    break;
                }
            }
        }

        if ($backToNodeList || !$url) {
            $url = $instanceAdmin->generateUrl('list');
        }

        return new RedirectResponse($url);
    }
}
