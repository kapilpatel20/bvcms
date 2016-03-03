<?php

namespace BvCmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use BvCmsBundle\Entity\Pages;
use \DateTime;
use BvCmsBundle\Form\Type\CmsFormType;

class PagesController extends Controller {

    public function indexAction() {

        $admin = $this->get('security.context')->getToken()->getUser();
        if ($admin) {
            return $this->render('BvCmsBundle:Pages:index.html.twig');
        } else {
            $this->get('session')->getFlashBag()->add('failure', "You are not allowed to view email templates!");
            return $this->redirect($this->generateUrl('dashboard'));
        }
    }

    public function cmsListJsonAction($orderBy = "id", $sortOrder = "asc", $search = "all", $offset = 0) {

        $aColumns = array('Name', 'Status', 'Id');
        
        $admin = $this->get('security.context')->getToken()->getUser();

        $helper = $this->get('grid_helper_function');
        $gridData = $helper->getSearchData($aColumns);

        $sortOrder = $gridData['sort_order'];
        $orderBy = $gridData['order_by'];

        if ($gridData['sort_order'] == '' && $gridData['order_by'] == '') {

            $orderBy = 'c.name';
            $sortOrder = 'DESC';
        } else {

            if ($gridData['order_by'] == 'Name') {
                $orderBy = 'c.name';
            }
            if ($gridData['order_by'] == 'Status') {
                $orderBy = 'c.status';
            }
        }

        // Paging
        $per_page = $gridData['per_page'];
        $offset = $gridData['offset'];

        $em = $this->getDoctrine()->getManager();
        
        $data = $em->getRepository('BvCmsBundle:Pages')->getCmsGridList($per_page, $offset, $orderBy, $sortOrder, $gridData['search_data'], $gridData['SearchType'], $helper, $admin);

        $output = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => 0,
            "iTotalDisplayRecords" => 0,
            "aaData" => array()
        );
        if (isset($data) && !empty($data)) {

            if (isset($data['result']) && !empty($data['result'])) {

                $output = array(
                    "sEcho" => intval($_GET['sEcho']),
                    "iTotalRecords" => $data['totalRecord'],
                    "iTotalDisplayRecords" => $data['totalRecord'],
                    "aaData" => array()
                );

                foreach ($data['result'] AS $resultRow) {
                    $row = array();
                    $row[] = $resultRow['name'];
                    $row[] = $resultRow['status'];
                    $row[] = $resultRow["id"];
                    $output['aaData'][] = $row;
                }
            }
        }
        
        $response = new Response(json_encode($output));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    public function newAction(Request $request) {
       
        $admin = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $objPages = new Pages();
        $form = $this->createForm(new CmsFormType(), $objPages);

        if ($request->getMethod() == "POST") {

            $form->handleRequest($request);
            $formValues = $request->request->get('bragshare_admin_cms_pages');

            if ($form->isValid()) {
                $objAdmin = $form->getData();
                $objAdmin->setStatus('Active');
                $objAdmin->setCreatedAt(new \DateTime());
                $objAdmin->setUpdatedAt(new \DateTime());
                $em->persist($objAdmin);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', "Cms page added successfully.");
                return $this->redirect($this->generateUrl('bv_cms_list'));
            }
        }
        return $this->render('BvCmsBundle:Pages:new.html.twig', array('form' => $form->createView()));
    }

    public function editAction(Request $request, $id) {
        
        $admin = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $objPage = $em->getRepository('BvCmsBundle:Pages')->find($id);

        if (!$objPage) {
            $this->get('session')->getFlashBag()->add('danger', "Cms page does not exist.");
            return $this->redirect($this->generateUrl('bv_cms_list'));
        }
        $form = $this->createForm(new CmsFormType(), $objPage);

         
        //$form->remove('metatitle');
        //$form->remove('metakeyword');
        //$form->remove('metadescription');

        if ($request->getMethod() == "POST") {

            $form->handleRequest($request);
            $formValues = $request->request->get('bragshare_admin_cms_pages');

            if ($form->isValid()) {
                $objAdmin = $form->getData();
                $objAdmin->setUpdatedAt(new \DateTime());
                $em->persist($objAdmin);
                $em->flush();

                $this->get('session')->getFlashBag()->add('success', "Cms page updated successfully.");
                return $this->redirect($this->generateUrl('bv_cms_list'));
            }
        }
        return $this->render('BvCmsBundle:Pages:edit.html.twig', array('form' => $form->createView(), 'id' => $id));
    }

    public function deleteAction(Request $request) {

        $id = $request->get('id');

        $admin = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $objPages = $em->getRepository('BvCmsBundle:Pages')->find($id);


        if (!$objPages) {
            $result = array('type' => 'danger', 'message' => 'CMS page does not exist.');
        }

        $objPages->setStatus('Deleted');

        $em->persist($objPages);
        $em->flush();

        $result = array('type' => 'success', 'message' => "CMS page deleted successfully.");

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function statusAction(Request $request) {
        $id = $request->get('id');

        $admin = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();

        $objPages = $em->getRepository('BvCmsBundle:Pages')->find($id);


        if (!$objPages || $objPages->getStatus() == 'Deleted') {
            $result = array('type' => 'danger', 'message' => 'CMS page does not exist.');
        }

        $currentStatus = $objPages->getStatus();
        $modifiedStatus = ($currentStatus == 'Active') ? 'InActive' : 'Active';
        $objPages->setStatus($modifiedStatus);

        $em->persist($objPages);
        $em->flush();

        $result = array('type' => 'success', 'message' => "CMS page marked as " . ucwords(strtolower($modifiedStatus)) . ".");

        $response = new Response(json_encode($result));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    public function detailAction(Request $request) {

       

        $id = $request->get('id');

        $em = $this->getDoctrine()->getManager();

        $objPages = $em->getRepository('BvCmsBundle:Pages')->find($id);


        if (!$objPages) {
            $this->get('session')->getFlashBag()->add('danger', "CMS page does not exist.");
            return $this->redirect($this->generateUrl('bv_cms_list'));
        }

        //$objCommonHelper = $this->get('dispensaries.helper.common');
        //$metaInfo = $objCommonHelper->getMetaInfo($objPages->getName());
        $metaInfo = '';
        return $this->render('BvCmsBundle:Pages:detail.html.twig', array(
                    'page' => $objPages, 'metaInfo' => $metaInfo
        ));
    }

}
