<?php
namespace Aheadworks\Blog\Model\Source\Post;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Post Status source model
 * @package Aheadworks\Blog\Model\Source\Post
 */
class Status extends AbstractSource implements \Magento\Framework\Option\ArrayInterface
{
    // Statuses to store in DB
    const DRAFT = 'draft';
    const PUBLICATION = 'publication';
    const SCHEDULED = 'scheduled';

    /**
     * @var array
     */
    private $options;

    /**
     * Array of the view
     * ['value' => 'label', 'value' => 'label', 'value' => 'label']
     *
     * @return array
     */
    public function getOptionsForPostForm()
    {
        return [
            self::DRAFT => __('Draft'),
            self::SCHEDULED => __('Scheduled'),
            self::PUBLICATION => __('Published')
        ];
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [];
            foreach ($this->getOptionsForPostForm() as $value => $label) {
                $this->options[] = ['value' => $value, 'label' => $label];
            }
        }
        return $this->options;
    }

    /**
     * Array of the view
     * [['value' => '', 'label' = ''], ['value' => '', 'label' = ''], ['value' => '', 'label' = '']]
     *
     * @return array
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
