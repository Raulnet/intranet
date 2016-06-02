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
    const MASK_UNDEFINED = 0;

    /**
     * @return array
     */
    public static function getMaskList(){
        return [
            'VIEW'      => MaskBuilder::MASK_VIEW,
            'EDIT'      => MaskBuilder::MASK_EDIT,
//            'CREATE'    => MaskBuilder::MASK_CREATE,
            'DELETE'    => MaskBuilder::MASK_DELETE,
//            'UNDELETE'  => MaskBuilder::MASK_UNDELETE,
            'OPERATOR'  => MaskBuilder::MASK_OPERATOR,
            'MASTER'    => MaskBuilder::MASK_MASTER,
            'OWNER'     => MaskBuilder::MASK_OWNER
        ];
    }
    
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
        $mask = 0;
        foreach ($roles as $role){
            if(!in_array($role, $this->getMaskList() )){
                throw new \Exception('Role submit not exist');
            }
            if($role > $mask){
                $mask = $role;
            }
        }

        try{
            $acl = $this->aclProvider->findAcl($objectIdentity);
            $objectAce = $acl->getObjectAces();
            foreach ($objectAce as $key => $ace){
                if($ace->getSecurityIdentity() == $securityIdentity){
                    if($ace->getMask() < MaskBuilder::MASK_OWNER) { // don't remove OWNER ROLE
                        $acl->deleteObjectAce($key);
                        $this->aclProvider->updateAcl($acl);
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        } catch (\Exception $e){
            $acl = $this->aclProvider->createAcl($objectIdentity);
        }

        $acl->insertObjectAce($securityIdentity, $mask);
        $this->aclProvider->updateAcl($acl);
        return true;
    }

    /**
     * @param $entity
     * @param User $user
     * @return int
     */
    public function getPermission($entity, User $user){
        $securityIdentity = UserSecurityIdentity::fromAccount($user);
        $objectIdentity = ObjectIdentity::fromDomainObject($entity);
        try{
            $acl = $this->aclProvider->findAcl($objectIdentity);
            $objectAce = $acl->getObjectAces();
            foreach ($objectAce as $key => $ace){
                if($ace->getSecurityIdentity() == $securityIdentity){
                    return $ace->getMask() ;
                }
            }
            return self::MASK_UNDEFINED;
        } catch (\Exception $e){
            return self::MASK_UNDEFINED;
        }
        return self::MASK_UNDEFINED;
    }

}