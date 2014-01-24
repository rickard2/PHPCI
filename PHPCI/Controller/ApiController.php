<?php
namespace PHPCI\Controller;

use b8;
use PHPCI\Model\Build;
use PHPCI\Store\BuildStore;

class ApiController extends \PHPCI\Controller
{
    /**
     * @var BuildStore
     */
    protected $buildStore;

    public function init()
    {
        $this->buildStore = b8\Store\Factory::getStore('Build');
    }

    public function build($projectId, $key, $branch, $commit, $email)
    {
        $verification = $this->createVerification($projectId);

        if ($key !== $verification) {
            die('access denied');
        }

        $build = new Build();
        $build->setProjectId($projectId);
        $build->setStatus(Build::STATUS_NEW);
        $build->setBranch($branch);
        $build->setCommitId($commit);
        $build->setCommitterEmail($email);
        $build->setCreated(new \DateTime());

        $build = $this->buildStore->save($build);

        $response = array(
            'build' => array(
                'id' => $build->getId()
            )
        );

        echo json_encode($response);
        exit;
    }

    protected function createVerification($projectId)
    {
        if (!defined('API_KEY')) {
            return false;
        }

        return sha1( $projectId . API_KEY );
    }
}