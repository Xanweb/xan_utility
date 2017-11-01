<?php
namespace XanUtility\Block;

use Concrete\Core\Block\BlockController as CoreBlockController;

abstract class BlockController extends CoreBlockController
{
    /**
     * {@inheritdoc}
     *
     * @see CoreBlockController::isValidControllerTask()
     */
    public function isValidControllerTask($method, $parameters = [])
    {
        $result = false;
        if (parent::isValidControllerTask($method, $parameters)) {
            $bID = array_pop($parameters);
            if (is_int($bID) || (is_string($bID) && is_numeric($bID))) {
                if ($this->bID == $bID) {
                    $result = true;
                }
            }
        }

        return $result;
    }

    /**
     * Check the data received when users edit/add a block instance.
     *
     * @param array $args
     *
     * @return \Concrete\Core\Error\Error|\Concrete\Core\Error\ErrorList\ErrorList|array
     */
    abstract protected function normalizeArgs($args);

    /**
     * {@inheritdoc}
     *
     * @see CoreBlockController::validate()
     */
    public function validate($args)
    {
        $check = $this->normalizeArgs($args);

        return is_array($check) ? true : $check;
    }

    /**
     * {@inheritdoc}
     *
     * @see CoreBlockController::save()
     */
    public function save($args)
    {
        $normalized = $this->normalizeArgs($args);
        if (!is_array($normalized)) {
            throw new Exception(implode("\n", $normalized->getList()));
        }
        parent::save($normalized);
    }
}
