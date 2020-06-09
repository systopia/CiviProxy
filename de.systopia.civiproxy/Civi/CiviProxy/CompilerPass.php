<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */
namespace Civi\CiviProxy;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class CompilerPass implements CompilerPassInterface {

  /**
   * You can modify the container here before it is dumped to PHP code.
   */
  public function process(ContainerBuilder $container) {
    if (!$container->hasDefinition('data_processor_factory')) {
      return;
    }
    $factoryDefinition = $container->getDefinition('data_processor_factory');
    $factoryDefinition->addMethodCall('addOutputHandler', array('civiproxy_file_field', 'Civi\CiviProxy\DataProcessor\FileFieldOutputHandler', ts('CiviProxy File download link')));
  }


}
