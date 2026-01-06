<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace Systopia\CiviProxy\Plugin\Logger;

interface LoggerInterface {

  /**
   * Writes data to the log file.
   * 
   * @param Data $data
   * @return bool
   *   Return true when data is sucessfully written
   */
  public function writeToLog(Data $data): bool;

  /**
   * Reads data from the log and discards the data after it is been read.
   */
  public function readLog(): array;

}