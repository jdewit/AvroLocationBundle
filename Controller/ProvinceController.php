<?php

namespace Avro\LocationBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Avro\LocationBundle\Document\Province;
use Avro\UserBundle\Form\Type\ProvinceFormType;

/**
 * Province controller.
 *
 * @Route("/province")
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class ProvinceController extends Controller
{
    /**
     * Get Provinces as json
     * @Cache(smaxage="3600")
     */
    public function getAction($id)
    {
        $serializer = $this->get('serializer');
        $request = $this->get('request');

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $qb = $dm->createQueryBuilder('AvroLocationBundle:Province')
            ->sort('name', 'ASC')
            ->field('country')->equals($id)
        ;

        $provinces = $qb->getQuery()->execute()->toArray();

        $response = new Response('{
            "results": '.$serializer->serialize($provinces, 'json').'
        }');

        $response->headers->set('Content-Type', 'avro/json');

        return $response;
    }

    /**
     * List Provinces.
     *
     * @Route("/", name="avro_location_province_list")
     * @Template()
     */
    public function listAction()
    {
        $paginator = $this->get('avro_core.paginator');
        $paginator->setClass('AvroLocationBundle:Province');
        $provinces = $paginator->getResults();

        $request = $this->get('request');

        $id = $request->query->get('id');
        if ($id) {
            $province = $this->get('doctrine.odm.mongodb.document_manager')
                ->getRepository('AvroLocationBundle:Province')
                ->find($id);

            if (!$province) {
                throw $this->createNotFoundException('No province found');
            }
        } else {
            $province = new Province();
        }

        $form = $this->createForm(new ProvinceFormType(), $province);
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if (true === $form->isValid()) {
                $dm = $this->get('doctrine.odm.mongodb.document_manager');

                $province = $form->getData();

                $dm->persist($province);
                $dm->flush();

                if ($id) {
                    $this->get('session')->getFlashBag()->set('success', 'Province updated.');
                } else {
                    $this->get('session')->getFlashBag()->set('success', 'Province created.');
                }

                return new RedirectResponse($this->get('request')->headers->get('referer'), 301);
            }
        }

        return array(
            'provinces' => $provinces,
            'province' => $province,
            'paginator' => $paginator,
            'form' => $form->createView()
        );
    }
    /**
     * Edit one Province, show the edit form.
     *
     * @Template()
     */
    public function editAction($id)
    {
        $province = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('AvroLocationBundle:Province')
            ->find($id);

        if (!$province) {
            throw $this->createNotFoundException('No province found');
        }

        $form = $this->createForm(new ProvinceFormType(), $province);

        $formAction = $this->generateUrl('avro_location_province_list').'?id='.$id;

        parse_str(parse_url($this->get('request')->headers->get('referer'), PHP_URL_QUERY), $params);
        foreach($params as $k => $v) {
            if (!empty($v)) {
                $formAction = $formAction.'&'.$k.'='.$v;
            }
        }

        return array(
            'form' => $form->createView(),
            'formAction' => $formAction,
            'province' => $province
        );
    }

    /**
     * Delete oneProvince.
     *
     */
    public function deleteAction($id)
    {
        $province = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('AvroLocationBundle:Province')
            ->find($id);

        $province->setIsDeleted(true);

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->persist($province);
        $dm->flush();

        $this->container->get('session')->getFlashBag()->set('success', 'Province deleted.');

        $uri = $this->get('request')->headers->get('referer');

        return new RedirectResponse($uri);
    }

    /**
     * Restore one Province.
     *
     * @Route("/restore/{id}", name="avro_location_province_restore")
     */
    public function restoreAction($id)
    {
        $province = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('AvroLocationBundle:Province')
            ->find($id);

        $province->setIsDeleted(false);
        $province->setDeletedAt(null);

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->persist($province);
        $dm->flush();

        $this->container->get('session')->getFlashBag()->set('success', 'Province restored.');

        $uri = $this->get('request')->headers->get('referer');

        return new RedirectResponse($uri);
    }

}
