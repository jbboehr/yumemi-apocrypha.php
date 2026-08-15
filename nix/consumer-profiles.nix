{ releaseVersion }:

let
  profile = php: mode: suite: version: compatibility: {
    inherit
      php
      mode
      suite
      version
      compatibility
      ;
    environment = { };
  };

  plain =
    php: mode: suite: version:
    profile php mode suite version "plain";

  withEnvironment = environment: consumerProfile: consumerProfile // { inherit environment; };

  illuminateProfiles = suite: [
    (profile "82" "source" suite "11" "plain")
    (profile "82" "source" suite "11" "larastan")
    (profile "82" "archive" suite "12" "plain")
    (profile "82" "source" suite "12" "larastan")
    (profile "83" "source" suite "13" "plain")
    (profile "83" "source" suite "13" "larastan")
  ];

  illuminateIntegrationProfiles = suite: illuminateProfiles suite;
in
{
  carbon = [
    (plain "82" "source" "carbon" "2.62.1")
    (plain "82" "archive" "carbon" "2")
    (plain "82" "source" "carbon" "3.0.0")
    (plain "82" "source" "carbon" "3.1.1")
    (plain "82" "source" "carbon" "3.2.0")
    (plain "82" "archive" "carbon" "3")
    (profile "82" "source" "carbon" "3" "larastan")
  ];

  getid3 = [
    (plain "82" "source" "getid3" "1.9.22")
    (plain "82" "archive" "getid3" "1")
    (plain "82" "source" "getid3" "2")
  ];

  intervention-image = [
    (plain "82" "source" "intervention-image" "3.0.0")
    (plain "82" "archive" "intervention-image" "3")
    (plain "83" "source" "intervention-image" "4.0.0")
    (plain "83" "archive" "intervention-image" "4")
  ];

  guzzle = [
    (plain "82" "source" "guzzle" "7")
    (plain "82" "archive" "guzzle" "8")
    (plain "82" "source" "guzzle" "7.10.0")
    (plain "82" "source" "guzzle" "7.11.0")
    (withEnvironment { CONSUMER_VENDOR_DIR = "dependencies"; } (plain "82" "source" "guzzle" "8"))
  ];

  illuminate-cache = illuminateProfiles "illuminate-cache";
  illuminate-console = (illuminateIntegrationProfiles "illuminate-console") ++ [
    (plain "83" "source" "illuminate-console" "13.1.1")
    (plain "83" "source" "illuminate-console" "13.2.0")
  ];

  illuminate-auth = (illuminateIntegrationProfiles "illuminate-auth") ++ [
    (plain "82" "source" "illuminate-auth" "11.30.0")
    (plain "82" "source" "illuminate-auth" "11.31.0")
    (plain "82" "source" "illuminate-auth" "11.44.0")
    (plain "82" "source" "illuminate-auth" "11.45.0")
    (plain "82" "source" "illuminate-auth" "12.13.0")
    (plain "82" "source" "illuminate-auth" "12.14.0")
    (plain "82" "source" "illuminate-auth" "12.19.3")
    (plain "82" "source" "illuminate-auth" "12.20.0")
    (plain "82" "source" "illuminate-auth" "12.44.0")
    (plain "82" "source" "illuminate-auth" "12.45.0")
  ];

  illuminate-http = (illuminateProfiles "illuminate-http") ++ [
    (plain "82" "source" "illuminate-http" "11.35.0")
    (plain "82" "source" "illuminate-http" "11.35.1")
  ];

  illuminate-cookie = illuminateIntegrationProfiles "illuminate-cookie";

  illuminate-database = (illuminateIntegrationProfiles "illuminate-database") ++ [
    (plain "82" "source" "illuminate-database" "12.50.0")
    (plain "82" "source" "illuminate-database" "12.51.0")
  ];

  illuminate-filesystem = illuminateIntegrationProfiles "illuminate-filesystem";
  illuminate-process = illuminateIntegrationProfiles "illuminate-process";

  illuminate-queue = (illuminateIntegrationProfiles "illuminate-queue") ++ [
    (plain "82" "source" "illuminate-queue" "11.52.0")
    (plain "82" "source" "illuminate-queue" "11.53.0")
    (plain "82" "source" "illuminate-queue" "12.59.0")
    (plain "82" "source" "illuminate-queue" "12.60.0")
    (plain "83" "source" "illuminate-queue" "13.9.0")
    (plain "83" "source" "illuminate-queue" "13.10.0")
  ];

  illuminate-redis = illuminateIntegrationProfiles "illuminate-redis";
  illuminate-routing = illuminateIntegrationProfiles "illuminate-routing";
  illuminate-session = illuminateIntegrationProfiles "illuminate-session";
  illuminate-support = illuminateIntegrationProfiles "illuminate-support";

  illuminate-validation = (illuminateIntegrationProfiles "illuminate-validation") ++ [
    (
      (profile "82" "source" "illuminate-validation" "11" "phpstan-laravel-validation")
      // {
        phpstanLaravelValidationDevelopmentHead = true;
      }
    )
    (
      (profile "82" "source" "illuminate-validation" "12" "phpstan-laravel-validation")
      // {
        phpstanLaravelValidationDevelopmentHead = true;
      }
    )
    (
      (profile "83" "source" "illuminate-validation" "13" "phpstan-laravel-validation")
      // {
        phpstanLaravelValidationDevelopmentHead = true;
      }
    )
  ];

  laravel-framework = illuminateProfiles "laravel-framework";

  measurements = [
    (plain "82" "source" "measurements" "1.4.0")
    (plain "82" "archive" "measurements" "1")
  ];

  phpgeo = [
    (plain "82" "source" "phpgeo" "4")
    (plain "82" "archive" "phpgeo" "5")
    (plain "82" "source" "phpgeo" "6")
  ];

  symfony-http-foundation = [
    (plain "82" "source" "symfony-http-foundation" "6.4.0")
    (plain "82" "source" "symfony-http-foundation" "6")
    (profile "82" "source" "symfony-http-foundation" "6" "phpstan-symfony")
    (plain "82" "source" "symfony-http-foundation" "7.2.9")
    (plain "82" "source" "symfony-http-foundation" "7.3.0")
    (plain "82" "archive" "symfony-http-foundation" "7")
    (profile "82" "source" "symfony-http-foundation" "7" "phpstan-symfony")
    (plain "84" "source" "symfony-http-foundation" "8.0.0")
    (plain "84" "source" "symfony-http-foundation" "8")
    (profile "84" "source" "symfony-http-foundation" "8" "phpstan-symfony")
  ];

  symfony-stopwatch = [
    (plain "82" "source" "symfony-stopwatch" "6")
    (profile "82" "source" "symfony-stopwatch" "6" "phpstan-symfony")
    (plain "82" "archive" "symfony-stopwatch" "7")
    (profile "82" "source" "symfony-stopwatch" "7" "phpstan-symfony")
    (plain "84" "source" "symfony-stopwatch" "8")
    (profile "84" "source" "symfony-stopwatch" "8" "phpstan-symfony")
  ];

  manual = [
    (plain "82" "source" "manual" "12")
    (withEnvironment { CONSUMER_VENDOR_DIR = "dependencies"; } (plain "82" "source" "manual" "12"))
  ];

  yumemi-development-head =
    map (consumerProfile: consumerProfile // { yumemiDevelopmentHead = true; })
      [
        (plain "82" "source" "illuminate-cache" "12")
        (plain "82" "source" "manual" "12")
      ];

  stable-release-artifact =
    map
      (
        consumerProfile:
        withEnvironment {
          APOCRYPHA_PACKAGE_VERSION = releaseVersion;
          CONSUMER_MINIMUM_STABILITY = "stable";
        } consumerProfile
      )
      [
        (plain "82" "archive" "symfony-stopwatch" "7")
        (plain "82" "archive" "manual" "12")
        (profile "82" "archive" "laravel-framework" "12" "larastan")
      ];
}
