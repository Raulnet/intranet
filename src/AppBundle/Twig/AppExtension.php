<?php
namespace AppBundle\Twig;

/**
 * Created by PhpStorm.
 * User: raulnet
 * Date: 22/05/16
 * Time: 18:28
 */

use FfjvBoBundle\Entity\UserHasClubs;
use FfjvBoBundle\Entity\UserHasTeams;

class AppExtension extends \Twig_Extension
{
    /**
     * @return array
     */
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
        $roles['auteur'] = 'ROLE_AUTHOR';
        $roles['enregistrer'] = 'ROLE_USER';
        $roles['postulant'] = 'ROLES_REQUEST_TO_JOIN';
        return array_keys($roles, $role)[0];
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

    public function getName()
    {
        return 'app_extension';
    }
}