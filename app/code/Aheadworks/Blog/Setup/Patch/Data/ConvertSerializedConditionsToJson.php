<?php
namespace Aheadworks\Blog\Setup\Patch\Data;

use Aheadworks\Blog\Api\Data\PostInterface;
use Aheadworks\Blog\Model\Serialize\Factory as SerializeFactory;
use Magento\Framework\Serialize\Serializer\Serialize;
use Magento\Framework\Serialize\Serializer\SerializeFactory as PhpSerializeFactory;
use Aheadworks\Blog\Model\Serialize\SerializeInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

/**
 * Class ConvertSerializedConditionsToJson
 */
class ConvertSerializedConditionsToJson implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var SerializeInterface
     */
    private $serializer;

    /**
     * @var Serialize
     */
    private $phpSerializer;

    /**
     * ConvertSerializedConditionsToJson constructor.
     * @param ModuleDataSetupInterface $moduleDataSetup
     * @param PhpSerializeFactory $phpSerializerFactory
     * @param SerializeFactory $serializeFactory
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        PhpSerializeFactory $phpSerializerFactory,
        SerializeFactory $serializeFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->serializer = $serializeFactory->create();
        $this->phpSerializer = $phpSerializerFactory->create();
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $connection = $this->moduleDataSetup->getConnection();
        $table = $this->moduleDataSetup->getTable('aw_blog_post');
        $select = $connection->select()->from(
            $table,
            [
                PostInterface::ID,
                PostInterface::PRODUCT_CONDITION
            ]
        );
        $rulesConditions = $connection->fetchAssoc($select);
        foreach ($rulesConditions as $ruleConditions) {
            $unserializeCond = $this->unserialize($ruleConditions[PostInterface::PRODUCT_CONDITION]);
            if ($unserializeCond !== false) {
                $ruleConditions[PostInterface::PRODUCT_CONDITION] = empty($unserializeCond)
                    ? ''
                    : $this->serializer->serialize($unserializeCond);

                $connection->update(
                    $table,
                    [
                        PostInterface::PRODUCT_CONDITION => $ruleConditions[PostInterface::PRODUCT_CONDITION]
                    ],
                    PostInterface::ID . ' = ' . $ruleConditions[PostInterface::ID]
                );
            }
        }
        $this->moduleDataSetup->getConnection()->endSetup();
    }

    /**
     * Unserialize string
     *
     * @param string $string
     * @return array|bool
     */
    private function unserialize($string)
    {
        $result = '';

        if ($string === 'b:0;') {
            return false;
        }
        if (!empty($string)) {
            try {
                $result = $this->phpSerializer->unserialize($string);
            } catch (\InvalidArgumentException $e) {
                $result = false;
            }
        }
        return $result;
    }

    /**
     * @inheritDoc
     */
    public static function getVersion()
    {
        return '2.4.6';
    }
}
