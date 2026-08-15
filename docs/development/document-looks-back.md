# The Document Looks Back integration

Yumemi Apocrypha's mdBook mounts The Document Looks Back from Doctrine of the Second Sun. An occasional letter in
article prose forms one eye, notices the reader, and returns to ordinary type without replacing text or changing layout.

The upstream JavaScript and stylesheet are copied verbatim into `docs/pages/assets/doctrine-web/`. Heliogenesis and The
Document Looks Back distribute identical Three.js r185 files, so both integrations use the one shared `vendor/`
directory at that asset root. Keeping the module files beside the shared directory preserves their upstream relative
imports without adapting either runtime.

Apocrypha-specific mounting remains in `docs/theme/yumemi.js`. The adapter waits 45–90 seconds before loading the module
and shared Three.js runtime. The controller scans only paragraphs and list items inside `#mdbook-content > main`;
upstream defaults exclude links, code, controls, hidden content, and editable content. One eye may appear at a time,
with 45–90 seconds between automatic attempts.

The adapter resets the effect before printing and while Heliogenesis is active, then resumes its schedule after printing
or when Heliogenesis returns to idle. The upstream reduced-motion treatment, visibility reset, and renderer fallback
remain intact. The browser-rendered source glyph remains in place while WebGL draws only the added fill and eye,
avoiding the former light-on-dark contrast and subpixel-alignment problems.

For manual browser verification, `window.documentLooksBack.summon()` cancels the pending delay and attempts one eye
immediately through the same visibility, print, and Heliogenesis gates.

`DoctrineBrowserAssetsTest` verifies both copied runtimes, the shared Three.js files, the Doctrine license, the locked
revision in the provenance notice, and the mdBook mounting configuration. When the Doctrine pin advances, review both
browser integrations before copying changed assets. Do not edit copied files to hide drift. If the integrations stop
shipping identical Three.js files, restore separate vendor directories rather than silently selecting one version.
