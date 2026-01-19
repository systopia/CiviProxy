<?php
/*--------------------------------------------------------+
| SYSTOPIA CiviProxy                                      |
|  a simple proxy solution for external access to CiviCRM |
| Copyright (C) 2015-2021 SYSTOPIA                        |
| Author: Jaap Jansma (jaap.jansma@civicoop.org           |
| http://www.systopia.de/                                 |
+---------------------------------------------------------*/

namespace Systopia\CiviProxy\JWT;

use Lcobucci\JWT\Signer\Hmac;

final class Sha256 extends Hmac {

  public function algorithmId(): string {
    return 'HS256';
  }

  public function algorithm(): string {
    return 'sha256';
  }

  public function minimumBitsLengthForKey(): int {
    return 4;
  }

}
