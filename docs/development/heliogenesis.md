# Heliogenesis integration

Yumemi Apocrypha's mdBook mounts the optional Heliogenesis browser integration from Doctrine of the Second Sun. The
trigger in the mdBook toolbar starts a temporary **Dawning of the Second Sun** event; ordinary documentation behavior
remains the default. The mounting adapter selects the `synthwave` photosphere explicitly so a later upstream default
cannot silently change the site's presentation.

The upstream runtime is copied verbatim into `docs/pages/assets/doctrine-web/`. Committing those assets keeps mdBook,
GitHub Pages, and the Nix documentation derivation independent of a populated `vendor/` directory. The copied notice
records the Doctrine revision and preserves both the Doctrine and Three.js license texts. Apocrypha-specific mounting
and shell selection remain in `docs/theme/yumemi.js` and `docs/theme/yumemi.css` rather than modifying the upstream
runtime. The integration lights the page wrapper and navigation chrome but deliberately leaves the article unmarked so
the reader's selected mdBook theme retains its content colors throughout the event. The unmarked article also supplies
no elements to Heliogenesis's optional document-tomography effect; the environmental animation remains active.

The Document Looks Back uses the same asset root. Both upstream integrations distribute identical Three.js r185 files,
so the committed `vendor/` directory serves both without adapting either integration's relative imports.

The integration preserves the upstream lifecycle boundaries:

- mounting does not allocate a WebGL context;
- hover, keyboard focus, or activation prepares the renderer;
- reduced-motion users receive the upstream static treatment;
- initialization failure removes or disables the optional effect without disabling the documentation;
- the controller restores the normal document state after the event.

`DoctrineBrowserAssetsTest` verifies that the public runtime remains byte-for-byte identical to the Composer-installed
copy and that the provenance notice names the locked Doctrine revision. The lowest-dependencies CI job excludes these
two lock-sensitive checks because that job intentionally replaces the committed lock; the normal PHP matrix continues to
enforce both. When the Doctrine pin advances, review both browser integrations before copying changed assets; do not
modify a copied file to hide drift.

If Apocrypha later removes only Heliogenesis, remove the `heliogenesis*` assets, toolbar mount,
`markHeliogenesisShell()`, and Heliogenesis hooks in `yumemi.css`. Revise the shared notice and
`DoctrineBrowserAssetsTest`, but retain the `doctrine-web/` root, license, shared `vendor/` directory, and narrowed
drift test while [The Document Looks Back](document-looks-back.md) remains.
