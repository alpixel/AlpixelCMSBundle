<?php
namespace Alpixel\Bundle\CMSBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;

class AdminNodeController extends Controller
{

    public function editContentAction()
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if(!$this->container->hasParameter('cms.content_types')) {
            throw new NotFoundHttpException('cms.content_types parameters in ' . __FILE__ .'  file at line ' . __LINE__ . ' in ' . __FUNCTION__ . ' method, has not been  not found, maybe you must be configured cms.yml file');
        }

        // Get admin class define in cms.yml
        $cmsContentType = $this->container->getParameter('cms.content_types');
        $entityManager  = $this->getDoctrine()->getManager();

        if(array_key_exists($object->getType(), $cmsContentType)) {
            $contentType = $cmsContentType[$object->getType()];

            $content = $entityManager
                        ->getRepository($contentType['class'])
                        ->findOneByNode($object);

            if($content !== null) {
                $instanceAdmin = $this->admin->getConfigurationPool()->getAdminByClass($contentType['class']);

                if($instanceAdmin !== null) {
                    return $this->redirect($instanceAdmin->generateUrl('edit', array('id' => $content->getId())));
                }

            }
        }
        throw new NotFoundHttpException(sprintf('unable to find a class admin for the %s class', $contentType['class']));
    }

    public function listAction(Request $request = NULL)
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        if(!$this->container->hasParameter('cms.content_types')) {
            throw $this->createNotFoundException('cms.content_types parameters in ' . __FILE__ .'  file at line ' . __LINE__ . ' in ' . __FUNCTION__ . ' method, has not been  not found, maybe you must be configured cms.yml file');
        }

        $cmsContentType = $this->container->getParameter('cms.content_types');

        foreach($cmsContentType as $key => $value) {
            if($this->checkRolesCMS($value)) {
                if($instanceAdmin = $this->admin->getConfigurationPool()->getAdminByClass($value['class'])) {
                    $cmsContentType[$key]['admin'] = $instanceAdmin;
                }
            }
        }

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render($this->admin->getTemplate('list'), array(
            'action'         => 'list',
            'cmsContentType' => $cmsContentType,
            'form'           => $formView,
            'datagrid'       => $datagrid,
            'csrf_token'     => $this->getCsrfToken('sonata.batch'),
        ), null, $request);
    }

    protected function checkRolesCMS(array $role) {
        $user = $this->getUser();

        if (!$user) {
            throw new NotFoundHttpException(sprintf('unable to find user in %s file', __FILE__));
        }

        $userRole = $user->getRoles()[0];

        if(!array_key_exists('role', $role) || in_array($userRole, $role['role'])) {
            return true;
        }

        return false;
    }

}
