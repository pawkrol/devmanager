<?php
/**
 * Created by PhpStorm.
 * User: pawkrol
 * Date: 10/30/16
 * Time: 1:47 PM
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ProjectRepository")
 * @ORM\Table(name="projects")
 */
class Project implements \JsonSerializable
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

//    /**
//     * @ORM\ManyToOne(targetEntity="User")
//     * @ORM\JoinColumn(name="admin_id", referencedColumnName="id")
//     */
//    private $adminId;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_start;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_end;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_create;

    /**
     * @ORM\Column(type="string", length=256)
     */
    private $github_repo;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $github_owner;


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

//    /**
//     * Set adminId
//     *
//     * @param integer $adminId
//     *
//     * @return Project
//     */
//    public function setAdminId($adminId)
//    {
//        $this->adminId = $adminId;
//
//        return $this;
//    }
//
//    /**
//     * Get adminId
//     *
//     * @return integer
//     */
//    public function getAdminId()
//    {
//        return $this->adminId;
//    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Project
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Project
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set dateStart
     *
     * @param \DateTime $dateStart
     *
     * @return Project
     */
    public function setDateStart($dateStart)
    {
        $this->date_start = $dateStart;

        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime
     */
    public function getDateStart()
    {
        return $this->date_start;
    }

    /**
     * Set dateEnd
     *
     * @param \DateTime $dateEnd
     *
     * @return Project
     */
    public function setDateEnd($dateEnd)
    {
        $this->date_end = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd
     *
     * @return \DateTime
     */
    public function getDateEnd()
    {
        return $this->date_end;
    }

    /**
     * Set dateCreate
     *
     * @param \DateTime $dateCreate
     *
     * @return Project
     */
    public function setDateCreate($dateCreate)
    {
        $this->date_create = $dateCreate;

        return $this;
    }

    /**
     * Get dateCreate
     *
     * @return \DateTime
     */
    public function getDateCreate()
    {
        return $this->date_create;
    }

    /**
     * Set githubRepo
     *
     * @param string $githubRepo
     *
     * @return Project
     */
    public function setGithubRepo($githubRepo)
    {
        $this->github_repo = $githubRepo;

        return $this;
    }

    /**
     * Get githubRepo
     *
     * @return string
     */
    public function getGithubRepo()
    {
        return $this->github_repo;
    }

    /**
     * Set githubOwner
     *
     * @param string $githubOwner
     *
     * @return Project
     */
    public function setGithubOwner($githubOwner)
    {
        $this->github_owner = $githubOwner;

        return $this;
    }

    /**
     * Get githubOwner
     *
     * @return string
     */
    public function getGithubOwner()
    {
        return $this->github_owner;
    }


    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return [
                'id' => $this->id,
                'name' => $this->name,
                'description' => $this->description,
                'date_start' => $this->date_start,
                'date_end' => $this->date_end,
                'date_create' => $this->date_create,
                'github_owner' => $this->github_owner,
                'github_repo' => $this->github_repo
            ];
    }
}
