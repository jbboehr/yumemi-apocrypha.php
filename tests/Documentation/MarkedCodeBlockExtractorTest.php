<?php

/**
 * +--------------------------------------------------------------------------------------------------------------+
 * |        *                 .                         *                  .                         *            |
 * |   .              *                      .                    *                      .                        |
 * |             .                 .                  *                         .                 *               |
 * -      *                    .             *                    .                         .                     -
 *
 *                          Yumemi Apocrypha『夢見外典』〜ＹＵＭＥＭＩ　ＡＰＯＣＲＹＰＨＡ〜
 *
 * -                                          .----------------.                                                  -
 * |                                      .--'        __        '--.                                              |
 * |                                  .--'          .'  '.          '--.                                          |
 * |                             .---'            .'      '.            '---.                                     |
 * +--------------------------------------------------------------------------------------------------------------+
 *
 * Copyright (c) anno Domini nostri Jesu Christi MMXXVI, John Boehr & contributors
 *
 * SPDX-License-Identifier: AGPL-3.0-only WITH romic-exception
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License version 3,
 * as published by the Free Software Foundation, together with the Romic
 * Exception (an additional permission under section 7 of that license).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * and the Romic Exception along with this program.  If not, see
 * <http://www.gnu.org/licenses/> and the LICENSE_EXCEPTION file.
 */

declare(strict_types=1);

namespace jbboehr\Yumemi\Apocrypha\Tests\Documentation;

use PHPUnit\Framework\TestCase;

final class MarkedCodeBlockExtractorTest extends TestCase
{
    public function testExtractsExactlyOneIdentifiedPhpFence(): void
    {
        $file = $this->temporaryMarkdown(<<<'MARKDOWN'
# Example

<!-- yumemi-example: selected-example -->

```php
<?php

echo 'selected';
```

```php
<?php

echo 'unselected';
```
MARKDOWN);

        self::assertSame(
            "<?php\n\necho 'selected';\n",
            MarkedCodeBlockExtractor::extract($file, 'selected-example'),
        );
    }

    public function testRejectsMissingExample(): void
    {
        $file = $this->temporaryMarkdown('# Example');

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Expected exactly one documentation example missing-example');

        MarkedCodeBlockExtractor::extract($file, 'missing-example');
    }

    public function testRejectsInvalidIdentifier(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid documentation example identifier');

        MarkedCodeBlockExtractor::extract(__FILE__, 'Invalid Identifier');
    }

    private function temporaryMarkdown(string $contents): string
    {
        $file = tempnam(sys_get_temp_dir(), 'yumemi-apocrypha-markdown-');
        self::assertIsString($file);
        self::assertNotFalse(file_put_contents($file, $contents));

        $this->files[] = $file;

        return $file;
    }

    /** @var list<string> */
    private array $files = [];

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            unlink($file);
        }
    }
}
