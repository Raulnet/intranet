<?php
namespace Ffjv\FoBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class HomeController extends Controller
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('@FfjvFo/Home/index.html.twig', array());
    }

    /**
     *
     * @return JsonResponse
     */
    public function getColorsMapAction()
    {
        $data = array(
            'players' => $this->getColorsByType($this->get('user')->getCountByIdZipCode()),
            'clubs'   => $this->getColorsByType($this->get('clubs')->getCountByIdZipCode()),
        );

        return new JsonResponse($data);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function findClubsAction(Request $request){

        $regionId = $request->get('regionId');
        $clubs = $this->getDoctrine()->getRepository('FfjvBoBundle:Clubs')->findBy(array('idZipCode' => $regionId));
        $listClubs = array();
        foreach($clubs as $club){
            $listClubs[] = array(
                'id' => $club->getId(),
                'title' => $club->getTitle(),
                'url'   => $this->generateUrl('fo_clubs_show', array('clubId' => $club->getId()))
            );
        }
        return new JsonResponse($listClubs);
    }

    /* ****** PRIVATE ***************************************** */

    /**
     * @param array $counts
     *
     * @return array
     */
    private function getColorsByType(array $counts)
    {
        $colors = array();
        foreach ($counts as $count) {
            $colors[$count['idZipCode']] = $this->getColorByCount($count['count_zipcode']);
        }

        return $colors;

    }

    /**
     * @param int $count
     *
     * @return string
     */
    private function getColorByCount($count)
    {
        if ($count >= 1000) {
            return "#FF1000";
        }
        if ($count >= 500) {
            return "#FF7000";
        }
        if ($count >= 1) {
            return "#FFC000";
        }

        return "#FFFFFF";
    }
}
