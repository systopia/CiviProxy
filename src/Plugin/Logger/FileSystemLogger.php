<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace Systopia\CiviProxy\Plugin\Logger;

class FileSystemLogger implements LoggerInterface {

  /**
   * @var String
   */
  protected $directory;

  /**
   * @var String
   */
  protected $archiveDirectory;

  /**
   * @var int
   */
  protected $archiveExpireTime;

  /**
   * var bool
   */
  protected $rotationEnabled = false;

  /**
   * @var int
   */
  protected $maxCallsPerFile = 0;

  /**
   * @var int
   */
  protected $maxTimePerFile = 0;


  public function __construct(array $configuration) {
    $this->directory = $configuration['directory'];
    $this->archiveDirectory = $configuration['archive'];
    $this->archiveExpireTime = 0;
    if (!empty($configuration['keep_archive'])) {
      $this->archiveExpireTime = time() - ($configuration['keep_archive'] * 24 * 60 * 60);
    }
    if (!file_exists($this->directory)) {
      mkdir($this->directory);
    }
    if ($this->archiveDirectory && !file_exists($this->archiveDirectory)) {
      mkdir($this->archiveDirectory);
    }

    if (!empty($configuration['rotation']['enabled'])) {
      $this->rotationEnabled = true;
      if (array_key_exists('max_calls_per_file', $configuration['rotation'])) {
        $this->maxCallsPerFile = $configuration['rotation']['max_calls_per_file'];
      }
      if (array_key_exists('max_time_per_file', $configuration['rotation'])) {
        $this->maxTimePerFile = $configuration['rotation']['max_time_per_file'];
      }
    }
  }

  public function __destruct()
  {
    $this->cleanArchive();
  }

  /**
   * Writes data to the log file.
   * 
   * @param Data $data
   * @return bool
   *   Return true when data is sucessfully written
   */
  public function writeToLog(Data $data): bool {
    $json = json_encode($data->toArray(),JSON_PRETTY_PRINT);
    $rotation = $this->getCurrentRotationInfo();
    $fh = fopen($this->directory . DIRECTORY_SEPARATOR . $rotation['file'] .'.log', 'a+');
    if (!$fh) {
      return false;
    }
    $stat = fstat($fh);
    if ($rotation['calls'] == 0 || empty($stat['size'])) {
      $json = '[' . $json  . ']';
    } else {
      $json = ',' . $json . ']';
      // Remove the last ] from the file. We add this when we will write.
      ftruncate($fh, $stat['size']-1);
    }
    $rotation['calls'] ++;
    fwrite($fh, $json);
    $this->writeRotationInfo($rotation);
    fclose($fh);
    return true;
  }

  /**
   * Reads data from the log and discards the data after it is been read.
   */
  public function readLog(): array {
    $archiveDirExists = false;
    if ($this->archiveDirectory && file_exists($this->archiveDirectory) && is_dir($this->archiveDirectory)) {
      $archiveDirExists = true;
    }
    $files = glob($this->directory . DIRECTORY_SEPARATOR . '*.log');
    usort($files, function($fileA, $fileB) {
      return filemtime($fileA) - filemtime($fileB);
    });
    $return = [];
    foreach($files as $file) {
      $contents = file_get_contents($file);
      if (substr($contents, -1) == ',') {
        $contents = substr($contents, 0, -1);
      }
      if (substr($contents, 0, 1) !== '[' && substr($contents, -1, 1) !== ']') {
        $contents = '[' . $contents . ']';
      }
      $data = json_decode($contents, TRUE);
      $return = array_merge($return, $data);

      // Archive the log file. 
      if ($archiveDirExists) {
        $baseName = substr(pathinfo($file, PATHINFO_BASENAME), 0, -4);
        if (file_exists($this->archiveDirectory . DIRECTORY_SEPARATOR . $baseName . '.log')) {
          [$baseName, $i] = explode('-', $baseName);
          do  {
            $i++;
          } while(file_exists($this->archiveDirectory . DIRECTORY_SEPARATOR . $baseName . '-'.$i.'.log'));
          $baseName .= '-' . $i;
        }
        rename($file, $this->archiveDirectory . DIRECTORY_SEPARATOR . $baseName . '.log');
      } else {
        unlink($file);
      }
    }
    return $return;
  }

  /**
   * Returns info about the current rotation
   * Such as the file name to use and how many calss there are.
   */
  protected function getCurrentRotationInfo(): array {
    $rotation['tstamp'] = time();
    $rotation['calls'] = 0;
    $useExistingFile = false;
    if ($this->rotationEnabled) {
      if (file_exists($this->directory . DIRECTORY_SEPARATOR . 'log.json')) {
        $rotation = file_get_contents($this->directory . DIRECTORY_SEPARATOR . 'log.json');
        $rotation = json_decode($rotation, TRUE);
        $useExistingFile = true;
        if (!is_array($rotation) || empty($rotation)) {
          $useExistingFile = false;
        }
      }
      $minTstamp = time() - $this->maxTimePerFile;
      if ($rotation['calls'] >= $this->maxCallsPerFile || $rotation['tstamp'] < $minTstamp) {
        $useExistingFile = false;
      }
    }
    if (!$useExistingFile) {
      $filename = date('YmdHis');
      $i = 1;
      while(file_exists($this->directory . DIRECTORY_SEPARATOR . $filename . '-'.$i.'.log')) {
        $i++;
      }
      $rotation['calls'] = 0;
      $rotation['tstamp'] = time();
      $rotation['file'] = $filename . '-'.$i;
    }
    return $rotation;
  }

  protected function writeRotationInfo(array $rotation) {
    if ($this->rotationEnabled) {
      file_put_contents($this->directory . DIRECTORY_SEPARATOR . 'log.json', json_encode($rotation, JSON_PRETTY_PRINT));
    }
  }

  /**
   * Cleans the archive directory.
   * 
   * Only remove the files which are expired.
   * There is a setting how long to keep the archive.
   */
  protected function cleanArchive() {
    $archiveDirExists = false;
    if ($this->archiveDirectory && file_exists($this->archiveDirectory) && is_dir($this->archiveDirectory)) {
      $archiveDirExists = true;
    }
    if ($archiveDirExists && $this->archiveExpireTime) {
      $files = glob($this->archiveDirectory . DIRECTORY_SEPARATOR . '*.log');
      usort($files, function($fileA, $fileB) {
        return filemtime($fileA) - filemtime($fileB);
      });
      foreach($files as $file) {
        if (filemtime($file) > $this->archiveExpireTime) {
          break;
        }
        unlink($file);
      }
    }
  }

}