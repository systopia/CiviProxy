<?php
/**
 * Copyright (C) 2021  Jaap Jansma (jaap.jansma@civicoop.org)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

class CRM_Civiproxy_Mosaico {

  /**
   * @var CRM_Civiproxy_Mosaico
   */
  private static $singleton;

  /**
   * @var String
   */
  private $mosiacoExtenionUrl;

  /**
   * @var bool
   */
  private $isMosaicoInstalled = false;

  private function __construct() {
    try {
      $mosaicoExt = civicrm_api3('Extension', 'getsingle', ['full_name' => "uk.co.vedaconsulting.mosaico"]);
      $this->isMosaicoInstalled = true;
      $this->mosiacoExtenionUrl = CRM_Mosaico_ExtensionUtil::url();
    } catch (\Exception $ex) {
      // Do nothing
    }
  }

  /**
   * @return CRM_Civiproxy_Mosaico
   */
  public static function singleton() {
    if (!self::$singleton) {
      self::$singleton = new CRM_Civiproxy_Mosaico();
    }
    return self::$singleton;
  }

  /**
   * @return bool
   */
  public function isMosaicoInstalled() {
    return $this->isMosaicoInstalled;
  }

  /**
   * @return string
   */
  public function getMosaicoExtensionUrl() {
    return $this->mosiacoExtenionUrl;
  }

}