/* global path_to_root */

(function () {
    "use strict";

    // mdBook only supplies headings for the active page. Keep this outline synchronized with public h2/h3 headings.
    const headingsByChapter = {
        "index.html": [{ id: "start-here", title: "Start Here" }],
        "getting-started.html": [
            { id: "installation", title: "Installation" },
            { id: "automatic-registration", title: "Automatic Registration" },
            { id: "select-integrations", title: "Select Integrations" },
            { id: "autodetect-integrations", title: "Autodetect Integrations" },
            { id: "manual-registration", title: "Manual Registration" },
            { id: "verify-analysis", title: "Verify Analysis" },
            { id: "troubleshooting", title: "Troubleshooting" },
        ],
        "integrations.html": [
            { id: "version-policy", title: "Version Policy" },
            { id: "compatibility-before-10", title: "Compatibility Before 1.0" },
            { id: "carbon", title: "Carbon" },
            { id: "guzzle", title: "Guzzle" },
            { id: "getid3", title: "getID3" },
            { id: "illuminate-auth", title: "Illuminate Auth" },
            { id: "illuminate-cache", title: "Illuminate Cache" },
            { id: "illuminate-console", title: "Illuminate Console" },
            { id: "illuminate-cookie", title: "Illuminate Cookie" },
            { id: "illuminate-database", title: "Illuminate Database" },
            { id: "illuminate-filesystem", title: "Illuminate Filesystem" },
            { id: "illuminate-http", title: "Illuminate HTTP" },
            { id: "illuminate-support", title: "Illuminate Support" },
            { id: "illuminate-process", title: "Illuminate Process" },
            { id: "illuminate-queue", title: "Illuminate Queue" },
            { id: "illuminate-redis", title: "Illuminate Redis" },
            { id: "illuminate-routing", title: "Illuminate Routing" },
            { id: "illuminate-session", title: "Illuminate Session" },
            { id: "illuminate-validation", title: "Illuminate Validation" },
            { id: "intervention-image", title: "Intervention Image" },
            { id: "measurements", title: "Measurements" },
            { id: "phpgeo", title: "phpgeo" },
            { id: "symfony-httpfoundation", title: "Symfony HttpFoundation" },
            { id: "symfony-stopwatch", title: "Symfony Stopwatch" },
            { id: "limitations", title: "Limitations" },
        ],
        "contributing/maintaining-integrations.html": [
            { id: "before-changing-an-integration", title: "Before Changing An Integration" },
            { id: "verify-upstream-signatures", title: "Verify Upstream Signatures" },
            { id: "verify-package-behavior", title: "Verify Package Behavior" },
            { id: "add-an-integration", title: "Add An Integration" },
            { id: "compatibility-decisions", title: "Compatibility Decisions" },
        ],
    };

    function createHeadingList(pageUrl, headings) {
        const list = document.createElement("ol");
        list.classList.add("section");

        for (const heading of headings) {
            const item = document.createElement("li");
            item.classList.add("header-item");

            const wrapper = document.createElement("span");
            wrapper.classList.add("chapter-link-wrapper");

            const link = document.createElement("a");
            link.href = `${pageUrl}#${heading.id}`;
            link.textContent = heading.title;

            wrapper.append(link);
            item.append(wrapper);

            if (heading.children) {
                item.classList.add("expanded");
                item.append(createHeadingList(pageUrl, heading.children));
            }

            list.append(item);
        }

        return list;
    }

    function addDoctrineStylesheet(assetRoot, filename) {
        const stylesheet = document.createElement("link");
        stylesheet.rel = "stylesheet";
        stylesheet.href = new URL(filename, assetRoot).href;
        document.head.append(stylesheet);
    }

    function doctrineWebAssetRoot() {
        return new URL(path_to_root + "assets/doctrine-web/", document.location.href);
    }

    function markHeliogenesisShell() {
        const world = document.querySelector("#mdbook-page-wrapper") ?? document.body;
        world.dataset.heliogenesisWorld = "";

        for (const selector of ["#mdbook-menu-bar", "#mdbook-sidebar"]) {
            const element = document.querySelector(selector);
            if (element) {
                element.dataset.heliogenesisChrome = "";
            }
        }
    }

    async function mountHeliogenesis() {
        const controls = document.querySelector("#mdbook-menu-bar .right-buttons");
        if (!controls) {
            return;
        }

        const assetRoot = doctrineWebAssetRoot();
        addDoctrineStylesheet(assetRoot, "heliogenesis.css");
        addDoctrineStylesheet(assetRoot, "heliogenesis-document.css");
        markHeliogenesisShell();

        const trigger = document.createElement("button");
        trigger.id = "yumemi-second-sun";
        trigger.type = "button";
        trigger.title = "Dawn the Second Sun";
        trigger.setAttribute("aria-label", "Dawn the Second Sun");
        controls.prepend(trigger);

        try {
            const moduleUrl = new URL("heliogenesis.js", assetRoot);
            const { Heliogenesis } = await import(moduleUrl.href);
            const heliogenesis = new Heliogenesis({ trigger, sunStyle: "synthwave" });
            heliogenesis.mount();
        } catch (error) {
            trigger.remove();
            console.error("Unable to mount Heliogenesis.", error);
        }
    }

    function mountDocumentLooksBack() {
        const root = document.querySelector("#mdbook-content > main");
        if (!root) {
            return;
        }

        const assetRoot = doctrineWebAssetRoot();
        addDoctrineStylesheet(assetRoot, "document-looks-back.css");
        const frequency = { min: 45000, max: 90000 };
        let controllerPromise = null;
        let documentLooksBack = null;
        let timer = 0;
        let printing = false;
        let heliogenesisActive = Boolean(
            document.documentElement.dataset.heliogenesisState &&
            document.documentElement.dataset.heliogenesisState !== "idle",
        );

        function clearTimer() {
            if (timer) {
                window.clearTimeout(timer);
                timer = 0;
            }
        }

        function blocked() {
            return document.hidden || printing || heliogenesisActive;
        }

        function reset() {
            clearTimer();
            documentLooksBack?.reset();
        }

        function loadController() {
            if (!controllerPromise) {
                const moduleUrl = new URL("document-looks-back.js", assetRoot);
                controllerPromise = import(moduleUrl.href).then(({ DocumentLooksBack }) => {
                    documentLooksBack = new DocumentLooksBack({
                        frequency: 0,
                        maxEyes: 1,
                        root,
                        selector: "p, li",
                    });
                    documentLooksBack.mount();
                    return documentLooksBack;
                });
            }

            return controllerPromise;
        }

        async function attempt() {
            if (blocked()) {
                return;
            }

            try {
                const controller = await loadController();
                if (blocked()) {
                    controller.reset();
                    return;
                }
                controller.summon();
                schedule();
            } catch (error) {
                console.error("Unable to mount Document Looks Back.", error);
            }
        }

        function schedule() {
            if (timer || blocked()) {
                return;
            }

            const delay = frequency.min + Math.random() * (frequency.max - frequency.min);
            timer = window.setTimeout(() => {
                timer = 0;
                void attempt();
            }, delay);
        }

        for (const eventName of ["dawning", "radiant", "receding"]) {
            document.documentElement.addEventListener(`heliogenesis:${eventName}`, () => {
                heliogenesisActive = true;
                reset();
            });
        }

        document.documentElement.addEventListener("heliogenesis:idle", () => {
            heliogenesisActive = false;
            schedule();
        });
        document.addEventListener("visibilitychange", () => {
            if (document.hidden) {
                reset();
            } else {
                schedule();
            }
        });
        window.addEventListener("beforeprint", () => {
            printing = true;
            reset();
        });
        window.addEventListener("afterprint", () => {
            printing = false;
            schedule();
        });

        window.documentLooksBack = Object.freeze({
            summon() {
                clearTimer();
                return attempt();
            },
        });

        schedule();
    }

    document.addEventListener("DOMContentLoaded", function () {
        const chapterLinks = document.querySelectorAll("#mdbook-sidebar .chapter-item > .chapter-link-wrapper > a");

        for (const [chapterPath, headings] of Object.entries(headingsByChapter)) {
            const pageUrl = new URL(path_to_root + chapterPath, document.location.href);
            const chapterLink = Array.from(chapterLinks).find(function (link) {
                return new URL(link.href, document.location.href).href === pageUrl.href;
            });

            if (!chapterLink || chapterLink.classList.contains("active")) {
                continue;
            }

            const container = document.createElement("div");
            container.classList.add("yumemi-page-outline");
            container.append(createHeadingList(pageUrl.href, headings));
            chapterLink.parentElement.after(container);
        }

        void mountHeliogenesis();
        mountDocumentLooksBack();
    });
})();
