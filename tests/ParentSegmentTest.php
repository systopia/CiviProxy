<?php

declare(strict_types=1);

namespace Systopia\CiviProxy\Tests;

use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

#[CoversFunction('civiproxy_has_parent_segment')]
final class ParentSegmentTest extends TestCase {

  /**
   * @return array<string, array{string}>
   */
  public static function provideTraversalPaths(): array {
    return [
      'leading parent' => ['../secret'],
      'parent inside path' => ['a/../../b.png'],
      'parent only' => ['..'],
      'trailing parent' => ['a/..'],
      'encoded dots' => ['%2e%2e/secret'],
      'mixed encoded dots' => ['.%2E/secret'],
      'encoded slash' => ['..%2Fsecret'],
      'encoded question mark before parent' => ['a%3F/../secret'],
      'backslash' => ['..\\secret'],
    ];
  }

  /**
   * @return array<string, array{string}>
   */
  public static function provideRegularPaths(): array {
    return [
      'plain file name' => ['plain.png'],
      'space' => ['My Image.png'],
      'subdirectories' => ['contribute/images/uploads/x.png'],
      'dots inside file name' => ['bild..png'],
      'dots inside directory name' => ['v1..2/x.png'],
      'hidden file' => ['.hidden.png'],
      'current directory' => ['./a.png'],
      'current directory inside path' => ['a/./b.png'],
      'parent in query string' => ['images/x.png?v=../1'],
      'double encoded dots' => ['%252e%252e/secret'],
    ];
  }

  #[DataProvider('provideTraversalPaths')]
  public function testDetectsParentSegment(string $path): void {
    self::assertTrue(civiproxy_has_parent_segment($path));
  }

  #[DataProvider('provideRegularPaths')]
  public function testAcceptsRegularPath(string $path): void {
    self::assertFalse(civiproxy_has_parent_segment($path));
  }

}
