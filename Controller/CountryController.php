<?php

namespace Avro\LocationBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Avro\UserBundle\Document\Country;
use Avro\UserBundle\Form\Type\CountryFormType;

/**
 * Country controller.
 *
 * @author Joris de Wit <joris.w.dewit@gmail.com>
 */
class CountryController extends Controller
{
    /**
     * List Countrys.
     *
     * @Template()
     */
    public function listAction()
    {
        $paginator = $this->get('avro_core.paginator');
        $paginator->setClass('AvroLocationBundle:Country');
        $countrys = $paginator->getResults();

        $request = $this->get('request');

        $id = $request->query->get('id');
        if ($id) {
            $country = $this->get('doctrine.odm.mongodb.document_manager')
                ->getRepository('AvroLocationBundle:Country')
                ->find($id);

            if (!$country) {
                throw $this->createNotFoundException('No country found');
            }
        } else {
            $country = new Country();
        }

        $form = $this->createForm(new CountryFormType(), $country);
        if ($request->getMethod() == 'POST') {
            $form->bindRequest($request);
            if (true === $form->isValid()) {
                $dm = $this->get('doctrine.odm.mongodb.document_manager');

                $country = $form->getData();

                $dm->persist($country);
                $dm->flush();

                if ($id) {
                    $this->get('session')->getFlashBag()->set('success', 'Country updated.');
                } else {
                    $this->get('session')->getFlashBag()->set('success', 'Country created.');
                }

                return new RedirectResponse($this->get('request')->headers->get('referer'), 301);
            }
        }

        return array(
            'countrys' => $countrys,
            'country' => $country,
            'paginator' => $paginator,
            'form' => $form->createView()
        );
    }
    /**
     * Edit one Country, show the edit form.
     *
     * @Template()
     */
    public function editAction($id)
    {
        $country = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('AvroLocationBundle:Country')
            ->find($id);

        if (!$country) {
            throw $this->createNotFoundException('No country found');
        }

        $form = $this->createForm(new CountryFormType(), $country);

        $formAction = $this->generateUrl('avro_location_country_list').'?id='.$id;

        parse_str(parse_url($this->get('request')->headers->get('referer'), PHP_URL_QUERY), $params);
        foreach($params as $k => $v) {
            if (!empty($v)) {
                $formAction = $formAction.'&'.$k.'='.$v;
            }
        }

        return array(
            'form' => $form->createView(),
            'formAction' => $formAction,
            'country' => $country
        );
    }

    /**
     * Delete oneCountry.
     *
     */
    public function deleteAction($id)
    {
        $country = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('AvroLocationBundle:Country')
            ->find($id);

        $country->setIsDeleted(true);

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->persist($country);
        $dm->flush();

        $this->container->get('session')->getFlashBag()->set('success', 'Country deleted.');

        $uri = $this->get('request')->headers->get('referer');

        return new RedirectResponse($uri);
    }

    /**
     * Restore one Country.
     *
     */
    public function restoreAction($id)
    {
        $country = $this->get('doctrine.odm.mongodb.document_manager')
            ->getRepository('AvroLocationBundle:Country')
            ->find($id);

        $country->setIsDeleted(false);
        $country->setDeletedAt(null);

        $dm = $this->get('doctrine.odm.mongodb.document_manager');
        $dm->persist($country);
        $dm->flush();

        $this->container->get('session')->getFlashBag()->set('success', 'Country restored.');

        $uri = $this->get('request')->headers->get('referer');

        return new RedirectResponse($uri);
    }

}
