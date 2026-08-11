{
  description = "jbboehr/yumemi-apocrypha";

  inputs = {
    nixpkgs.url = "github:nixos/nixpkgs/nixos-26.05";
    flake-utils.url = "github:numtide/flake-utils";
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
    akashi = {
      url = "git+https://github.com/jbboehr/akashi.php?ref=master";
      inputs.flake-utils.follows = "flake-utils";
      inputs.gitignore.follows = "gitignore";
      inputs.nixpkgs.follows = "nixpkgs";
      inputs.pre-commit-hooks.follows = "pre-commit-hooks";
      inputs.treefmt-nix.follows = "treefmt-nix";
    };
    php-perfidious = {
      url = "github:jbboehr/php-perfidious";
      flake = false;
    };
  };

  outputs =
    {
      self,
      nixpkgs,
      flake-utils,
      pre-commit-hooks,
      treefmt-nix,
      gitignore,
      akashi,
      php-perfidious,
    }:
    flake-utils.lib.eachDefaultSystem (
      system:
      let
        pkgs = nixpkgs.legacyPackages.${system};
        php-unwrapped = pkgs.php82;
        perfidious = pkgs.callPackage "${php-perfidious}/nix/derivation.nix" {
          php = php-unwrapped;
          src = php-perfidious;
          buildPecl = pkgs.callPackage "${nixpkgs}/pkgs/build-support/php/build-pecl.nix" {
            php = php-unwrapped;
          };
          valgrindSupport = false;
        };
        php = php-unwrapped.buildEnv {
          extraConfig = "memory_limit = 2G";
          extensions =
            {
              enabled,
              all,
            }:
            enabled ++ [ all.pcov ] ++ pkgs.lib.optional pkgs.stdenv.isLinux perfidious;
        };
        php-xdebug = php-unwrapped.buildEnv {
          extraConfig = ''
            memory_limit = 2G
            xdebug.mode = off
          '';
          extensions =
            {
              enabled,
              all,
            }:
            enabled ++ [ all.xdebug ];
        };
        src = gitignore.lib.gitignoreSource ./.;
        treefmt = treefmt-nix.lib.evalModule pkgs {
          projectRootFile = "flake.nix";
          settings.global.excludes = [ "docs/pages/assets/heliogenesis/**" ];
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
        mkDevShell =
          phpPackage:
          pkgs.mkShell {
            buildInputs = with pkgs; [
              actionlint
              akashi.packages.${system}.agent-badge
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
          inherit pre-commit-check;
          documentation =
            pkgs.runCommand "yumemi-apocrypha-documentation"
              {
                nativeBuildInputs = [
                  pkgs.mdbook
                  php-unwrapped
                ];
              }
              ''
                mdbook build ${src}/docs --dest-dir "$out"
                php ${src}/tests/Documentation/check-generated-links.php "$out"
              '';
          formatting = treefmt.config.build.check self;
        };
        devShells = {
          default = mkDevShell php;
          xdebug = mkDevShell php-xdebug;
        };
        formatter = treefmt.config.build.wrapper;
      }
    );
}
