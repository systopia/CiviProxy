<?php

declare(strict_types=1);

namespace Systopia\CiviProxy\Tests;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversFunction('civiproxy_encode_url_path')]
final class FileUrlEncodingTest extends TestCase {

  /**
   * @return array<string, array{string, string}>
   */
  public static function provideUrlPaths(): array {
    return [
      'plain file name' => ['plain.png', 'plain.png'],
      'space' => ['My Image.png', 'My%20Image.png'],
      'already encoded space' => ['My%20Image.png', 'My%20Image.png'],
      'umlaut and parentheses' => ['Ä(1).png', '%C3%84%281%29.png'],
      'subdirectories' => ['contribute/images/uploads/My Image.png', 'contribute/images/uploads/My%20Image.png'],
      'space in directory' => ['sub dir/a b.png', 'sub%20dir/a%20b.png'],
      'literal plus' => ['a+b.png', 'a%2Bb.png'],
      'literal percent' => ['50%.png', '50%25.png'],
      'query string is kept' => ['images/a b.png?v=123&w=1', 'images/a%20b.png?v=123&w=1'],
      'leading and double slashes are kept' => ['/leading//double.png', '/leading//double.png'],
      'empty path' => ['', ''],
    ];
  }

  #[DataProvider('provideUrlPaths')]
  public function testEncodesUrlPath(string $path, string $expected): void {
    self::assertSame($expected, civiproxy_encode_url_path($path));
  }

}
