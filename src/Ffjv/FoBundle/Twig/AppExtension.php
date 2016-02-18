<?php
namespace Ffjv\FoBundle\Twig;
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 17/02/16
 * Time: 22:50
 */
use Ffjv\BoBundle\Entity\UserHasClubs;
class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('rolesMember', array($this, 'rolesMember')),
        );
    }

    /**
     * @param string $role
     * @return mixed
     */
    public function rolesMember($role = '')
    {
        $roles = UserHasClubs::$listRoles;
        $roles['ROLE_AUTHOR'] = 'auteur';
        $roles['ROLES_REQUEST_TO_JOIN'] = 'postulant';
        return$roles[$role];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }
}