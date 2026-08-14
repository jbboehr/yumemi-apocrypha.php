{
  description = "jbboehr/yumemi-apocrypha";

  inputs = {
    nixpkgs.url = "github:nixos/nixpkgs/nixos-26.05";
    flake-utils.url = "github:numtide/flake-utils";
    nix-github-actions = {
      url = "github:nix-community/nix-github-actions";
      inputs.nixpkgs.follows = "nixpkgs";
    };
    pre-commit-hooks = {
      url = "github:cachix/pre-commit-hooks.nix";
      inputs.nixpkgs.follows = "nixpkgs";
    };
    treefmt-nix = {
      url = "github:numtide/treefmt-nix";
      inputs.nixpkgs.follows = "nixpkgs";
    };
    gitignore = {
      url = "github:hercules-ci/gitignore.nix";
      inputs.nixpkgs.follows = "nixpkgs";
    };
    agent-badge = {
      url = "git+https://github.com/jbboehr/agent-badge.ts?ref=master";
      inputs.flake-utils.follows = "flake-utils";
      inputs.gitignore.follows = "gitignore";
      inputs.nixpkgs.follows = "nixpkgs";
    };
    php-perfidious = {
      url = "github:jbboehr/php-perfidious";
      flake = false;
    };
    phpstan-laravel-validation-development-head = {
      url = "github:jbboehr/phpstan-laravel-validation";
      flake = false;
    };
    yumemi-development-head = {
      url = "github:jbboehr/yumemi.php";
      flake = false;
    };
  };

  outputs =
    inputs@{
      self,
      nixpkgs,
      flake-utils,
      nix-github-actions,
      pre-commit-hooks,
      treefmt-nix,
      gitignore,
      agent-badge,
      php-perfidious,
      phpstan-laravel-validation-development-head,
      yumemi-development-head,
      ...
    }:
    flake-utils.lib.eachDefaultSystem (
      system:
      let
        pkgs = nixpkgs.legacyPackages.${system};
        lib = pkgs.lib;
        php-unwrapped = {
          "82" = pkgs.php82;
          "83" = pkgs.php83;
          "84" = pkgs.php84;
          "85" = pkgs.php85;
        };
        mkValidationPhp =
          phpPackage:
          phpPackage.buildEnv {
            extraConfig = "memory_limit = 2G";
            extensions =
              { enabled, all }:
              lib.unique (
                enabled
                ++ [
                  all.gd
                  all.gmp
                  all.mbstring
                  all.pcov
                ]
              );
          };
        php = lib.mapAttrs (_: mkValidationPhp) php-unwrapped;
        canonicalPhp = php."82";
        canonicalComposer = php-unwrapped."82".packages.composer;
        composerWithLocalRepository = php-unwrapped."82".packages.composer-local-repo-plugin;

        perfidious = pkgs.callPackage "${php-perfidious}/nix/derivation.nix" {
          php = php-unwrapped."82";
          src = php-perfidious;
          buildPecl = pkgs.callPackage "${nixpkgs}/pkgs/build-support/php/build-pecl.nix" {
            php = php-unwrapped."82";
          };
          valgrindSupport = false;
        };
        developmentPhp = php-unwrapped."82".buildEnv {
          extraConfig = "memory_limit = 2G";
          extensions =
            { enabled, all }:
            lib.unique (
              enabled
              ++ [
                all.gd
                all.gmp
                all.mbstring
                all.pcov
              ]
              ++ lib.optional pkgs.stdenv.isLinux perfidious
            );
        };
        php-xdebug = php-unwrapped."82".buildEnv {
          extraConfig = ''
            memory_limit = 2G
            xdebug.mode = off
          '';
          extensions =
            { enabled, all }:
            lib.unique (
              enabled
              ++ [
                all.gd
                all.gmp
                all.mbstring
                all.xdebug
              ]
            );
        };

        src = gitignore.lib.gitignoreSource ./.;
        composerSource = lib.fileset.toSource {
          root = ./.;
          fileset = lib.fileset.unions [
            ./composer.json
            ./composer.lock
          ];
        };
        composerLockFingerprint = builtins.substring 0 12 (builtins.hashFile "sha256" ./composer.lock);

        composerRepository = php-unwrapped."82".mkComposerRepository {
          pname = "yumemi-apocrypha-${composerLockFingerprint}";
          version = "0.0.0";
          src = composerSource;
          composerNoDev = false;
          composerNoPlugins = true;
          composerNoScripts = true;
          dontFixup = true;
          vendorHash = import ./nix/vendor-hash.nix;
        };
        composerDependencies = pkgs.stdenvNoCC.mkDerivation {
          pname = "yumemi-apocrypha-composer-dependencies";
          version = "0.0.0";
          src = composerSource;
          nativeBuildInputs = [
            composerWithLocalRepository
            php-unwrapped."82".composerHooks.composerInstallHook
          ];
          inherit composerRepository;
          composerNoDev = false;
          composerNoPlugins = true;
          composerNoScripts = true;
          COMPOSER_DISABLE_NETWORK = "1";
          COMPOSER_NO_AUDIT = "1";
        };
        vendor = "${composerDependencies}/share/php/yumemi-apocrypha-composer-dependencies/vendor";

        treefmt = treefmt-nix.lib.evalModule pkgs {
          projectRootFile = "flake.nix";
          settings.global.excludes = [
            "docs/pages/assets/heliogenesis/**"
            "tmp*"
          ];
          programs.nixfmt = {
            enable = true;
            package = pkgs.nixfmt;
          };
          programs.prettier = {
            enable = true;
            settings = {
              proseWrap = "always";
              printWidth = 120;
              overrides = [
                {
                  files = "LICENSE.md";
                  options.proseWrap = "preserve";
                }
              ];
            };
          };
        };
        pre-commit-check = pre-commit-hooks.lib.${system}.run {
          inherit src;
          hooks = {
            actionlint.enable = true;
            shellcheck.enable = true;
            treefmt = {
              enable = true;
              package = treefmt.config.build.wrapper;
              require_serial = true;
            };
          };
        };

        prepareVendorFrom = vendorPath: ''
          mkdir -p -- vendor
          cp -R -- ${vendorPath}/. vendor/
          chmod -R u+w -- vendor
          composer dump-autoload --no-interaction --no-plugins --no-scripts
          patchShebangs vendor/bin
        '';
        prepareVendor = prepareVendorFrom vendor;
        projectSetup = ''
          cp -R -- ${src}/. "$NIX_BUILD_TOP/project"
          chmod -R u+w -- "$NIX_BUILD_TOP/project"
          cd -- "$NIX_BUILD_TOP/project"
          export COMPOSER_CACHE_DIR="$NIX_BUILD_TOP/composer-cache"
          export XDG_CACHE_HOME="$NIX_BUILD_TOP/xdg-cache"
          ${prepareVendor}
        '';
        mkProjectCheck =
          {
            name,
            command,
            phpPackage ? canonicalPhp,
            extraNativeBuildInputs ? [ ],
          }:
          pkgs.runCommand "yumemi-apocrypha-${name}"
            {
              nativeBuildInputs = [
                phpPackage
                canonicalComposer
              ]
              ++ extraNativeBuildInputs;
            }
            ''
              ${projectSetup}
              ${command}
              touch "$out"
            '';

        releaseVersion = lib.strings.removeSuffix "\n" (builtins.readFile ./nix/release-version);
        consumerProfiles = import ./nix/consumer-profiles.nix { inherit releaseVersion; };
        routineConsumerProfiles = lib.concatLists (
          lib.attrValues (
            removeAttrs consumerProfiles [
              "stable-release-artifact"
              "yumemi-development-head"
            ]
          )
        );
        dependencyConsumerProfiles =
          builtins.filter (consumerProfile: consumerProfile.compatibility == "plain") routineConsumerProfiles
          ++ builtins.filter (
            consumerProfile: consumerProfile.compatibility == "larastan"
          ) consumerProfiles.laravel-framework
          ++ builtins.filter (
            consumerProfile: consumerProfile.compatibility == "phpstan-laravel-validation"
          ) consumerProfiles.illuminate-validation
          ++ builtins.filter (consumerProfile: consumerProfile.compatibility == "phpstan-symfony") (
            consumerProfiles.symfony-http-foundation ++ consumerProfiles.symfony-stopwatch
          )
          ++ consumerProfiles.stable-release-artifact
          ++ consumerProfiles.yumemi-development-head;
        # Isolate Composer writes while resolving four balanced profile groups concurrently, then merge them below.
        consumerCacheShardCount = 4;
        indexedDependencyConsumerProfiles = lib.imap0 (index: consumerProfile: {
          inherit index consumerProfile;
        }) dependencyConsumerProfiles;
        consumerCacheShards = lib.genList (
          shardIndex:
          map (indexedProfile: indexedProfile.consumerProfile) (
            builtins.filter (
              indexedProfile: lib.mod indexedProfile.index consumerCacheShardCount == shardIndex
            ) indexedDependencyConsumerProfiles
          )
        ) consumerCacheShardCount;
        consumerDependencyFiles = lib.fileset.unions [
          ./composer.json
          ./tests/Consumer
        ];
        consumerDependencySource = lib.fileset.toSource {
          root = ./.;
          fileset = consumerDependencyFiles;
        };
        consumerCacheFingerprint = builtins.substring 0 12 (
          builtins.hashString "sha256" (
            "consumer-cache-v2"
            + builtins.toJSON dependencyConsumerProfiles
            + toString consumerDependencySource
            + toString phpstan-laravel-validation-development-head
            + toString yumemi-development-head
          )
        );
        consumerTools = [
          pkgs.gitMinimal
          pkgs.gnutar
          pkgs.unzip
        ];
        profileEnvironment =
          consumerProfile:
          lib.concatStringsSep "\n" (
            lib.mapAttrsToList (
              name: value: "export ${name}=${lib.escapeShellArg value}"
            ) consumerProfile.environment
          );
        profileCommand =
          dependencyOnly: consumerProfile:
          let
            selectedPhp = php.${consumerProfile.php};
            selectedComposer = php-unwrapped.${consumerProfile.php}.packages.composer;
            mode = if dependencyOnly then "source" else consumerProfile.mode;
          in
          ''
            (
              export PATH=${
                lib.escapeShellArg (
                  lib.makeBinPath (
                    [
                      selectedPhp
                      selectedComposer
                    ]
                    ++ consumerTools
                  )
                )
              }:$PATH
              ${profileEnvironment consumerProfile}
              ${lib.optionalString (consumerProfile.yumemiDevelopmentHead or false) (
                "export YUMEMI_PACKAGE_DIR=${lib.escapeShellArg (toString yumemi-development-head)}"
              )}
              ${lib.optionalString (consumerProfile.phpstanLaravelValidationDevelopmentHead or false) (
                "export PHPSTAN_LARAVEL_VALIDATION_PACKAGE_DIR=${lib.escapeShellArg (toString phpstan-laravel-validation-development-head)}"
              )}
              ${lib.optionalString dependencyOnly "export CONSUMER_DEPENDENCIES_ONLY=1"}
              ${lib.optionalString (!dependencyOnly) ''
                export COMPOSER_CACHE_READ_ONLY=1
                export COMPOSER_DISABLE_NETWORK=1
                export VERIFY_GIT_ARCHIVE=0
              ''}
              bash tests/Consumer/run \
                ${lib.escapeShellArg mode} \
                ${lib.escapeShellArg consumerProfile.suite} \
                ${lib.escapeShellArg consumerProfile.version} \
                ${lib.escapeShellArg consumerProfile.compatibility}
            )
          '';
        consumerCacheShardCommand = shardIndex: consumerProfilesInShard: ''
          (
            export COMPOSER_CACHE_DIR="$NIX_BUILD_TOP/composer-cache-shards/${toString shardIndex}"
            export COMPOSER_HOME="$NIX_BUILD_TOP/composer-home-shards/${toString shardIndex}"
            export XDG_CACHE_HOME="$NIX_BUILD_TOP/xdg-cache-shards/${toString shardIndex}"
            mkdir -p -- "$COMPOSER_CACHE_DIR" "$COMPOSER_HOME" "$XDG_CACHE_HOME"
            ${lib.concatMapStringsSep "\n" (profileCommand true) consumerProfilesInShard}
          ) &
          consumer_cache_pids+=("$!")
        '';

        consumerComposerCache = pkgs.stdenvNoCC.mkDerivation {
          pname = "yumemi-apocrypha-consumer-composer-cache-${consumerCacheFingerprint}";
          version = "0.0.0";
          dontUnpack = true;
          nativeBuildInputs = [
            canonicalComposer
            canonicalPhp
          ]
          ++ consumerTools;
          installPhase = ''
            runHook preInstall
            mkdir -p -- "$NIX_BUILD_TOP/project" "$out"
            cp -R -- ${consumerDependencySource}/. "$NIX_BUILD_TOP/project"
            chmod -R u+w -- "$NIX_BUILD_TOP/project"
            cd -- "$NIX_BUILD_TOP/project"
            ${prepareVendor}
            consumer_cache_pids=()
            consumer_cache_status=0
            ${lib.concatStringsSep "\n" (lib.imap0 consumerCacheShardCommand consumerCacheShards)}
            for pid in "''${consumer_cache_pids[@]}"; do
              if ! wait "$pid"; then
                consumer_cache_status=1
              fi
            done
            if ((consumer_cache_status != 0)); then
              exit "$consumer_cache_status"
            fi
            # The numeric shard paths give conflicting metadata files a stable merge order.
            for cache_dir in "$NIX_BUILD_TOP"/composer-cache-shards/*; do
              cp -R -- "$cache_dir"/. "$out"/
            done
            runHook postInstall
          '';
          outputHashMode = "recursive";
          outputHash = import ./nix/consumer-cache-hash.nix;
        };

        lowestDependencies = pkgs.stdenvNoCC.mkDerivation {
          pname = "yumemi-apocrypha-lowest-dependencies-${composerLockFingerprint}";
          version = "0.0.0";
          src = composerSource;
          dontFixup = true;
          nativeBuildInputs = [
            canonicalComposer
            canonicalPhp
          ];
          buildPhase = ''
            runHook preBuild
            export COMPOSER_CACHE_DIR="$NIX_BUILD_TOP/composer-cache"
            export COMPOSER_ROOT_VERSION=0.0.0
            mapfile -t lowest_packages < <(php -r '
              $composer = json_decode(file_get_contents("composer.json"), true, flags: JSON_THROW_ON_ERROR);
              foreach (array_merge($composer["require"], $composer["require-dev"]) as $package => $constraint) {
                  if (
                      str_starts_with($package, "ext-")
                      || in_array($package, ["php", "composer-runtime-api"], true)
                      || $constraint === "dev-master"
                  ) {
                      continue;
                  }
                  echo $package, "\n";
              }
            ')
            composer update "''${lowest_packages[@]}" \
              --with-all-dependencies \
              --prefer-lowest \
              --prefer-stable \
              --prefer-dist \
              --no-audit \
              --no-interaction \
              --no-plugins \
              --no-progress \
              --no-scripts
            runHook postBuild
          '';
          installPhase = ''
            runHook preInstall
            mkdir -p -- "$out"
            cp -- composer.lock "$out/"
            cp -R -- vendor "$out/"
            runHook postInstall
          '';
          outputHashMode = "recursive";
          outputHash = import ./nix/lowest-dependencies-hash.nix;
        };
        lowestDependenciesCheck =
          pkgs.runCommand "yumemi-apocrypha-lowest-dependencies-check"
            {
              nativeBuildInputs = [
                canonicalComposer
                canonicalPhp
              ];
            }
            ''
              cp -R -- ${src}/. "$NIX_BUILD_TOP/project"
              chmod -R u+w -- "$NIX_BUILD_TOP/project"
              cd -- "$NIX_BUILD_TOP/project"
              cp -- ${lowestDependencies}/composer.lock composer.lock
              export COMPOSER_CACHE_DIR="$NIX_BUILD_TOP/composer-cache"
              export XDG_CACHE_HOME="$NIX_BUILD_TOP/xdg-cache"
              ${prepareVendorFrom "${lowestDependencies}/vendor"}
              composer analyse
              vendor/bin/phpunit --colors=never --no-coverage --exclude-group=locked-dependencies
              touch "$out"
            '';
        mkConsumerCheck =
          name: profiles:
          mkProjectCheck {
            name = "consumer-${name}";
            extraNativeBuildInputs = consumerTools;
            command = ''
              export COMPOSER_CACHE_DIR=${lib.escapeShellArg (toString consumerComposerCache)}
              export XDG_CACHE_HOME="$NIX_BUILD_TOP/xdg-cache"
              ${lib.concatMapStringsSep "\n" (profileCommand false) profiles}
            '';
          };
        consumerChecks = lib.mapAttrs' (
          name: profiles: lib.nameValuePair "consumer-${name}" (mkConsumerCheck name profiles)
        ) consumerProfiles;

        documentation =
          pkgs.runCommand "yumemi-apocrypha-documentation"
            {
              nativeBuildInputs = [
                pkgs.mdbook
                canonicalPhp
              ];
            }
            ''
              mdbook build ${src}/docs --dest-dir "$out"
              php ${src}/tests/Documentation/check-generated-links.php "$out"
            '';

        mutation = mkProjectCheck {
          name = "mutation";
          phpPackage = canonicalPhp;
          command = ''
            mkdir -p -- vendor/phpunit/phpunit/11.5
            ln -s -- ../phpunit.xsd vendor/phpunit/phpunit/11.5/phpunit.xsd
            substituteInPlace phpunit.xml.dist \
              --replace-fail \
              'https://schema.phpunit.de/11.5/phpunit.xsd' \
              'vendor/phpunit/phpunit/11.5/phpunit.xsd'
            vendor/bin/infection \
              --configuration=infection.json5.dist \
              --no-progress \
              --min-msi=90 \
              --min-covered-msi=90
          '';
        };

        mkDevShell =
          phpPackage:
          pkgs.mkShell {
            buildInputs = with pkgs; [
              actionlint
              agent-badge.packages.${system}.default
              mdbook
              phpPackage
              phpPackage.packages.composer
              pre-commit
              time
              treefmt.config.build.wrapper
            ];
            shellHook = ''
              ${pre-commit-check.shellHook}
              export PATH="$PWD/vendor/bin:$PATH"
            '';
          };
      in
      rec {
        checks = {
          inherit documentation pre-commit-check;
          phpunit-php82 = mkProjectCheck {
            name = "phpunit-php82";
            phpPackage = php."82";
            command = "vendor/bin/phpunit --colors=never --no-coverage";
          };
          phpunit-php83 = mkProjectCheck {
            name = "phpunit-php83";
            phpPackage = php."83";
            command = "vendor/bin/phpunit --colors=never --no-coverage";
          };
          phpunit-php84 = mkProjectCheck {
            name = "phpunit-php84";
            phpPackage = php."84";
            command = "vendor/bin/phpunit --colors=never --no-coverage";
          };
          phpunit-php85 = mkProjectCheck {
            name = "phpunit-php85";
            phpPackage = php."85";
            command = "vendor/bin/phpunit --colors=never --no-coverage";
          };
          phpstan = mkProjectCheck {
            name = "phpstan";
            command = "composer analyse";
          };
          php-cs-fixer = mkProjectCheck {
            name = "php-cs-fixer";
            command = "composer cs -- --using-cache=no";
          };
          composer-validate = mkProjectCheck {
            name = "composer-validate";
            command = "composer validate --strict";
          };
          php-lint = mkProjectCheck {
            name = "php-lint";
            extraNativeBuildInputs = [ pkgs.findutils ];
            command = ''
              find benchmarks src tests tools -type f -name '*.php' -print0 \
                | xargs -0 -n 1 php -l
            '';
          };
          benchmark-smoke = mkProjectCheck {
            name = "benchmark-smoke";
            command = "composer benchmark:smoke";
          };
          package-archive = mkProjectCheck {
            name = "package-archive";
            extraNativeBuildInputs = [ pkgs.gnutar ];
            command = "composer package:check";
          };
          lowest-dependencies = lowestDependenciesCheck;
        }
        // lib.optionalAttrs pkgs.stdenv.isLinux consumerChecks;

        packages = {
          inherit mutation;
          composer-dependencies = composerDependencies;
          consumer-composer-cache = consumerComposerCache;
          lowest-dependencies = lowestDependencies;
          github-actions-matrix =
            let
              checksMatrix = nix-github-actions.lib.mkGithubMatrix {
                checks = {
                  x86_64-linux = self.checks.x86_64-linux;
                };
                attrPrefix = "checks";
              };
              mutationMatrix = nix-github-actions.lib.mkGithubMatrix {
                checks = {
                  x86_64-linux = {
                    inherit (self.packages.x86_64-linux) mutation;
                  };
                };
                attrPrefix = "packages";
              };
            in
            pkgs.writeText "yumemi-apocrypha-github-actions-matrix.json" (
              builtins.toJSON {
                include = checksMatrix.matrix.include ++ mutationMatrix.matrix.include;
              }
            );
        };

        devShells = {
          default = mkDevShell developmentPhp;
          xdebug = mkDevShell php-xdebug;
        };
        formatter = treefmt.config.build.wrapper;
      }
    );
}
