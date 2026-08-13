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
            { id: "illuminate-cache", title: "Illuminate Cache" },
            { id: "illuminate-cookie", title: "Illuminate Cookie" },
            { id: "illuminate-database", title: "Illuminate Database" },
            { id: "illuminate-filesystem", title: "Illuminate Filesystem" },
            { id: "illuminate-http", title: "Illuminate HTTP" },
            { id: "illuminate-support", title: "Illuminate Support" },
            { id: "illuminate-process", title: "Illuminate Process" },
            { id: "illuminate-queue", title: "Illuminate Queue" },
            { id: "illuminate-redis", title: "Illuminate Redis" },
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

    function addHeliogenesisStylesheet(assetRoot, filename) {
        const stylesheet = document.createElement("link");
        stylesheet.rel = "stylesheet";
        stylesheet.href = new URL(filename, assetRoot).href;
        document.head.append(stylesheet);
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

        const assetRoot = new URL(path_to_root + "assets/heliogenesis/", document.location.href);
        addHeliogenesisStylesheet(assetRoot, "heliogenesis.css");
        addHeliogenesisStylesheet(assetRoot, "heliogenesis-document.css");
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
            const heliogenesis = new Heliogenesis({ trigger });
            heliogenesis.mount();
        } catch (error) {
            trigger.remove();
            console.error("Unable to mount Heliogenesis.", error);
        }
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
    });
})();
