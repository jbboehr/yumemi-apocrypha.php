# Stub Candidate Survey

- Snapshot: `science-20260805`
- Profile: `science`
- Manifest: [`stub-candidate-survey-science-2026-08-05.json`](stub-candidate-survey-science-2026-08-05.json)
- Collected at: `2026-08-06T02:14:38+00:00`
- Selected repositories: 250
- Successfully inspected: 250
- Repository cap: 250
- New repositories: 225
- Baseline overlap: 25

## Yield by stratum

| Stratum | Selected | Inspected | Collision candidates | Single-unit | No evidence |
| ------- | -------: | --------: | -------------------: | ----------: | ----------: |
| curated |       25 |        25 |                   18 |           3 |           4 |
| focused |      165 |       165 |                   57 |          65 |          43 |
| noisy   |       60 |        60 |                   27 |          18 |          15 |
| popular |        0 |         0 |                    0 |           0 |           0 |

## Yield by tag

Repositories can appear under more than one tag, so rows are not additive.

| Stratum | Tag                      | Discovered | Selected | Inspected | Collision candidates |
| ------- | ------------------------ | ---------: | -------: | --------: | -------------------: |
| focused | `math`                   |         50 |       19 |        19 |                    8 |
| focused | `mathematics`            |         50 |       17 |        17 |                    3 |
| focused | `algebra`                |         11 |        8 |         8 |                    5 |
| focused | `linear-algebra`         |         12 |        5 |         5 |                    3 |
| focused | `matrix`                 |         50 |       18 |        18 |                    3 |
| focused | `vector`                 |         50 |       16 |        16 |                    5 |
| focused | `statistics`             |         50 |       16 |        16 |                    9 |
| focused | `probability`            |         18 |       14 |        14 |                    5 |
| focused | `calculus`               |          1 |        1 |         1 |                    0 |
| focused | `numerical-analysis`     |          1 |        1 |         1 |                    1 |
| focused | `optimization`           |         50 |       15 |        15 |                    7 |
| focused | `geometry`               |         50 |       24 |        24 |                   10 |
| focused | `computational-geometry` |          2 |        1 |         1 |                    0 |
| focused | `trigonometry`           |          7 |        2 |         2 |                    2 |
| focused | `scientific-computing`   |          3 |        1 |         1 |                    1 |
| focused | `physics`                |         11 |        5 |         5 |                    4 |
| focused | `chemistry`              |          4 |        2 |         2 |                    0 |
| focused | `astronomy`              |         14 |        9 |         9 |                    8 |
| focused | `geodesy`                |          2 |        2 |         2 |                    2 |
| focused | `geospatial`             |         37 |       15 |        15 |                    6 |
| focused | `gis`                    |         50 |       16 |        16 |                    9 |
| focused | `dsp`                    |         38 |       10 |        10 |                    1 |
| focused | `signal-processing`      |          2 |        1 |         1 |                    1 |
| focused | `measurement`            |         28 |       13 |        13 |                    9 |
| focused | `sensor`                 |          7 |        2 |         2 |                    1 |
| noisy   | `science`                |         10 |        6 |         6 |                    2 |
| noisy   | `geolocation`            |         50 |        8 |         8 |                    5 |
| noisy   | `engineering`            |          5 |        1 |         1 |                    1 |
| noisy   | `simulation`             |          7 |        2 |         2 |                    2 |
| noisy   | `modeling`               |         14 |        4 |         4 |                    2 |
| noisy   | `modelling`              |          5 |        4 |         4 |                    1 |
| noisy   | `solver`                 |         12 |        4 |         4 |                    1 |
| noisy   | `formula`                |         31 |        4 |         4 |                    1 |
| noisy   | `calculation`            |         44 |        8 |         8 |                    6 |
| noisy   | `analytics`              |         50 |        4 |         4 |                    4 |
| noisy   | `data-science`           |         26 |        4 |         4 |                    2 |
| noisy   | `machine-learning`       |         50 |        7 |         7 |                    4 |
| noisy   | `telemetry`              |         50 |        4 |         4 |                    1 |
| noisy   | `iot`                    |         50 |        5 |         5 |                    4 |
| noisy   | `fluid`                  |         50 |        4 |         4 |                    0 |
| noisy   | `units`                  |         43 |       11 |        11 |                    9 |
| noisy   | `unit-conversion`        |         22 |        5 |         5 |                    2 |
| noisy   | `metrics`                |         50 |        5 |         5 |                    2 |
| noisy   | `coordinates`            |         50 |        6 |         6 |                    6 |
| noisy   | `distance`               |         50 |        6 |         6 |                    4 |

## Human interpretation

The science profile inspected all 250 selected repositories. It retained 25 controls from the general survey,
blacklisted the remaining repositories from that survey, and added 225 repositories. The tag search discovered 1,094
unique packages; 782 had eligible stable releases, and 15 abandoned packages were excluded before selection.

The nominal tag yields require substantial qualification. Astronomy produced collision findings for 8 of 9 selected
repositories, GIS for 9 of 16, measurement for 9 of 13, and coordinates for all 6. Much of that apparent signal comes
from unit-conversion libraries that already represent quantities with runtime objects, geographic APIs whose output unit
is selected by another argument, and ordinary words or internal conversions rather than fixed native scalar boundaries.
PHP files under test, fixture, example, benchmark, and demo directories are excluded from this snapshot; relevant README
and documentation files remain secondary discovery evidence.

`seamapi/seam` is the strongest new follow-up candidate. Its generated SDK exposes separately named Celsius and
Fahrenheit float fields and method parameters across its thermostat models and client. `telnyx/telnyx-php` also exposes
seconds and milliseconds across generated model boundaries, but its size, release velocity, and generated surface argue
for a deliberately bounded review. The fixed degree boundaries in `sobhanmohammadi/geometry` and
`tecnickcom/tc-lib-pdf-graph` may justify smaller source reviews, although their ecosystem value and adjacent-unit
collision potential are weaker.

GeoTools, Stadia Maps, Ricklab Location, and similar geographic results generally select a unit through a sibling
argument or object state and therefore fail the current [evaluation gates](planning.md#evaluation-gates). OPC UA results
mostly attach units through runtime metadata, while the highest-ranked measurement packages already use dedicated unit
objects or enums. The survey therefore adds targeted review leads but does not promote a package directly to the
roadmap.

## Ranked findings

1. **`andanteproject/measurement` (`v1.0.0`)** — focused; signature; 51 distinct units; 0 monthly downloads
   - Units: angle: degree, radian; data: byte, gibibyte, gigabyte, kibibyte, kilobyte, mebibyte, megabyte; energy:
     joule, kilojoule, kilowatt-hour, watt-hour; frequency: gigahertz, hertz, kilohertz, megahertz; length: centimetre,
     foot, inch, kilometre, metre, micrometre, mile, millimetre, yard; mass: gram, kilogram, milligram, ounce, pound;
     power: kilowatt, watt; pressure: kilopascal, pascal, psi; ratio: fraction, percent; temperature: celsius,
     fahrenheit, kelvin; time: day, hour, microsecond, millisecond, minute, nanosecond, second; volume: gallon, litre,
     millilitre
2. **`kolaybi/unit-converter` (`v1.0.0`)** — focused; signature; 50 distinct units; 0 monthly downloads
   - Units: angle: degree, radian; data: byte, gibibyte, gigabyte, kibibyte, kilobyte, mebibyte, megabyte; energy:
     joule, kilojoule, kilowatt-hour, watt-hour; frequency: gigahertz, hertz, kilohertz, megahertz; length: centimetre,
     foot, inch, kilometre, metre, micrometre, mile, millimetre, yard; mass: gram, kilogram, milligram, ounce, pound;
     power: kilowatt, watt; pressure: kilopascal, pascal; ratio: fraction, percent; temperature: celsius, fahrenheit,
     kelvin; time: day, hour, microsecond, millisecond, minute, nanosecond, second; volume: gallon, litre, millilitre
3. **`jamal/unit-converter` (`2.0.2`)** — focused; signature; 46 distinct units; 0 monthly downloads
   - Units: data: byte, gibibyte, gigabyte, kibibyte, kilobyte, mebibyte, megabyte; energy: joule, kilojoule,
     kilowatt-hour, watt-hour; frequency: gigahertz, hertz, kilohertz, megahertz; length: centimetre, foot, inch,
     kilometre, metre, micrometre, mile, millimetre, yard; mass: gram, kilogram, milligram, ounce, pound; power:
     kilowatt, watt; pressure: kilopascal, pascal, psi; temperature: celsius, fahrenheit, kelvin; time: day, hour,
     microsecond, millisecond, minute, second; volume: gallon, litre, millilitre
4. **`jobmetric/laravel-unit` (`2.1.0`)** — focused; signature; 42 distinct units; 8 monthly downloads
   - Units: angle: degree, radian; data: byte, gigabyte, kibibyte, kilobyte, megabyte; energy: joule, kilojoule;
     frequency: gigahertz, hertz, kilohertz, megahertz; length: centimetre, foot, inch, kilometre, metre, mile, yard;
     mass: gram, kilogram, milligram, ounce, pound; power: kilowatt, watt; pressure: kilopascal, pascal; temperature:
     celsius, fahrenheit, kelvin; time: day, hour, microsecond, millisecond, minute, nanosecond, second; volume: gallon,
     litre, millilitre
5. **`diversified-design/mesuraphp` (`v0.3.1`)** — focused; signature; 35 distinct units; 0 monthly downloads
   - Units: angle: degree, radian; energy: joule, kilojoule, kilowatt-hour, watt-hour; length: centimetre, foot, inch,
     kilometre, metre, micrometre, mile, millimetre, yard; mass: gram, kilogram, milligram, ounce, pound; power:
     kilowatt, watt; pressure: kilopascal, pascal; ratio: fraction, percent; temperature: celsius, fahrenheit, kelvin;
     time: day, hour, minute, second; volume: litre, millilitre
6. **`samsara/newton` (`v1.0.0`)** — curated; signature; 29 distinct units; 0 monthly downloads
   - Units: energy: joule; frequency: gigahertz, hertz, kilohertz, megahertz; length: centimetre, foot, inch, kilometre,
     metre, mile, millimetre, yard; mass: gram, kilogram, milligram; power: kilowatt, watt; pressure: kilopascal,
     pascal, psi; temperature: kelvin; time: day, hour, millisecond, minute, second; volume: gallon, litre
7. **`mibo/properties` (`1.2.0`)** — curated; signature; 26 distinct units; 0 monthly downloads
   - Units: angle: degree, radian; length: centimetre, foot, inch, kilometre, metre, mile, pica, twip, yard; mass: gram,
     kilogram, ounce, pound; ratio: fraction; temperature: celsius, fahrenheit, kelvin; time: day, hour, microsecond,
     minute, second; volume: gallon, litre
8. **`irrevion/science` (`0.0.5`)** — curated; signature; 20 distinct units; 0 monthly downloads
   - Units: angle: degree, radian; data: byte, kilobyte; energy: joule; frequency: hertz; length: kilometre, metre,
     mile; mass: kilogram, pound; power: watt; pressure: pascal; ratio: fraction; temperature: celsius, fahrenheit,
     kelvin; time: hour, minute, second
9. **`xynnn/unicorn` (`1.0.1`)** — noisy; signature; 20 distinct units; 0 monthly downloads
   - Units: data: byte, gibibyte, gigabyte, kibibyte, kilobyte, mebibyte, megabyte; length: centimetre, foot, inch,
     kilometre, metre, micrometre, mile, millimetre, yard; temperature: celsius, fahrenheit, kelvin; time: second
10. **`pdobrovolny/quantity` (`3.1.28`)** — noisy; signature; 19 distinct units; 176 monthly downloads

- Units: angle: degree; length: foot, inch, mile, yard; mass: kilogram, ounce, pound; temperature: celsius, fahrenheit,
  kelvin; time: day, hour, microsecond, minute, nanosecond, second; volume: gallon, litre

11. **`telnyx/telnyx-php` (`v7.96.0`)** — noisy; signature; 18 distinct units; 36902 monthly downloads

- Units: angle: degree; data: byte, gigabyte, kilobyte, mebibyte, megabyte; frequency: kilohertz; length: metre, pixel,
  point; ratio: fraction, percent; time: day, hour, microsecond, millisecond, minute, second

12. **`markrogoyski/math-php` (`v2.13.0`)** — curated; signature; 16 distinct units; 188333 monthly downloads

- Units: angle: degree, radian; data: byte; length: centimetre, inch, mile; mass: milligram, pound; pressure: pascal;
  ratio: fraction, percent; time: microsecond, millisecond, second; volume: gallon, litre

13. **`phpbench/phpbench` (`1.7.0`)** — focused; signature; 15 distinct units; 645275 monthly downloads

- Units: data: byte, gibibyte, gigabyte, kibibyte, kilobyte, mebibyte, megabyte; ratio: percent; time: day, hour,
  microsecond, millisecond, minute, nanosecond, second

14. **`stadiamaps/api` (`v5.1.0`)** — focused; signature; 14 distinct units; 116 monthly downloads

- Units: angle: degree; data: byte, gigabyte; length: foot, kilometre, metre, mile, point; ratio: percent; time: hour,
  microsecond, minute, nanosecond, second

15. **`rubix/ml` (`2.5.3`)** — noisy; signature; 14 distinct units; 58474 monthly downloads

- Units: angle: degree, radian; data: byte; length: pixel; mass: gram; ratio: fraction, percent; temperature: celsius,
  fahrenheit, kelvin; time: day, hour, microsecond, second

16. **`php-opcua/opcua-client` (`v4.4.1`)** — focused; signature; 12 distinct units; 32 monthly downloads

- Units: data: byte, gigabyte, kilobyte, megabyte; ratio: percent; time: day, hour, microsecond, millisecond, minute,
  nanosecond, second

17. **`aspose-cloud/aspose-words-cloud` (`26.7.0`)** — focused; signature; 11 distinct units; 5975 monthly downloads

- Units: angle: degree; data: byte; length: centimetre, inch, pixel, point; ratio: fraction, percent; time: microsecond,
  millisecond, second

18. **`sobhanmohammadi/geometry` (`v1.2.0`)** — focused; signature; 11 distinct units; 0 monthly downloads

- Units: angle: degree, radian; length: centimetre, foot, inch, kilometre, metre, mile, millimetre, yard; ratio:
  fraction

19. **`league/geotools` (`1.3.1`)** — curated; signature; 10 distinct units; 79537 monthly downloads

- Units: angle: degree, radian; length: foot, kilometre, metre, mile; pressure: pascal; ratio: fraction; time: minute,
  second

20. **`hi-folks/statistics` (`v1.5.2`)** — focused; signature; 10 distinct units; 6538 monthly downloads

- Units: angle: degree; length: kilometre, metre; ratio: fraction, percent; temperature: celsius, fahrenheit; time:
  hour, minute, second

21. **`techdock/opcua` (`v0.3.0`)** — noisy; signature; 9 distinct units; 65 monthly downloads

- Units: data: byte; ratio: percent; time: day, hour, microsecond, millisecond, minute, nanosecond, second

22. **`ricklab/location` (`v7.0.0`)** — curated; signature; 9 distinct units; 818 monthly downloads

- Units: angle: degree, radian; length: foot, metre, mile, yard; ratio: fraction; time: minute, second

23. **`seamapi/seam` (`v3.5.1`)** — noisy; signature; 9 distinct units; 37806 monthly downloads

- Units: angle: degree; ratio: fraction; temperature: celsius, fahrenheit; time: day, hour, millisecond, minute, second

24. **`vittix/panchang` (`v2.0.0`)** — curated; signature; 8 distinct units; 8 monthly downloads

- Units: angle: degree, radian; length: metre; ratio: fraction; time: day, hour, minute, second

25. **`mjaschen/phpgeo` (`6.0.2`)** — curated; signature; 8 distinct units; 198738 monthly downloads

- Units: angle: degree, radian; length: kilometre, metre, millimetre; ratio: fraction; time: minute, second

26. **`omelya/phpgeo` (`v1.0.0`)** — focused; signature; 8 distinct units; 16 monthly downloads

- Units: angle: degree, radian; length: kilometre, metre, millimetre; ratio: fraction; time: minute, second

27. **`wp-media/imagify-plugin` (`v1.6.9.1`)** — focused; signature; 8 distinct units; 1803 monthly downloads

- Units: data: byte, gigabyte, megabyte; ratio: percent; time: day, hour, minute, second

28. **`dreamfactory/df-core` (`1.0.16`)** — focused; signature; 8 distinct units; 86 monthly downloads

- Units: data: byte, kilobyte; time: day, hour, microsecond, millisecond, minute, second

29. **`ypid/suncalc` (`v1.7.0`)** — curated; signature; 7 distinct units; 252 monthly downloads

- Units: angle: radian; ratio: fraction; time: day, hour, millisecond, minute, second

30. **`bakame/tokei` (`0.3.0`)** — focused; signature; 7 distinct units; 0 monthly downloads

- Units: ratio: fraction; time: day, hour, microsecond, millisecond, minute, second

31. **`geokit/geokit` (`v1.3.0`)** — focused; signature; 7 distinct units; 11021 monthly downloads

- Units: angle: degree, radian; length: foot, kilometre, metre, mile; time: second

32. **`tuxonice/suncalc-php` (`v1.0.1`)** — curated; signature; 7 distinct units; 339 monthly downloads

- Units: angle: degree, radian; length: kilometre; ratio: fraction; time: day, hour, second

33. **`azuyalabs/yasumi` (`2.11.0`)** — noisy; signature; 6 distinct units; 457833 monthly downloads

- Units: length: kilometre, mile; pressure: pascal; time: day, hour, second

34. **`samsara/fermat` (`v2.1.1`)** — curated; signature; 6 distinct units; 0 monthly downloads

- Units: angle: degree, radian; data: byte; ratio: fraction, percent; time: second

35. **`lootils/geo` (`0.1.1`)** — noisy; signature; 6 distinct units; 345 monthly downloads

- Units: angle: degree, radian; length: metre, mile; time: minute, second

36. **`proj4php/proj4php` (`v2.0.19`)** — curated; signature; 6 distinct units; 58004 monthly downloads

- Units: angle: degree, radian; length: metre; time: millisecond, nanosecond, second

37. **`malenki/math` (`0.5.1`)** — focused; signature; 6 distinct units; 26 monthly downloads

- Units: angle: degree, radian; ratio: fraction, percent; time: minute, second

38. **`dwo/math` (`v1.0.0`)** — focused; signature; 6 distinct units; 0 monthly downloads

- Units: angle: degree, radian; ratio: fraction, percent; time: minute, second

39. **`acato-plugins/geophp` (`2.0.4`)** — focused; signature; 6 distinct units; 225 monthly downloads

- Units: angle: degree, radian; data: byte; length: metre; ratio: percent; time: second

40. **`gally90/geophp` (`v3.0.0`)** — focused; signature; 6 distinct units; 33 monthly downloads

- Units: angle: degree, radian; data: byte; length: metre; ratio: percent; time: second

41. **`shkilya/geophp` (`3.0.1`)** — focused; signature; 6 distinct units; 23 monthly downloads

- Units: angle: degree, radian; data: byte; length: metre; ratio: percent; time: second

42. **`matomo/matomo-php-tracker` (`4.0.1`)** — noisy; signature; 6 distinct units; 437725 monthly downloads

- Units: data: byte; time: day, hour, millisecond, minute, second

43. **`mjaschen/astrotools` (`1.0.0`)** — curated; signature; 6 distinct units; 0 monthly downloads

- Units: angle: degree; time: day, hour, microsecond, minute, second

44. **`rokka/client` (`1.22.0`)** — focused; signature; 6 distinct units; 3657 monthly downloads

- Units: data: byte; length: pixel; ratio: percent; time: hour, minute, second

45. **`philiprehberger/php-geo` (`v1.2.0`)** — focused; signature; 5 distinct units; 0 monthly downloads

- Units: angle: degree, radian; length: kilometre, metre, mile

46. **`funiq/geophp` (`v2.0.3`)** — focused; signature; 5 distinct units; 1045 monthly downloads

- Units: angle: degree, radian; data: byte; length: metre; time: second

47. **`jayeshmepani/swiss-ephemeris-ffi` (`v1.1.1`)** — curated; signature; 5 distinct units; 333 monthly downloads

- Units: angle: degree, radian; time: day, hour, second

48. **`poietic/flight-recorder` (`v0.1.0`)** — noisy; signature; 5 distinct units; 0 monthly downloads

- Units: data: byte; ratio: fraction; time: microsecond, millisecond, second

49. **`vancuren/php-turf` (`v1.0.4`)** — focused; signature; 5 distinct units; 16 monthly downloads

- Units: angle: degree, radian; length: kilometre, metre, mile

50. **`tecnickcom/tc-lib-pdf-graph` (`2.15.0`)** — focused; signature; 4 distinct units; 73065 monthly downloads

- Units: angle: degree, radian; ratio: fraction; time: second

51. **`adrian-cid/julian-converter` (`v1.0.1`)** — focused; signature; 4 distinct units; 3 monthly downloads

- Units: time: day, hour, minute, second

52. **`segmentio/analytics-php` (`3.8.2`)** — noisy; signature; 4 distinct units; 380855 monthly downloads

- Units: data: byte, kilobyte; time: millisecond, second

53. **`data-values/geo` (`4.6.0`)** — noisy; signature; 3 distinct units; 8323 monthly downloads

- Units: angle: degree; time: minute, second

54. **`baha2rmirzazadeh/phpmath` (`1.5.2`)** — focused; signature; 3 distinct units; 0 monthly downloads

- Units: angle: degree, radian; time: second

55. **`nubs/coordinator` (`v0.1.0`)** — curated; signature; 3 distinct units; 0 monthly downloads

- Units: angle: degree, radian; length: metre

56. **`jeorgy/laravel-postgis` (`v2.0.1`)** — focused; signature; 3 distinct units; 16 monthly downloads

- Units: length: kilometre, metre, mile

57. **`jlawrence/eos` (`v3.2.2`)** — focused; signature; 3 distinct units; 3796 monthly downloads

- Units: angle: degree, radian; time: second

58. **`djtommek/coordinates` (`2.1.1`)** — noisy; signature; 3 distinct units; 429 monthly downloads

- Units: angle: degree, radian; length: metre

59. **`amendozaaguiar/filament-route-statistics` (`4.2`)** — focused; signature; 3 distinct units; 188 monthly downloads

- Units: time: hour, minute, second

60. **`rubix/tensor` (`3.0.5`)** — curated; signature; 2 distinct units; 57727 monthly downloads

- Units: angle: degree, radian

61. **`codewithkyrian/transformers` (`0.5.3`)** — noisy; class; 12 distinct units; 13241 monthly downloads

- Units: angle: degree; data: byte, megabyte; frequency: hertz; length: kilometre, metre, pixel; mass: gram; ratio:
  fraction, percent; time: millisecond, second

62. **`giggsey/libphonenumber-for-php` (`9.0.36`)** — noisy; class; 6 distinct units; 3825307 monthly downloads

- Units: data: gigabyte; length: point; time: hour, microsecond, millisecond, second

63. **`uptimex/laravel-client` (`v0.2.2`)** — noisy; class; 5 distinct units; 365 monthly downloads

- Units: data: byte; time: microsecond, millisecond, minute, second

64. **`rkondratuk/geo-math-php` (`1.0.1`)** — focused; class; 5 distinct units; 253 monthly downloads

- Units: angle: degree, radian; length: metre, mile; time: second

65. **`jiri.jozif/datesuninfo` (`1.0.4`)** — focused; class; 5 distinct units; 2 monthly downloads

- Units: angle: degree; length: metre; time: hour, minute, second

66. **`geoip2/geoip2` (`v3.4.0`)** — noisy; class; 5 distinct units; 2512341 monthly downloads

- Units: data: byte; length: kilometre; ratio: percent; time: hour, second

67. **`niktomo/weighted-sample` (`v3.0.1`)** — focused; class; 5 distinct units; 0 monthly downloads

- Units: data: byte; ratio: percent; time: microsecond, millisecond, second

68. **`open-telemetry/sem-conv` (`1.38.0`)** — noisy; class; 4 distinct units; 2158514 monthly downloads

- Units: data: byte; length: pixel; time: millisecond, second

69. **`chriskonnertz/string-calc` (`v2.0.0`)** — focused; class; 4 distinct units; 16052 monthly downloads

- Units: angle: degree, radian; ratio: fraction; time: second

70. **`spatie/laravel-analytics` (`5.7.1`)** — noisy; class; 3 distinct units; 124385 monthly downloads

- Units: length: pixel; time: day, minute

71. **`longitude-one/geo-parser` (`3.0.1`)** — focused; class; 3 distinct units; 116587 monthly downloads

- Units: angle: degree; time: minute, second

72. **`phpmentors/domain-commons` (`v1.1.3`)** — noisy; class; 2 distinct units; 5014 monthly downloads

- Units: time: day, hour

73. **`opscale-co/nova-geospatial-fields` (`1.1.0`)** — focused; class; 2 distinct units; 2 monthly downloads

- Units: length: metre, pixel

74. **`helgesverre/toon` (`v3.2.1`)** — focused; package; 10 distinct units; 39630 monthly downloads

- Units: data: byte, kilobyte, megabyte; ratio: percent; time: day, hour, microsecond, millisecond, minute, second

75. **`vesper/unit-conversion` (`1.0.3`)** — focused; package; 8 distinct units; 0 monthly downloads

- Units: angle: radian; length: metre; mass: gram, kilogram, milligram; ratio: fraction; temperature: kelvin; time:
  second

76. **`jtejido/geodesy-php` (`1.41`)** — curated; package; 7 distinct units; 7234 monthly downloads

- Units: angle: degree, radian; length: kilometre, metre, mile, millimetre; time: second

77. **`nalabdou/algebra-php` (`1.1.0`)** — focused; package; 6 distinct units; 0 monthly downloads

- Units: data: byte; ratio: fraction; time: day, hour, millisecond, second

78. **`open-telemetry/gen-otlp-protobuf` (`1.10.0`)** — noisy; package; 5 distinct units; 1807698 monthly downloads

- Units: angle: degree; data: byte; ratio: fraction; time: nanosecond, second

79. **`brick/geo` (`0.13.1`)** — curated; package; 5 distinct units; 197418 monthly downloads

- Units: angle: degree, radian; data: byte; ratio: fraction; time: second

80. **`laratusk/larasvg` (`v2.5.1`)** — focused; package; 5 distinct units; 279 monthly downloads

- Units: length: pixel; ratio: fraction, percent; resolution: dpi; time: second

81. **`teqnogen/units` (`v1.2.1`)** — noisy; package; 5 distinct units; 0 monthly downloads

- Units: length: kilometre, metre; mass: gram, kilogram; ratio: fraction

82. **`tiamo/spss` (`2.2.2`)** — focused; package; 4 distinct units; 14366 monthly downloads

- Units: data: byte; ratio: percent; time: hour, second

83. **`flobee/spss` (`5.0.0`)** — focused; package; 4 distinct units; 163 monthly downloads

- Units: data: byte; ratio: percent; time: hour, second

84. **`rhubarbphp/module-stem` (`1.9.6`)** — noisy; package; 4 distinct units; 190 monthly downloads

- Units: ratio: percent; time: day, hour, second

85. **`danihidayatx/image-optimizer` (`v2.2.2`)** — focused; package; 4 distinct units; 4814 monthly downloads

- Units: length: pixel; ratio: fraction, percent; time: second

86. **`jkphl/iconizr` (`v1.0.2`)** — focused; package; 4 distinct units; 11 monthly downloads

- Units: data: kilobyte, megabyte; length: pixel; time: second

87. **`antikirra/probability` (`4.0.0`)** — focused; package; 4 distinct units; 191 monthly downloads

- Units: data: byte; ratio: fraction, percent; time: microsecond

88. **`ionux/phactor` (`v1.0.8`)** — focused; package; 3 distinct units; 3635 monthly downloads

- Units: data: byte; time: millisecond, second

89. **`helgesverre/chromadb` (`v3.0.0`)** — focused; package; 3 distinct units; 793 monthly downloads

- Units: data: byte; time: nanosecond, second

90. **`creof/geo-parser` (`2.2.1`)** — focused; package; 3 distinct units; 37522 monthly downloads

- Units: angle: degree; time: minute, second

91. **`nlp-tools/nlp-tools` (`v0.1.3`)** — noisy; package; 3 distinct units; 7338 monthly downloads

- Units: data: byte; time: millisecond, second

92. **`joshembling/image-optimizer` (`v1.6.4`)** — focused; package; 3 distinct units; 5208 monthly downloads

- Units: length: pixel; ratio: fraction, percent

93. **`nullform/app-timer` (`v2.1.0`)** — focused; package; 3 distinct units; 1 monthly downloads

- Units: data: byte; time: microsecond, second

94. **`nxp/math-executor` (`v2.3.8`)** — focused; package; 3 distinct units; 46219 monthly downloads

- Units: angle: degree, radian; time: second

95. **`bilfeldt/laravel-route-statistics` (`v4.4.0`)** — focused; package; 3 distinct units; 5769 monthly downloads

- Units: time: day, hour, minute

96. **`muaraicaptcha/muaraicaptcha` (`1.1.0`)** — noisy; package; 2 distinct units; 7 monthly downloads

- Units: time: minute, second

97. **`nicolasleborgne/moon-phases-calculator` (`v1.0.0`)** — focused; package; 2 distinct units; 0 monthly downloads

- Units: time: minute, second

98. **`maatify/data-fakes` (`v1.0.4`)** — noisy; package; 2 distinct units; 0 monthly downloads

- Units: time: millisecond, second

99. **`rotexsoft/callable-execution-timer` (`3.0.1`)** — focused; package; 2 distinct units; 0 monthly downloads

- Units: time: nanosecond, second

100. **`tarunkorat/laravel-asset-cleaner` (`v1.0.1`)** — focused; package; 2 distinct units; 1251 monthly downloads

- Units: data: byte, kilobyte

101. **`nlpcloud/nlpcloud-client` (`v1.0.41`)** — noisy; package; 2 distinct units; 545 monthly downloads

- Units: time: hour, second

102. **`uconv/uconv` (`1.1.0`)** — focused; repository; 4 distinct units; 0 monthly downloads

- Units: length: metre; mass: gram; time: hour, second

103. **`php-ai/php-ml` (`0.10.0`)** — noisy; single-unit; 4 distinct units; 45973 monthly downloads

- Units: angle: degree; length: pixel; mass: gram; ratio: fraction

104. **`alto/bezier` (`v1.0.0`)** — focused; single-unit; 3 distinct units; 0 monthly downloads

- Units: angle: degree; ratio: fraction; time: second

105. **`mcordingley/regression` (`2.2.0`)** — focused; single-unit; 3 distinct units; 7838 monthly downloads

- Units: angle: degree; length: foot; time: second

106. **`menarasolutions/geographer` (`v0.3.13`)** — noisy; single-unit; 3 distinct units; 45891 monthly downloads

- Units: data: byte; length: kilometre; time: millisecond

107. **`switchcat/periodic` (`v1.0.2`)** — curated; single-unit; 3 distinct units; 0 monthly downloads

- Units: angle: degree; temperature: kelvin; time: second

108. **`ionux/rapim` (`1.0.0`)** — focused; single-unit; 2 distinct units; 0 monthly downloads

- Units: data: byte; time: second

109. **`yellow-twins/fluid-lens` (`v0.8.0`)** — noisy; single-unit; 2 distinct units; 126 monthly downloads

- Units: mass: gram; time: second

110. **`bitandblack/measurement` (`2.2.0`)** — focused; single-unit; 2 distinct units; 55 monthly downloads

- Units: ratio: percent; time: second

111. **`alekseykorzun/memcached-wrapper-php` (`v2.0.0`)** — focused; single-unit; 2 distinct units; 1190 monthly
     downloads

- Units: data: byte; time: second

112. **`omaressaouaf/laravel-statistician` (`1.0.0`)** — focused; single-unit; 2 distinct units; 67 monthly downloads

- Units: ratio: percent; time: second

113. **`vaibhavpandeyvpz/ank` (`2.0.0`)** — focused; single-unit; 2 distinct units; 0 monthly downloads

- Units: angle: degree; length: pixel

114. **`maxmind-db/reader` (`v1.13.1`)** — noisy; single-unit; 2 distinct units; 3112384 monthly downloads

- Units: data: byte; time: second

115. **`rindow/rindow-math-matrix` (`2.1.2`)** — focused; single-unit; 2 distinct units; 14547 monthly downloads

- Units: data: byte; ratio: fraction

116. **`helgesverre/milvus` (`v0.3.1`)** — focused; single-unit; 2 distinct units; 154 monthly downloads

- Units: data: byte; ratio: fraction

117. **`spinen/laravel-geometry` (`2.9.2`)** — focused; single-unit; 2 distinct units; 9599 monthly downloads

- Units: angle: radian; length: metre

118. **`nubs/vectorix` (`v1.1.0`)** — focused; single-unit; 2 distinct units; 7 monthly downloads

- Units: angle: radian; time: second

119. **`satyapraneel/unit-conversion` (`1.1.0`)** — noisy; single-unit; 2 distinct units; 0 monthly downloads

- Units: length: pixel; mass: kilogram

120. **`sitegeist/fluid-tagbuilder` (`1.0.3`)** — noisy; single-unit; 2 distinct units; 570 monthly downloads

- Units: length: metre; time: hour

121. **`mykola-ivashchuk-gl/complex` (`2.0.0`)** — focused; single-unit; 2 distinct units; 0 monthly downloads

- Units: angle: radian; time: second

122. **`markbaker/complex` (`3.0.2`)** — curated; single-unit; 2 distinct units; 8972873 monthly downloads

- Units: angle: radian; time: second

123. **`traderinteractive/memoize` (`v3.0.0`)** — focused; single-unit; 2 distinct units; 3005 monthly downloads

- Units: ratio: percent; time: second

124. **`techies-africa/laravel-reverb-ui` (`v1.0.0`)** — noisy; single-unit; 2 distinct units; 189 monthly downloads

- Units: data: byte; time: minute

125. **`karomap/php-ogc` (`2.0.1`)** — focused; single-unit; 2 distinct units; 0 monthly downloads

- Units: length: metre; time: second

126. **`szczyglis/php-ulam-spiral-generator` (`v1.2.3`)** — focused; single-unit; 2 distinct units; 0 monthly downloads

- Units: length: pixel; time: second

127. **`koolreport/statistics` (`1.2.0`)** — focused; single-unit; 2 distinct units; 6543 monthly downloads

- Units: ratio: percent; time: second

128. **`omaralalwi/laravel-trash-cleaner` (`1.0.4`)** — focused; single-unit; 2 distinct units; 1547 monthly downloads

- Units: data: byte; length: pixel

129. **`richjenks/stats` (`v1.0`)** — focused; single-unit; 2 distinct units; 1378 monthly downloads

- Units: ratio: percent; time: second

130. **`devilsberg/laravel-mariadb-vector` (`v1.2.1`)** — focused; single-unit; 2 distinct units; 372 monthly downloads

- Units: data: byte; time: second

131. **`php-standard-library/collection` (`6.2.1`)** — focused; single-unit; 1 distinct units; 45707 monthly downloads

- Units: time: second

132. **`nxsys/library.data-telemetry` (`0.0.1`)** — focused; single-unit; 1 distinct units; 0 monthly downloads

- Units: length: metre

133. **`guillaumetissier/galois-fields` (`v1.3.1`)** — focused; single-unit; 1 distinct units; 0 monthly downloads

- Units: angle: degree

134. **`kirouane/interval` (`1.3.3`)** — focused; single-unit; 1 distinct units; 762 monthly downloads

- Units: time: second

135. **`short-edition/interval` (`1.3.3`)** — focused; single-unit; 1 distinct units; 397 monthly downloads

- Units: time: second

136. **`siriusphp/validation` (`4.0.0`)** — noisy; single-unit; 1 distinct units; 11932 monthly downloads

- Units: ratio: fraction

137. **`mibo/prices` (`2.0.1`)** — focused; single-unit; 1 distinct units; 0 monthly downloads

- Units: time: second

138. **`rumenx/php-seo` (`v1.1.0`)** — focused; single-unit; 1 distinct units; 668 monthly downloads

- Units: time: second

139. **`gestixi/php-matrix-sdk` (`1.0.0`)** — focused; single-unit; 1 distinct units; 184 monthly downloads

- Units: time: millisecond

140. **`robier/probability-checker` (`v1.0.2`)** — focused; single-unit; 1 distinct units; 0 monthly downloads

- Units: ratio: percent

141. **`dr-que/polynomial-regression` (`v1.2.1.1`)** — focused; single-unit; 1 distinct units; 1894 monthly downloads

- Units: angle: degree

142. **`andreekeberg/abby` (`1.1.1`)** — focused; single-unit; 1 distinct units; 267 monthly downloads

- Units: ratio: percent

143. **`rindow/rindow-math-buffer-ffi` (`1.0.6`)** — focused; single-unit; 1 distinct units; 47 monthly downloads

- Units: data: byte

144. **`longitude-one/wkb-parser` (`3.0.1`)** — focused; single-unit; 1 distinct units; 116928 monthly downloads

- Units: data: byte

145. **`geo-io/wkb-parser` (`v1.0.2`)** — focused; single-unit; 1 distinct units; 97127 monthly downloads

- Units: data: byte

146. **`creof/wkb-parser` (`v2.4`)** — focused; single-unit; 1 distinct units; 42970 monthly downloads

- Units: data: byte

147. **`beberlei/metrics` (`v2.11.0`)** — noisy; single-unit; 1 distinct units; 13492 monthly downloads

- Units: time: millisecond

148. **`oefenweb/damerau-levenshtein` (`v3.0.2`)** — noisy; single-unit; 1 distinct units; 3978 monthly downloads

- Units: time: second

149. **`numphp/numphp` (`v1.2.0`)** — focused; single-unit; 1 distinct units; 1995 monthly downloads

- Units: time: second

150. **`0to10/observability-php` (`2.0.2`)** — noisy; single-unit; 1 distinct units; 1843 monthly downloads

- Units: time: millisecond

151. **`tenqz/qdrant` (`v1.0.0`)** — focused; single-unit; 1 distinct units; 223 monthly downloads

- Units: time: second

152. **`lisachenko/native-php-matrix` (`0.2.0`)** — focused; single-unit; 1 distinct units; 0 monthly downloads

- Units: time: second

153. **`theprofessor/unit-conversion` (`1.0.0`)** — noisy; single-unit; 1 distinct units; 0 monthly downloads

- Units: mass: kilogram

154. **`dreamfactory/df-oauth` (`1.0.3`)** — focused; single-unit; 1 distinct units; 61 monthly downloads

- Units: time: second

155. **`tigrov/intldata` (`1.1.4`)** — focused; single-unit; 1 distinct units; 16 monthly downloads

- Units: time: microsecond

156. **`leaditin/distribution` (`1.0.1`)** — focused; single-unit; 1 distinct units; 0 monthly downloads

- Units: ratio: percent

157. **`siriusphp/orm` (`2.0.0`)** — noisy; single-unit; 1 distinct units; 0 monthly downloads

- Units: time: second

158. **`psr/cache` (`3.0.0`)** — curated; single-unit; 1 distinct units; 14114514 monthly downloads

- Units: time: second

159. **`open-telemetry/exporter-otlp` (`1.4.0`)** — noisy; single-unit; 1 distinct units; 1836599 monthly downloads

- Units: data: byte

160. **`php-standard-library/vec` (`6.2.1`)** — focused; single-unit; 1 distinct units; 39591 monthly downloads

- Units: time: second

161. **`krowinski/bcmath-extended` (`8.1.0`)** — focused; single-unit; 1 distinct units; 31387 monthly downloads

- Units: ratio: percent

162. **`php-science/textrank` (`1.2.3`)** — noisy; single-unit; 1 distinct units; 17342 monthly downloads

- Units: ratio: percent

163. **`ps/image-optimizer` (`2.0.5`)** — focused; single-unit; 1 distinct units; 6138 monthly downloads

- Units: time: second

164. **`webmozart/expression` (`1.0.0`)** — noisy; single-unit; 1 distinct units; 3918 monthly downloads

- Units: time: second

165. **`andritiana/mysql8-doctrine2-spatial` (`1.2.2`)** — focused; single-unit; 1 distinct units; 1658 monthly
     downloads

- Units: length: point

166. **`sqlite-vec/sqlite-vec` (`v0.1.1`)** — focused; single-unit; 1 distinct units; 1650 monthly downloads

- Units: data: byte

167. **`mathjax/mathjax` (`4.1.3`)** — focused; single-unit; 1 distinct units; 1061 monthly downloads

- Units: time: second

168. **`serendipity_hq/component-text-matrix` (`3.0.7`)** — focused; single-unit; 1 distinct units; 984 monthly
     downloads

- Units: length: pixel

169. **`fitztrev/laravel-html-minify` (`1.0.3`)** — focused; single-unit; 1 distinct units; 856 monthly downloads

- Units: data: byte

170. **`kerigard/lpsolve` (`v1.1.3`)** — noisy; single-unit; 1 distinct units; 681 monthly downloads

- Units: time: second

171. **`tonymans33/video-optimizer` (`v2.0.0`)** — focused; single-unit; 1 distinct units; 578 monthly downloads

- Units: data: megabyte

172. **`bcremer/doctrine-mysql-spatial` (`2.0.1`)** — focused; single-unit; 1 distinct units; 478 monthly downloads

- Units: length: point

173. **`javaabu/geospatial` (`v1.7.0`)** — focused; single-unit; 1 distinct units; 176 monthly downloads

- Units: time: second

174. **`dreamfactory/df-rackspace` (`0.16.0`)** — focused; single-unit; 1 distinct units; 136 monthly downloads

- Units: data: byte

175. **`markbaker/complex-functions` (`1.0.1`)** — focused; single-unit; 1 distinct units; 83 monthly downloads

- Units: time: second

176. **`assisted-mindfulness/naive-bayes` (`2.0.1`)** — focused; single-unit; 1 distinct units; 71 monthly downloads

- Units: time: day

177. **`ici-be/ici-tools` (`0.3.5`)** — focused; single-unit; 1 distinct units; 69 monthly downloads

- Units: length: metre

178. **`slkxmail/doctrine2-spatial` (`1.2.1`)** — focused; single-unit; 1 distinct units; 52 monthly downloads

- Units: length: point

179. **`serhiiorlovs/doctrine2-spatial` (`1.0.2`)** — focused; single-unit; 1 distinct units; 17 monthly downloads

- Units: length: point

180. **`thelorry/doctrine2-spatial` (`1.3.1`)** — focused; single-unit; 1 distinct units; 13 monthly downloads

- Units: length: point

181. **`sandfox/kdtree` (`0.1.0`)** — focused; single-unit; 1 distinct units; 6 monthly downloads

- Units: time: second

182. **`smoren/probability-selector` (`v2.1.0`)** — focused; single-unit; 1 distinct units; 1 monthly downloads

- Units: time: second

183. **`cafuego/phits` (`0.0.4`)** — focused; single-unit; 1 distinct units; 0 monthly downloads

- Units: ratio: percent

184. **`dreamfactory/dreamfactory` (`2.4.2`)** — focused; single-unit; 1 distinct units; 0 monthly downloads

- Units: time: second

185. **`karomap/laravel-geo` (`2.0.1`)** — focused; single-unit; 1 distinct units; 0 monthly downloads

- Units: angle: radian

186. **`mibo/prices-combinations` (`2.0.0`)** — focused; single-unit; 1 distinct units; 0 monthly downloads

- Units: ratio: percent

187. **`unoapp-dev/laravel-geo` (`1.1.1`)** — focused; single-unit; 1 distinct units; 0 monthly downloads

- Units: angle: radian

188. **`woisks/textrank` (`1.1`)** — noisy; single-unit; 1 distinct units; 0 monthly downloads

- Units: ratio: percent

189. **`markbaker/matrix` (`3.0.1`)** — curated; none; 0 distinct units; 8966318 monthly downloads
190. **`torann/geoip` (`3.0.10`)** — noisy; none; 0 distinct units; 398920 monthly downloads
191. **`prestashop/decimal` (`1.5.0`)** — focused; none; 0 distinct units; 200112 monthly downloads
192. **`longitude-one/wkt-parser` (`3.0.1`)** — focused; none; 0 distinct units; 126377 monthly downloads
193. **`geo-io/interface` (`v1.0.1`)** — focused; none; 0 distinct units; 97715 monthly downloads
194. **`creof/wkt-parser` (`2.2.0`)** — focused; none; 0 distinct units; 39278 monthly downloads
195. **`php-decimal/stubs` (`v1.1.0`)** — focused; none; 0 distinct units; 29961 monthly downloads
196. **`php-decimal/php-decimal` (`v1.1.0`)** — focused; none; 0 distinct units; 29603 monthly downloads
197. **`madorin/matex` (`v1.0.2`)** — focused; none; 0 distinct units; 16181 monthly downloads
198. **`rindow/rindow-matlib-ffi` (`1.1.3`)** — focused; none; 0 distinct units; 13290 monthly downloads
199. **`denissimon/formula-parser` (`v2.7.2`)** — focused; none; 0 distinct units; 12708 monthly downloads
200. **`mcordingley/linearalgebra` (`3.0.0`)** — curated; none; 0 distinct units; 8032 monthly downloads
201. **`signalwire-community/signalwire` (`v3.2.0`)** — noisy; none; 0 distinct units; 7684 monthly downloads
202. **`nunomaduro/laravel-optimize-database` (`v1.0.5`)** — focused; none; 0 distinct units; 7590 monthly downloads
203. **`phpmentors/domain-kata` (`v1.4.0`)** — noisy; none; 0 distinct units; 7001 monthly downloads
204. **`upstash/vector` (`v1.2.0`)** — focused; none; 0 distinct units; 6642 monthly downloads
205. **`scotteuser/pinecone-php` (`1.0.4`)** — focused; none; 0 distinct units; 4974 monthly downloads
206. **`drupol/phpermutations` (`1.4.0`)** — focused; none; 0 distinct units; 4508 monthly downloads
207. **`pragmarx/countries-laravel` (`v0.7.0`)** — focused; none; 0 distinct units; 3833 monthly downloads
208. **`lara-zeus/matrix-choice` (`5.0.1`)** — focused; none; 0 distinct units; 3330 monthly downloads
209. **`donatj/php-dnf-solver` (`v0.5.0`)** — noisy; none; 0 distinct units; 1733 monthly downloads
210. **`toin0u/geotools-laravel` (`1.0.0`)** — focused; none; 0 distinct units; 955 monthly downloads
211. **`sciphp/numphp` (`0.4.0`)** — curated; none; 0 distinct units; 779 monthly downloads
212. **`oefenweb/statistics` (`v3.0.1`)** — focused; none; 0 distinct units; 698 monthly downloads
213. **`spiral/otel-bridge` (`1.2.3`)** — noisy; none; 0 distinct units; 696 monthly downloads
214. **`xeeeveee/sudoku` (`0.2.1`)** — noisy; none; 0 distinct units; 353 monthly downloads
215. **`peterbodnar.com/mx2svg` (`1.0.0`)** — focused; none; 0 distinct units; 178 monthly downloads
216. **`cowegis/cowegis-geojson` (`1.1.1`)** — focused; none; 0 distinct units; 154 monthly downloads
217. **`laravel-enso/measurement-units` (`3.8.0`)** — noisy; none; 0 distinct units; 144 monthly downloads
218. **`thedataist/drill-connector` (`v0.1.1`)** — noisy; none; 0 distinct units; 137 monthly downloads
219. **`dreamfactory/df-rws` (`0.18.2`)** — focused; none; 0 distinct units; 133 monthly downloads
220. **`dreamfactory/df-couchdb` (`0.19.0`)** — focused; none; 0 distinct units; 131 monthly downloads
221. **`ritey/laravel-manticore` (`v2.0.4`)** — focused; none; 0 distinct units; 127 monthly downloads
222. **`decodelabs/fluidity` (`v0.3.7`)** — noisy; none; 0 distinct units; 113 monthly downloads
223. **`laravel-enso/packaging-units` (`2.6.1`)** — noisy; none; 0 distinct units; 91 monthly downloads
224. **`cozy/value-objects` (`v0.1.1`)** — focused; none; 0 distinct units; 85 monthly downloads
225. **`dreamfactory/df-sqldb` (`1.4.3`)** — focused; none; 0 distinct units; 68 monthly downloads
226. **`dreamfactory/df-user` (`0.17.2`)** — focused; none; 0 distinct units; 60 monthly downloads
227. **`mouf/html.widgets.statsgrid` (`v1.0.0`)** — focused; none; 0 distinct units; 38 monthly downloads
228. **`rodriados/mathr` (`v3.0`)** — focused; none; 0 distinct units; 31 monthly downloads
229. **`dreamfactory/df-mongodb` (`0.22.1`)** — focused; none; 0 distinct units; 18 monthly downloads
230. **`saggre/weighted-random` (`1.0.0`)** — focused; none; 0 distinct units; 10 monthly downloads
231. **`ins0/zf2-analytics` (`1.0.0`)** — focused; none; 0 distinct units; 6 monthly downloads
232. **`remind/typo3-fluid-viewhelper` (`2.0.0`)** — noisy; none; 0 distinct units; 6 monthly downloads
233. **`markbaker/matrix-functions` (`1.0.1`)** — focused; none; 0 distinct units; 5 monthly downloads
234. **`phpdepend/phpdepend` (`0.1.0`)** — focused; none; 0 distinct units; 3 monthly downloads
235. **`irfa/php-gatcha` (`v2.0`)** — focused; none; 0 distinct units; 2 monthly downloads
236. **`balpom/entity` (`v0.3.0`)** — noisy; none; 0 distinct units; 0 monthly downloads
237. **`devmarketer/laramongo` (`v1.0.0`)** — focused; none; 0 distinct units; 0 monthly downloads
238. **`dreamfactory/javascript-sdk` (`1.0.18`)** — focused; none; 0 distinct units; 0 monthly downloads
239. **`eriksulymosi/geojson` (`v2.0`)** — focused; none; 0 distinct units; 0 monthly downloads
240. **`fieg/propositional-calculus` (`1.0`)** — focused; none; 0 distinct units; 0 monthly downloads
241. **`fiisoft/molecular-weight-calc` (`1.1.1`)** — curated; none; 0 distinct units; 0 monthly downloads
242. **`jerry58321/probability-random` (`v1.0.0`)** — focused; none; 0 distinct units; 0 monthly downloads
243. **`mykola-ivashchuk-gl/matrix` (`2.1.1`)** — focused; none; 0 distinct units; 0 monthly downloads
244. **`nemesis/laragis` (`1.0.3`)** — focused; none; 0 distinct units; 0 monthly downloads
245. **`notrix/php-bcmath` (`v0.3`)** — focused; none; 0 distinct units; 0 monthly downloads
246. **`php-science/pagerank` (`1.0.1`)** — noisy; none; 0 distinct units; 0 monthly downloads
247. **`s-mcdonald/functions` (`0.3.0`)** — focused; none; 0 distinct units; 0 monthly downloads
248. **`shapeways/shapeways` (`v1.02`)** — noisy; none; 0 distinct units; 0 monthly downloads
249. **`vansales/uconvert` (`1.0`)** — noisy; none; 0 distinct units; 0 monthly downloads
250. **`xihe/xihe` (`0.0.1`)** — focused; none; 0 distinct units; 0 monthly downloads

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

1. `andanteproject/measurement` — signature
2. `kolaybi/unit-converter` — signature
3. `jamal/unit-converter` — signature
4. `jobmetric/laravel-unit` — signature
5. `diversified-design/mesuraphp` — signature
6. `samsara/newton` — signature
7. `mibo/properties` — signature
8. `irrevion/science` — signature
9. `xynnn/unicorn` — signature
10. `pdobrovolny/quantity` — signature
11. `telnyx/telnyx-php` — signature
12. `markrogoyski/math-php` — signature
13. `phpbench/phpbench` — signature
14. `stadiamaps/api` — signature
15. `rubix/ml` — signature
16. `php-opcua/opcua-client` — signature
17. `aspose-cloud/aspose-words-cloud` — signature
18. `sobhanmohammadi/geometry` — signature
19. `league/geotools` — signature
20. `hi-folks/statistics` — signature
21. `techdock/opcua` — signature
22. `ricklab/location` — signature
23. `seamapi/seam` — signature
24. `vittix/panchang` — signature
25. `mjaschen/phpgeo` — signature
26. `azuyalabs/yasumi` — signature
27. `lootils/geo` — signature
28. `matomo/matomo-php-tracker` — signature
29. `poietic/flight-recorder` — signature
30. `segmentio/analytics-php` — signature

> Automated findings are discovery leads. Verify units and scales against upstream source, tests, and documentation
> before adding an integration.
