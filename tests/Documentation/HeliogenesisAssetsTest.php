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

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

final class HeliogenesisAssetsTest extends TestCase
{
    private const COPIED_FILES = [
        'heliogenesis.css',
        'heliogenesis-document.css',
        'heliogenesis-scene.js',
        'heliogenesis.js',
        'vendor/THREE-LICENSE.txt',
        'vendor/three.core.min.js',
        'vendor/three.module.min.js',
    ];

    #[Group('locked-dependencies')]
    public function testPublicRuntimeMatchesTheComposerInstalledIntegration(): void
    {
        $projectRoot = dirname(__DIR__, 2);
        $upstreamRoot = $projectRoot
            . '/vendor/jbboehr/doctrine-of-the-second-sun/integrations/web/heliogenesis';
        $publicRoot = $projectRoot . '/docs/pages/assets/heliogenesis';

        foreach (self::COPIED_FILES as $relativePath) {
            self::assertFileEquals(
                $upstreamRoot . '/' . $relativePath,
                $publicRoot . '/' . $relativePath,
                $relativePath . ' must remain an unmodified copy of the installed Heliogenesis runtime.',
            );
        }

        self::assertFileEquals(
            $projectRoot . '/vendor/jbboehr/doctrine-of-the-second-sun/LICENSE.md',
            $publicRoot . '/DOCTRINE-LICENSE.txt',
        );
    }

    #[Group('locked-dependencies')]
    public function testProvenanceNoticeRecordsTheLockedDoctrineRevision(): void
    {
        $projectRoot = dirname(__DIR__, 2);
        $lock = json_decode(
            (string) file_get_contents($projectRoot . '/composer.lock'),
            true,
            flags: JSON_THROW_ON_ERROR,
        );
        if (!is_array($lock)) {
            self::fail('composer.lock must decode to an object.');
        }

        $packages = $lock['packages-dev'] ?? null;
        if (!is_array($packages)) {
            self::fail('composer.lock must contain development packages.');
        }

        $reference = null;
        foreach ($packages as $package) {
            if (!is_array($package)) {
                continue;
            }

            if ('jbboehr/doctrine-of-the-second-sun' === ($package['name'] ?? null)) {
                $source = $package['source'] ?? null;
                if (is_array($source)) {
                    $reference = $source['reference'] ?? null;
                }
                break;
            }
        }

        self::assertIsString($reference);
        self::assertMatchesRegularExpression('/^[a-f0-9]{40}$/', $reference);

        $notice = file_get_contents($projectRoot . '/docs/pages/assets/heliogenesis/NOTICE.txt');
        self::assertNotFalse($notice);
        self::assertStringContainsString('revision ' . $reference, $notice);
    }

    public function testMdBookThemeMountsTheOptInRuntimeWithAccessibleControls(): void
    {
        $theme = file_get_contents(dirname(__DIR__, 2) . '/docs/theme/yumemi.js');
        self::assertNotFalse($theme);

        self::assertStringContainsString('assets/heliogenesis/', $theme);
        self::assertStringContainsString('path_to_root + "assets/heliogenesis/"', $theme);
        self::assertStringContainsString('addHeliogenesisStylesheet(assetRoot, "heliogenesis.css")', $theme);
        self::assertStringContainsString('addHeliogenesisStylesheet(assetRoot, "heliogenesis-document.css")', $theme);
        self::assertStringContainsString('trigger.id = "yumemi-second-sun"', $theme);
        self::assertStringContainsString('aria-label", "Dawn the Second Sun"', $theme);
        self::assertStringContainsString('new Heliogenesis({ trigger })', $theme);
        self::assertStringContainsString('for (const selector of ["#mdbook-menu-bar", "#mdbook-sidebar"])', $theme);
        self::assertStringContainsString('trigger.remove();', $theme);
        self::assertStringContainsString('document.querySelector("#mdbook-page-wrapper") ?? document.body', $theme);
        self::assertStringContainsString('world.dataset.heliogenesisWorld', $theme);
        self::assertStringNotContainsString('dataset.heliogenesisSurface', $theme);
        self::assertStringNotContainsString('dataset.heliogenesisCallout', $theme);
        self::assertStringNotContainsString('dataset.heliogenesisCode', $theme);
        self::assertStringNotContainsString('dataset.heliogenesisRule', $theme);
    }

    public function testMdBookThemeKeepsIdleShellTransitionsImmediate(): void
    {
        $theme = file_get_contents(dirname(__DIR__, 2) . '/docs/theme/yumemi.css');
        self::assertNotFalse($theme);

        self::assertStringContainsString('var(--heliogenesis-layer, 30)', $theme);
        self::assertStringContainsString(':root:not([data-heliogenesis-state])', $theme);
        self::assertStringContainsString('[data-heliogenesis-state="idle"]', $theme);
        self::assertStringContainsString('transition-duration: 0s', $theme);
    }

    public function testMdBookThemeCentersWideNavigationAroundTheProjectTitle(): void
    {
        $theme = file_get_contents(dirname(__DIR__, 2) . '/docs/theme/yumemi.css');
        self::assertNotFalse($theme);

        self::assertStringContainsString('#mdbook-menu-bar .menu-title', $theme);
        self::assertStringContainsString('position: fixed', $theme);
        self::assertStringContainsString('html.sidebar-visible #mdbook-menu-bar .menu-title', $theme);
        self::assertStringContainsString('left: calc(50% + var(--sidebar-width) / 2)', $theme);
        self::assertStringContainsString('#mdbook-menu-bar .right-buttons', $theme);
        self::assertStringContainsString('margin-left: auto', $theme);
        self::assertStringContainsString('width: 32rem', $theme);
    }
}
