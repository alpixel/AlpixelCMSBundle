<?php

namespace Alpixel\Bundle\CMSBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class AdminBlockController extends Controller
{
    private $_cmsParameter = 'alpixel_cms.blocks';
    private $_cmsContentParameter = null;
    private $_blockDefaultClass = 'Alpixel\Bundle\CMSBundle\Entity\Block';

    public function editContentAction()
    {
        $object = $this->admin->getSubject();
        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object'));
        }

        $instanceAdmin = $this->admin->getConfigurationPool()->getAdminByClass(get_class($object));
        if ($instanceAdmin !== null) {
            return $this->redirect($instanceAdmin->generateUrl('edit', ['id' => $object->getId()]));
        }

        throw new NotFoundHttpException(sprintf('unable to find a class admin for the %s class', get_class($content)));
    }

    public function listAction(Request $request = null)
    {
        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $this->getInstancesAdmin();
        // set the theme for the current Admin Form
        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render($this->admin->getTemplate('list'), [
            'action'         => 'list',
            'cmsContentType' => $this->getCMSParameter(),
            'form'           => $formView,
            'datagrid'       => $datagrid,
            'csrf_token'     => $this->getCsrfToken('sonata.batch'),
        ], null, $request);
    }

    /**
     *  Set instances of admin in $_cmsContentParameter.
     */
    protected function getInstancesAdmin()
    {
        foreach ($this->getCMSParameter() as $key => $value) {
            if ($this->checkRolesCMS($value)) {
                $instanceAdmin = $this->admin->getConfigurationPool()->getAdminByClass($value['class']);
                if ($instanceAdmin !== null) {
                    $this->_cmsContentParameter[$key]['admin'] = $instanceAdmin;
                }
            }
        }
    }

    /**
     * @param $role array Role in cms.yml
     *
     * Check role define in different parameter of block or content_types cms.yml
     */
    protected function checkRolesCMS(array $role)
    {
        $user = $this->getUser();

        if (!$user) {
            throw new NotFoundHttpException(sprintf('unable to find user'));
        }

        if (!array_key_exists('role', $role) || in_array($user->getRoles()[0], $role['role'])) {
            return true;
        }

        return false;
    }

    /**
     * Get content in cms.yml and set in $_cmsParameter.
     *
     * @return content of cms.yml
     */
    protected function getCMSParameter()
    {
        if ($this->_cmsContentParameter !== null) {
            return $this->_cmsContentParameter;
        }

        $this->_cmsContentParameter = $this->container->getParameter($this->_cmsParameter);

        return $this->_cmsContentParameter;
    }
}
