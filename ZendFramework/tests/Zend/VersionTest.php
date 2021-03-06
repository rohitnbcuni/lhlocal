<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Version
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: VersionTest.php 8064 2008-02-16 10:58:39Z thomas $
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../TestHelper.php';

/**
 * @see Zend_Version
 */
require_once 'Zend/Version.php';

/**
 * @category   Zend
 * @package    Zend_Version
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_VersionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests that version_compare() and its "proxy"
     * Zend_Version::compareVersion() work as expected.
     */
    public function testVersionCompare()
    {
        $expect = -1;
        // unit test breaks if ZF version > 1.x
        for ($i=0; $i <= 1; $i++) {
            for ($j=0; $j < 10; $j++) {
                for ($k=0; $k < 20; $k++) {
                    foreach (array('PR', 'dev', 'alpha', 'beta', 'RC', 'RC1', 'RC2', 'RC3', '', 'pl') as $rel) {
                        $ver = "$i.$j.$k$rel";
                        if ($ver === Zend_Version::VERSION
                            || "$i.$j.$k-$rel" === Zend_Version::VERSION
                            || "$i.$j.$k.$rel" === Zend_Version::VERSION
                            || "$i.$j.$k $rel" === Zend_Version::VERSION) {

                            if ($expect != -1) {
                                $this->fail("Unexpected double match for Zend_Version::VERSION ("
                                    . Zend_Version::VERSION . ")");
                            }
                            else {
                                $expect = 1;
                            }
                        } else {
                            $this->assertSame(Zend_Version::compareVersion($ver), $expect,
                                "For version '$ver' and Zend_Version::VERSION = '"
                                . Zend_Version::VERSION . "': result=" . (Zend_Version::compareVersion($ver))
                                . ', but expected ' . $expect);
                        }
                    }
                }
            }
        }
        if ($expect === -1) {
            $this->fail('Unable to recognize Zend_Version::VERSION ('. Zend_Version::VERSION . ')');
        }
    }

}
