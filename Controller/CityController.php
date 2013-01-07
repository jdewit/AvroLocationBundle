<?php

namespace Avro\LocationBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Avro\LocationBundle\Document\City;
use Avro\UserBundle\Form\Type\CityFormType;

/**
 * City controller.
 *
 * @Route("/city")
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class CityController extends Controller
{
    /**
     * @Cache(smaxage="3600")
     * @Route("/get/{id}", name="avro_location_city_get", defaults={"id"=false})
     */
    public function getAction($id)
    {
        $serializer = $this->get('serializer');
        $request = $this->get('request');

        $term = $request->get('term');

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $qb = $dm->createQueryBuilder('AvroLocationBundle:City')
            ->sort('name', 'ASC')
            ->field('province')->equals($id)
        ;

        $provinces = $qb->getQuery()->execute()->toArray();

        $response = new Response('{
            "results": '.$serializer->serialize($provinces, 'json').'
        }');

        $response->headers->set('Content-Type', 'avro/json');

        return $response;
    }

    /**
     * List Citys.
     *
     * @Route("/", name="avro_location_city_list")
     * @Template()
     */
    public function listAction()
    {
        $paginator = $this->get('avro_core.paginator');
        $paginator->setClass('AvroLocationBundle:City');
        $citys = $paginator->getResults();

        $request = $this->get('request');

        $id = $request->query->get('id');
        if ($id) {
            $city = $this->get('doctrine.odm.mongodb.document_manager')
                ->getRepository('AvroLocationBundle:City')
                ->find($id);

            if (!$city) {
                throw $this->createNotFoundException('No city found');
            }
        } else {
            $city = new City();
        }

        $form = $this->createForm(new CityFormType(), $city);
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if (true === $form->isValid()) {
                $dm = $this->get('doctrine.odm.mongodb.document_manager');

                $city = $form->getData();

                $dm->persist($city);
                $dm->flush();

                if ($id) {
                    $this->get('session')->getFlashBag()->set('success', 'City updated.');
                } else {
                    $this->get('session')->getFlashBag()->set('success', 'City created.');
                }

                return new RedirectResponse($this->get('request')->headers->get('referer'), 301);
            }
        }

        return array(
            'citys' => $citys,
            'city' => $city,
            'paginator' => $paginator,
            'form' => $form->createView()
        );
    }
    /**
     * Edit one City, show the edit form.
     *
     * @Route("/edit/{id}", name="avro_location_city_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $city = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('AvroLocationBundle:City')
            ->find($id);

        if (!$city) {
            throw $this->createNotFoundException('No city found');
        }

        $form = $this->createForm(new CityFormType(), $city);

        $formAction = $this->generateUrl('avro_location_city_list').'?id='.$id;

        parse_str(parse_url($this->get('request')->headers->get('referer'), PHP_URL_QUERY), $params);
        foreach($params as $k => $v) {
            if (!empty($v)) {
                $formAction = $formAction.'&'.$k.'='.$v;
            }
        }

        return array(
            'form' => $form->createView(),
            'formAction' => $formAction,
            'city' => $city
        );
    }

    /**
     * Delete oneCity.
     *
     * @Route("/delete/{id}", name="avro_location_city_delete")
     */
    public function deleteAction($id)
    {
        $city = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('AvroLocationBundle:City')
            ->find($id);

        $city->setIsDeleted(true);

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->persist($city);
        $dm->flush();

        $this->container->get('session')->getFlashBag()->set('success', 'City deleted.');

        $uri = $this->get('request')->headers->get('referer');

        return new RedirectResponse($uri);
    }

    /**
     * Restore one City.
     *
     * @Route("/restore/{id}", name="avro_location_city_restore")
     */
    public function restoreAction($id)
    {
        $city = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('AvroLocationBundle:City')
            ->find($id);

        $city->setIsDeleted(false);
        $city->setDeletedAt(null);

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->persist($city);
        $dm->flush();

        $this->container->get('session')->getFlashBag()->set('success', 'City restored.');

        $uri = $this->get('request')->headers->get('referer');

        return new RedirectResponse($uri);
    }

}
