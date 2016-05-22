<?php
namespace AppBundle\Service;

use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;

use Symfony\Component\Security\Acl\Dbal\MutableAclProvider;

use FfjvBoBundle\Entity\User;

/**
 * Created by PhpStorm.
 * User: raulnet
 * Date: 22/05/16
 * Time: 20:13
 */
class AclService
{
    /**
     * @var array
     */
    private $permission = [
        'VIEW'      => 'view',
        'EDIT'      => 'edit',
        'CREATE'    => 'create',
        'DELETE'    => 'delete',
        'UNDELETE'  => 'undelete',
        'OPERATOR'  => 'operator',
        'MASTER'    => 'MASTER',
        'OWNER'     => 'owner'
    ];

    /**
     * @var MutableAclProvider
     */
    private $aclProvider;

    /**
     * AclService constructor.
     * @param MutableAclProvider $aclProvider
     */
    public function __construct(MutableAclProvider $aclProvider)
    {
        $this->aclProvider = $aclProvider;
    }

    /**
     * @param $entity
     * @param User $user
     * @param array $roles
     * @return bool
     * @throws \Exception
     */
    public  function setAcl($entity, User $user, array $roles = array()){

        if(!is_array($roles)){
            throw new \Exception('Role Undefined');
        }

        $securityIdentity = UserSecurityIdentity::fromAccount($user);
        $objectIdentity = ObjectIdentity::fromDomainObject($entity);
        $builder = new MaskBuilder();
        foreach ($roles as $role){
            if(!array_key_exists($role, $this->permission )){
                throw new \Exception('Role submit not exist');
            }
            $builder->add($this->permission[$role]);
        }
        $mask = $builder->get();

        $objectAce = false;
        try{
            $acl = $this->aclProvider->findAcl($objectIdentity, [$securityIdentity]);
            $objectAce = $acl->getObjectAces();
        } catch (\Exception $e){
            $acl = $this->aclProvider->createAcl($objectIdentity);

        }

        if($objectAce){
            $acl->updateObjectAce(0, $mask);
        } else {
            $acl->insertObjectAce($securityIdentity, $mask);
        }

        $this->aclProvider->updateAcl($acl);
        return true;
    }






}