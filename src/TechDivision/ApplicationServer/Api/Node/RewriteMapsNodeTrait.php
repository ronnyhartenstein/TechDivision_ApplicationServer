<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Johann Zelger <jz@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\ApplicationServer\Api\Node;

/**
 * TechDivision\ApplicationServer\Api\Node\RewriteMapsNodeTrait
 *
 * Abstract node that serves nodes having a rewriteMaps/rewriteMap child.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Johann Zelger <jz@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
trait RewriteMapsNodeTrait
{
    /**
     * The rewriteMaps definitions
     *
     * @var array
     * @AS\Mapping(nodeName="rewriteMaps/rewriteMap", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\RewriteMapNode")
     */
    protected $rewriteMaps = array();

    /**
     * Will return rewriteMaps definitions
     *
     * @return array
     */
    public function getRewriteMaps()
    {
        return $this->rewriteMaps;
    }

    /**
     * Returns the rewriteMaps as an associative array.
     *
     * @return array The array with the rewriteMaps
     */
    public function getRewriteMapsAsArray()
    {
        // Iterate over the rewriteMaps nodes and sort them into an array
        $rewriteMaps = array();
        foreach ($this->getRewriteMaps() as $rewriteMapNode) {
            // Restructure to an array
            $rewriteMaps[$rewriteMapNode->getType()] = array(
                'params' => $rewriteMapNode->getParamsAsArray()
            );
        }
        // Return what we got
        return $rewriteMaps;
    }
}
