<?php
/**
 * Created by PhpStorm.
 * User: pawkrol
 * Date: 10/30/16
 * Time: 3:37 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ProjectUserRepository")
 * @ORM\Table(name="project_user")
 */
class ProjectUser
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Project")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(name="project_role", type="string", length=32)
     */
    private $projectRole;

    /**
     * @ORM\Column(name="owner", type="boolean")
     */
    private $owner;


    /**
     * Set projectId
     *
     * @param integer $project
     *
     * @return ProjectUser
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get projectId
     *
     * @return integer
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set userId
     *
     * @param integer $user
     *
     * @return ProjectUser
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set projectRole
     *
     * @param string $projectRole
     *
     * @return ProjectUser
     */
    public function setProjectRole($projectRole)
    {
        $this->projectRole = $projectRole;

        return $this;
    }

    /**
     * Get projectRole
     *
     * @return string
     */
    public function getProjectRole()
    {
        return $this->projectRole;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set owner
     *
     * @param boolean $owner
     *
     * @return ProjectUser
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return boolean
     */
    public function getOwner()
    {
        return $this->owner;
    }
}
