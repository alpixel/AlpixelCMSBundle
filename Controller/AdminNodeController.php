<?php

namespace Alpixel\Bundle\CMSBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdminNodeController extends Controller
{
    public function editContentAction()
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object'));
        }

        $content = $this->get('cms.helper')->getNodeElementEntityFromNode($object);

        if ($content !== null) {
            $instanceAdmin = $this->admin->getConfigurationPool()->getAdminByClass(get_class($content));

            if ($instanceAdmin !== null) {
                return $this->redirect($instanceAdmin->generateUrl('edit', ['id' => $content->getId()]));
            }
        }

        throw new NotFoundHttpException(sprintf('unable to find a class admin for the %s class', $contentType['class']));
    }

    public function createTranslationAction(Request $request)
    {
        $object = $this->admin->getSubject();
        $locale = $request->query->get('locale');

        if ($locale === null || $object === null) {
            return $this->createNotFoundException();
        }

        $entityManager = $this->get('doctrine.orm.entity_manager');
        $translation = $entityManager->getRepository('CMSBundle:Node')
                                     ->findTranslation($object, $locale);

        if ($translation !== null) {
            return $this->redirect($this->admin->generateUrl('editContent', ['id' => $translation->getId()]));
        } else {
            $translatedContent = $this->get('cms.helper')->createTranslation($object, $locale);
            $entityManager->persist($translatedContent);
            $entityManager->persist($translatedContent->getNode());
            $entityManager->flush();

            return $this->redirect($this->admin->generateUrl('editContent', ['id' => $translatedContent->getNode()->getId()]));
        }
    }

    public function listAction(Request $request = null)
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        if (!$this->container->hasParameter('cms.content_types')) {
            throw $this->createNotFoundException('cms.content_types parameters has not been  not found, maybe you must be configured cms.yml file');
        }

        $cmsContentType = $this->container->getParameter('cms.content_types');

        foreach ($cmsContentType as $key => $value) {
            if ($this->checkRolesCMS($value)) {
                if ($instanceAdmin = $this->admin->getConfigurationPool()->getAdminByClass($value['class'])) {
                    $cmsContentType[$key]['admin'] = $instanceAdmin;
                }
            }
        }

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render($this->admin->getTemplate('list'), [
            'action'         => 'list',
            'cmsContentType' => $cmsContentType,
            'form'           => $formView,
            'datagrid'       => $datagrid,
            'csrf_token'     => $this->getCsrfToken('sonata.batch'),
        ], null, $request);
    }

    protected function checkRolesCMS(array $role)
    {
        $user = $this->getUser();

        if (!$user) {
            throw new NotFoundHttpException(sprintf('unable to find user'));
        }

        $userRole = $user->getRoles()[0];

        if (!array_key_exists('role', $role) || in_array($userRole, $role['role'])) {
            return true;
        }

        return false;
    }
}
