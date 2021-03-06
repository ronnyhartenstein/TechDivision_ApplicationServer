<?php

/**
 * TechDivision\ApplicationServer\GenericDeployment
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use TechDivision\PBC\Config;
use TechDivision\Storage\StackableStorage;
use TechDivision\Application\Application;
use TechDivision\Application\Interfaces\ContextInterface;
use TechDivision\ApplicationServer\AbstractDeployment;
use TechDivision\ApplicationServer\Interfaces\ContainerInterface;
use TechDivision\ApplicationServer\Utilities\DirectoryKeys;
use TechDivision\ApplicationServer\Api\Node\ContextNode;

/**
 * Specific deployment implementation for web applications.
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class GenericDeployment extends AbstractDeployment
{

    /**
     * Initializes the available applications and adds them to the deployment instance.
     *
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerInterface $container The container we want to add the applications to
     *
     * @return void
     */
    public function deploy(ContainerInterface $container)
    {

        // load the context instances for this container
        $contextInstances = $this->getDeploymentService()->loadContextInstancesByContainer($container);

        // gather all the deployed web applications
        foreach (new \FilesystemIterator($container->getAppBase()) as $folder) {

            if ($folder->isDir()) { // check if we've a directory (possible application)

                // try to load a context configuration for the context path
                $context = $contextInstances[$contextPath = '/'. $folder->getBasename()];

                // create a new application instance
                $application = $this->newInstance($context->getType());

                // initialize the generic instances and information
                $application->injectName($folder->getBasename());
                $application->injectInitialContext($this->getInitialContext());
                $application->injectBaseDirectory($container->getBaseDirectory());
                $application->injectTmpDir($container->getTmpDir($contextPath));
                $application->injectAppBase($container->getAppBase());

                // create the applications temporary folders and cleans the folders up
                $this->getDeploymentService()->createTmpFolders($application);
                $this->getDeploymentService()->cleanUpFolders($application);

                // add the default class loaders
                $application->addClassLoader($this->getInitialContext()->getClassLoader());

                // add the configured class loaders
                foreach ($context->getClassLoaders() as $classLoader) {
                    $classLoaderType = $classLoader->getType();
                    $classLoaderType::visit($application, $classLoader);
                }

                // add the configured managers
                foreach ($context->getManagers() as $manager) {
                    $managerType = $manager->getType();
                    $managerType::visit($application, $manager);
                }

                // add the application to the container
                $container->addApplication($application);
            }
        }
    }
}
