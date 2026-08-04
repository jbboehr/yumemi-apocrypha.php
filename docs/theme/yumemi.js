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
        ],
        "integrations.html": [
            { id: "version-policy", title: "Version Policy" },
            { id: "illuminate-cache", title: "Illuminate Cache" },
            { id: "illuminate-http", title: "Illuminate HTTP" },
            { id: "illuminate-support", title: "Illuminate Support" },
            { id: "illuminate-process", title: "Illuminate Process" },
            { id: "illuminate-queue", title: "Illuminate Queue" },
            { id: "limitations", title: "Limitations" },
        ],
        "contributing/maintaining-integrations.html": [
            { id: "before-changing-a-stub", title: "Before Changing A Stub" },
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
    });
})();
