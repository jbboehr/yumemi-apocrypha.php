# Stub Candidate Survey

- Snapshot: `full-20260805`
- Manifest: [`stub-candidate-survey-2026-08-05.json`](stub-candidate-survey-2026-08-05.json)
- Collected at: `2026-08-06T01:21:33+00:00`
- Selected repositories: 250
- Successfully inspected: 247
- Repository cap: 250

## Yield by stratum

| Stratum | Selected | Inspected | Collision candidates | Single-unit | No evidence |
| ------- | -------: | --------: | -------------------: | ----------: | ----------: |
| curated |       21 |        21 |                   18 |           2 |           1 |
| focused |       90 |        88 |                   51 |          23 |          14 |
| noisy   |       30 |        29 |                   21 |           3 |           5 |
| popular |      109 |       109 |                   26 |          27 |          56 |

## Human interpretation

The survey inspected 98.8% of the selected repositories. The three omissions were safety-limit rejections rather than
network failures: `media-alchemyst/media-alchemyst` and `jayeshmepani/jpl-moshier-ephemeris-php` exceeded the 20 MiB
compressed-archive limit, while `pragmarx/countries` exceeded the 100 MiB uncompressed limit.

Focused tags produced substantially more collision leads than the popular-package baseline. The still-higher nominal
yield from noisy tags does not imply better precision: its highest results are dominated by libraries that already
represent units with objects, general unit-conversion packages, and lexical matches that do not describe native scalar
boundaries.

The useful signal mostly confirms the existing planning priorities around PHPSpreadsheet, PHPWord, Intervention Image,
and Imagine. OpenSpout, FastExcelWriter, GeoTools, MathPHP, and the PDF packages merit targeted source review, but this
survey does not promote any of them directly to the roadmap. Many of their apparent boundaries select a unit through a
sibling argument, object state, or runtime option and therefore must first pass the
[evaluation gates](planning.md#evaluation-gates).

## Ranked findings

1. **`khaledalam/unit` (`v1.3.0`)** — noisy; signature; 48 distinct units; 13 monthly downloads
   - Units: angle: degree, radian; data: byte, gibibyte, gigabyte, kibibyte, kilobyte, mebibyte, megabyte; energy:
     joule, kilojoule, kilowatt-hour, watt-hour; frequency: gigahertz, hertz, kilohertz, megahertz; length: centimetre,
     foot, inch, kilometre, metre, mile, millimetre, yard; mass: gram, kilogram, milligram, ounce, pound; power:
     kilowatt, watt; pressure: kilopascal, pascal, psi; ratio: fraction; temperature: celsius, fahrenheit, kelvin; time:
     day, hour, microsecond, millisecond, minute, second; volume: gallon, litre, millilitre
2. **`nmarfurt/measurements` (`v1.4.0`)** — noisy; signature; 43 distinct units; 4465 monthly downloads
   - Units: angle: degree, radian; data: byte, kibibyte, kilobyte, mebibyte, megabyte; energy: joule, kilojoule,
     kilowatt-hour; frequency: gigahertz, hertz, kilohertz, megahertz; length: centimetre, foot, inch, kilometre, metre,
     micrometre, mile, millimetre, yard; mass: gram, kilogram, milligram, ounce, pound; power: kilowatt, watt; pressure:
     kilopascal, pascal, psi; ratio: fraction; temperature: celsius, fahrenheit, kelvin; time: hour, minute, second;
     volume: gallon, litre, millilitre
3. **`asika/better-units` (`0.3.0`)** — noisy; signature; 39 distinct units; 38 monthly downloads
   - Units: data: byte, gibibyte, gigabyte, kibibyte, kilobyte, mebibyte, megabyte; energy: joule, kilojoule,
     kilowatt-hour, watt-hour; length: centimetre, foot, inch, kilometre, metre, micrometre, mile, millimetre, pixel,
     point, yard; mass: gram, kilogram, milligram, ounce, pound; power: kilowatt, watt; ratio: fraction; time: day,
     hour, microsecond, millisecond, minute, nanosecond, second; volume: gallon, litre
4. **`samsara/newton` (`v1.0.0`)** — curated; signature; 29 distinct units; 0 monthly downloads
   - Units: energy: joule; frequency: gigahertz, hertz, kilohertz, megahertz; length: centimetre, foot, inch, kilometre,
     metre, mile, millimetre, yard; mass: gram, kilogram, milligram; power: kilowatt, watt; pressure: kilopascal,
     pascal, psi; temperature: kelvin; time: day, hour, millisecond, minute, second; volume: gallon, litre
5. **`james-heinrich/getid3` (`v1.9.25`)** — curated; signature; 26 distinct units; 487469 monthly downloads
   - Units: angle: degree, radian; data: byte, gigabyte, kilobyte, megabyte; frame-rate: fps; frequency: hertz,
     kilohertz; length: centimetre, inch, metre, micrometre, pixel; mass: pound; pressure: pascal; ratio: fraction,
     percent; temperature: kelvin; time: day, hour, microsecond, millisecond, minute, nanosecond, second
6. **`mibo/properties` (`1.2.0`)** — curated; signature; 26 distinct units; 0 monthly downloads
   - Units: angle: degree, radian; length: centimetre, foot, inch, kilometre, metre, mile, pica, twip, yard; mass: gram,
     kilogram, ounce, pound; ratio: fraction; temperature: celsius, fahrenheit, kelvin; time: day, hour, microsecond,
     minute, second; volume: gallon, litre
7. **`phpoffice/phpspreadsheet` (`5.9.0`)** — curated; signature; 25 distinct units; 9105995 monthly downloads
   - Units: angle: degree, radian; data: byte, kilobyte, megabyte; length: centimetre, emu, inch, kilometre, mile,
     millimetre, pica, pixel, point, twip; ratio: fraction, percent; resolution: dpi; time: day, hour, microsecond,
     millisecond, minute, nanosecond, second
8. **`irrevion/science` (`0.0.5`)** — curated; signature; 20 distinct units; 0 monthly downloads
   - Units: angle: degree, radian; data: byte, kilobyte; energy: joule; frequency: hertz; length: kilometre, metre,
     mile; mass: kilogram, pound; power: watt; pressure: pascal; ratio: fraction; temperature: celsius, fahrenheit,
     kelvin; time: hour, minute, second
9. **`php-unit-conversion/php-unit-conversion` (`1.30`)** — noisy; signature; 18 distinct units; 21566 monthly downloads
   - Units: length: foot, inch, metre, mile, pica, yard; mass: gram, ounce, pound; temperature: celsius, fahrenheit,
     kelvin; time: day, hour, minute, second; volume: gallon, litre
10. **`phpoffice/phpword` (`1.4.0`)** — curated; signature; 18 distinct units; 1733254 monthly downloads

- Units: angle: degree; data: byte, gigabyte; length: centimetre, emu, inch, pica, pixel, point, twip; ratio: fraction,
  percent; resolution: dpi; time: hour, microsecond, millisecond, minute, second

11. **`markrogoyski/math-php` (`v2.13.0`)** — focused; signature; 17 distinct units; 188333 monthly downloads

- Units: angle: degree, radian; data: byte, kilobyte; length: centimetre, inch, mile; mass: milligram, pound; pressure:
  pascal; ratio: fraction, percent; time: microsecond, millisecond, second; volume: gallon, litre

12. **`avadim/fast-excel-writer` (`v6.15.0`)** — focused; signature; 15 distinct units; 80325 monthly downloads

- Units: angle: degree; data: kilobyte; length: centimetre, emu, inch, millimetre, pixel, point; ratio: fraction,
  percent; time: day, hour, microsecond, millisecond, second

13. **`laravel/framework` (`v13.24.0`)** — popular; signature; 14 distinct units; 12679083 monthly downloads

- Units: data: byte, gigabyte, kilobyte, megabyte; length: foot; pressure: pascal; ratio: fraction, percent; time: day,
  hour, microsecond, millisecond, minute, second

14. **`nightowl/agent` (`v2.1.0`)** — noisy; signature; 14 distinct units; 4217 monthly downloads

- Units: data: byte, gigabyte, kilobyte, megabyte; length: pixel; ratio: fraction, percent; time: day, hour,
  microsecond, millisecond, minute, nanosecond, second

15. **`lwwcas/laravel-countries` (`4.13.5`)** — noisy; signature; 14 distinct units; 3036 monthly downloads

- Units: angle: degree; data: gigabyte; length: kilometre, pixel, point; mass: pound; ratio: percent; time: day, hour,
  microsecond, millisecond, minute, nanosecond, second

16. **`aws/aws-sdk-php` (`3.390.5`)** — popular; signature; 13 distinct units; 11715494 monthly downloads

- Units: data: byte, gigabyte, megabyte; length: metre; ratio: fraction, percent; time: day, hour, microsecond,
  millisecond, minute, nanosecond, second

17. **`dragonofmercy/phppdf` (`v1.12.3`)** — focused; signature; 12 distinct units; 37 monthly downloads

- Units: angle: degree, radian; data: byte; length: inch, millimetre, pixel, point; ratio: fraction, percent;
  resolution: dpi; time: nanosecond, second

18. **`intervention/image` (`4.2.0`)** — curated; signature; 12 distinct units; 5529452 monthly downloads

- Units: angle: degree, radian; data: byte; length: centimetre, inch, pixel; ratio: fraction, percent; resolution: dpi,
  ppi; time: millisecond, second

19. **`imagine/imagine` (`1.5.4`)** — curated; signature; 12 distinct units; 1092855 monthly downloads

- Units: angle: degree, radian; data: byte; length: centimetre, inch, pixel, point; ratio: fraction, percent;
  resolution: ppi; time: millisecond, second

20. **`pixelandtonic/imagine` (`1.5.2.1`)** — focused; signature; 12 distinct units; 83969 monthly downloads

- Units: angle: degree, radian; data: byte; length: centimetre, inch, pixel, point; ratio: fraction, percent;
  resolution: ppi; time: millisecond, second

21. **`kolay/xlsx-stream` (`v3.4.0`)** — focused; signature; 11 distinct units; 4971 monthly downloads

- Units: data: byte, gigabyte, kilobyte, megabyte; ratio: fraction, percent; time: day, hour, microsecond, millisecond,
  second

22. **`antradar/gspdf` (`v1.0.1`)** — focused; signature; 11 distinct units; 300 monthly downloads

- Units: angle: radian; data: byte, kilobyte, megabyte; length: pixel, point; ratio: fraction; resolution: dpi; time:
  day, hour, second

23. **`openspout/openspout` (`v5.10.2`)** — focused; signature; 11 distinct units; 5375344 monthly downloads

- Units: data: byte, kilobyte, megabyte; length: pixel, point; ratio: fraction, percent; time: day, hour, minute, second

24. **`league/geotools` (`1.3.1`)** — noisy; signature; 10 distinct units; 79537 monthly downloads

- Units: angle: degree, radian; length: foot, kilometre, metre, mile; pressure: pascal; ratio: fraction; time: minute,
  second

25. **`nette/utils` (`v4.1.5`)** — popular; signature; 10 distinct units; 13668537 monthly downloads

- Units: data: byte, kilobyte; length: pixel; ratio: fraction, percent; time: day, hour, microsecond, minute, second

26. **`monolog/monolog` (`3.10.0`)** — popular; signature; 10 distinct units; 18031598 monthly downloads

- Units: data: byte, kilobyte, megabyte; ratio: fraction; resolution: ppi; time: day, microsecond, millisecond, minute,
  second

27. **`wilsonglasser/spout` (`v3.0.21`)** — focused; signature; 10 distinct units; 579 monthly downloads

- Units: data: byte, kilobyte, megabyte; length: pixel, point; ratio: percent; time: day, hour, minute, second

28. **`koolreport/spout` (`3.4.1`)** — focused; signature; 10 distinct units; 5463 monthly downloads

- Units: data: byte, kilobyte, megabyte; length: pixel, point; ratio: percent; time: day, hour, minute, second

29. **`exment-git/spout` (`v4.0.0`)** — focused; signature; 10 distinct units; 1117 monthly downloads

- Units: data: byte, kilobyte, megabyte; length: pixel, point; ratio: percent; time: day, hour, minute, second

30. **`myqaa/spout` (`v3.3.0`)** — focused; signature; 10 distinct units; 533 monthly downloads

- Units: data: byte, kilobyte, megabyte; length: pixel, point; ratio: percent; time: day, hour, minute, second

31. **`nesbot/carbon` (`3.13.1`)** — curated; signature; 9 distinct units; 15032092 monthly downloads

- Units: length: mile, pixel; ratio: fraction; time: day, hour, microsecond, millisecond, minute, second

32. **`ricklab/location` (`v7.0.0`)** — focused; signature; 9 distinct units; 818 monthly downloads

- Units: angle: degree, radian; length: foot, metre, mile, yard; ratio: fraction; time: minute, second

33. **`flow-php/telemetry` (`0.42.0`)** — noisy; signature; 9 distinct units; 28063 monthly downloads

- Units: data: byte; length: metre; ratio: fraction, percent; time: microsecond, millisecond, minute, nanosecond, second

34. **`viitech/php-ffmpeg` (`v0.15`)** — focused; signature; 9 distinct units; 0 monthly downloads

- Units: data: kilobyte; frame-rate: fps; length: pixel; ratio: fraction, percent; time: day, hour, minute, second

35. **`youssefmediating/php-ffmpeg-laravel` (`0.21`)** — focused; signature; 9 distinct units; 0 monthly downloads

- Units: data: kilobyte; frame-rate: fps; length: pixel; ratio: fraction, percent; time: day, hour, minute, second

36. **`farzai/color-palette` (`2.0.0`)** — focused; signature; 9 distinct units; 12349 monthly downloads

- Units: angle: degree; data: byte, kilobyte, megabyte; length: pixel, point; ratio: fraction, percent; time: second

37. **`laravel-enso/unit-conversion` (`2.0.5`)** — noisy; signature; 9 distinct units; 24 monthly downloads

- Units: energy: joule; length: centimetre, kilometre, metre, millimetre; mass: gram, kilogram; power: kilowatt, watt

38. **`voxsoftware/php-ffmpeg` (`0.9.0`)** — focused; signature; 9 distinct units; 0 monthly downloads

- Units: data: kilobyte; frame-rate: fps; length: pixel; ratio: fraction, percent; time: day, hour, minute, second

39. **`foysal50x/h3-php` (`v1.1.0`)** — focused; signature; 9 distinct units; 8503 monthly downloads

- Units: angle: degree, radian; data: byte; length: kilometre, metre; ratio: fraction, percent; time: minute, second

40. **`php-ffmpeg/php-ffmpeg` (`v1.4.0`)** — focused; signature; 9 distinct units; 818305 monthly downloads

- Units: data: kilobyte; frame-rate: fps; length: pixel; ratio: fraction, percent; time: day, hour, minute, second

41. **`wherewhat/php-ffmpeg` (`0.6.0`)** — focused; signature; 9 distinct units; 0 monthly downloads

- Units: data: kilobyte; frame-rate: fps; length: pixel; ratio: fraction, percent; time: day, hour, minute, second

42. **`hiqdev/php-units` (`1.0.0`)** — noisy; signature; 9 distinct units; 438 monthly downloads

- Units: data: byte, kilobyte, mebibyte, megabyte; length: metre; temperature: fahrenheit, kelvin; time: hour, minute

43. **`psy/psysh` (`v0.12.24`)** — popular; signature; 9 distinct units; 13137629 monthly downloads

- Units: data: byte, kilobyte, megabyte; length: pixel; time: day, microsecond, minute, nanosecond, second

44. **`ramsey/uuid` (`4.9.3`)** — popular; signature; 8 distinct units; 16091850 monthly downloads

- Units: data: byte; pressure: pascal; ratio: fraction; time: microsecond, millisecond, minute, nanosecond, second

45. **`vittix/panchang` (`v2.0.0`)** — focused; signature; 8 distinct units; 8 monthly downloads

- Units: angle: degree, radian; length: metre; ratio: fraction; time: day, hour, minute, second

46. **`mjaschen/phpgeo` (`6.0.2`)** — curated; signature; 8 distinct units; 198738 monthly downloads

- Units: angle: degree, radian; length: kilometre, metre, millimetre; ratio: fraction; time: minute, second

47. **`soluble/mediatools` (`2.2.0`)** — focused; signature; 8 distinct units; 3 monthly downloads

- Units: frame-rate: fps; length: pixel; ratio: fraction, percent; time: hour, millisecond, minute, second

48. **`iakumai/php-ffmpeg` (`0.5.3`)** — focused; signature; 8 distinct units; 0 monthly downloads

- Units: data: kilobyte; frame-rate: fps; ratio: fraction, percent; time: day, hour, minute, second

49. **`phpunit/php-timer` (`9.0.0`)** — popular; signature; 8 distinct units; 15722751 monthly downloads

- Units: data: byte, megabyte; time: hour, microsecond, millisecond, minute, nanosecond, second

50. **`pdfbolt/pdfbolt` (`v1.0.1`)** — focused; signature; 8 distinct units; 4 monthly downloads

- Units: data: byte, megabyte; length: pixel; time: day, hour, millisecond, minute, second

51. **`ypid/suncalc` (`v1.7.0`)** — focused; signature; 7 distinct units; 252 monthly downloads

- Units: angle: radian; ratio: fraction; time: day, hour, millisecond, minute, second

52. **`gabrielelana/byte-units` (`0.5.0`)** — noisy; signature; 7 distinct units; 37455 monthly downloads

- Units: data: byte, gibibyte, gigabyte, kibibyte, kilobyte, mebibyte, megabyte

53. **`illuminate/database` (`v13.24.0`)** — curated; signature; 7 distinct units; 909601 monthly downloads

- Units: data: byte, gigabyte, kilobyte, megabyte; time: day, millisecond, second

54. **`phpunit/phpunit` (`13.2.6`)** — popular; signature; 7 distinct units; 16255131 monthly downloads

- Units: data: byte; length: metre, pixel; time: hour, minute, nanosecond, second

55. **`proj4php/proj4php` (`v2.0.19`)** — noisy; signature; 7 distinct units; 58004 monthly downloads

- Units: angle: degree, radian; length: foot, metre; time: millisecond, nanosecond, second

56. **`szymach/c-pchart` (`v3.1.1`)** — noisy; signature; 7 distinct units; 442138 monthly downloads

- Units: angle: degree, radian; length: pixel; ratio: fraction, percent; time: hour, second

57. **`cline/numerus` (`5.0.3`)** — focused; signature; 7 distinct units; 1753 monthly downloads

- Units: data: byte, gigabyte, kilobyte, megabyte; ratio: fraction, percent; time: second

58. **`jtejido/geodesy-php` (`1.41`)** — focused; signature; 7 distinct units; 7234 monthly downloads

- Units: angle: degree, radian; length: kilometre, metre, mile, millimetre; time: second

59. **`cloudlayerio/cloudlayerio-php` (`v2.0.0`)** — focused; signature; 7 distinct units; 437 monthly downloads

- Units: data: byte; length: pixel; ratio: fraction; time: microsecond, millisecond, minute, second

60. **`tuxonice/suncalc-php` (`v1.0.1`)** — focused; signature; 7 distinct units; 339 monthly downloads

- Units: angle: degree, radian; length: kilometre; ratio: fraction; time: day, hour, second

61. **`samsara/fermat` (`v2.1.1`)** — focused; signature; 6 distinct units; 0 monthly downloads

- Units: angle: degree, radian; data: byte; ratio: fraction, percent; time: second

62. **`open-telemetry/sdk` (`1.15.0`)** — curated; signature; 6 distinct units; 2289794 monthly downloads

- Units: data: byte; length: metre; ratio: fraction; time: millisecond, nanosecond, second

63. **`mjaschen/astrotools` (`1.0.0`)** — focused; signature; 6 distinct units; 0 monthly downloads

- Units: angle: degree; time: day, hour, microsecond, minute, second

64. **`maennchen/zipstream-php` (`3.2.2`)** — popular; signature; 6 distinct units; 10004303 monthly downloads

- Units: data: byte, gigabyte, kilobyte; time: hour, minute, second

65. **`wgirhad/geophp` (`v3.0.0`)** — focused; signature; 6 distinct units; 2773 monthly downloads

- Units: angle: degree, radian; data: byte; length: metre; ratio: percent; time: second

66. **`aspera/xlsx-reader` (`v2.0.2`)** — focused; signature; 6 distinct units; 21983 monthly downloads

- Units: data: kilobyte; ratio: fraction, percent; time: day, nanosecond, second

67. **`swen100/geophp` (`v1.0.10`)** — focused; signature; 6 distinct units; 2200 monthly downloads

- Units: angle: degree, radian; data: byte; length: metre; ratio: fraction; time: second

68. **`nuovo/spreadsheet-reader` (`0.5.11`)** — focused; signature; 6 distinct units; 9897 monthly downloads

- Units: data: byte; ratio: percent; time: day, hour, minute, second

69. **`symfony/finder` (`v8.1.1`)** — popular; signature; 6 distinct units; 19330872 monthly downloads

- Units: data: byte, gigabyte, kilobyte, megabyte; time: day, hour

70. **`phpseclib/phpseclib` (`3.0.56`)** — popular; signature; 5 distinct units; 11009041 monthly downloads

- Units: angle: degree; data: byte, megabyte; length: pixel; time: second

71. **`illuminate/queue` (`v13.24.0`)** — curated; signature; 5 distinct units; 436795 monthly downloads

- Units: data: byte, megabyte; time: hour, minute, second

72. **`guzzlehttp/guzzle` (`8.0.2`)** — curated; signature; 5 distinct units; 19745727 monthly downloads

- Units: data: byte, megabyte; ratio: percent; time: millisecond, second

73. **`symfony/console` (`v8.1.2`)** — popular; signature; 5 distinct units; 20251680 monthly downloads

- Units: data: byte; ratio: fraction, percent; time: millisecond, second

74. **`jayeshmepani/swiss-ephemeris-ffi` (`v1.1.1`)** — focused; signature; 5 distinct units; 333 monthly downloads

- Units: angle: degree, radian; time: day, hour, second

75. **`mossadal/math-parser` (`v1.3.16`)** — focused; signature; 5 distinct units; 50358 monthly downloads

- Units: angle: degree, radian; length: yard; ratio: fraction; time: second

76. **`nadybot/math-parser` (`v1.4.2`)** — focused; signature; 5 distinct units; 339 monthly downloads

- Units: angle: degree, radian; length: yard; ratio: fraction; time: second

77. **`php-collective/file-storage` (`1.0.1`)** — focused; signature; 5 distinct units; 1019 monthly downloads

- Units: data: byte, kilobyte; time: hour, minute, second

78. **`symfony/var-dumper` (`v8.1.2`)** — popular; signature; 5 distinct units; 18088051 monthly downloads

- Units: data: byte; ratio: fraction, percent; time: day, second

79. **`rubix/tensor` (`3.0.5`)** — focused; signature; 4 distinct units; 57727 monthly downloads

- Units: angle: degree, radian; time: millisecond, second

80. **`dragonmantank/cron-expression` (`v3.6.0`)** — popular; signature; 4 distinct units; 13254267 monthly downloads

- Units: time: day, hour, minute, second

81. **`jiri.jozif/moonriset` (`1.0.3`)** — focused; signature; 4 distinct units; 5 monthly downloads

- Units: angle: degree; time: day, hour, second

82. **`phpunit/php-code-coverage` (`14.2.4`)** — popular; signature; 3 distinct units; 15793025 monthly downloads

- Units: length: pixel; ratio: fraction, percent

83. **`symfony/stopwatch` (`v8.1.0`)** — curated; signature; 3 distinct units; 8337307 monthly downloads

- Units: data: byte; time: microsecond, millisecond

84. **`nubs/coordinator` (`v0.1.0`)** — focused; signature; 3 distinct units; 0 monthly downloads

- Units: angle: degree, radian; length: metre

85. **`jeroendesloovere/distance` (`1.0.2.1`)** — noisy; signature; 3 distinct units; 1032 monthly downloads

- Units: angle: degree, radian; time: second

86. **`akeneo-labs/spreadsheet-parser` (`v1.3.0`)** — focused; signature; 3 distinct units; 3866 monthly downloads

- Units: time: day, millisecond, second

87. **`symfony/polyfill-mbstring` (`v1.38.2`)** — popular; signature; 2 distinct units; 21118467 monthly downloads

- Units: data: byte, megabyte

88. **`laravel/prompts` (`v0.3.22`)** — popular; signature; 2 distinct units; 11992927 monthly downloads

- Units: ratio: fraction, percent

89. **`ins0/google-measurement-php-client` (`v2.2.0`)** — noisy; signature; 2 distinct units; 1510 monthly downloads

- Units: time: hour, millisecond

90. **`avadim/fast-excel-reader` (`v4.3.0`)** — focused; class; 9 distinct units; 48668 monthly downloads

- Units: data: byte, kilobyte, megabyte; mass: pound; ratio: fraction; time: day, millisecond, minute, second

91. **`phpoffice/phppresentation` (`1.2.0`)** — curated; class; 9 distinct units; 145366 monthly downloads

- Units: angle: degree; data: byte; length: centimetre, emu, inch, millimetre, pixel; ratio: fraction, percent

92. **`martin-georgiev/postgresql-for-doctrine` (`v4.7.0`)** — focused; class; 8 distinct units; 160180 monthly
    downloads

- Units: angle: degree, radian; data: byte; ratio: percent; time: day, hour, minute, second

93. **`doctrine/dbal` (`4.4.4`)** — popular; class; 8 distinct units; 9510045 monthly downloads

- Units: data: byte, gigabyte; ratio: fraction; time: day, hour, microsecond, minute, second

94. **`open-telemetry/api` (`1.10.0`)** — noisy; class; 6 distinct units; 3310990 monthly downloads

- Units: data: byte; length: metre; time: microsecond, millisecond, nanosecond, second

95. **`fakerphp/faker` (`v1.24.1`)** — popular; class; 5 distinct units; 11530063 monthly downloads

- Units: angle: degree; data: byte; ratio: percent; time: day, second

96. **`nunomaduro/termwind` (`v2.4.0`)** — popular; class; 5 distinct units; 11662437 monthly downloads

- Units: data: megabyte; length: pixel, point; ratio: fraction; time: hour

97. **`longitude-one/doctrine-spatial` (`5.0.4`)** — focused; class; 5 distinct units; 127875 monthly downloads

- Units: angle: degree; data: byte; length: metre; time: minute, second

98. **`creof/doctrine2-spatial` (`1.2.0`)** — focused; class; 4 distinct units; 21158 monthly downloads

- Units: angle: degree; length: point; time: minute, second

99. **`illuminate/session` (`v13.24.0`)** — curated; class; 2 distinct units; 641894 monthly downloads

- Units: time: minute, second

100. **`illuminate/redis` (`v13.24.0`)** — curated; class; 2 distinct units; 194769 monthly downloads

- Units: time: millisecond, second

101. **`zlikavac32/php-measure-units` (`0.3.0`)** — noisy; package; 10 distinct units; 61 monthly downloads

- Units: angle: degree, radian; frequency: hertz; length: kilometre, metre; ratio: fraction; temperature: celsius,
  kelvin; time: second; volume: litre

102. **`ezyang/htmlpurifier` (`v4.19.0`)** — popular; package; 9 distinct units; 8886370 monthly downloads

- Units: data: byte; length: foot, pixel; mass: pound; ratio: fraction, percent; time: hour, nanosecond, second

103. **`popphp/pop-pdf` (`5.2.12`)** — focused; package; 7 distinct units; 585 monthly downloads

- Units: angle: degree; data: byte; length: pixel, point; ratio: fraction, percent; time: second

104. **`brick/geo` (`0.13.1`)** — focused; package; 5 distinct units; 197418 monthly downloads

- Units: angle: degree, radian; data: byte; ratio: fraction; time: second

105. **`rokka/imagine-vips` (`0.41.0`)** — focused; package; 5 distinct units; 23804 monthly downloads

- Units: angle: degree; length: pixel; ratio: percent; time: millisecond, second

106. **`mevdschee/php-crud-api` (`v2.16.2`)** — focused; package; 5 distinct units; 699 monthly downloads

- Units: ratio: fraction; time: microsecond, millisecond, minute, second

107. **`guzzlehttp/psr7` (`3.0.0`)** — popular; package; 4 distinct units; 21193711 monthly downloads

- Units: data: byte, megabyte; ratio: percent; time: second

108. **`oceanmoon/math` (`v3.0.0`)** — focused; package; 4 distinct units; 0 monthly downloads

- Units: angle: degree, radian; ratio: fraction; time: second

109. **`symfony/http-foundation` (`v8.1.2`)** — popular; package; 4 distinct units; 17963573 monthly downloads

- Units: data: byte; ratio: percent; time: millisecond, second

110. **`league/uri-interfaces` (`7.8.1`)** — popular; package; 4 distinct units; 13207526 monthly downloads

- Units: data: byte; ratio: fraction, percent; time: second

111. **`techvoot/engineering-intelligence-package` (`v1.0.8`)** — noisy; package; 3 distinct units; 0 monthly downloads

- Units: ratio: fraction; time: millisecond, second

112. **`firebase/php-jwt` (`v7.1.0`)** — popular; package; 3 distinct units; 12155751 monthly downloads

- Units: data: megabyte; time: minute, second

113. **`inspector-apm/inspector-php` (`3.18.0`)** — noisy; package; 3 distinct units; 245571 monthly downloads

- Units: data: byte; time: millisecond, second

114. **`georgebuilds/livewire-molecule` (`v2.1.0`)** — noisy; package; 3 distinct units; 0 monthly downloads

- Units: length: pixel; time: hour, second

115. **`nikic/php-parser` (`v5.8.0`)** — popular; package; 2 distinct units; 19174672 monthly downloads

- Units: time: nanosecond, second

116. **`slickdeals/statsd` (`3.2.3`)** — noisy; package; 2 distinct units; 148224 monthly downloads

- Units: time: millisecond, second

117. **`php-collective/file-storage-image-processor` (`2.0.0`)** — focused; single-unit; 5 distinct units; 761 monthly
     downloads

- Units: angle: degree; data: byte; length: pixel; ratio: fraction; time: second

118. **`imsus/laravel-imgproxy` (`v1.1.0`)** — focused; single-unit; 5 distinct units; 97 monthly downloads

- Units: angle: degree; data: byte; length: pixel; ratio: fraction; time: hour

119. **`omaralalwi/gpdf` (`1.0.8`)** — focused; single-unit; 5 distinct units; 3075 monthly downloads

- Units: data: byte; length: pixel; ratio: fraction; resolution: dpi; time: second

120. **`brick/math` (`0.19.0`)** — focused; single-unit; 4 distinct units; 15930593 monthly downloads

- Units: angle: degree; data: byte; ratio: fraction; time: second

121. **`sharapov/php-ffmpeg-extensions` (`0.2.3`)** — focused; single-unit; 4 distinct units; 0 monthly downloads

- Units: data: byte; length: pixel; ratio: percent; time: second

122. **`illuminate/validation` (`v13.24.0`)** — curated; single-unit; 3 distinct units; 546247 monthly downloads

- Units: data: kilobyte; ratio: fraction; time: second

123. **`psr/http-message` (`2.0`)** — popular; single-unit; 3 distinct units; 20040803 monthly downloads

- Units: data: byte; ratio: percent; time: second

124. **`guzzlehttp/uri-template` (`v2.0.0`)** — popular; single-unit; 3 distinct units; 12405707 monthly downloads

- Units: data: byte; ratio: percent; time: second

125. **`simonschaufi/php-libkml` (`v1.0.1`)** — focused; single-unit; 3 distinct units; 533 monthly downloads

- Units: length: pixel; ratio: fraction; time: second

126. **`nunomaduro/collision` (`v8.9.5`)** — popular; single-unit; 3 distinct units; 9558403 monthly downloads

- Units: data: megabyte; ratio: percent; time: second

127. **`doctrine/inflector` (`2.1.0`)** — popular; single-unit; 3 distinct units; 16369919 monthly downloads

- Units: data: byte; length: foot; time: microsecond

128. **`switchcat/periodic` (`v1.0.2`)** — noisy; single-unit; 3 distinct units; 0 monthly downloads

- Units: angle: degree; temperature: kelvin; time: second

129. **`maba/math` (`v1.0.1`)** — focused; single-unit; 2 distinct units; 726 monthly downloads

- Units: ratio: fraction; time: second

130. **`symfony/process` (`v8.1.0`)** — popular; single-unit; 2 distinct units; 18914700 monthly downloads

- Units: data: byte; time: second

131. **`masterminds/html5` (`2.10.1`)** — popular; single-unit; 2 distinct units; 10146019 monthly downloads

- Units: data: byte; time: nanosecond

132. **`dskripchenko/laravel-php-pdf` (`v1.1.1`)** — focused; single-unit; 2 distinct units; 316 monthly downloads

- Units: data: byte; time: second

133. **`voku/portable-ascii` (`2.1.1`)** — popular; single-unit; 2 distinct units; 13300893 monthly downloads

- Units: data: byte; mass: pound

134. **`symfony/string` (`v8.1.2`)** — popular; single-unit; 2 distinct units; 19398900 monthly downloads

- Units: data: byte; pressure: pascal

135. **`league/uri` (`7.8.1`)** — popular; single-unit; 2 distinct units; 12728110 monthly downloads

- Units: data: byte; ratio: percent

136. **`hamhamfonfon/astrobin-ws` (`2.6.2`)** — focused; single-unit; 2 distinct units; 4 monthly downloads

- Units: data: byte; time: day

137. **`markbaker/complex` (`3.0.2`)** — focused; single-unit; 2 distinct units; 8972873 monthly downloads

- Units: angle: radian; time: second

138. **`elevenlab/php-ogc` (`1.0.1`)** — focused; single-unit; 2 distinct units; 346 monthly downloads

- Units: length: metre; time: second

139. **`egulias/email-validator` (`4.0.4`)** — popular; single-unit; 2 distinct units; 15577212 monthly downloads

- Units: data: byte; ratio: percent

140. **`nette/schema` (`v1.3.5`)** — popular; single-unit; 2 distinct units; 12747165 monthly downloads

- Units: data: byte; time: second

141. **`fidry/cpu-core-counter` (`1.3.0`)** — popular; single-unit; 2 distinct units; 8768821 monthly downloads

- Units: ratio: percent; time: minute

142. **`hosmelq/laravel-imgproxy` (`v1.0.0`)** — focused; single-unit; 2 distinct units; 510 monthly downloads

- Units: data: byte; time: minute

143. **`league/commonmark` (`2.9.0`)** — popular; single-unit; 1 distinct units; 12639282 monthly downloads

- Units: data: byte

144. **`sciphp/numphp` (`0.4.0`)** — focused; single-unit; 1 distinct units; 779 monthly downloads

- Units: time: second

145. **`aws/aws-crt-php` (`v1.2.7`)** — popular; single-unit; 1 distinct units; 11251531 monthly downloads

- Units: time: second

146. **`endless-creativity/elephant-php` (`v0.4.1`)** — focused; single-unit; 1 distinct units; 123 monthly downloads

- Units: data: byte

147. **`markbaker/quadtrees` (`2.2.2`)** — focused; single-unit; 1 distinct units; 439 monthly downloads

- Units: angle: degree

148. **`composer/pcre` (`3.4.0`)** — popular; single-unit; 1 distinct units; 12692828 monthly downloads

- Units: data: byte

149. **`mtdowling/jmespath.php` (`2.9.2`)** — popular; single-unit; 1 distinct units; 12083823 monthly downloads

- Units: time: second

150. **`filp/whoops` (`2.18.4`)** — popular; single-unit; 1 distinct units; 10253915 monthly downloads

- Units: data: byte

151. **`phpmyadmin/shapefile` (`4.0.0`)** — focused; single-unit; 1 distinct units; 22068 monthly downloads

- Units: data: byte

152. **`php-standard-library/math` (`6.2.1`)** — focused; single-unit; 1 distinct units; 21219 monthly downloads

- Units: time: second

153. **`onramplab/laravel-transcription` (`v0.1.0`)** — focused; single-unit; 1 distinct units; 47 monthly downloads

- Units: time: second

154. **`psr/http-factory` (`1.1.0`)** — popular; single-unit; 1 distinct units; 19891272 monthly downloads

- Units: data: byte

155. **`guzzlehttp/promises` (`3.0.1`)** — popular; single-unit; 1 distinct units; 19272054 monthly downloads

- Units: time: second

156. **`sebastian/comparator` (`8.3.0`)** — popular; single-unit; 1 distinct units; 15353293 monthly downloads

- Units: time: second

157. **`ramsey/collection` (`2.1.1`)** — popular; single-unit; 1 distinct units; 15255628 monthly downloads

- Units: time: second

158. **`vlucas/phpdotenv` (`v5.6.4`)** — popular; single-unit; 1 distinct units; 14342283 monthly downloads

- Units: data: byte

159. **`psr/cache` (`3.0.0`)** — curated; single-unit; 1 distinct units; 14114514 monthly downloads

- Units: time: second

160. **`webmozart/assert` (`2.4.1`)** — popular; single-unit; 1 distinct units; 13346334 monthly downloads

- Units: time: second

161. **`tijsverkoyen/css-to-inline-styles` (`v2.4.0`)** — popular; single-unit; 1 distinct units; 13004014 monthly
     downloads

- Units: time: second

162. **`paragonie/constant_time_encoding` (`v3.1.3`)** — popular; single-unit; 1 distinct units; 12489227 monthly
     downloads

- Units: data: byte

163. **`composer/semver` (`3.4.4`)** — popular; single-unit; 1 distinct units; 12123887 monthly downloads

- Units: time: second

164. **`phpdocumentor/type-resolver` (`2.0.0`)** — popular; single-unit; 1 distinct units; 11066466 monthly downloads

- Units: time: second

165. **`wnx/laravel-stats` (`v2.20.0`)** — noisy; single-unit; 1 distinct units; 30379 monthly downloads

- Units: ratio: fraction

166. **`edgaras/strsim` (`v1.1.1`)** — noisy; single-unit; 1 distinct units; 13784 monthly downloads

- Units: ratio: fraction

167. **`dcat/easy-excel` (`1.1.0`)** — focused; single-unit; 1 distinct units; 6126 monthly downloads

- Units: data: byte

168. **`kunjara/swetest` (`1.0`)** — focused; single-unit; 1 distinct units; 74 monthly downloads

- Units: time: second

169. **`vermotr/php-matrix` (`0.1.0`)** — focused; single-unit; 1 distinct units; 1 monthly downloads

- Units: time: second

170. **`aboks/power-iteration` (`v1.1.0`)** — focused; single-unit; 1 distinct units; 0 monthly downloads

- Units: time: second

171. **`antalaron/video-gif` (`v0.1.1`)** — focused; single-unit; 1 distinct units; 0 monthly downloads

- Units: time: second

172. **`symfony/deprecation-contracts` (`v3.7.1`)** — popular; none; 0 distinct units; 21436186 monthly downloads

- Units:

173. **`psr/log` (`3.0.2`)** — popular; none; 0 distinct units; 20577085 monthly downloads

- Units:

174. **`symfony/service-contracts` (`v3.7.1`)** — popular; none; 0 distinct units; 20269878 monthly downloads

- Units:

175. **`symfony/polyfill-intl-normalizer` (`v1.38.0`)** — popular; none; 0 distinct units; 19935057 monthly downloads

- Units:

176. **`ralouphie/getallheaders` (`3.0.3`)** — popular; none; 0 distinct units; 19830120 monthly downloads

- Units:

177. **`psr/container` (`2.0.2`)** — popular; none; 0 distinct units; 19566603 monthly downloads

- Units:

178. **`symfony/polyfill-ctype` (`v1.37.0`)** — popular; none; 0 distinct units; 19514737 monthly downloads

- Units:

179. **`symfony/polyfill-php80` (`v1.37.0`)** — popular; none; 0 distinct units; 19086819 monthly downloads

- Units:

180. **`symfony/polyfill-intl-grapheme` (`v1.41.0`)** — popular; none; 0 distinct units; 19033573 monthly downloads

- Units:

181. **`symfony/event-dispatcher` (`v8.1.2`)** — popular; none; 0 distinct units; 18947581 monthly downloads

- Units:

182. **`symfony/event-dispatcher-contracts` (`v3.7.1`)** — popular; none; 0 distinct units; 18528567 monthly downloads

- Units:

183. **`psr/http-client` (`1.0.3`)** — popular; none; 0 distinct units; 18521271 monthly downloads

- Units:

184. **`doctrine/lexer` (`3.0.1`)** — popular; none; 0 distinct units; 17477766 monthly downloads

- Units:

185. **`psr/event-dispatcher` (`1.0.0`)** — popular; none; 0 distinct units; 17082697 monthly downloads

- Units:

186. **`psr/clock` (`1.0.0`)** — popular; none; 0 distinct units; 16898710 monthly downloads

- Units:

187. **`psr/simple-cache` (`3.0.0`)** — popular; none; 0 distinct units; 16830127 monthly downloads

- Units:

188. **`sebastian/exporter` (`8.1.1`)** — popular; none; 0 distinct units; 15917124 monthly downloads

- Units:

189. **`phpunit/php-file-iterator` (`7.0.0`)** — popular; none; 0 distinct units; 15843685 monthly downloads

- Units:

190. **`sebastian/diff` (`9.0.0`)** — popular; none; 0 distinct units; 15693787 monthly downloads

- Units:

191. **`myclabs/deep-copy` (`1.13.4`)** — popular; none; 0 distinct units; 15650506 monthly downloads

- Units:

192. **`sebastian/object-enumerator` (`8.0.0`)** — popular; none; 0 distinct units; 15615874 monthly downloads

- Units:

193. **`sebastian/object-reflector` (`6.0.0`)** — popular; none; 0 distinct units; 15551077 monthly downloads

- Units:

194. **`phar-io/version` (`3.2.1`)** — popular; none; 0 distinct units; 15438892 monthly downloads

- Units:

195. **`phar-io/manifest` (`2.0.4`)** — popular; none; 0 distinct units; 15435136 monthly downloads

- Units:

196. **`sebastian/type` (`7.0.1`)** — popular; none; 0 distinct units; 15389786 monthly downloads

- Units:

197. **`sebastian/recursion-context` (`8.0.1`)** — popular; none; 0 distinct units; 15265194 monthly downloads

- Units:

198. **`sebastian/environment` (`9.3.2`)** — popular; none; 0 distinct units; 15216272 monthly downloads

- Units:

199. **`sebastian/global-state` (`9.0.1`)** — popular; none; 0 distinct units; 15212416 monthly downloads

- Units:

200. **`sebastian/version` (`7.0.0`)** — popular; none; 0 distinct units; 15146816 monthly downloads

- Units:

201. **`phpunit/php-text-template` (`6.0.0`)** — popular; none; 0 distinct units; 15136555 monthly downloads

- Units:

202. **`theseer/tokenizer` (`2.0.1`)** — popular; none; 0 distinct units; 15046900 monthly downloads

- Units:

203. **`league/flysystem` (`3.35.2`)** — curated; none; 0 distinct units; 14956429 monthly downloads

- Units:

204. **`league/mime-type-detection` (`1.17.0`)** — popular; none; 0 distinct units; 14818699 monthly downloads

- Units:

205. **`phpoption/phpoption` (`1.9.5`)** — popular; none; 0 distinct units; 13955609 monthly downloads

- Units:

206. **`carbonphp/carbon-doctrine-types` (`3.2.0`)** — popular; none; 0 distinct units; 13868134 monthly downloads

- Units:

207. **`doctrine/deprecations` (`1.1.6`)** — popular; none; 0 distinct units; 13715491 monthly downloads

- Units:

208. **`graham-campbell/result-type` (`v1.1.4`)** — popular; none; 0 distinct units; 13474446 monthly downloads

- Units:

209. **`laravel/serializable-closure` (`v2.0.15`)** — popular; none; 0 distinct units; 13308712 monthly downloads

- Units:

210. **`dflydev/dot-access-data` (`v3.0.3`)** — popular; none; 0 distinct units; 13072225 monthly downloads

- Units:

211. **`league/flysystem-local` (`3.31.0`)** — popular; none; 0 distinct units; 12808126 monthly downloads

- Units:

212. **`phpstan/phpdoc-parser` (`2.3.3`)** — popular; none; 0 distinct units; 12518161 monthly downloads

- Units:

213. **`league/config` (`v1.2.0`)** — popular; none; 0 distinct units; 12472996 monthly downloads

- Units:

214. **`laravel/tinker` (`v3.0.2`)** — popular; none; 0 distinct units; 11959806 monthly downloads

- Units:

215. **`paragonie/random_compat` (`v9.99.100`)** — popular; none; 0 distinct units; 11662925 monthly downloads

- Units:

216. **`fruitcake/php-cors` (`v1.4.0`)** — popular; none; 0 distinct units; 11567120 monthly downloads

- Units:

217. **`phpdocumentor/reflection-common` (`2.2.0`)** — popular; none; 0 distinct units; 11336157 monthly downloads

- Units:

218. **`hamcrest/hamcrest-php` (`v3.0.0`)** — popular; none; 0 distinct units; 11044605 monthly downloads

- Units:

219. **`phpdocumentor/reflection-docblock` (`6.0.3`)** — popular; none; 0 distinct units; 11033347 monthly downloads

- Units:

220. **`mockery/mockery` (`1.6.12`)** — popular; none; 0 distinct units; 10980997 monthly downloads

- Units:

221. **`phpstan/phpstan` (`2.1.29`)** — popular; none; 0 distinct units; 10859506 monthly downloads

- Units:

222. **`staabm/side-effects-detector` (`1.0.5`)** — popular; none; 0 distinct units; 10618490 monthly downloads

- Units:

223. **`jean85/pretty-package-versions` (`2.1.1`)** — popular; none; 0 distinct units; 10110637 monthly downloads

- Units:

224. **`doctrine/instantiator` (`2.1.0`)** — popular; none; 0 distinct units; 9938192 monthly downloads

- Units:

225. **`react/promise` (`v3.3.0`)** — popular; none; 0 distinct units; 9720279 monthly downloads

- Units:

226. **`php-http/discovery` (`1.14.3`)** — popular; none; 0 distinct units; 9468763 monthly downloads

- Units:

227. **`composer/xdebug-handler` (`3.0.5`)** — popular; none; 0 distinct units; 9465330 monthly downloads

- Units:

228. **`markbaker/matrix` (`3.0.1`)** — focused; none; 0 distinct units; 8966318 monthly downloads

- Units:

229. **`doctrine/event-manager` (`2.1.1`)** — popular; none; 0 distinct units; 8633754 monthly downloads

- Units:

230. **`open-telemetry/context` (`1.5.0`)** — noisy; none; 0 distinct units; 3134413 monthly downloads

- Units:

231. **`phpseclib/bcmath_compat` (`2.0.3`)** — noisy; none; 0 distinct units; 238931 monthly downloads

- Units:

232. **`jmikola/geojson` (`1.2.0`)** — focused; none; 0 distinct units; 220237 monthly downloads

- Units:

233. **`asimlqt/php-google-spreadsheet-client` (`v3.0.2`)** — focused; none; 0 distinct units; 31700 monthly downloads

- Units:

234. **`jsor/doctrine-postgis` (`v2.4.0`)** — focused; none; 0 distinct units; 29977 monthly downloads

- Units:

235. **`probots-io/pinecone-php` (`1.1.0`)** — noisy; none; 0 distinct units; 13673 monthly downloads

- Units:

236. **`mcordingley/linearalgebra` (`3.0.0`)** — focused; none; 0 distinct units; 8032 monthly downloads

- Units:

237. **`faisalman/simple-excel-php` (`v0.3.15`)** — focused; none; 0 distinct units; 3998 monthly downloads

- Units:

238. **`torgodly/html2media` (`v2.1.2`)** — focused; none; 0 distinct units; 2257 monthly downloads

- Units:

239. **`rkorebrits/htmltoopenxml` (`0.1.9`)** — focused; none; 0 distinct units; 2021 monthly downloads

- Units:

240. **`neutron/silex-imagine-provider` (`0.1.2`)** — focused; none; 0 distinct units; 520 monthly downloads

- Units:

241. **`plin-code/kml-parser` (`v1.0.2`)** — focused; none; 0 distinct units; 511 monthly downloads

- Units:

242. **`muka/shape-reader` (`v1.0.5`)** — focused; none; 0 distinct units; 196 monthly downloads

- Units:

243. **`mikailfaruqali/invoice-template` (`3.6.0`)** — focused; none; 0 distinct units; 37 monthly downloads

- Units:

244. **`vortechstudio/html2media` (`4.0`)** — focused; none; 0 distinct units; 30 monthly downloads

- Units:

245. **`podcasthosting/auphonic-client` (`v0.9.3`)** — focused; none; 0 distinct units; 15 monthly downloads

- Units:

246. **`fiisoft/molecular-weight-calc` (`1.1.1`)** — noisy; none; 0 distinct units; 0 monthly downloads

- Units:

247. **`orta93/economics` (`v1.0.2`)** — noisy; none; 0 distinct units; 0 monthly downloads

- Units:

## Reading the results

These findings are broad discovery leads, not verified integration recommendations. A `signature` locality means that
multiple units appeared within one public declaration across its signature, documentation, or inspected implementation;
it does not prove that the upstream signature exposes each unit as a branded scalar boundary.

Unit-conversion libraries, runtime-selected units, ordinary-language matches, and implementation-only conversions
require human rejection or reclassification. Apply the evaluation gates in `docs/development/planning.md` before
promoting any finding to the roadmap.

## Manual verification queue

The first 25 entries follow the overall collision ranking. Five additional entries preserve the highest-ranked noisy-tag
results.

1. `khaledalam/unit` — signature
2. `nmarfurt/measurements` — signature
3. `asika/better-units` — signature
4. `samsara/newton` — signature
5. `james-heinrich/getid3` — signature
6. `mibo/properties` — signature
7. `phpoffice/phpspreadsheet` — signature
8. `irrevion/science` — signature
9. `php-unit-conversion/php-unit-conversion` — signature
10. `phpoffice/phpword` — signature
11. `markrogoyski/math-php` — signature
12. `avadim/fast-excel-writer` — signature
13. `laravel/framework` — signature
14. `nightowl/agent` — signature
15. `lwwcas/laravel-countries` — signature
16. `aws/aws-sdk-php` — signature
17. `dragonofmercy/phppdf` — signature
18. `intervention/image` — signature
19. `imagine/imagine` — signature
20. `pixelandtonic/imagine` — signature
21. `kolay/xlsx-stream` — signature
22. `antradar/gspdf` — signature
23. `openspout/openspout` — signature
24. `league/geotools` — signature
25. `nette/utils` — signature
26. `flow-php/telemetry` — signature
27. `laravel-enso/unit-conversion` — signature
28. `hiqdev/php-units` — signature
29. `gabrielelana/byte-units` — signature
30. `proj4php/proj4php` — signature

> Automated findings are discovery leads. Verify units and scales against upstream source, tests, and documentation
> before adding an integration.
