<?php

/**
 * TechDivision\ApplicationServer\SocketThreadReceiver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\AbstractReceiver;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Johann Zelger <jz@techdivision.com>
 */
class SocketThreadReceiver extends AbstractReceiver {

    /**
     * @see TechDivision\ApplicationServer\Interfaces\ReceiverInterface::start()
     */
    public function start() {

        try {
            
            // load the socket instance
            /** @var \TechDivision\Socket\Client $socket */
            $socket = $this->newInstance('\TechDivision\Socket\Server');
            
            // prepare the main socket and listen
            $socket->setAddress($this->getAddress())
                   ->setPort($this->getPort())
                   ->start();

            try {
                // check if resource been initiated
                if ($socket->getResource()) {
                    // init worker number
                    $worker = 0;
                    // init workers array holder
                    $workers = array();
                    // open threads where accept connections
                    while ($worker++ < $this->getWorkerNumber()) {
                        // init thread
                        $workers[$worker] = $this->newWorker($socket->getResource());
                        // start thread async
                        $workers[$worker]->start();
                    }
                }
            } catch (\Exception $e) {
                error_log($e->__toString());
            }

        } catch (\Exception $ge) {
            
            error_log($ge->__toString());
            
            if (is_resource($socket->getResource())) {
                $socket->close();
            }
        } 
    }
}