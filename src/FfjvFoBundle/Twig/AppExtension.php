<?php
namespace FfjvFoBundle\Twig;
/**
 * Created by PhpStorm.
 * User: laurent
 * Date: 17/02/16
 * Time: 22:50
 */
use FfjvBoBundle\Entity\UserHasClubs;
use FfjvBoBundle\Entity\UserHasTeams;

class AppExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('rolesMember', array($this, 'rolesMember')),
            new \Twig_SimpleFilter('rolesTeamMember', array($this, 'rolesTeamMember')),
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
        $roles['ROLE_USER'] = 'enregistrer';
        $roles['ROLES_REQUEST_TO_JOIN'] = 'postulant';
        return$roles[$role];
    }
    /**
     * @param string $role
     * @return mixed
     */
    public function rolesTeamMember($role = '')
    {
        $roles = UserHasTeams::$listRoles;
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