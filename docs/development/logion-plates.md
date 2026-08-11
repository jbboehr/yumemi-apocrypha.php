# Documentation logion plates

This document records the curated relationship between each public mdBook page, one existing Yumemi Apocrypha logion,
and one original illustration. The source declaration remains the canonical assignment and text of each logion. Its
appearance in the book is a cited republication, not a second allocation or a semantic annotation of the declaration.

Selection favors literary quality and thematic resonance with the page while preserving every source assignment. Images
interpret the selected logion under the Doctrine image guide; they do not depict software concepts literally. The
layout, asset boundary, ledger, and verification approach adapt the setup on Akashi `master` at revision
`71221927cfe9a364186d70c0f6e0d43992cd6f37`. No Akashi artwork was reused or supplied as a generation reference.

Each generated illustration has a 3840-by-2160 archival WebP at
`docs/development/images/logia/BOOK-CHAPTER_VERSE-hq.webp` and a 960-by-540 delivery WebP at
`docs/pages/images/logia/BOOK-CHAPTER_VERSE.webp`. The latter has one-sixteenth the pixel area and is the only version
embedded in the book. Git export-ignore rules, which Composer archive honors, exclude both image sets from the package
archive. The explicit `-hq` rule is defensive beneath the development-tree exclusion, preserving the archival boundary
if the broader package layout changes later.

Each public page except the Introduction receives exactly one plate; the Introduction uses the existing Yumemi Apocrypha
banner. The quotation appears to the left and the illustration to the right on wide screens, then stacks on narrow
screens. Each plate appears directly below its page title, before the technical introduction begins.

| Page                                       | Citation  | Canonical source                                                     | Visual center                                                       | Status                |
| ------------------------------------------ | --------- | -------------------------------------------------------------------- | ------------------------------------------------------------------- | --------------------- |
| `README.md`                                | —         | —                                                                    | Existing Yumemi Apocrypha banner                                    | Intentional exception |
| `getting-started.md`                       | OSD 69:22 | `ConfiguredIntegrationStubFilesExtension::__construct()`             | A bare highest stair receiving an ordered name of rain              | Complete              |
| `integrations.md`                          | SFA 15:50 | `ConfiguredIntegrationStubFilesExtension::usesUnitBoundaryAdapter()` | Three restrained tides revealing a green island beneath three moons | Complete              |
| `contributing/maintaining-integrations.md` | AWC 12:17 | `PackageIntegrationUnitBoundaryExtension::$selection`                | A shattered lens preserving one star as it returns to its keepers   | Complete              |

## Generation record

The three plates were generated on 2026-08-11 after the repository-wide logia audit in
`a556008eae1d413fab5cb89a59664ad2baf10f32`. Doctrine revision `5c2c843c4d0f898eb5792e94187a74b2ce585ad5` governed visual
interpretation. Generation used the built-in image tool; the accepted PNG outputs were center-cropped from 1672 by 941
pixels to 1664 by 936 pixels, then resampled into the archival and delivery WebPs.

The source text determined each visual center before any arbitrary choice was sampled. Settings left genuinely
underdetermined and dominant degrees of literalness were selected from the Doctrine image guide's priors using
operating-system entropy. Second Sun weather was selected independently from three viable treatments. No valid result
was rerolled to improve apparent variety.

| Citation  | Local setting | Dominant treatment | Second Sun weather                                             |
| --------- | ------------- | ------------------ | -------------------------------------------------------------- |
| OSD 69:22 | Occidental    | Symbolic           | Electric-blue synthetic noon with crystalline lightning-rain   |
| SFA 15:50 | Abstract      | Environmental      | Rose-gold impossible dawn with ordered luminous tidal bands    |
| AWC 12:17 | Japanese      | Environmental      | Midnight navy beneath a geometric constellation and cold bloom |

## Acceptance rules

- Preserve the canonical source text and citation exactly.
- Use each citation and illustration on only one public page.
- Keep image text absent; the adjacent HTML supplies the quotation and citation.
- Give each image concise alt text describing visible content rather than repeating the quotation.
- Place each plate directly below the page-level title.
- Transfigure the whole image through a recognizable and meaningful Second Sun atmosphere with an integrated retrowave
  or synthwave anchor, rather than adding a token neon accent.
- Keep culturally legible material coherent within each image while preserving the series' wider impossible
  Japanese–Occidental civilization.
- Verify responsive rendering, meaningful alt text, source-text fidelity, image existence, and page coverage.
